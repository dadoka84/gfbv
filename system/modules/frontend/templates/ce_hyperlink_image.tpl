
<div class="<?php echo $this->class; ?> block"<?php echo $this->cssID; ?><?php if ($this->style): ?> style="<?php echo $this->style; ?>"<?php endif; ?>>
<?php if ($this->headline): ?>

<<?php echo $this->hl; ?>><?php echo $this->headline; ?></<?php echo $this->hl; ?>>
<?php endif; ?>

<div class="image_container">
<?php echo $this->embed_pre; ?><a href="<?php echo $this->href; ?>" class="hyperlink_img" title="<?php echo $this->title; ?>"<?php echo $this->target; ?>><img src="<?php echo $this->src; ?>"<?php echo $this->imgSize; ?> alt="<?php echo $this->alt; ?>" title="<?php echo $this->title; ?>" /></a><?php echo $this->embed_post; ?>
<?php if ($this->caption): ?>
<div class="caption"><?php echo $this->caption; ?></div>
<?php endif; ?>
</div>

</div>
