<?php $postAddUrl = $this->Url->build([
    'controller' => 'Posts',
    'action' => 'add'
]) ?>
<button class="btn btn-outline-primary" type="button" onclick="location.href = '<?= $postAddUrl ?>'">
    <i class="fas fa-cloud-upload-alt"></i>
    <?= __d('cakebooru', 'Add post') ?>
</button>