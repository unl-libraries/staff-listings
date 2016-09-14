<?php
/**
 * Dynamic content controller.
 *
 * This file will render views from views/addresses/
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
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('AppController', 'Controller');
App::import('Vendor', 'PHPExcel/Classes/PHPExcel');
Configure::load('feed_settings');
/**
 * Dynamic content controller
 * 
 * @author srickel1
 *
 */
class AddressesController extends AppController {
	//public $components = array(RequestHandler'); //to page results and handle json and xml requests
	public $helpers = array('Text'); //to highlight and handle text related functions, and to output excel
	public $summary_list_fields = array('name','email','phone','address','unl_status','Department.name','location','unl_position','library_position','Subjects.subject');
	var $scaffold;
	/**
	 * Listing of entries
	 * @param string $filter filter by this item (status, etc..)
	 */
	
	public function beforeFilter() {
		parent::beforeFilter();
		// Allow only these actions.
		
		$this->Auth->allow('view', 'index','print_view','search','vcard','export','get_letters','public_index','public_feed','staff_listing','faculty_listing');
		if (isset($this->params['url']['ext']) && $this->params['url']['ext'] == 'json') {
			$this->Auth->allow($this->action);
		}

	}
	
	public function index(){	

		$filters = array('location'=>'','unl_status'=>'','department'=>'','subdepartment'=>'');
		//setup the options for filter lists		
		
		$locations=$this->Address->find('list',array('fields'=>array('location'),'order'=>'location'));
		foreach ($locations as $location_id=>$location) $location_options[$location]=$location;
		$this->set('locations',$location_options);
		
		$departments = $this->Address->StaffData->Department->find('list',array('order'=>'name'));
		foreach ($departments as $dept_id=>$dept) $dept_options[$dept_id]=trim($dept);
		$this->set('departments',$dept_options);
		
		$statuses = $this->Address->find('list',array('fields'=>array('unl_status'),'order'=>'unl_status'));
		foreach ($statuses as $status_id=>$status) $status_options[$status]=$status;
		$this->set('statuses',$status_options);
		
		$no_results = false;
		
		if (!empty($_GET['location']) || !empty($_GET['unl_status']) || !empty($_GET['department'])){
			//we have filters set to process by
			$filter_note ='';
			foreach (array_keys($filters) as $filter){
				if (!empty($_GET[$filter])) {
					if ($filter == 'department' || $filter=='subdepartment'){
						$staff_userids = array();
						$department_id = (int) $_GET[$filter];
						
						//filter on staff->department connection
						$department_data = $this->Address->StaffData->Department->findById($department_id);
						$filter_note .= " > ".$department_data['Department']['name'];
						$staff_userids = array_merge($staff_userids,Set::combine($department_data['Staff'],'{n}.id','{n}.userid'));
						//add in the subdepartments
						$subdepartments = $this->Address->StaffData->Department->find('list',array('conditions'=>array('parent_id'=>$department_id),'order'=>'name'));
						if (!empty($subdepartments)){
							foreach ($subdepartments as $sub_id=>$subdept){ 
								$sub_options[$sub_id]=trim($subdept);
								$department_data = $this->Address->StaffData->Department->findById($sub_id);
								$staff_userids = array_merge($staff_userids,Set::combine($department_data['Staff'],'{n}.id','{n}.userid'));
							}
							$this->set('subdepartments',$sub_options);
						}
						if (!empty($staff_userids)) $conditions['Address.userid'] = array_values($staff_userids);
						else $conditions['Address.userid'] = NULL;
						
					}
					else {
						$conditions['Address.'.$filter]=$_GET[$filter];
						$filter_note .= " > ". htmlentities(ucwords($_GET[$filter]));
					}
					$filters[$filter]=$_GET[$filter];  //keep track of it for paging
					$this->request->data['Address'][$filter]=$filters[$filter];
						
				}
			}
			
			$addresses = $this->Paginator->paginate('Address',$conditions);
			//if (empty($addresses)) $this->Session->setFlash('No results match the criteria','default',array('class'=>'info'));
			$this->set('filters',$filters);
			$this->set('filter_note',$filter_note);
		}
		else{
			//grab all addresses and pass it to the view:
		//$this->Paginator->settings= array('order'=>array('StaffData.Department.name'=>'desc'),'recursive'=>3,'group'=>array('StaffData.Department.name'));
		//$this->Paginator->settings = array('contain'=>array('StaffData','Department'));
			$addresses =  $this->Paginator->paginate('Address');
		}
		$this->set('addressFields',$this->summary_list_fields);				
		$this->set('addresses', $addresses);
		$this->set('_serialize',array('addresses'));
		$this->set('title_for_layout', 'Staff Listing');
	}

