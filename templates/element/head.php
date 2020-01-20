<?php
use Cake\Core\Configure;
?>

<?= $this->Html->charset() ?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>
    <?= Configure::read('AppName') ?>
</title>
<?= $this->Html->meta('icon') ?>

<?= $this->Html->css('normalize', ['block' => true]) ?>
<?= $this->Html->css('fontawesome/all.min', ['block' => true]) ?>
<?= $this->Html->css('material-icon.css', ['block' => true]) ?>
<?= $this->Html->css('BootstrapUI.bootstrap.min', ['block' => true]) ?>

<?= $this->Html->script('BootstrapUI.bootstrap.min', ['block' => true]) ?>
<?= $this->Html->script('BootstrapUI.jquery.min') ?>
<?= $this->Html->script('vue-development.js') ?>

<?= $this->Html->css('style', ['block' => true]) ?>
<?= $this->fetch('meta') ?>
<?= $this->fetch('css') ?>
<?= $this->fetch('script') ?>