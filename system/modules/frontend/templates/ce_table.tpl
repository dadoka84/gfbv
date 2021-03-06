
<div class="<?php echo $this->class; ?> block"<?php echo $this->cssID; ?><?php if ($this->style): ?> style="<?php echo $this->style; ?>"<?php endif; ?>>
<?php if ($this->headline): ?>

<<?php echo $this->hl; ?>><?php echo $this->headline; ?></<?php echo $this->hl; ?>>
<?php endif; ?>

<table cellspacing="0" cellpadding="0"<?php if ($this->sortable): ?> class="sortable"<?php endif; ?> id="<?php echo $this->id; ?>" summary="<?php echo $this->summary; ?>">
<?php if ($this->useHeader): ?>
<thead>
<tr>
<?php foreach ($this->header as $col): ?>
  <th class="<?php echo $col['class']; ?>"><?php echo $col['content']; ?></th>
<?php endforeach; ?>
</tr>
</thead>
<?php endif; ?>
<?php if ($this->useFooter): ?>
<tfoot>
<tr>
<?php foreach ($this->footer as $col): ?>
  <td class="<?php echo $col['class']; ?>"><?php echo $col['content']; ?></td>
<?php endforeach; ?>
</tr>
</tfoot>
<?php endif; ?>
<tbody>
<?php foreach ($this->body as $class=>$row): ?>
<tr class="<?php echo $class; ?>">
<?php foreach ($row as $col): ?>
  <td class="<?php echo $col['class']; ?>"><?php echo $col['content']; ?></td>
<?php endforeach; ?>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php if ($this->sortable): ?>

<script type="text/javascript">
<!--//--><![CDATA[//><!--
window.addEvent('domready', function() { new TableSort('<?php echo $this->id; ?>'); });
//--><!]]>
</script>
<?php endif; ?>

</div>
