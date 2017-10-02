<?php $html = "";
foreach ($people as $person):
	$html .= "<p class='directory_info'>\n";
	$html .="<strong>{$person['Address']['display_name']}</strong><br />\n";
	$html .=$person['Address']['unl_position']."<br />\n";
	$html .=(!empty($person['Address']['library_position'])?$person['Address']['library_position']."<br />\n":'');
	$html .= "Address: {$person['Address']['address']}<br />\n";
	$html .= "Telephone: {$person['Address']['phone']}<br />\n";
	$html .= "Email: <a href=\"mailto:{$person['Address']['email']}\">{$person['Address']['email']}</a><br />\n";
	$html .="</p>"; 
endforeach;

echo json_encode(array('html'=>utf8_encode($html)));
?>