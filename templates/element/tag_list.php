<?php
use Cake\Core\Configure;
?>
<nav class="p-2">
    <p><?= __d('cakebooru', 'DisplayTags') ?></p>

    <?php if (isset($tags) && count($tags)) : ?>
        <?php foreach($tags as $tag): ?>
            <div>
                <a href="" class="chips mb-2">
                    <?= $tag['tag'] ?>
                </a>
                <span class="badge badge-secondary">
                    <?= $tag['tag_count'] ?>
                </span>
            </div>
        <?php endforeach ?>
    <?php else : ?>
        <p><?= __d('cakebooru', 'No Tags') ?></p>
    <?php endif ?>
</nav>