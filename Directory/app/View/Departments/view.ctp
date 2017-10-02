<?php
// app/View/departmentes/view.ctp
$this->extend('/Common/view');

$this->assign('title', $department['Department']['name']." ".(!empty($department['Department']['abbreviation'])?"(".$department['Department']['abbreviation'].' )':''));

$this->start('sidebar');
?>


<?php
//if authorized show edit link
if (AuthComponent::user('id')){
echo "<li>";
echo $this->Html->link('edit '.$this->Html->image("pencil.png", array("alt" => "edit")), array(
	'controller'=>'departments',
    'action' => 'edit',	
    $department['Department']['id']
),array('escape'=>false,'class'=>'edit_button')); 
echo "</li>";}?>

<?php $this->end(); ?>

<div class="department view form">
<h3>Library Unit information</h3>
	<div class="department">
		<ul>
			<li><label>Name</label><span><?php echo $department['Department']['name']?></span></li><br />
			<li><label>Abbreviation</label><span><?php echo $department['Department']['abbreviation']?></span></li><br />
			<?php if (!empty($department['Department_above']['id'])){?><li><label>Under </label><span><?php echo $this->Html->link($department['Department_above']['name'],array('controller'=>'departments','action'=>'view',$department['Department_above']['id']));?></span></li><br />
			<?php }?>		
		</ul>
	</div>
	<?php if (!empty($department['Sub_department'])):?>
	<h3><?php echo (empty($department['Department_above']['id'])?'Departments':'Sub departments');?></h3>
	<div class="subdepartment">
		<ul>
		<?php foreach ($department['Sub_department'] as $subdepartment):?>
			<li><?php echo $this->Html->link($subdepartment['name'],array('controller'=>'departments','action'=>'view',$subdepartment['id']));?></li><br />
		<?php endforeach;?>
		
		</ul>
	</div>
	<?php endif;
	if (!empty($department_staff)):?>
	<h3>Staff</h3>
	<div class="staff">
		<ul>
			<?php foreach ($department_staff as $staff):?>
			<li><?php echo $this->Html->link(($staff['Address']['full_name']),array('controller'=>'addresses','action'=>'view',$staff['Staff']['userid']),array('escapeTitle'=>false));?></li><br />
			<?php endforeach;?>
		</ul>
	</div>
	<?php endif; ?>
</div>