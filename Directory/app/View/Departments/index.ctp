<?php
// app/View/Addresses/index.ctp
//$this->extend('/Common/index');

$this->assign('title', 'Library Departments');
?>

<div class="departments index">

<h3>Library Departments</h3>
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
	?><?php echo $this->Html->link('Print',array('action'=>'print_view'),array('class'=>'icon-print','target'=>'_blank','title'=>'Print this view'));?>
	</div>
	</div>
<div class="right-side" >
	<?php if ($this->action=='search' && isset($query)) {
		$url_array =array('controller'=>'addresses','action'=>'search',"query"=>$query); 
	}
	else $url_array = array('controller'=>'departments','action'=>'index');
	echo $this->Form->create('Department',array('type'=>'get','url'=>$url_array,'inputDefaults' => array(
        'label' => false,
        'div' => false
    )));
		
	?>
</div>

<table>
<tr>
	<th><?php echo $this->Paginator->sort('name','Name'); ?></th>
	<th><?php echo $this->Paginator->sort('abbreviation','Abbreviation'); ?></th>
	<th><?php echo $this->Paginator->sort('Department_above.name','Under'); ?></th>
	<?php if (AuthComponent::user('id')) echo '<th>Actions</th>'; ?>
</tr>
<?php
foreach ($departments as $department):
	echo '<tr>';
		foreach (array('name','abbreviation','parent_id') as $_field) {
			
			if ($_field =='parent_id') {
				echo '<td>' . $this->Html->link($department['Department_above']['name'], array('controller' => 'departments', 'action' => 'view', $department['Department']['parent_id'])) . '</td>';
						
					}
			else {
				echo '<td>' . $this->Html->link(htmlentities($department['Department'][$_field]),array('controller'=>'departments','action'=>'view',$department['Department']['id'])) . '</td>';
			}		
			
		}
		if (AuthComponent::user('id')){
			echo '<td class="actions">';
			echo $this->Html->link(__d('cake', 'View'), array('action' => 'view', $department['Department']['id']));
		 	echo ' ' . $this->Html->link(__d('cake', 'Edit'), array('controller' => 'departments','action' => 'edit', $department['Department']['id']));
			echo '</td>';
		}
	echo '</tr>';

endforeach;

?>
</table>
</div>
<?php if (AuthComponent::user('id')):?>
<div class="actions">
	<h3><?php echo __d('cake', 'Menu'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__d('cake', 'New %s', 'Department'), array('action' => 'edit')); ?></li>
	</ul>
</div>
<?php endif;?>
