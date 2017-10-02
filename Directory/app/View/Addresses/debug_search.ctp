<?php
// app/View/Addresses/debug_search.ctp
//$this->extend('/Common/index');

$this->assign('title', 'Staff Listing');
if (isset($query)) {
	debug($this->Paginator->options);
	debug($this->passedArgs);
	//debug($this->params);
	$this->Paginator->options(array('url' => array_merge(array("query"=>$query), $this->Paginator->options['url'])));
	debug($this->Paginator->options);
}

debug($this->Paginator->params->paging);
?>
<form accept-charset="utf-8" method="post" id="AddressSearchForm" action="<?php echo $this->Html->url(array("controller" => "addresses","action" => "debug_search","?" => $this->params->query));?>" name="searchform"> 
					<input type="text" id="search-query" value="<?php echo (isset($query)?$query:''); ?>" name="query" title="debug the search for any text" size="45" tabindex="0"/>
 					<input type="submit" value="Search" />     
 					<?php // if we have a search query, add a clear button
						if (isset($query)):
						?>
						<input type=submit value="clear search" onclick="javascript:this.form.elements['search-query'].setAttribute('value','');javascript:this.form.submit();">
						<?php endif;?>
 				</form> 
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
	?>
	</div>
	</div>
<div class="filters" >
	<?php if ($this->action=='debug_search' && isset($query)) {
		$url_array =array('controller'=>'addresses','action'=>'debug_search',"query"=>$query); 
	}
	else $url_array = array('controller'=>'addresses','action'=>'debug_search');
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
	<th><?php echo (($_field=='Department.name')?  $this->Paginator->sort('library_dept', 'Department'):($_field=='Subjects.subject'? 'Subjects':$this->Paginator->sort($_field))); ?></th>
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
						if (!empty($search_terms)) $department_data[$dept_id]= html_entity_decode($this->Text->highlight(h($this->Html->link($dept_name, array('controller'=>'departments','action'=>'view',$dept_id))),$search_terms, array('format' => '<span class="highlight">\1</span>')));
						else $department_data[$dept_id]= $this->Html->link($dept_name, array('controller'=>'departments','action'=>'view',$dept_id));
					}
				}
				echo '<td>'.(!empty($department_data)?join($department_data,"/"):'').'</td>';
			}
			//elseif ($_field == 'library_title') echo '<td>'.(!empty($address['StaffData']['library_title'])?$address['StaffData']['library_title']:'').'</td>';
			elseif ($_field == 'Subjects.subject') {
				if (!empty($address['StaffData']['Subjects'])){
					$subjects = Set::combine($address['StaffData']['Subjects'],'{n}.id','{n}.subject');
					echo '<td>';
					foreach ($subjects as $subject_id=>$subject) echo "<span class='table_listing'>".(!empty($search_terms)?$this->Text->highlight(h($subject),$search_terms,array('format'=>'<span class="highlight">\1</span>')):$subject)."</span>";
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
				if ($_field=='first_name' && !empty($address['StaffData']['preferred_name'])) 
					echo '<td>' . (!empty($search_terms)?$this->Text->highlight(h(html_entity_decode($address['Address'][$_field],ENT_QUOTES). " (".$address['StaffData']['preferred_name']).")", $search_terms, array('format' => '<span class="highlight">\1</span>')):h(html_entity_decode($address['Address'][$_field],ENT_QUOTES)." (".$address['StaffData']['preferred_name']).")") . '</td>';
				elseif ($_field=='email' && !empty($address['Address']['email']))
					echo '<td><a href="mailto:'.$address['Address'][$_field].'">' . (!empty($search_terms)?$this->Text->highlight(h($address['Address'][$_field]), $search_terms, array('format' => '<span class="highlight">\1</span>')):h($address['Address'][$_field])) . '</a></td>';
				else echo '<td>' . (!empty($search_terms)?$this->Text->highlight(h(html_entity_decode($address['Address'][$_field],ENT_QUOTES)), $search_terms, array('format' => '<span class="highlight">\1</span>')):h(html_entity_decode($address['Address'][$_field],ENT_QUOTES))) . '</td>';
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
<?php echo $this->element('sql_dump');?>