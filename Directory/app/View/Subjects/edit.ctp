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
<div class="subjects form">
<?php

	echo $this->Form->create();
	echo $this->Form->input('id');
	echo $this->Form->input('return_url',array('type'=>'hidden','default'=>$return_url))
	?>
	<fieldset><legend><?php echo $title_for_layout?></legend>
	<?php echo $this->Form->input('subject');
	echo $this->Form->input('Faculty',array('label'=>'Assigned Faculty','options'=>$faculty,'size'=>count($faculty)));
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
			array('controller' => 'subjects', 'action' => 'edit')
		) . "</li>\n";
		
?>
	</ul>
</div>
