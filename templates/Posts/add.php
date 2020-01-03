<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Post $post
 */
?>
<?php $this->Html->script('posts_add', ['block' => true]) ?>

<div class="posts form content container">
    <?= $this->Form->create($post, ['type' => 'file']) ?>
    <fieldset>
        <legend><?= __d('cakebooru', 'Add post') ?></legend>
        <?= $this->Form->hidden('user_id', ['value' => 1]) ?>
        <div class="row">
            <div class="col-4">
                <?php $this->Form->unlockField('file') ?>
                <?= $this->Form->control('file', [
                    'id' => 'uploadFile',
                    'type' => 'file',
                    'label' => __d('cakebooru', 'File'),
                    'required' => true,
                    'accept' => '.jpg,.jpeg,.png,.bmp,.gif'
                ]) ?>
            </div>
            <div class="col-8">
                <div class="card image-preview d-none">
                    <img id="imagePreviewer" class="card-img-top" src="">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-8">
                <?= $this->Form->control('tags', [
                    'label' => __d('cakebooru', 'Tags'),
                    'help' => __d('cakebooru', 'tag separate "@"'),
                    'required' => false
                ]) ?>
            </div>
            <div class="offset-4"></div>
        </div>
    </fieldset>
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-cloud-upload-alt"></i>
        <?= __d('cakebooru', 'Upload') ?>
    </button>
    <?= $this->Form->end() ?>
</div>
