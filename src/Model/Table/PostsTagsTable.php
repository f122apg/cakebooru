<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * PostsTags Model
 *
 * @property \App\Model\Table\PostsTable&\Cake\ORM\Association\BelongsTo $Posts
 * @property \App\Model\Table\TagsTable&\Cake\ORM\Association\BelongsTo $Tags
 *
 * @method \App\Model\Entity\PostsTag get($primaryKey, $options = [])
 * @method \App\Model\Entity\PostsTag newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\PostsTag[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\PostsTag|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\PostsTag saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\PostsTag patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\PostsTag[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\PostsTag findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PostsTagsTable extends Table
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

        $this->setTable('posts_tags');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('CounterCache', [
            'Tags' => [
                'tag_count' => [
                    'finder' => 'tagging'
                ]
            ]
        ]);

        $this->belongsTo('Posts', [
            'foreignKey' => 'post_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Tags', [
            'foreignKey' => 'tag_id',
            'joinType' => 'INNER',
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
        $rules->add($rules->existsIn(['post_id'], 'Posts'));
        $rules->add($rules->existsIn(['tag_id'], 'Tags'));

        return $rules;
    }

    public function findTagging(Query $query, array $options)
    {
        return $query->group('tag_id');
    }
}
