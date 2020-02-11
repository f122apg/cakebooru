<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\ResultSet;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Error\Debugger;
use Cake\Collection\Collection;
use Cake\Core\Configure;

/**
 * Posts Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\FavoritesTable&\Cake\ORM\Association\HasMany $Favorites
 *
 * @method \App\Model\Entity\Post get($primaryKey, $options = [])
 * @method \App\Model\Entity\Post newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Post[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Post|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Post saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Post patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Post[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Post findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PostsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('posts');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Tag');

        // $this->hasMany('Favorites', [
        //     'foreignKey' => 'post_id',
        // ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsToMany('Tags', [
            'through' => 'PostsTags',
            'cascadeCallbacks' => true
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->nonNegativeInteger('id')
            ->allowEmptyString('id', null, 'create');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }

    /**
     * beforeSave
     *
     * @param Event $event event
     * @param EntityInterface $entity entity
     * @param array $options options
     * @return bool
     */
    public function beforeSave($event, $entity, $options) : bool
    {
        //新規追加の場合、アップロードされたファイルを保存する
        if ($entity->isNew()) {
            $entity->prepareInsert();
        }

        return true;
    }

    /**
     * 検索文字列からpostsを検索する
     *
     * @param string $search 検索文字列
     * @return ResultSet 検索結果
     */
    public function getPostsBySearch(string $search): ResultSet
    {
        $tagCons = $this->getTagConditions($search);
        $notOr = $this->__getPostsNotOr($tagCons);
        $andNormal = $this->__getPostsAndNormal($tagCons);

        $col[0] = new Collection($notOr);
        $col[1] = new Collection($andNormal);
        $postIds = [];
        foreach ($col as $v) {
            $postIds = array_merge($postIds, $v->extract('id')->toList());
        }

        return $this->find()
            ->contain(['Tags'])
            ->where(['Posts.id IN' => $postIds])
            ->all();
    }

    /**
     * NOT、OR条件で検索を行う
     *
     * @param array $tagCons タグの検索条件
     * @return ResultSet|array 検索結果
     */
    public function __getPostsNotOr(array $tagCons)
    {
        if (!(isset($tagCons['NOT']) || isset($tagCons['OR']))) {
            return [];
        }

        $q = $this->find()
            ->select(['Posts.id'])
            ->contain(['Tags'])
            ->matching('Tags', fn($q) => $q->where(function($exp, $query) use ($tagCons) {
                //OR like
                if (isset($tagCons['OR'])) {
                    $orCons = $exp->or(function ($or) use ($tagCons) {
                        foreach ($tagCons['OR'] as $key => $v) {
                            $or->like('Tags.tag', '%' . $v . '%');
                        }

                        return $or;
                    });
                }

                //NOT like
                if (isset($tagCons['NOT'])) {
                    foreach ($tagCons['NOT'] as $key => $v) {
                        $exp->notLike('Tags.tag', '%' . $v . '%');
                    }
                }

                return $exp->add($orCons);
            }))
            ->all();

        return $q;
    }

    /**
     * AND条件で検索を行う
     *
     * @param array $tagCons タグの検索条件
     * @return ResultSet|array 検索結果
     */
    public function __getPostsAndNormal($tagCons)
    {
        if (!(isset($tagCons['AND']) || isset($tagCons['NORMAL']))) {
            return [];
        }

        $cons = array_merge($tagCons['AND'] ?? [], $tagCons['NORMAL'] ?? []);
        //LIKE用の条件を作成
        $sqlCons = array_map(fn($c) => ['Tags.tag LIKE' => '%' . $c. '%'], $cons);

        $q = $this->find()
            ->select(['Posts.id'])
            ->contain(['Tags'])
            ->matching('Tags', function (Query $q) use ($sqlCons) {
                return $q->where($sqlCons);
            })
            ->group('Posts.id')
            ->having(['COUNT(Posts.id) >=' => count($sqlCons)])
            ->all();

        return $q;
    }
}