	function print_view(){
		$filters = array('location'=>'','unl_status'=>'','department'=>'','subdepartment'=>'');		
		if (!empty($_GET['location']) || !empty($_GET['unl_status']) || !empty($_GET['department'])){
			//we have filters set to process by
			$filter_note ='';
			foreach (array_keys($filters) as $filter){
				if (!empty($_GET[$filter])) {
					if ($filter == 'department' || $filter=='subdepartment'){
						$staff_userids = array();
						$department_id = (int) $_GET[$filter];
						//filter on staff->department connection
						$department_data = $this->Address->StaffData->Department->findById($department_id);
						$filter_note .= " > ".$department_data['Department']['name'];
						$staff_userids = array_merge($staff_userids,Set::combine($department_data['Staff'],'{n}.id','{n}.userid'));
						//add in the subdepartments
						$subdepartments = $this->Address->StaffData->Department->find('list',array('conditions'=>array('parent_id'=>$department_id),'order'=>'name'));
						if (!empty($subdepartments)){
							foreach ($subdepartments as $sub_id=>$subdept){
								$sub_options[$sub_id]=trim($subdept);
								$department_data = $this->Address->StaffData->Department->findById($sub_id);
								$staff_userids = array_merge($staff_userids,Set::combine($department_data['Staff'],'{n}.id','{n}.userid'));
							}
							$this->set('subdepartments',$sub_options);
						}
						if (!empty($staff_userids)) $conditions['Address.userid'] = array_values($staff_userids);
						else $conditions['Address.userid'] = NULL;
					}
					else {
						$conditions['Address.'.$filter]=$_GET[$filter];
						$filter_note .= " > ". htmlentities(ucwords($_GET[$filter]));
					}
					$filters[$filter]=$_GET[$filter];  //keep track of it for paging
					$this->request->data['Address'][$filter]=$filters[$filter];	
				}
			}
			//for printing we want all of them - so no paginate function... 	
			$addresses = $this->Address->find('all',array('conditions'=>$conditions));
			$this->set('filters',$filters);
			$this->set('filter_note',$filter_note);
		}
		else{
			//grab all addresses and pass it to the view:
			$addresses =   $this->Address->find('all');
		}
		$this->set('addressFields',$this->summary_list_fields);
		$this->set('addresses', $addresses);		
		$this->set('title_for_layout', 'Staff Listing');
		$this->layout='print';
	}
	
	function new_records(){
		$this->set('addresses',$this->Paginator->paginate('Address',array("OR"=>array('Address.library_dept'=>'','Address.location'=>'','Address.library_dept'=>NULL,'Address.location'=>NULL),array('unl_status !='=>'emeriti'))));
		$this->set('addressFields',$this->summary_list_fields);
	}
	
