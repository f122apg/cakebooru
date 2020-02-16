<?php
//スマートフォン用class
if ($sp ?? false) {
    $class = 'nav flex-column';
} else {
//PC用class
    $class = 'navbar-nav mr-auto';
}
?>

<ul class="<?= $class ?>">
    <?= $this->NavBar->link('My Account', '/users') ?>
    <?= $this->NavBar->link('Posts', '/posts') ?>
</ul>