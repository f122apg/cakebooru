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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>

<?= $this->Html->css('BootstrapUI.bootstrap.min', ['block' => true]) ?>
<?= $this->Html->script('BootstrapUI.bootstrap.min', ['block' => true]) ?>
<?= $this->Html->script('BootstrapUI.jquery.min') ?>

<?= $this->Html->css('style', ['block' => true]) ?>
<?= $this->fetch('meta') ?>
<?= $this->fetch('css') ?>
<?= $this->fetch('script') ?>