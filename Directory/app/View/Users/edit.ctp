<div class="userData form">
<?php
echo $this->Session->flash();

echo $this->Form->create('User');
echo $this->Form->input('username');
echo $this->Form->input('password');
echo $this->Form->input('password_hint');
echo $this->Form->input('firstname',array('label'=>'First name'));
echo $this->Form->input('lastname',array('label'=>'Last name'));
echo $this->Form->input('email');
echo $this->Form->input('token',array('type'=>'hidden','value'=>''));
echo $this->Form->end('save');

?>
</div>
<div class="actions">
	<h3><?php echo __d('cake', 'Actions'); ?></h3>
	<ul>

		
<?php
		echo "\t\t<li>" . $this->Html->link(
						__d('cake', 'List users'),
						array('controller' => 'users', 'action' => 'index')
					) . "</li>\n";
		echo "\t\t<li>" . $this->Html->link(
			__d('cake', 'New User'),
			array('controller' => 'users', 'action' => 'add')
		) . "</li>\n";
		
?>
	</ul>
</div>
		