
<div class="<?php echo $this->class; ?> block"<?php echo $this->cssID; ?><?php if ($this->style): ?> style="<?php echo $this->style; ?>"<?php endif; ?>>
<?php if ($this->headline): ?>

<<?php echo $this->hl; ?>><?php echo $this->headline; ?></<?php echo $this->hl; ?>>
<?php endif; ?>

<ul>
<?php foreach($this->newsletters as $newsletter): ?>
  <li><?php echo $newsletter['datim']; ?>: <a href="<?php echo $newsletter['href']; ?>"><?php echo $newsletter['subject']; ?></a></li>
<?php endforeach; ?>
</ul>

</div>
