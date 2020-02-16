<?php //スマホだけ表示 ?>
<p class="text-light">
    <?= __d('cakebooru', 'DisplayTags') ?>
</p>

<?= $this->element('tag_list') ?>