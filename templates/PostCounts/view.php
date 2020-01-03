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
            <?= $this->Html->link(__('Edit Post Count'), ['action' => 'edit', $postCount->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Post Count'), ['action' => 'delete', $postCount->id], ['confirm' => __('Are you sure you want to delete # {0}?', $postCount->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Post Counts'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Post Count'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="postCounts view content">
            <h3><?= h($postCount->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($postCount->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Post Count') ?></th>
                    <td><?= $this->Number->format($postCount->post_count) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($postCount->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($postCount->modified) ?></td>
                </tr>
            </table>
            <div class="text">
                <strong><?= __('Folder') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($postCount->folder)); ?>
                </blockquote>
            </div>
        </div>
    </div>
</div>
