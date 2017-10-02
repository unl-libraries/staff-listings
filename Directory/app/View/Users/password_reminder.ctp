<p>
<?php
echo $this->Session->flash();
echo $this->Session->flash('auth');
if (isset($token)) echo '<div id="flashMessage" class="success">Please verify your email address</div>';
else echo "If you have forgotten your password or username, you can use this form to send a reminder to yourself.<br />";
echo $this->Form->create('User', array('action' => 'password_reminder'));
echo $this->Form->input('email');
echo $this->Form->input('token',array('type'=>'hidden','value'=>(isset($token)?$token:'')));

echo $this->Form->end('submit');

?>
</p>