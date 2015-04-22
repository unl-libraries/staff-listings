<?php
/**
 * Configuration file for the Directory information applications.
 *
 * There is a public and administrative side to this application.
 *
 * Public side produces html code for inclusion in another web page
 * Administrative side edits the Directory data it has access to.
 *
 */

/**
 * suppressed data fields
 * fields to keep from view
 * @var array
 */
$config['feeds']['suppressed_fields'] = array('nuid');

 /**
  * Allowed formats for the records
  * @var array
  */
 $config['feeds']['allowed_formats'] = array('json','xml','hcard');
 

 /**
  * Defines the default value of format the results should be returned in
  * Allowed formats are defined above: json, xml, hcard with a userid.   
  */
 $config['feeds']['format'] = 'xml';
 

 /**
  * Defines the default setting for the number of records to be returned for paging results
  * Default value: 0 = all records
  * @var $number_of_records int
  */
 $config['feeds']['number_of_records']=0;
 
 /**
  * Default sort order should be one of the database fields, most likely last_name or library_dept
  * 
  */
 $config['feeds']['sort'] = 'last_name';
 
 
 /**
  * Default fields to display in output
  *@var array
  */
 $config['feeds']['display_fields'] = array('userid', 'full_name','first_name','last_name', 'unl_status','unl_dept','location','unl_position','position_2','position_3','library_position', 'address','phone','email');
 $config['feeds']['combined_fields'] = array('library_dept'=>'departments');
 ?>