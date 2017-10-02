<?php
// app/View/Addresses/index.ctp
//$this->extend('/Common/index');

$this->assign('title', 'Staff Listing');
?>

<div class="addresses print index">

<h3>Staff Directory <?php echo (isset($filter_note)?$filter_note:'');?></h3> 

<table>
<tr>
<?php foreach ($addressFields as $_field): ?>
	<th><?php echo (($_field=='Department.name')? 'Department name':($_field=='Subjects.subject'? 'Subjects':Inflector::humanize($_field))); ?></th>
<?php endforeach; ?>

</tr>
<?php
if (!empty($addresses)){
	foreach ($addresses as $address):
		echo '<tr>';
			$department_data = array();
			foreach ($addressFields as $_field) {
				if ($_field =='Department.name'){
					if (!empty($address['StaffData']['Department'])) {
						$department_data = Set::combine($address['StaffData']['Department'],'{n}.id','{n}.name');
						foreach ($department_data as $dept_id=>$dept_name){
							if (!empty($search_terms)) $department_data[$dept_id]= $dept_name;
							else $department_data[$dept_id]= $dept_name;
						}
					}
					echo '<td>'.(!empty($department_data)?join($department_data,"/"):'').'</td>';
				}
				elseif ($_field == 'Subjects.subject') {
					if (!empty($address['StaffData']['Subjects'])){
						$subjects = Set::combine($address['StaffData']['Subjects'],'{n}.id','{n}.subject');
						echo '<td>';
						//echo join(';<br />',array_values($subjects));
						foreach ($subjects as $subject_id=>$subject) echo "<span class='table_listing'>".$subject."</span>";
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
							echo '<td>' . ${$singularVar}[$_alias][$_details['displayField']] . '</td>';
							break;
						}
					}
				}
				if ($isKey !== true) {
					if ($_field=='first_name' )	echo '<td>' . html_entity_decode($address['Address'][$_field],ENT_QUOTES) . '</td>';
					elseif ($_field=='email' && !empty($address['Address']['email']))
						echo '<td>'.$address['Address'][$_field].'</td>';
					else echo '<td>' . html_entity_decode($address['Address'][$_field],ENT_QUOTES) . '</td>';
				}		
			}	
			}
		echo '</tr>';
	endforeach;
}
else echo "<tr><td colspan=".(count($addressFields)+1)." class='noresults'><span >No entries</span></td></tr>";

?>
</table>
	
</div>
	<script type="text/javascript">
	jQuery.noConflict();
	jQuery(document).ready(function() {
		window.print();
		setTimeout("window.close()", 100);
	});	
	</script>
