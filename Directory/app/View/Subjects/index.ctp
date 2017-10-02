<?php
// app/View/Subjects/index.ctp
//$this->extend('/Common/index');
?>
<div class="subjects index">
<h3>Subject areas</h3>
<?php echo $this->Html->link('Print',array('action'=>'print_view'),array('class'=>'icon-print','target'=>'_blank','title'=>'Print this view'));?>
<span class="actions" style="margin-right:5px; float:right;">
<?php 
	if ($this->action=='view_all') echo $this->Html->link('View paged',array('action'=>'index',array('title'=>'View paged entries')));
	else echo $this->Html->link('View all', array('action'=>'view_all',array('title'=>'View all entries')));
?>
	
</span>
<table cellpadding="0" cellspacing="0">
<tr>

	<th><?php echo $this->Paginator->sort('subject'); ?></th>
	<th>Faculty</th>

	<?php if (AuthComponent::user('id')){?><th><?php echo __d('cake', 'Actions'); ?></th><?php }?>
</tr>
<?php
foreach ($subjects as $subject):
	echo '<tr>';
	echo '<td>' . $this->Html->link(htmlentities($subject['Subject']['subject']),array('action'=>'view',$subject['Subject']['id'])) . '</td>';
	echo '<td>';
	foreach ($subject['Faculty'] as $faculty){
		echo $this->Html->link(h($faculty['full_name']),array('controller'=>'addresses','action'=>'view',$faculty['userid']))."<br />";
	}
	echo "</td>";
	if (AuthComponent::user('id')){
		echo '<td class="actions">';
		echo $this->Html->link(__d('cake', 'View'), array('action' => 'view', $subject['Subject']['id']));
		if (AuthComponent::user('id')){
		echo ' ' . $this->Html->link(__d('cake', 'Edit'), array('action' => 'edit', $subject['Subject']['id']));
		echo ' ' . $this->Form->postLink(
				__d('cake', 'Delete'),
				array('action' => 'delete', $subject['Subject']['id']),
				null,
				__d('cake', 'Are you sure you want to delete # %s?', $subject['Subject']['id'])
			);
		}
		echo '</td>';
	}
	echo '</tr>';
endforeach;

?>
</table>
	<p><?php
	echo $this->Paginator->counter(array(
		'format' => __d('cake', 'Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?></p>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __d('cake', 'previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__d('cake', 'next') .' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
<?php if (AuthComponent::user('id')):?>
<div class="actions">
	<h3><?php echo __d('cake', 'Menu'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__d('cake', 'New %s', 'Subject'), array('action' => 'edit')); ?></li>

	</ul>
</div>
<?php endif;?>