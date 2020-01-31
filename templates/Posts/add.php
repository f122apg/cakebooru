<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Post $post
 */
?>
<?php $this->Html->script('posts_add', ['block' => true]) ?>
<?= $this->element('js_const') ?>

<div id="vue" class="posts form content container">
    <?= $this->Form->create($post, [
        'type' => 'file',
        'id' => 'postForm'
    ]) ?>
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
            <div class="col-4">
                <div class="form-group">
                    <?= $this->Form->label('tag', __d('cakebooru', 'Tag')); ?>
                    <div class="input-group">
                        <?= $this->Form->text('tagInput', [
                            'id' => 'tag',
                            'onkeydown' => 'if (event.key === "Enter") { $("#tagAdd").click(); $("#tag").val(""); return false; }'
                        ]) ?>
                        <div class="input-group-append">
                            <?= $this->Form->button('Add', [
                                'type' => 'button',
                                'id' => 'tagAdd',
                                'v-on:click' => 'addTag',
                            ]) ?>
                        </div>
                    </div>
                    <div class="alert alert-danger p-1 text-center" role="alert" v-cloak v-if="tag.addError">
                        {{ tag.addErrorMsg }}
                    </div>
                </div>
            </div>
            <div class="col-4">
                <p><?= __d('cakebooru', 'Tags') ?></p>
                <div v-for="(tag, i) in tag.tags" class="d-inline-block pr-1">
                    <?php $this->Form->unlockField('tag') ?>
                    <?= $this->Form->hidden('tag', [
                        'v-bind:value' => 'tag',
                        'v-bind:name' => 'tagInputName(i)'
                    ]) ?>
                    <p class="chips" v-cloak v-bind:data-tag-index="i">
                        {{ tag }}
                        <i class="material-icons" v-on:click="removeTag">close</i>
                    </p>
                </div>
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
