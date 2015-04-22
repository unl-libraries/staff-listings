<?php
// app/View/Addresses/index.ctp
//$this->extend('/Common/index');

$this->assign('title', 'Library Departments');
?>

<div class="departments print index">

<h3>Library Departments</h3>


<table>
<tr>
	<th>Name</th>
	<th>Abbreviation</th>
	<th>Under</th>
</tr>
<?php
foreach ($departments as $department):
	echo '<tr>';
		foreach (array('name','abbreviation','parent_id') as $_field) {
			
			if ($_field =='parent_id') {
				echo '<td>' . ($department['Department_above']['name']) . '</td>';
			}
			else {
				echo '<td>' . h($department['Department'][$_field]) . '</td>';
			}		
			
		}
		
	echo '</tr>';

endforeach;

?>
</table>
</div>
	<script type="text/javascript">
	jQuery.noConflict();
	jQuery(document).ready(function() {
		window.print();
		window.close();
	});	
	</script>
