<?php
declare(strict_types=1);

namespace App\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Table;
use Cake\Core\Configure;

/**
 * Tag behavior
 */
class TagBehavior extends Behavior
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

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
                    if (isset($searchWords[$k + 1])) {
                        //次の文字列（k + 1）にNOT、AND、OR条件が付いていなければ通常の条件とする
                        if ($searchWords[$k + 1] !== $operators['NOT'] &&
                            $searchWords[$k + 1] !== $operators['AND'] &&
                            $searchWords[$k + 1] !== $operators['OR']) {
                            $tagConditions['NORMAL'][] = $word;
                        }
                    } else {
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
