
<!-- indexer::stop -->
<div class="<?php echo $this->class; ?> block"<?php echo $this->cssID; ?><?php if ($this->style): ?> style="<?php echo $this->style; ?>"<?php endif; ?>>
<?php if ($this->headline): ?>

<<?php echo $this->hl; ?>><?php echo $this->headline; ?></<?php echo $this->hl; ?>>
<?php endif; ?>

<div class="image_container"<?php if ($this->margin): ?> style="<?php echo $this->margin; ?>"<?php endif; ?>>
<?php if ($this->link): ?><a href="<?php echo $this->link; ?>" title="<?php echo $this->alt; ?>"><?php endif; ?><img src="<?php echo $this->src; ?>"<?php echo $this->imgSize; ?> alt="<?php echo $this->alt; ?>" /><?php if ($this->link): ?></a><?php endif; ?>
<?php if ($this->caption): ?>
<div class="caption"><?php echo $this->caption; ?></div>
<?php endif; ?>
</div>

</div>
<!-- indexer::continue -->
