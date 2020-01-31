<?php
use Cake\Core\Configure;
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <?= $this->Html->link(
        Configure::read('AppName'),
        '/',
        ['class' => 'navbar-brand']
    ) ?>

    <div class="collapse navbar-collapse">
        <ul class="navbar-nav mr-auto">
            <?= $this->NavBar->link('My Account', '/users') ?>
            <?= $this->NavBar->link('Posts', '/posts') ?>
        </ul>

        <?php //CSRFトークンは必要ないので、CakePHPのヘルパーは使わない ?>
        <form method="get" class="form-inline my-2 my-lg-0 mr-3">
            <?= $this->Form->control('search', [
                'type' => 'text',
                'label' => false,
                'required' => true,
                'class' => 'form-control mr-sm-2',
                'aria-label' => 'Search',
                'placeholder' => 'Search'
            ]) ?>
            <?= $this->Form->button(__d('cakebooru', 'Search'), [
                'class' => 'btn btn-outline-success my-2 my-sm-0'
            ]) ?>
        </form>

        <?php $postAddUrl = $this->Url->build([
            'controller' => 'Posts',
            'action' => 'add'
        ]) ?>
        <button class="btn btn-outline-primary" type="button" onclick="location.href = '<?= $postAddUrl ?>'">
            <i class="fas fa-cloud-upload-alt"></i>
            <?= __d('cakebooru', 'Add post') ?>
        </button>
    </div>
</nav>
<div class="pt-3"></div>