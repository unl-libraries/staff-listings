<p>
<?php
echo $this->Session->flash('auth');
echo "<p>";
echo $this->Form->create('User', array('action' => 'login'));
echo $this->Form->input('username');
echo $this->Form->input('password');
echo "</p>";
echo $this->Form->end('Login');
echo $this->Html->link("Forgot your password or username?",array('controller'=>'users','action'=>'password_reminder'));

?>
</p>