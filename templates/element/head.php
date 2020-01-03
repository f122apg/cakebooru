<?php
use Cake\Core\Configure;
?>

<?= $this->Html->charset() ?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>
    <?= Configure::read('AppName') ?>
</title>
<?= $this->Html->meta('icon') ?>

<?php //M PLUS Roundedフォントを使用 ?>
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=M+PLUS+Rounded+1c">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/normalize.css@8.0.1/normalize.css">

<?= $this->Html->css('BootstrapUI.bootstrap.min', ['block' => true]) ?>
<?= $this->Html->script('BootstrapUI.bootstrap.min', ['block' => true]) ?>
<?= $this->fetch('meta') ?>
<?= $this->fetch('css') ?>
<?= $this->fetch('script') ?>