	/**
	 * View an addressbook entry. If no id sent, show index
	 * @param int $id
	*/
	public function view($id){
		$address = $this->Address->findByUserid($id);
		if ($id=='srickel1') debug($address);
		$this->set('title_for_layout', 'View addressbook entry');
		$this->set('address',$address);
		$this->set('_serialize',array('address'));
	}
	
	
	/** Generate a vacard format of the address
	 * 
	 * @param int $id
	 */
	public function vcard($userid){
		
		$entry =  $this->Address->findByUserid($userid);
		$vcard = "BEGIN:VCARD
VERSION:3.0
N:{$entry['Address']['last_name']};{$entry['Address']['first_name']};;;
FN:{$entry['Address']['full_name']}
ORG:University of Nebraska-Lincoln;{$entry['Address']['unl_dept']};{$entry['Address']['library_dept']}
EMAIL;type=INTERNET;type=WORK:{$entry['Address']['email']}
TEL;type=WORK:{$entry['Address']['phone']}
ADR;type=WORK:;;{$entry['Address']['address']}
TITLE:{$entry['Address']['unl_position']}
ROLE:{$entry['Address']['unl_status']}
END:VCARD";
		$this->response->body($vcard);
		$this->response->type("text/vcard");		
		
		$this->response->header("Content-Disposition: inline; filename=contact.vcf");
		return $this->response;	
	}
	
	
	/**
	 * Search the addressbook for an entry
	 * @param string $query
	 */
	public function search(){
		$filters = array('location'=>'','unl_status'=>'','department'=>'','subdepartment'=>'');
		//setup the options for filter lists		
		$locations=$this->Address->find('list',array('fields'=>array('location'),'order'=>'location'));
		foreach ($locations as $location_id=>$location) $location_options[$location]=$location;
		$this->set('locations',$location_options);
		
		$departments = $this->Address->StaffData->Department->find('list',array('order'=>'name'));
		foreach ($departments as $dept_id=>$dept) $dept_options[$dept_id]=trim($dept);
		$this->set('departments',$dept_options);
		
		$statuses = $this->Address->find('list',array('fields'=>array('unl_status'),'order'=>'unl_status'));
		foreach ($statuses as $status_id=>$status) $status_options[$status]=$status;
		$this->set('statuses',$status_options);
		
		if (!empty($_GET['location']) || !empty($_GET['unl_status']) || !empty($_GET['department'])){
			//we have filters set to process by
			$filter_note = '';
			foreach (array_keys($filters) as $filter){
				if (!empty($_GET[$filter])) {
					if ($filter=='department' || $filter =='subdepartment'){
						
						//filter on staff->department connection
						$staff_userids = array();
						$department_id = (int)$_GET[$filter];
						$department = $this->Address->StaffData->Department->findById((int)$_GET[$filter]);

						$filter_note .= " > ".$department['Department']['name'];
						$staff_userids = array_merge(Set::combine($department['Staff'],'{n}.id','{n}.userid'));
						//get subdepartment information
						$subdepartments = $this->Address->StaffData->Department->find('list',array('conditions'=>array('parent_id'=>$department['Department']['id']),'order'=>'name'));
						if (!empty($subdepartments)){
							foreach ($subdepartments as $sub_id=>$subdept) {
								$sub_options[$sub_id]=trim($subdept);
								$department_data = $this->Address->StaffData->Department->findById($sub_id);
								$staff_userids = array_merge($staff_userids,Set::combine($department_data['Staff'],'{n}.id','{n}.userid'));
							}
							$this->set('subdepartments',$sub_options);
						}
						if (!empty($staff_userids)) $conditions['Address.userid'] = array_values($staff_userids);
						//this section is for  the filter notes
						else $conditions['Address.userid']=null;
						
							
					}
					else {
						$conditions['Address.'.$filter]=$_GET[$filter];
						$filter_note .= " > ". htmlentities(ucwords($_GET[$filter]));
					}
					$filters[$filter]=$_GET[$filter];  //keep track of it for paging
					$this->request->data['Address'][$filter]=$filters[$filter];						
				}
			}		
			$this->set('filters',$filters);
			$this->set('filter_note',$filter_note);
		}
		if ($this->request->is('post') && $this->request->data['query']){			
			
			//split on the spaces for keyword search
			$search_terms = preg_split('/\s/',$this->request->data['query']);
			$this->set('query',$this->request->data['query']);
		}
		elseif (!empty($this->params['named']['query'])){
			//encoding for search terms - remove htmlentities to allow search on o'grady
			$search_terms =preg_split('/\s/',htmlentities(rawurldecode($this->params['named']['query']),ENT_QUOTES,'UTF-8'));
			$this->set('query',$this->params['named']['query']);
		}
		if (!empty($search_terms)){
			//determine which fields to search
			$fields = $this->Address->schema();
			
			$this->set('search_terms',$search_terms);
			foreach ($fields as $field_name=>$field_spec):
			if ($field_spec['type']=='text' or $field_spec['type']=='string'){
				foreach ($search_terms as $term){
					$conditions["OR"][]=array("Address.$field_name LIKE" => "%$term%");
				}
			}
			endforeach;
			//add in the subject search			
			$staff_data_ids=array();
			foreach ($search_terms as $term){
				$subject_staff = $this->Address->StaffData->Subjects->find('all',array('conditions'=>array("Subjects.subject LIKE"=>"%$term%")));	
				foreach ($subject_staff as $staff){
					foreach ($staff['Faculty'] as $liaison) $staff_data_ids[]=$liaison['id'];
				}
			}
			if (!empty($staff_data_ids)) {
				if (count($staff_data_ids)==1) $conditions["OR"][]['StaffData.'.$this->Address->StaffData->primaryKey]=$staff_data_ids[0];
				else $conditions["OR"][]['StaffData.'.$this->Address->StaffData->primaryKey.' in']=$staff_data_ids;
			}
		}
		if (!empty($conditions)) $addresses = $this->Paginator->paginate('Address',$conditions);
		else $addresses = $this->Paginator->paginate('Address');
		//if (empty($addresses)) $this->Session->setFlash('No results found matching your criteria','default',array('class'=>'info'));
		
		$this->set('addresses', $addresses);
		$this->set('_serialize',array('addresses'));
		$this->set('addressFields',$this->summary_list_fields);				
		$this->set('title_for_layout', 'Addressbook Search');
		$this->render('index'); //render the results using the index page
	}
	
