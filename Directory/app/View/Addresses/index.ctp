<?php
// app/View/Addresses/index.ctp
//$this->extend('/Common/index');

$this->assign('title', 'Staff Listing');
//debug($this->Paginator->options);
//debug($this->params);
if (!empty($this->Paginator->options['url']['?'])) $print_params = $this->Paginator->options['url']['?'];
else $print_params = array();
if (isset($query)) {	
	$this->Paginator->options(array('url' => array_merge(array("query"=>$query), $this->Paginator->options['url'])));
	$print_params["query"]=$query;
}


?>

<div class="addresses index">

<h3>Staff Directory <?php echo (isset($filter_note)?$filter_note:'');?></h3> 

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
	?><?php echo $this->Html->link('Print',array('controller'=>'addresses','action'=>'print_view','?'=>$print_params),array('class'=>'icon-print','target'=>'_blank','title'=>'Print this view'));?>
	</div>
	
	</div>
<div class="filters" >
	<?php if ($this->action=='search' && isset($query)) {
		$url_array =array('controller'=>'addresses','action'=>'search',"query"=>$query); 
	}
	else $url_array = array('controller'=>'addresses','action'=>'search');
	echo $this->Form->create('Address',array('type'=>'get','url'=>$url_array,'inputDefaults' => array(
        'label' => false,
        'div' => false
    )));
		//show all the possilble filters
		echo "<span>Filters:</span>";
		echo $this->Form->input('unl_status',array('onchange' => "javascript:this.form.submit();",'options'=>$statuses,'empty' => 'Filter by Status..'));
		echo $this->Form->input('department',array('onchange' => "javascript:this.form.submit();",'options'=>$departments,'empty' => 'Filter by department...'));
		if (!empty($_GET['department']) && !empty($subdepartments)) echo $this->Form->input('subdepartment',array('onchange'=>"javascript:this.form.submit();",'options'=>$subdepartments,'empty'=>'Filter by sub department'));
		echo $this->Form->input('location',array('onchange' => "javascript:this.form.submit();",'options'=>$locations,'empty' => 'Filter by location...'));
		//echo $this->Form->submit('filter',array('div'=>false));
	?>
	
</div>

<table>
<tr>
<?php foreach ($addressFields as $_field): ?>
	<th><?php echo (($_field=='Department.name')? $this->Paginator->sort('library_dept','Department'):($_field=='Subjects.subject'? 'Subjects':$this->Paginator->sort($_field))); ?></th>
<?php endforeach; 
	if (AuthComponent::user('id')){?><th><?php echo __d('cake', 'Actions'); ?></th><?php }?>
</tr>
<?php
if (!empty($addresses)){
	foreach ($addresses as $address):
		echo '<tr>';
			$department_data = array();
			foreach ($addressFields as $_field) {
				if ($_field =='name'){
					//display the display_name instead which is different formatting
					echo '<td>'.(!empty($search_terms)?$this->Html->link(
							$this->Text->highlight(h(html_entity_decode($address['Address']['display_name'],ENT_QUOTES)),$search_terms, array('format' => '<span class="highlight">\1</span>','html'=>true)),
							array('action'=>'view',$address['Address']['userid']),array('escapeTitle'=>false)):$this->Html->link(h(html_entity_decode($address['Address']['display_name'],ENT_QUOTES)),
									array('action'=>'view',$address['Address']['userid']),array('escapeTitle'=>false))).'</td>';
				}
				elseif ($_field =='Department.name'){
					if (!empty($address['StaffData']['Department'])) {
						$department_data = Set::combine($address['StaffData']['Department'],'{n}.id','{n}.name');
						foreach ($department_data as $dept_id=>$dept_name){
							if (!empty($search_terms)) $department_data[$dept_id]= $this->Html->link($this->Text->highlight(h($dept_name), $search_terms,array('format'=>'<span class="highlight">\1</span>','html'=>true)), array('controller'=>'departments','action'=>'view',$dept_id),array('escapeTitle'=>false));
							else $department_data[$dept_id]= $this->Html->link($dept_name, array('controller'=>'departments','action'=>'view',$dept_id));
						}
					}
					echo '<td>'.(!empty($department_data)?join($department_data,"/"):'').'</td>';
				}
				elseif ($_field == 'Subjects.subject') {
					if (!empty($address['StaffData']['Subjects'])){
						$subjects = Set::combine($address['StaffData']['Subjects'],'{n}.id','{n}.subject');
						echo '<td>';
						//echo join(';<br />',array_values($subjects));
						foreach ($subjects as $subject_id=>$subject) echo "<span class='table_listing'>".(!empty($search_terms)?$this->Html->link($this->Text->highlight(h($subject),$search_terms,array('format'=>'<span class="highlight">\1</span>','html'=>true)),array('controller'=>'subjects','action'=>'view',$subject_id),array('escapeTitle'=>false)):$this->Html->link($subject,array('controller'=>'subjects','action'=>'view',$subject_id)))."</span>";
						echo '</td>';
					}
					else echo '<td></td>';
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
						if ($_field=='first_name' )	echo '<td>' . (!empty($search_terms)?$this->Text->highlight(h(html_entity_decode($address['Address'][$_field],ENT_QUOTES)), $search_terms, array('format' => '<span class="highlight">\1</span>','html'=>true)):h(html_entity_decode($address['Address'][$_field],ENT_QUOTES))) . '</td>';
						elseif ($_field=='email' && !empty($address['Address']['email']))
							echo '<td><a href="mailto:'.$address['Address'][$_field].'">' . (!empty($search_terms)?$this->Text->highlight(h($address['Address'][$_field]), $search_terms, array('format' => '<span class="highlight">\1</span>','html'=>true)):h($address['Address'][$_field])) . '</a></td>';
						else echo '<td>' . (!empty($search_terms)?$this->Text->highlight(h(html_entity_decode($address['Address'][$_field],ENT_QUOTES)), $search_terms, array('format' => '<span class="highlight">\1</span>','html'=>true)):h(html_entity_decode($address['Address'][$_field],ENT_QUOTES))) . '</td>';
					}		
				}	
			}
			if (AuthComponent::user('id')){
				echo '<td class="actions">';
				echo $this->Html->link(__d('cake', 'View'), array('action' => 'view', $address['Address']['userid']));
				if (AuthComponent::user('id')) echo ' ' . $this->Html->link(__d('cake', 'Edit'), array('controller' => 'staff_data','action' => 'edit', $address['StaffData']['id']));
				//if (AuthComponent::user('id')) echo ' ' . $this->Html->link(__d('cake', 'Test'), array('controller' => 'staff_data','action' => 'edit_test', $address['StaffData']['id']));
				echo '</td>';
			}
		echo '</tr>';
	endforeach;
}
else echo "<tr><td colspan=".(count($addressFields)+1)." class='noresults'><span >No entries</span></td></tr>";

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
