<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Favorite[]|\Cake\Collection\CollectionInterface $favorites
 */
?>
<div class="favorites index content">
    <?= $this->Html->link(__('New Favorite'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Favorites') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('post_id') ?></th>
                    <th><?= $this->Paginator->sort('user_id') ?></th>
                    <th><?= $this->Paginator->sort('created') ?></th>
                    <th><?= $this->Paginator->sort('modified') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($favorites as $favorite): ?>
                <tr>
                    <td><?= $this->Number->format($favorite->id) ?></td>
                    <td><?= $favorite->has('post') ? $this->Html->link($favorite->post->id, ['controller' => 'Posts', 'action' => 'view', $favorite->post->id]) : '' ?></td>
                    <td><?= $favorite->has('user') ? $this->Html->link($favorite->user->name, ['controller' => 'Users', 'action' => 'view', $favorite->user->id]) : '' ?></td>
                    <td><?= h($favorite->created) ?></td>
                    <td><?= h($favorite->modified) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $favorite->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $favorite->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $favorite->id], ['confirm' => __('Are you sure you want to delete # {0}?', $favorite->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>
</div>
