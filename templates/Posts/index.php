<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Post[]|\Cake\Collection\CollectionInterface $posts
 */
use App\Utility\File;
?>
<div class="posts index content">
    <div class="row row-cols-1">
        <?php foreach($posts as $post): ?>
            <div class="col-auto pt-3">
                <div class="card h-100">
                    <?php $detailsUrl = $this->Url->build([
                        'controller' => 'Posts',
                        'action' => 'view',
                        '?' => [
                            'id' => $post->id
                        ]
                    ]) ?>
                    <a href="<?= $detailsUrl ?>">
                        <img class="card-img-top" src="data:image/jpg;base64,<?= File::getThumbnailFileContent($post->thumbnail) ?>">
                    </a>
                </div>
            </div>
        <?php endforeach ?>
    </div>
</div>