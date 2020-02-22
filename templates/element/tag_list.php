<?php
    if (isset($tags) && count($tags)) : ?>
        <?php foreach($tags as $tag) : ?>
            <?php $tagUrl = $this->Url->build([
                'controller' => 'Posts',
                'action' => 'index',
                '?' => [
                    'search' => $tag['tag']
                ]
            ]) ?>
            <div>
                <a href="<?= $tagUrl ?>" class="chips mb-2">
                    <?= $tag['tag'] ?>
                </a>
                <span class="badge badge-secondary">
                    <?= $tag['tag_count'] ?>
                </span>
            </div>
        <?php endforeach ?>
    <?php else : ?>
        <p class="text-light d-block d-sm-none"><?= __d('cakebooru', 'No Tags') ?></p>
        <p class="d-sm-block d-none"><?= __d('cakebooru', 'No Tags') ?></p>
    <?php endif ?>