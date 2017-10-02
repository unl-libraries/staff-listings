<?php // app/View/Common/view.ctp ?>
<h1><?php echo $this->fetch('title'); ?></h1>
<?php echo $this->fetch('content'); ?>

<div class="actions">
    <h3>Menu</h3>
    <ul>
    <?php echo $this->fetch('sidebar'); ?>
    </ul>
</div>