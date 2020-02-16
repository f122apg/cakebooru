<?php //スマホ以外は表示 ?>
<nav class="p-2 d-sm-inline-block d-none">
    <p>
        <?= __d('cakebooru', 'DisplayTags') ?>
    </p>

    <?= $this->element('tag_list') ?>
</nav>