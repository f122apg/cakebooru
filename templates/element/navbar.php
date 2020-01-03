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
        <form class="form-inline my-2 my-lg-0">
        <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
    </div>
</nav>