	public function debug_search(){
		//$this->summary_list_fields = 

		$filters = array('location'=>'','unl_status'=>'','department'=>'');
		//setup the options for filter lists
		$locations=$this->Address->find('list',array('fields'=>array('location'),'order'=>'location'));
		foreach ($locations as $location_id=>$location) $location_options[$location]=$location;
		$this->set('locations',$location_options);
	
		$departments = $this->Address->StaffData->Department->find('list',array('order'=>'name'));
		foreach ($departments as $dept_id=>$dept) $dept_options[$dept_id]=trim($dept);
		$this->set('departments',$dept_options);
	
		$statuses = $this->Address->find('list',array('fields'=>array('unl_status'),'order'=>'unl_status'));
		foreach ($statuses as $status_id=>$status) $status_options[$status]=$status;
		$this->set('statuses',$status_options);
	
		if (!empty($_GET['location']) || !empty($_GET['unl_status']) || !empty($_GET['department'])){
			//we have filters set to process by
				
			foreach (array_keys($filters) as $filter){
				if (!empty($_GET[$filter])) {
					if ($filter=='department' || $filter =='subdepartment'){
	
						//filter on staff->department connection
						$staff_userids = array();
	
						$department = $this->Address->StaffData->Department->findById((int)$_GET[$filter]);
						$staff_userids = array_merge(Set::combine($department['Staff'],'{n}.id','{n}.userid'));
						//get subdepartment information
						$subdepartments = $this->Address->StaffData->Department->find('list',array('conditions'=>array('parent_id'=>$department['Department']['id']),'order'=>'name'));
						if (!empty($subdepartments)){
							foreach ($subdepartments as $sub_id=>$subdept) {
								$sub_options[$sub_id]=trim($subdept);
								$department_data = $this->Address->StaffData->Department->findById($sub_id);
								$staff_userids = array_merge($staff_userids,Set::combine($department_data['Staff'],'{n}.id','{n}.userid'));
							}
							$this->set('subdepartments',$sub_options);
						}
						if (!empty($staff_userids)) $conditions['Address.userid'] = array_values($staff_userids);
	
					}
					else $conditions['Address.'.$filter]=$_GET[$filter];
					$filters[$filter]=$_GET[$filter];  //keep track of it for paging
					$this->request->data['Address'][$filter]=$filters[$filter];
				}
			}
			$this->set('filters',$filters);
		}
		if ($this->request->is('post') && $this->request->data['query']){
				
			//split on the spaces for keyword search
			$search_terms = preg_split('/\s/',$this->request->data['query']);
			$this->set('query',$this->request->data['query']);
		}
		elseif (!empty($this->params['named']['query'])){
			//encoding for search terms - remove htmlentities to allow search on o'grady
			$search_terms =preg_split('/\s/',htmlentities(rawurldecode($this->params['named']['query']),ENT_QUOTES,'UTF-8'));
			$this->set('query',$this->params['named']['query']);
		}
		if (!empty($search_terms)){
			//determine which fields to search
			$fields = $this->Address->schema();
			//debug($fields);	
			$this->set('search_terms',$search_terms);
			foreach ($fields as $field_name=>$field_spec):
			if ($field_spec['type']=='text' or $field_spec['type']=='string'){
				foreach ($search_terms as $term){
					$conditions["OR"][]=array("Address.$field_name LIKE" => "%$term%");
				}
			}
			endforeach;
			//add in the subject search
			$staff_data_ids=array();
			foreach ($search_terms as $term){
				$subject_staff = $this->Address->StaffData->Subjects->find('all',array('conditions'=>array("Subjects.subject LIKE"=>"%$term%")));
				foreach ($subject_staff as $staff){
					foreach ($staff['Faculty'] as $liaison) $staff_data_ids[]=$liaison['id'];
				}
			}
			if (!empty($staff_data_ids)) {
				if (count($staff_data_ids)==1) $conditions["OR"][]['StaffData.'.$this->Address->StaffData->primaryKey]=$staff_data_ids[0];
				else $conditions["OR"][]['StaffData.'.$this->Address->StaffData->primaryKey.' in']=$staff_data_ids;
			}
		}
		//debug($conditions);
		
		
		
		//if (!empty($conditions)) $this->Paginator->settings = array('conditions'=>$conditions,'recursive'=>2);
		
		$addresses = $this->Paginator->paginate('Address');
		if (empty($addresses)) $this->Session->setFlash('No results','default',array('class'=>'info'));
		
		$this->set('addresses', $addresses);
		$this->set('_serialize',array('addresses'));
		$this->set('addressFields',$this->summary_list_fields);
		$this->set('title_for_layout', 'Addressbook Search');
		$this->render('debug_search'); //render the results using the index page
		
	}
	/**
	 * exports the directory information in a file that can be opened in excel
	 * @param string $what a filter option that can restrict what we are exporting
	 */
	public function export($what='all',$excel_type='xlsx'){
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Stacy Rickel");
		$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
		$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);		
		if ($what !='all'){
			//create filtered query based on $what
		}
		else{
			//grab all the information leaving out the emeriti staff				
			$addresses = $this->Address->find('all',array('fields'=>array('Address.name','Address.phone','Address.address','Address.email','StaffData.userid','StaffData.id'),'recursive'=>1,'conditions'=>array('Address.unl_status !='=>'emeriti')));
			//$addresses = $this->Address->find('all',array('recursive'=>2,'conditions'=>array('Address.unl_status !='=>'emeriti')));
			foreach ($addresses as $i=>$address){
				
				$department_result = $this->Address->StaffData->find('first',array('recursive'=>1,'conditions'=>array('StaffData.id'=>$address['StaffData']['id'])));
				if (!empty($department_result)) $addresses[$i]['Address']['library_dept']=join(Set::combine($department_result['Department'],'{n}.id','{n}.abbreviation'),'/');
				else $addresses[$i]['Address']['library_dept'] ='';
			}
			
			$this->set('addresses',$addresses);
			
			/* start adding to the spreadsheet
			 * $display_order tells us what order to output the fields in in the excel file as it defaults to cakePHP's retrieval order
			 */
			$display_order = array('name','phone','library_dept','address','zip'); 
			
			//set the style of the headers to bold
			$objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);
			
