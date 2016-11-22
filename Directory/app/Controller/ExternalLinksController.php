<?php
/**
 * Dynamic data controller
 *
 * This file will render views from views/staff_data/
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

/**
 * Dynamic data controller for Staff Data
 * 
 * @author srickel1
 *
 */
class ExternalLinksController extends AppController {
	var $components = array('RequestHandler');
	
	var $scaffold;
	/**
	 * Listing of entries
	 * @param string $filter filter by this item (status, etc..)
	 *
	 */
// 	public function index($filter=null){
// 		//grab all addresses and pass it to the view:
		
// 			$addresses = $this->find('all');
// 			$this->set('addresses', $addresses);
// 			$this->set('title_for_layout', 'Staff Data');
// 	}
	
	public function beforeFilter() {
		parent::beforeFilter();
		if (!$this->Auth->loggedIn()) {
			$this->Auth->authError = false;
		}
	}
	
	/**
	 * View an addressbook entry. If no id sent, show index
	 * @param int $id
	 */
	public function view($id){
		if (!$id) {
			throw new NotFoundException(__('Invalid post'));
		}
		$this->set('title_for_layout', 'View Library Staff data ');
	}
	
	/**
	 * Search the addressbook for an entry
	 * @param string $query
	 */
	public function search($query){
		$this->set('title_for_layout', 'Library Staff Search');
	}
	
	
		
	/**
	 * Edit an entry
	 * Will require admin level access
	 * @param int $id
	 */
	public function edit($id=null){
		if (!$id) {
			$this->Session->setFlash('Invalid entry.  You cannot add new staff to the database. ','error');
			//throw new NotFoundException(__('Invalid entry.  You cannot add new staff to the database. '));
			//$this->layout='error';
			$this->render(false);
		}
		else {
			$this->StaffDatum->id=$id;
		
		// Has any form data been POSTed?
		if ($this->request->is('post') || $this->request->is('put')) {
			//debug($this->request->data);
			// If the form data can be validated and saved...
			if ($this->StaffDatum->save($this->request->data)) {
				// Set a session flash message and redirect.
				$this->Session->setFlash('Library data Saved!');
				return $this->redirect($this->request->data['StaffDatum']['return_url']);
			}
			else {
				debug($this->data);
				debug($this->request->data);
				$this->Session->setFlash('Error saving Library data');
			}
		}
		
		// If no form data, find the entry to be edited
		// and hand it to the view.
		$this->set('return_url',$this->referer());

		$departments = $this->StaffDatum->Department->find('list');
		//foreach ($departments as $top_dept){
			//$department_options = $departments;
		//}
		//debug ($department_options);
		$this->set('departments',$departments);
		
		if (!$this->request->data){
			$this->request->data= $this->StaffDatum->findById($id);
			$this->set('subjects',$this->StaffDatum->Subjects->find('list'));
			
		}
		$this->set('title_for_layout', 'Edit entry');
		$this->render('form');
		}
	}
	
	function delete($id=null) {
		// set default class & message for setFlash
		$class = 'error';
		$msg   = 'Invalid Link Id';
	
		// check id is valid
		if($id!=null && is_numeric($id)) {
			// get the Item
			$item = $this->ExternalLink->read(null,$id);
	
			// check Item is valid
			if(!empty($item)) {
				// try deleting the item
				if($this->ExternalLink->delete($id)) {
					$class = 'success';
					$msg   = 'Your ExternalLink was successfully deleted';
				} else {
					$msg = 'There was a problem deleting your ExternalLink, please try again';
				}
			}
		}
	
		// output JSON on AJAX request
		if($this->RequestHandler->isAjax()) {
			$this->autoRender = $this->layout = false;
			echo json_encode(array('success'=>($class=='error') ? FALSE : TRUE,'msg'=>"<p id='flashMessage' class='{$class}'>{$msg}</p>"));
			exit;
		}
	
		// set flash message & redirect
		$this->Session->setFlash($msg,'default',array('class'=>$class));
		//$this->redirect(array('action'=>'index'));
	}

}