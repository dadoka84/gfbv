
<div class="<?php echo $this->class; ?>">
<h3><a href="<?php echo $this->href; ?>" title="<?php echo $this->title; ?>"><?php echo $this->link; ?></a> <span class="relevance">[<?php echo $this->relevance; ?>%]</span></h3>
<?php if ($this->context): ?>
<p class="context"><?php echo $this->context; ?></p>
<?php endif; ?>
<p class="url"><?php echo $this->url; ?><span class="filesize"> - <?php echo $this->filesize; ?> kB</span></p>
</div>
