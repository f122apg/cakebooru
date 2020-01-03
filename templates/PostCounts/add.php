<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\PostCount $postCount
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Post Counts'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="postCounts form content">
            <?= $this->Form->create($postCount) ?>
            <fieldset>
                <legend><?= __('Add Post Count') ?></legend>
                <?php
                    echo $this->Form->control('folder');
                    echo $this->Form->control('post_count');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
