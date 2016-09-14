<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Scaffolds
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
?>
<div class="staffData form">
<?php

	echo $this->Form->create();
	//echo $this->Form->input('Address.full_name', array('label' => 'Name',array('disabled'=>true));
	echo $this->Form->input('id');
	echo $this->Form->input('return_url',array('type'=>'hidden','default'=>$return_url))
	?>
	<fieldset><legend>Edit Staff Data</legend>
	<!--  unchangeable information first -->
	<div class="input text"><label>Name:</label><span><?php echo $this->data['Address']['full_name']?></span></div>
	<div class="input text"><label>UNL title:</label><span><?php echo $this->data['Address']['unl_position']?></span></div>
	<div class="input text"><label>Address:</label><?php echo $this->data['Address']['address']?></div>
	<div class="input text"><label>UNL department:</label><span><?php echo $this->data['Address']['unl_dept']?></span></div>
	
	<?php echo $this->Form->input('preferred_name');?>
	<?php echo $this->Form->input('library_title');?>
	<fieldset><legend>Links</legend>
		<?php echo $this->Form->input('website');?>
		<?php echo $this->Form->input('libguide_profile',array('label'=>"Libguide profile link"))?>
		<?php 
		echo "<div id='external_links'>";
		for ($i=0; $i<count($this->data['ExternalLinks']); $i++):
			echo "<div class='input text'>";
				echo $this->Form->input("ExternalLinks.$i.id",array("type"=>"hidden"));
				echo "<div class='inline-input'>";
					echo "External link:<br />";
					echo $this->Form->input("ExternalLinks.$i.url",array('div'=>false,'label'=>false,'class'=>'inline'));
				echo "</div>";
				echo "<div class='inline-input'>";
					echo "Type of link:<br/>";
					echo $this->Form->input("ExternalLinks.$i.link_type",array('type'=>'select','options'=>array(""=>"Select a type","digitalcommons"=>"Digital Commons","linkedin"=>"LinkedIn","facebook"=>"Facebook","twitter"=>"Twitter","blog"=>"Blog"),'div'=>false,'label'=>false,'class'=>'inline'));
				echo "</div>";				
				echo ' ' . $this->Html->link(
						__d('cake', " - "),
						array('controller'=>'external_links','action' => 'delete', $this->data['ExternalLinks'][$i]['id']),
						array('class'=>'confirm_delete remove_link')
				);
				
			echo "</div>";
		endfor;
		// add a link to add additional inputs for more links:		
		?>		
		</div><button id="add-link-button" style="background-color:green; color:white; padding:5px;cursor:pointer;margin-right:25%; float:right;"> + External Link</button>
	</fieldset>
	<?php
	echo $this->Form->input('Department',array('label'=>'Departments'));
	echo $this->Form->input('location',array('type'=>'select','options'=>array(""=>"Select a location","ARCH"=>"ARCH","CYT"=>"CYT","ENGR"=>"ENGR","GEOL"=>"GEOL","LDRF"=>"LDRF","LOVE"=>"LOVE","MATH"=>"MATH","MUSIC"=>"MUSIC","LAW"=>"LAW")));
	if (strtolower($this->data['Address']['unl_status'])=='faculty') echo $this->Form->input('Subjects',array('label'=>'Subject Assignments'));
	?></fieldset>
	<?php 
	echo $this->Form->end(__d('cake', 'Save'));
?>
</div>
<div class="actions">
	<h3><?php echo __d('cake', 'Actions'); ?></h3>
	<ul>

		
<?php
		echo "\t\t<li>" . $this->Html->link(
						__d('cake', 'List Subjects'),
						array('controller' => 'subjects', 'action' => 'index')
					) . "</li>\n";
		echo "\t\t<li>" . $this->Html->link(
			__d('cake', 'New Subject'),
			array('controller' => 'subjects', 'action' => 'add')
		) . "</li>\n";
		
?>
	</ul>
</div>
<script type="text/javascript">
$('#add-link-button').click(function(event){
    var linkCount = $('#external_links > div.input').size() + 1;
    var inputHtml = '<div class="input text"><input type="hidden" name="data[ExternalLinks]['+linkCount+'][id]" id="ExternalLinks'+linkCount+'Id">'+
    '<div class="inline-input">External link:<br><input name="data[ExternalLinks]['+linkCount+'][url]" class="inline" maxlength="255" type="text" id="ExternalLinks'+linkCount+'Url"></div>'+
    '<div class="inline-input">Type of link:<br><select name="data[ExternalLinks]['+linkCount+'][link_type]" class="inline" id="ExternalLinks'+linkCount+'LinkType">'+
    '<option value="">Select a type</option>'+
    '<option value="digitalcommons">Digital Commons</option>'+
    '<option value="linkedin">LinkedIn</option>'+
    '<option value="facebook">Facebook</option>'+
    '<option value="twitter">Twitter</option>'+
    '<option value="blog">Blog</option>'+
    '</select></div><button class="remove_link" id="remove_link'+linkCount+'" title="remove link" onclick="$(function(){$(this).remove();}); return false;">&nbsp;-&nbsp;</span></div>';
    event.preventDefault();
    $('#external_links').append(inputHtml);
});


if($('.confirm_delete').length) {
    // add click handler
$('.confirm_delete').click(function(){
	// ask for confirmation
	var result = confirm('Are you sure you want to delete this?');
	
	// show loading image
	$('.ajax_loader').show();
	$('#flashMessage').fadeOut();
	
	// get parent div
	var row = $(this).parent('div');
	
	// do ajax request
	if(result) {
		$.ajax({
			type:"POST",
			url:$(this).attr('href'),
			data:"ajax=1",
			dataType: "json",
			success:function(response){
				// hide loading image
				$('.ajax_loader').hide();
				
				// hide table row on success
				if(response.success == true) {
					row.before(response.msg);
					row.remove();
				}
				
				// show respsonse message
				if( response.msg ) {
					$('#ajax_msg').html( response.msg ).show();
				} else {
					$('#ajax_msg').html( "<p id='flashMessage' class='error'>An unexpected error has occured, please refresh and try again</p>" ).show();
				}
			}
		});
	}
return false;
});
}

$( "form" ).submit(function( event ) {
	 //check the links for empty entries.	 	 		
	 $('#external_links > div.input').each(function(){
		 if ($(this).find('input').filter(function() { return this.value == ""; }).size() >1) {$(this).remove();}
	});
});

$(document).on('click','.remove_link',function(){
		event.preventDefault();
		event.stopPropagation();
		$(this).parent('div').remove();	
});


</script>