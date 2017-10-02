<?php

// app/View/Subjects/view.ctp
$this->extend('/Common/view');

$this->assign('title', 'Subject area');

$this->start('sidebar');
?>

<?php
//if authorized show edit link
if (AuthComponent::user('id')){
	
echo "<li>";
echo $this->Html->link('new '.$this->Html->image("pencil.png", array("alt" => "edit")), array(
	'controller'=>'subjects',
    'action' => 'add'
),array('escape'=>false,'class'=>'edit_button')); 
echo "</li>";

echo "<li>";
echo $this->Html->link('edit '.$this->Html->image("pencil.png", array("alt" => "edit")), array(
	'controller'=>'subjects',
    'action' => 'edit',	
    $subject['Subject']['id']
),array('escape'=>false,'class'=>'edit_button')); 
echo "</li>";
}?>

<?php $this->end(); ?>

<div class="subject view form">
	
	<h3><?php echo $subject['Subject']['subject'];?></h3>
	
	Assigned to:<br />	
		<div class="indented_list">
		<ul>
		<?php foreach ($subject['Faculty'] as $faculty){?>
			<li><?php echo $this->Html->link($faculty['full_name'],array('controller'=>'addresses','action'=>'view',$faculty['userid']));?></li><br />
		<?php }?>
		</ul>
		</div>
</div>	