			//set the sheet title of the current worksheet
			$objPHPExcel->getActiveSheet()->setTitle('2 pages');
			
			//add the headers
			$col = 0;
			foreach ($display_order as $header ) {
				//adds a column heading in the first row in column $col
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col,1, Inflector::humanize($header));
				//sets the column to autosize
				$objPHPExcel->getActiveSheet()->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($col))->setAutoSize(true);
				$col++;					
			}
			
			//Now we can enter our main rows into the worksheet			
			$i = 2;
			foreach ($addresses as $datarow) {
				$col = 0;
				foreach ($display_order as $field){
					if ($field !='zip') $value = html_entity_decode($datarow['Address'][$field],ENT_QUOTES);
					if ($field =='phone') {
						//strip off the beginning 402 472
						$value=preg_replace('/402[\s]+472[\s]+/','',$value);
						//set the value as text explicitly so the leading zeros are not dropped
						$objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($col,$i,$value);
					}
					elseif ($field=='address'){
						//split into address and zip as they are stored in one field in format of 31A LLS, UNL, 68588-4100
						$address_data = split(',',$value);
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col,$i,$address_data[0]);
						$col++;
						//assuming it stays in format of address,UNL,zip: we will add the zip this way
						$objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($col,$i,preg_replace('/68588-/','',$address_data[2]));
					}
					elseif($field !='zip') $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col,$i,$value);						
					$col++;
				}
				$i++; //increment the row counter
			}
			//freeze the top row
			$objPHPExcel->getActiveSheet()->freezePane('A2');
			//turn on the gridlines	
			$objPHPExcel->getActiveSheet()->setPrintGridlines(true);
			//adjust the margins
			$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(1);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(.7);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(.25);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(.25);

			//fit to 1 page by 2
			$objPHPExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setFitToHeight(2);
			
			/* Second worksheet in file
			 *  Stores the same information in one page wrapping column information 
			 */
			//Create a new worksheet, after the default sheet				
			$objPHPExcel->createSheet();
			//switch to the second sheet
			$objPHPExcel->setActiveSheetIndex(1);
			//set the style of the header to bold
			$objPHPExcel->getActiveSheet()->getStyle('A1:M1')->getFont()->setBold(true);
			//set the title of this worksheet to '1 page'
			$objPHPExcel->getActiveSheet()->setTitle('1 page');
			
			/* duplicate the header columns from the 2 page view and copy them twice with an empty col between */
			//add the first set of column headings
			$col = 0;
			foreach ($display_order as $header ) {
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col,1, Inflector::humanize($header));
				$objPHPExcel->getActiveSheet()->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($col))->setAutoSize(true);
				$col++;					
			}
							
			//add the headers again after a skip			
			$blank_column = $col; //keep track of it for later fill in - we'll want to change the column width to 2.70 also.
			$objPHPExcel->getActiveSheet()->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($blank_column))->setWidth(2.70);
			$col++;
			foreach ($display_order as $header ) {
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col,1, Inflector::humanize($header));
				$objPHPExcel->getActiveSheet()->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($col))->setAutoSize(true);
				$col++;						
			}				
			
			/* Add records, splitting  at 1/2 of the people and start over in the second section */
			
			$i = 2; $col_offset=0;
			$stop_at = count($addresses)/2;
			foreach ($addresses as $datarow) {
				if ($i > ($stop_at +1)) {
					$i=2;  $col_offset=$col;
				}
				$col = 0;
				foreach ($display_order as $field){
					if ($field !='zip') $value = html_entity_decode($datarow['Address'][$field],ENT_QUOTES);
					if ($field =='phone') {
						$value=preg_replace('/402[\s]+472[\s]+/','',$value);
						$objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($col+$col_offset,$i,$value);
					}
					elseif ($field=='address'){
						//split into address and zip
						$address_data = split(',',$value);
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col+$col_offset,$i,$address_data[0]);
						$col++;
						//assuming it stays in format of address,UNL,zip:
						$objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($col+$col_offset,$i,preg_replace('/68588-/','',$address_data[2]));
					}
					elseif($field !='zip') $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col+$col_offset,$i,$value);
					$col++;					
				}					
				$i++;
			}
			
			
			//freeze the top row
			$objPHPExcel->getActiveSheet()->freezePane('A2');		
				
			//turn on gridlines
			$objPHPExcel->getActiveSheet()->setPrintGridlines(true);
			
			//adjust margins
			$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(.45);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(.7);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(.25);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(.25);
			
			//fit to 1 page by 1
			$objPHPExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setFitToHeight(1);
			
			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel->setActiveSheetIndex(0);
		}
		if ($excel_type=='xls'){
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="phone_list.xls"');
			header('Cache-Control: max-age=0');
			//writer
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		}
		else{
			//try to output a newer version in Excel 2007 xlsx
			//change the default zip settings
			PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
			define('PCLZIP_TEMPORARY_DIR',$this->ROOT."/tmp/");
			// Redirect output to a client’s web browser (Excel2007)
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="phone_list.xlsx"');
			header('Cache-Control: max-age=0');
			//writer
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		}
		$objWriter->save('php://output');
		exit;
	}
	
	/**
	 * Edit an entry
	 * Will require admin level access
	 * @param int $id
	 */
	public function edit($id){
		$this->set('title_for_layout', 'Edit entry');
	}
	
	/** 
	 * Proceed cautiously with this one, as many of the entires are created and updated elsewhere
	 * Deletes the deletable infomation for an entry.
	 * Requires admin level access
	 * @param int $id
	 */
	public function delete($id){
		
	}
	
