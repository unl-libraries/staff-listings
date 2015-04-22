<?php
//First Load the Utility Class
App::uses('Xml', 'Utility');
if ($format == 'json') echo json_encode(compact('addresses'));
if ($format =='xml') {
	try{
		$xml = Xml::fromArray($addresses);		
	} catch (XmlException $e) {
		throw new InternalErrorException();
	}
	echo $xml->asXML();
}