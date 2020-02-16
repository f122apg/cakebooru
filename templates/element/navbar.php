<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <?php //スマホはハンバーガーメニューを表示する ?>
    <div class="d-block d-sm-none">
        <a href="#" class="text-light" data-toggle="modal" data-target="#drawer">
            <i class="material-icons">menu</i>
        </a>
    </div>

    <div class="modal left fade" id="drawer" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content bg-dark">
                <div class="modal-body">
                    <?= $this->element('navbar/search', ['sp' => true]) ?>
                    <?= $this->element('navbar/add') ?>
                    <?= $this->element('navbar/links', ['sp' => true]) ?>

                    <hr class="bg-secondary">

                    <?= $this->element('tag_list_sp') ?>
                </div>
            </div>
        </div>
    </div>

    <?php //スマホ以外はAppNameのリンクを表示する ?>
    <?= $this->Html->link(
        \Cake\Core\Configure::read('AppName'),
        '/',
        ['class' => 'navbar-brand']
    ) ?>

    <div class="d-sm-inline-block d-none collapse navbar-collapse">
        <?= $this->element('navbar/links') ?>
        <?= $this->element('navbar/search') ?>
        <?= $this->element('navbar/add') ?>
    </div>
</nav>
<div class="pt-3"></div>