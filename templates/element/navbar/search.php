<?php
//スマートフォン用class
if ($sp ?? false) {
    $class = [
        'form' => 'form-group',
        'text' => '',
        'button' => 'btn btn-outline-success btn-block'
    ];
} else {
//PC用class
    $class = [
        'form' => 'form-inline my-2 my-lg-0 mr-3',
        'text' => 'form-control mr-sm-2',
        'button' => 'btn btn-outline-success my-2 my-sm-0'
    ];
}
?>

<?php //CSRFトークンは必要ないので、CakePHPのヘルパーは使わない ?>
<?php $actionUrl = \Cake\Routing\Router::url([
    'controller' => 'Posts',
    'action' => 'index'
]) ?>
<form method="get" class="<?= $class['form'] ?>" action="<?= $actionUrl ?>">
    <?= $this->Form->control('search', [
        'type' => 'text',
        'label' => false,
        'required' => true,
        'class' => $class['text'],
        'aria-label' => 'Search',
        'placeholder' => 'Search',
        'value' => $this->request->getQuery('search')
    ]) ?>
    <?= $this->Form->button(__d('cakebooru', 'Search'), [
        'class' => $class['button']
    ]) ?>
</form>