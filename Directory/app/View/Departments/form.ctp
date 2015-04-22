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
<div class="department form">
<?php

	echo $this->Form->create();
	
	echo $this->Form->input('id');
	echo $this->Form->input('return_url',array('type'=>'hidden','default'=>$return_url))
	?>
	<fieldset><legend>Edit Library Department structure</legend>
	<?php echo $this->Form->input('name');?>
	<?php echo $this->Form->input('abbreviation');?>
	<?php echo $this->Form->input('parent_id',array('label'=>'Department under','options'=>$departments,'empty' => '(choose one)'));
	?></fieldset>
	<?php	
	echo $this->Form->end(__d('cake', 'Save'));
	if (isset($this->request->data['Department']['id'])) {
		//echo "<span class='actions'>".$this->Html->link(__d('cake', 'Delete'), array('controller' => 'departments','action' => 'delete', $this->request->data['Department']['id']),array('style'=>'padding:8px 10px;'))."</span>";
		echo '<span class="actions"> ' . $this->Form->postLink(
				__d('cake', 'Delete'),
				array('action' => 'delete', $this->request->data['Department']['id']),
				array('style'=>'padding:8px 10px;'),
				__d('cake', 'Are you sure you want to delete  %s?', $this->request->data['Department']['name'])
		).'</span>';
}
?>
</div>
