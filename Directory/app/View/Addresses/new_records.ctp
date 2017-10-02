<?php
// app/View/Addresses/index.ctp
//$this->extend('/Common/index');

$this->assign('title', 'Entries needed information updated');
if (isset($query)) $this->Paginator->options(array('url' => array_merge(array("query"=>$query), $this->passedArgs)));


?>

<div class="addresses index">

<h3>Records needing department and location assignments </h3> 
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
<?php foreach ($addressFields as $_field): ?>
	<th><th><?php echo (($_field=='Department.name')? 'Department name':($_field=='Subjects.subject'? 'Subjects':$this->Paginator->sort($_field))); ?></th>
<?php endforeach; ?>
<!-- 	<th>Subject areas</th> -->
	<th><?php echo __d('cake', 'Actions'); ?></th>
</tr>
<?php
foreach ($addresses as $address):
	echo '<tr>';
		$department_data = array();
		foreach ($addressFields as $_field) {
			if ($_field =='Department.name'){
				if (!empty($address['StaffData']['Department'])) {
					$department_data = Set::combine($address['StaffData']['Department'],'{n}.id','{n}.name');
					foreach ($department_data as $dept_id=>$dept_name){
						$department_data[$dept_id]= $this->Html->link($dept_name, array('controller'=>'departments','action'=>'view',$dept_id));
					}
				}
				echo '<td>'.(!empty($department_data)?join($department_data,"/"):'').'</td>';
			}
			else{
			$isKey = false;
			if (!empty($associations['belongsTo'])) {
				foreach ($associations['belongsTo'] as $_alias => $_details) {
					if ($_field === $_details['foreignKey']) {
						$isKey = true;
						echo '<td>' . $this->Html->link(${$singularVar}[$_alias][$_details['displayField']], array('controller' => $_details['controller'], 'action' => 'view', ${$singularVar}[$_alias][$_details['primaryKey']])) . '</td>';
						break;
					}
				}
			}
			if ($isKey !== true) {
				if ($_field=='first_name' && !empty($address['StaffData']['preferred_name'])) 
					echo '<td>' . (!empty($search_terms)?$this->Text->highlight(h($address['Address'][$_field]. " (".$address['StaffData']['preferred_name']).")", $search_terms, array('format' => '<span class="highlight">\1</span>')):h($address['Address'][$_field]." (".$address['StaffData']['preferred_name']).")") . '</td>';
				elseif ($_field=='email' && !empty($address['Address']['email']))
					echo '<td><a href="mailto:'.$address['Address'][$_field].'">' . (!empty($search_terms)?$this->Text->highlight(h($address['Address'][$_field]), $search_terms, array('format' => '<span class="highlight">\1</span>')):h($address['Address'][$_field])) . '</a></td>';
				else echo '<td>' . (!empty($search_terms)?$this->Text->highlight(h($address['Address'][$_field]), $search_terms, array('format' => '<span class="highlight">\1</span>')):h($address['Address'][$_field])) . '</td>';
			}		
		}	
		}
		//and lastly add the subjects in
// 		$subject_list= array();
// 		foreach ($address['StaffData']['Subjects'] as $subject){
// 			$subject_list[]=$subject['subject'];
// 		}
// 		echo '<td>'.(!empty($search_terms)?$this->Text->highlight(join(';<br />',$subject_list),$search_terms,array('format'=>'<span class="highlight">\1</span>')):join(';<br />',$subject_list)).'</td>';
		echo '<td class="actions">';
		echo $this->Html->link(__d('cake', 'View'), array('action' => 'view', $address['Address']['userid']));
		echo ' ' . $this->Html->link(__d('cake', 'Edit'), array('controller' => 'staff_data','action' => 'edit', $address['StaffData']['id']));
		
		echo '</td>';
	echo '</tr>';

endforeach;

?>
</table>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __d('cake', 'previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__d('cake', 'next') .' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
