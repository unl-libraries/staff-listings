<?php 
$this->assign('title', 'Admin users');
if (isset($query)) $this->Paginator->options(array('url' => array_merge(array("query"=>$query), $this->passedArgs)));


?>

<div class="users index">

<h3>Administrators</h3>
<!-- 
//alphabet break up?
//echo "<div id='a-z'><a href='$link=a'>A</a> | <a href='$link=b'>B</a> | <a href='$link=c'>C</a> | <a href='$link=d'>D</a> | <a href='$link=e'>E</a> | <a href='$link=f'>F</a> | <a href='$link=g'>G</a> | <a href='$link=h'>H</a> | <a href='$link=i'>I</a> | <a href='$link=j'>J</a> | <a href='$link=k'>K</a> | <a href='$link=l'>L</a> | <a href='$link=m'>M</a> | <a href='$link=n'>N</a> | <a href='$link=o'>O</a> | <a href='$link=p'>P</a> | <a href='$link=q'>Q</a> | <a href='$link=r'>R</a> | <a href='$link=s'>S</a> | <a href='$link=t'>T</a> | <a href='$link=u'>U</a> | <a href='$link=v'>V</a> | <a href='$link=w'>W</a> | <a href='$link=x'>X</a> | <a href='$link=y'>Y</a> | <a href='$link=z'>Z</a> | <a href='index$page_ext'>".ucfmsg('ALL')."</a></div>" ;

//other filters?
	
 -->
 	<div style="text-align:center; margin-top:0px; margin-bottom:2px; ">
 	<p><?php
	echo $this->Paginator->counter(array(
		'format' => __d('cake', 'Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?></p>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __d('cake', 'previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__d('cake', 'next') .' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
	</div>

<table>
<tr>
	<th><?php echo $this->Paginator->sort('username','username'); ?></th>
	<th><?php echo $this->Paginator->sort('firstname','First name'); ?></th>
	<th><?php echo $this->Paginator->sort('lastname','Last name'); ?></th>
	<th><?php echo $this->Paginator->sort('email','Email'); ?></th>
	<th><?php echo $this->Paginator->sort('phone','Phone'); ?></th>

</tr>
<?php
foreach ($users as $user):
	echo '<tr>';
		foreach (array('username','firstname', 'lastname','email','phone') as $_field) {
			
				echo '<td>' . $user['User'][$_field] . '</td>';
			}		
			
		
		echo '<td class="actions">';
		echo $this->Html->link(__d('cake', 'View'), array('action' => 'view', $user['User']['id']));
		echo ' ' . $this->Html->link(__d('cake', 'Edit'), array('controller' => 'users','action' => 'edit', $user['User']['id']));
		
		echo '</td>';
	echo '</tr>';

endforeach;

?>
</table>
</div>
<?php if (AuthComponent::user('id')):?>
<div class="actions">
	<h3><?php echo __d('cake', 'Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__d('cake', 'New %s', 'User'), array('action' => 'add')); ?></li>
	</ul>
</div>
<?php endif;?>