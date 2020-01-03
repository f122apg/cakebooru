<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Post[]|\Cake\Collection\CollectionInterface $posts
 */
?>
<div class="posts index content">
    <div class="row row-cols-1">
        <?php for($i = 0; $i < 100; $i ++): ?>
            <div class="col-auto pt-3">
                <div class="card h-100">
                    <a href="#">
                        <!-- <img class="card-img-top" src="/img/image3.png"> -->
                    </a>
                </div>
            </div>
        <?php endfor ?>
    </div>
</div>