/**
 * public index is actually providing the json feed. we can rename it later 
 * 
 * @param string $letter the letter to filter the result set by
 * 
 */
 public function public_feed($letter=null){
 	$paginate = array('order'=>'name');
 		$suppressed_fields= Configure::read('feeds.suppressed_fields');
		$conditions = array('unl_status !='=>'emeriti');
		if (isset($letter)){
			$conditions['last_name LIKE']=$letter.'%';
		}
		$filters = array('location'=>'','unl_status'=>'','department'=>'');
		if (!empty($_GET['location']) || !empty($_GET['unl_status']) || !empty($_GET['department'])){
			//we have filters set to process by
			foreach (array_keys($filters) as $filter){
				if (!empty($_GET[$filter])) {
					if ($filter == 'department'){
						$staff_userids = array();
						$department_id = (int) $_GET[$filter];
						//filter on staff->department connection
						$department_data = $this->Address->StaffData->Department->findById($department_id);
						$filter_note .= " > ".$department_data['Department']['name'];
						$staff_userids = array_merge($staff_userids,Set::combine($department_data['Staff'],'{n}.id','{n}.userid'));
						//add in the subdepartments
						$subdepartments = $this->Address->StaffData->Department->find('list',array('conditions'=>array('parent_id'=>$department_id),'order'=>'name'));
						if (!empty($subdepartments)){
							foreach ($subdepartments as $sub_id=>$subdept){
								$department_data = $this->Address->StaffData->Department->findById($sub_id);
								$staff_userids = array_merge($staff_userids,Set::combine($department_data['Staff'],'{n}.id','{n}.userid'));
							}							
						}
						if (!empty($staff_userids)) $conditions['Address.userid'] = array_values($staff_userids);
						else $conditions['Address.userid'] = NULL;
					}
					else $conditions['Address.'.$filter]=$_GET[$filter];					
					$filters[$filter]=$_GET[$filter];  //keep track of it for paging
					//$this->request->data['Address'][$filter]=$filters[$filter];
				}
			}
			$this->set('filters',$filters);
		}	
		// TODO: retrieve the external links for Staff data too
		
		//if (!empty($conditions)) $people = $this->Address->find('all',array('conditions'=>$conditions));
		//else $people= $this->Address->find('all');
		$this->Paginator->settings = array('recursive'=>2);
		if (!empty($conditions)) $people = $this->Paginator->paginate('Address',$conditions);
		else $people = $this->Paginator->paginate('Address');
	
 		$content=array();
 		foreach ($people as $person):
 			$content['people'][]=$person;
		endforeach;
		$content['params']=$this->params;
		$this->autoRender = false; // We don't render a view in this example
		//$this->request->onlyAllow('ajax'); // No direct access via browser URL
		//header('Content-Type: application/json');
		$cb = $_GET['callback'];
		$this->RequestHandler->respondAs('json');
		$this->response->header('Content-Type: application/json');
    	if ($cb) echo $cb."(".json_encode($content).");";
    	else echo json_encode($content);
	}
	
	public function faculty_listing($letter=null){
		$suppressed_fields= Configure::read('feeds.suppressed_fields');
		$conditions = array('unl_status'=>'faculty');
		if (isset($letter)){
			$conditions['last_name LIKE']=$letter.'%';
		}
		if (!empty($conditions)) $people = $this->Address->find('all',array('conditions'=>$conditions));
		else $people = $this->Address->find('all');
		$content=array();
		foreach ($people as $person):
		$content['people'][]=$person;
		endforeach;
		$this->autoRender = false; // We don't render a view in this example
		//$this->request->onlyAllow('ajax'); // No direct access via browser URL
		//header('Content-Type: application/json');
		$cb = $_GET['callback'];
		$this->RequestHandler->respondAs('json');
		$this->response->header('Content-Type: application/json');
		if ($cb) echo $cb."(".json_encode($content).");";
		else echo json_encode($content);
	}
	

	public function staff_listing($letter=null){		
		$suppressed_fields= Configure::read('feeds.suppressed_fields');
		$conditions = array('unl_status'=>'staff');
		if (isset($letter)){
			$conditions['last_name LIKE']=$letter.'%';
		}		
		if (!empty($conditions)) $people = $this->Address->find('all',array('conditions'=>$conditions));		
		else $people = $this->Address->find('all');
		$content=array();
		foreach ($people as $person):
		$content['people'][]=$person;
		endforeach;
		$this->autoRender = false; // We don't render a view in this example
		//$this->request->onlyAllow('ajax'); // No direct access via browser URL
		//header('Content-Type: application/json');
		$cb = $_GET['callback'];
		$this->RequestHandler->respondAs('json');
		$this->response->header('Content-Type: application/json');
		if ($cb) echo $cb."(".json_encode($content).");";
		else echo json_encode($content);
	}
	
	public function get_letters($status){		
		foreach (range('a','z') as $starting_letter){
			if (!empty($status)) $content['letters'][$starting_letter]=$this->Address->find('count',array('conditions'=>array('unl_status'=>$status,'last_name LIKE'=>$starting_letter.'%')));
			else $content['letters'][$starting_letter]=$this->Address->find('count',array('conditions'=>array('last_name LIKE'=>$starting_letter.'%')));
		}
		$this->autoRender = false; // We don't render a view in this example
		$cb = $_GET['callback'];
		$this->RequestHandler->respondAs('json');
		if ($cb) echo $cb."(".json_encode($content).");";
		else echo json_encode($content);		
	}
	
	

}
