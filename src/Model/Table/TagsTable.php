<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Collection\Collection;
use Cake\Core\Configure;

/**
 * Tags Model
 *
 * @method \App\Model\Entity\Tag get($primaryKey, $options = [])
 * @method \App\Model\Entity\Tag newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Tag[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Tag|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Tag saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Tag patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Tag[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Tag findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TagsTable extends Table
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

        $this->setTable('tags');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->belongsToMany('Posts', [
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
        // $validator
        //     ->scalar('tag')
        //     ->maxLength('tag', 255)
        //     ->allowEmptyString('tag', null, 'create');

        // $validator
        //     ->integer('tag_count')
        //     ->allowEmptyString('tag_count');

        return $validator;
    }

    /**
     * タグからidを取得する
     *
     * @param string $tag タグ
     * @return int|null ヒットすればintを返し、なければnull
     */
    public function getIdByTag($tag) : ?int
    {
        $entity = $this
            ->find()
            ->where(['Tags.tag' => $tag])
            ->first();

        return !is_null($entity) ? $entity->id : null;
    }

    /**
     * belongsToMany用のデータを作成する
     *
     * @param array $datas 元データ
     * @return array 加工済みデータ
     */
    public function createBtmData(array $datas) : array
    {
        //配列じゃない or 要素が0ならば加工せずに返す
        if (!is_array($datas) || count($datas) === 0) {
            return $datas;
        }

        foreach ($datas as $k => $data) {
            //タグがなければ、次のループへ
            if (!isset($data['tag'])) {
                continue;
            }

            //既存タグがあれば、そのタグのidを入れる
            $id = $this->getIdByTag($data['tag']);
            if (!is_null($id)) {
                $datas[$k]['id'] = $id;
                unset($datas[$k]['tag']);
            }
        }

        return $datas;
    }

    /**
     * Postsからタグを取得
     *
     * @param array|\App\Model\Entity\Post $posts Postsのentity配列またはentity
     * @return array Tagsのentity
     */
    public function getTagsDistinctByPost($posts) : array
    {
        //to iterable
        if (!is_iterable($posts)) {
            $posts = [$posts];
        }

        $col = new Collection($posts);
        $tags = $col->extract('tags'); //Tagsのentityだけを取得
        //全てnullならば、タグはセットされていないとしてから配列を返す
        if ($tags->every(fn($v) => $v === null)) {
            return [];
        }

        //Tagsのentityからtagとtag_countだけ取ってくる
        $tags = $tags
            ->reject(fn($v) => !count($v)) //要素が0ならば削除
            ->unfold() //平坦化
            ->map(fn($v) => ['tag' => $v->tag, 'tag_count' => $v->tag_count]) //tagとtag_countの配列にする
            ->toList(); //配列化

        //重複を取り除きかつ、歯抜けの状態を直す
        return array_values(array_unique($tags, SORT_REGULAR));
    }

    /**
     * タグの検索条件を検索文字列から生成する
     * AND OR NOTに対応
     *
     * @param string $searchWord 検索文字列
     * @return array タグの検索条件
     */
    public function getTagConditions(string $searchWord) : array
    {
        $searchWords = explode(Configure::read('TagDelimiter'), $searchWord);
        $operators = Configure::read('TagOperators');
        $tagConditions = [
            'NOT' => [],
            'AND' => [],
            'OR' => [],
            //実質ANDだけど、ANDと混ぜると後々デバッグが大変になるので分けている
            'NORMAL' => []
        ];

        foreach ($searchWords as $k => $word) {
            //タグが既に何かしらの条件にある場合、条件に追加しない
            if (in_array($word, $tagConditions['NOT'], true) ||
                in_array($word, $tagConditions['AND'], true) ||
                in_array($word, $tagConditions['OR'], true) ||
                in_array($word, $tagConditions['NORMAL'], true)) {
                continue;
            }

            //$wordにもNOT、AND、ORが入ることに注意する
            switch ($word) {
                //NOT条件の追加
                case $operators['NOT']:
                    $tagConditions['NOT'][] = $searchWords[$k + 1];
                    break;
                //AND、OR条件の追加
                case $operators['AND']:
                case $operators['OR']:
                    //NOT条件にAND、ORオペレーターの前に入力された文字列（k - 1）が存在しなければ、
                    //AND、OR条件に追加する
                    //要は「NOT aaa AND bbb」の場合、NOTにaaaが入り、AND、ORにはaaa、bbbが入らない
                    //結果的にNORMAL行きとなる
                    if (!in_array($searchWords[$k - 1], $tagConditions['NOT'], true)) {
                        //AND、OR条件にAND、ORオペレーターの前に入力された文字列（k - 1）が存在しなければ、
                        //AND、OR条件に追加する
                        //要は「aaa OR bbb OR ccc」の場合、ORにaaa、bbb、cccが入る
                        //※aaa、bbb、bbb、cccとはならない
                        if (!in_array($searchWords[$k - 1], $tagConditions[$word], true)) {
                            $tagConditions[$word][] = $searchWords[$k - 1];
                        }
                        $tagConditions[$word][] = $searchWords[$k + 1];
                    }
                    break;
                // //AND条件の追加
                // case $operators['AND']:
                //     if (!in_array($searchWords[$k - 1], $tagConditions['NOT'], true)) {
                //         if (!in_array($searchWords[$k - 1], $tagConditions['AND'], true)) {
                //             $tagConditions['AND'][] = $searchWords[$k - 1];
                //         }
                //         $tagConditions['AND'][] = $searchWords[$k + 1];
                //     }
                //     break;
                // //OR条件の追加
                // case $operators['OR']:
                //     if (!in_array($searchWords[$k - 1], $tagConditions['NOT'], true)) {
                //         if (!in_array($searchWords[$k - 1], $tagConditions['OR'], true)) {
                //             $tagConditions['OR'][] = $searchWords[$k - 1];
                //         }
                //         $tagConditions['OR'][] = $searchWords[$k + 1];
                //     }
                //     break;
                default:
                    //次の文字列（k + 1）にNOT、AND、OR条件が付いていなければ通常の条件とする
                    if ($searchWords[$k + 1] !== $operators['NOT'] &&
                        $searchWords[$k + 1] !== $operators['AND'] &&
                        $searchWords[$k + 1] !== $operators['OR']) {
                        $tagConditions['NORMAL'][] = $word;
                    }
            }
        }

        //要素0ならばunset
        //Impossible to generate condition with empty list of values for field (Tags.tag)対策
        foreach ($tagConditions as $k => $con) {
            if (count($con) === 0) {
                unset($tagConditions[$k]);
            }
        }
        return $tagConditions;
    }
}