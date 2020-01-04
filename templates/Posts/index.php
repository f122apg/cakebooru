<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Post[]|\Cake\Collection\CollectionInterface $posts
 */
use App\Utility\File;
?>
<div class="posts index content mx-auto">
    <div class="pt-3"></div>
        <div class="card-columns">
            <?php foreach($posts as $post): ?>
                <?php $detailsUrl = $this->Url->build([
                    'controller' => 'Posts',
                    'action' => 'view',
                    '?' => [
                        'id' => $post->id
                    ]
                ]) ?>

                <div class="card">
                    <a href="<?= $detailsUrl ?>">
                        <img class="card-img-top thumbnail-container" src="data:image/jpg;base64,<?= File::getThumbnailFileContent($post->thumbnail) ?>">
                        <?php if ($post->ext === 'gif') : ?>
                            <div class="card-img-overlay">
                                <i class="far fa-play-circle fa-2x text-success"></i>
                            </div>
                        <?php endif ?>
                    </a>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</div>