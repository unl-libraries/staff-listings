<?php

// app/View/Users/view.ctp
$this->extend('/Common/view');

$this->assign('title', 'User information');
//if authorized show edit link
if (AuthComponent::user('id')){
$this->start('sidebar');
?>
<li>
<?php

echo $this->Html->link('new '.$this->Html->image("pencil.png", array("alt" => "edit")), array(
	'controller'=>'users',
    'action' => 'add'
),array('escape'=>false,'class'=>'edit_button')); ?>
</li>
<li>
<?php
//if authorized show edit link
echo $this->Html->link('edit '.$this->Html->image("pencil.png", array("alt" => "edit")), array(
	'controller'=>'users',
    'action' => 'edit',	
    $user['User']['id']
),array('escape'=>false,'class'=>'edit_button')); ?>
</li>
<li>
<?php
//if authorized show edit link
echo $this->Html->link('list ' ,array(
	'controller'=>'users',
    'action' => 'index',
	'class'=>'edit_button')    
); ?>
</li>
<?php $this->end(); }?>

<div class="user view form">
	<?php //print_r($user);?>
	<h3><?php echo $user['User']['username'];?></h3>
		<div class="indented_list">
		<ul>
			<li>Name: <?php echo $user['User']['firstname']." ".$user['User']['lastname'];?></li>
			<li>Email: <?php echo $user['User']['email'];?></li>
			<li>Phone: <?php echo $user['User']['phone'];?></li>
			<li>Created: <?php echo $user["User"]["created"];?></li>
			<li>Modified: <?php echo $user["User"]["modified"];?></li>
		
		</ul>
		</div>
</div>	
