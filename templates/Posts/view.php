<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Post $post
 */
use App\Utility\File;
?>
<?php $this->Html->script('posts_view', ['block' => true]) ?>

<div class="posts view content pt-3 row">
    <div class="col-2">
        <?php //tag area ?>
    </div>
    <div class="col-8">
        <div class="mx-auto image-container">
            <div class="card">
                <a class="originalImage" href="" target="_blank">
                    <img class="card-img-top" src="data:image/<?= $post->ext ?>;base64,<?= File::getUploadedFileContent($post->filename) ?>">
                </a>
            </div>
            <a class="originalImage" href="" target="_blank">
                <?= __d('cakebooru', 'Original image') ?>
            </a>
        </div>
    </div>
    <div class="col-2">
        <?php //image info area ?>
    </div>
</div>

<script>
$(() => {
    $('.originalImage').attr('href', window.URL.createObjectURL(toBlob('<?= File::getUploadedFileContent($post->filename) ?>', '<?= $post->ext ?>')));
})
</script>