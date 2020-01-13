<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PostsTag Entity
 *
 * @property int $id
 * @property int $post_id
 * @property int $tag_id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Post $post
 * @property \App\Model\Entity\Tag $tag
 */
class PostsTag extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'post_id' => true,
        'tag_id' => true,
        'created' => true,
        'modified' => true,
        'post' => true,
        'tag' => true,
    ];
}
