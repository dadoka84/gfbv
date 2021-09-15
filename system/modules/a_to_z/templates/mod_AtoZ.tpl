<div class="mod_atoz" <?php if($this->style): ?> style="<?php echo $this->style; ?>"<?php endif; ?>>

<?php if ($this->headline): ?>
<<?php echo $this->hl; ?>><?php echo $this->headline ?></<?php echo $this->hl; ?>>
<?php endif; ?>
<!-- indexer::stop -->
<?php if($this->pages): ?>
<?php foreach($this->pages as $page): ?>
	<?php if ($letter != mb_substr(ucfirst($page['pageTitle']), 0, 1)): ?>
	  <?php if ($letter != "") echo "</ul>"; ?>
	  <?php $letter = mb_substr(ucfirst($page['pageTitle']), 0, 1); ?>
	  <<?php echo $this->subheader; ?>><?php echo $letter; ?></<?php echo $this->subheader; ?>>
	  <ul class="linklist">
	<?php endif; ?>

	<li><a href="<?php echo $page['href'];?>"><?php echo $page['pageTitle']; ?></a>
	<?php if($page['description']):?>
	<div class="description">
	<?php echo $page['description']; ?>
	</div>
	<?php endif; ?>
	</li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
<!-- indexer::continue -->
</div>

