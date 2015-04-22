<?php foreach ($people as $person):?>
	<p class='directory_info'>
	<strong><?php echo $person['Address']['display_name'];?></strong><br />
	<?php echo $person['Address']['unl_position'];?><br />
	<?php echo (!empty($person['Address']['library_position'])?$person['Address']['library_position']."<br />":'');?>
	Address: <?php echo $person['Address']['address']?><br />
	Telephone: <?php echo $person['Address']['phone']?><br />
	Email: <a href="mailto:<?php echo $person['Address']['email']?>"><?php echo $person['Address']['email']?></a><br />
	</p> 
<?php endforeach;?>