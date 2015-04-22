<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Scaffolds
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
?>
<div class="staffData form">
<?php

	echo $this->Form->create();
	//echo $this->Form->input('Address.full_name', array('label' => 'Name',array('disabled'=>true));
	echo $this->Form->input('id');
	echo $this->Form->input('return_url',array('type'=>'hidden','default'=>$return_url))
	?>
	<fieldset><legend>Edit Staff Data</legend>
	<div class="input text"><label>Name:</label><span><?php echo $this->data['Address']['full_name']?></span></div>
	<?php echo $this->Form->input('preferred_name');?>
	<div class="input text"><label>UNL title:</label><span><?php echo $this->data['Address']['unl_position']?></span></div>
	<?php echo $this->Form->input('library_title');?>
	<div class="input text"><label>Address:</label><?php echo $this->data['Address']['address']?></div>
	<div class="input text"><label>UNL department:</label><span><?php echo $this->data['Address']['unl_dept']?></span></div>
	<?php
	echo $this->Form->input('Department',array('label'=>'Departments'));
	echo $this->Form->input('location',array('type'=>'select','options'=>array(""=>"Select a location","ARCH"=>"ARCH","CYT"=>"CYT","ENGR"=>"ENGR","GEOL"=>"GEOL","LDRF"=>"LDRF","LOVE"=>"LOVE","MATH"=>"MATH","MUSIC"=>"MUSIC","LAW"=>"LAW")));
	if (strtolower($this->data['Address']['unl_status'])=='faculty') echo $this->Form->input('Subjects',array('label'=>'Subject Assignments'));
	?></fieldset>
	<?php 
	echo $this->Form->end(__d('cake', 'Save'));
?>
</div>
<div class="actions">
	<h3><?php echo __d('cake', 'Actions'); ?></h3>
	<ul>

		
<?php
		echo "\t\t<li>" . $this->Html->link(
						__d('cake', 'List Subjects'),
						array('controller' => 'subjects', 'action' => 'index')
					) . "</li>\n";
		echo "\t\t<li>" . $this->Html->link(
			__d('cake', 'New Subject'),
			array('controller' => 'subjects', 'action' => 'add')
		) . "</li>\n";
		
?>
	</ul>
</div>
