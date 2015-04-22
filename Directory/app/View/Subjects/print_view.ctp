<?php
// app/View/Subjects/index.ctp
//$this->extend('/Common/index');
?>
<div class="subjects print index">
<h3>Subject areas</h3>
<table cellpadding="0" cellspacing="0">
<tr>

	<th>Subject</th>
	<th>Faculty</th>

</tr>
<?php
foreach ($subjects as $subject):
	echo '<tr>';
	echo '<td>' . h($subject['Subject']['subject']) . '</td>';
	echo '<td>';
	foreach ($subject['Faculty'] as $faculty){
		echo h($faculty['full_name'])."<br />";
	}
	echo "</td>";
	
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