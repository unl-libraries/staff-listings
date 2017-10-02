<?php
// app/View/Addresses/view.ctp
$this->extend('/Common/view');

$this->assign('title', (!empty($address['StaffData']['preferred_name'])?$address['StaffData']['preferred_name']." ".$address['Address']['last_name']:$address['Address']['full_name']).' - '.$address['Address']['unl_status']);

$this->start('sidebar');
?>
<li>
	<?php echo $this->Html->link('vcard '.$this->Html->image("vcard.png", array("alt" => "Vcard")),array(
			'action'=>'vcard',			
			$address['Address']['userid']
		),array('escape'=>false,'class'=>'edit_button'));?>
</li>
<li>
<?php
//if authorized show edit link
if (AuthComponent::user('id')){
echo "<li>";
echo $this->Html->link('edit '.$this->Html->image("pencil.png", array("alt" => "edit")), array(
	'controller'=>'staff_data',
    'action' => 'edit',	
    $address['StaffData']['id']
),array('escape'=>false,'class'=>'edit_button'));
echo "</li>";
} ?>

<?php $this->end(); ?>

<div class="address view form">
<h3>Contact</h3>
	<div class="contact">
		<ul>
			<li><label>Name</label><span><?php echo $address['Address']['first_name']." ".(!empty($address['Address']['middle_name'])?$address['Address']['middle_name']." ":'').$address['Address']['last_name']?></span></li><br />
			<li><label>Email</label><span><a href="mailto:<?php echo $address['Address']['email']?>"><?php echo $address['Address']['email']?></a></span></li><br />
			<li><label>Phone</label><span><?php echo $address['Address']['phone']?></span></li><br />
			<li><label>Address</label><span><?php echo $address['Address']['address']?></span></li><br />
			<?php echo (!empty($address['StaffData']['website'])?"<li><label>Website</label><span>".$this->Html->link($address['StaffData']['website'])."</span></li><br />":'');?>
			<li><label>Last Updated (UNL):</label><span><?php echo $address['StaffData']['last_updated']?></span></li>
		</ul>
	</div>
	<h3>Position/title</h3>
	<div class="positions">
		<ul>
			<li><label>UNL position:</label><span><?php echo $address['Address']['unl_position']?></span></li><br />
		<?php if (!empty($address['Address']['library_position']) && ($address['Address']['library_position']!=$address['Address']['unl_position'])){ ?>
			<li><label>Library title:</label><span><?php echo $address['Address']['library_position'];?></span></li><br />
			
		<?php  }?>

		<li><label>Department:</label>
			<?php if ($address['Address']['unl_dept']!='University Libraries'):?><span><?php echo $address['Address']['unl_dept']?></span><br /><?php endif;?>
			
			<?php if (!empty($address['StaffData']['Department'])):
				foreach ($address['StaffData']['Department'] as $dept){?>
				<span><?php echo $this->Html->link($dept['name'],array('controller'=>'departments','action'=>'view',$dept['id']));?></span><br />
			<?php }
			endif;?>			
		</li><br />
				<li><label>Other positions:</label> 
			<div>
			<?php for ($i=1; $i<=3; $i++){
				//debug ($address['Address']['position_'.$i]);
				if ($address['Address']['position_'.$i] != $address['Address']['unl_position'] &&(!empty($address['Address']['position_'.$i]))) echo "<span class='position'>{$address['Address']['org_unit_'.$i]} - {$address['Address']['position_'.$i]}</span><br />";
			}
			?>
			</div>
		</li><br />
		</ul>
	</div>
	<!--  only display if faculty -->
	<?php if ($address['Address']['unl_status']=='faculty'):?>
	<h3>Subject Areas</h3>
	<div class="subjects">
			<?php if (!empty($address['StaffData']['Subjects'])){?>
			<ul>
				<?php foreach ($address['StaffData']['Subjects'] as $subject):?>
				<li><?php echo $this->Html->link($subject['subject'],array('controller'=>'subjects','action'=>'view',$subject['id']));?></li>
				<?php endforeach?>
			</ul>
			<?php }
			 echo (!empty($address['Address']['libguide_profile'])?"<label>Libguide profile:</label><span>".$this->Html->link($address['Address']['libguide_profile'])."</span>":'');?>
	</div>
	
	
	<?php endif;?>
</div>