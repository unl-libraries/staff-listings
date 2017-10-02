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
class DepartmentsController extends AppController {


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
			$this->Auth->allow('view', 'index','search','print_view');
		}
	}
	
	public function index(){
		//grab all departments and pass it to the view:
		$departments =  $this->Paginator->paginate('Department');		
		$this->set('departments', $departments);
		$this->set('title_for_layout', 'Department Listing');
	}

	/**
	 * View a department entry. If no id sent, show index
	 * @param int $id
	 */
	public function view($id){
		if (!$id) {
			throw new NotFoundException(__('Invalid department'));
		}
		$this->set('department',$this->Department->findById($id));
		$department = $this->Department->findById($id);
		foreach ($department['Staff'] as $staff){
			$department_staff[]=$this->Department->Staff->findById($staff['id']);
		}
		if (!empty($department_staff)) $this->set('department_staff',Set::sort($department_staff,'{n}.Address.name','asc'));
		$this->set('title_for_layout', 'View Department data ');
	}

	 function print_view(){
		$this->set('departments',$this->Department->find('all'));	
		$this->layout='print';
	}
	
	/**
	 * Search the addressbook for an entry
	 * @param string $query
	 */
	public function search($query){
		$this->set('title_for_layout', 'Department Search');
	}

	

	/**
	 * Edit an entry
	 * Will require admin level access
	 * @param int $id
	 */
	public function edit($id=null){
		if (!$id) {
			$this->set('title_for_layout','Add a new department');
		}
		$this->Department->id=$id;
		$this->set('departments',$this->Department->find('list'));
		// Has any form data been POSTed?
		if ($this->request->is('post') || $this->request->is('put')) {
			//debug($this->request->data);
			// If the form data can be validated and saved...
			if ($this->Department->save($this->request->data)) {
				// Set a session flash message and redirect.
				$this->Session->setFlash('Library department Saved!');
				return $this->redirect($this->request->data['Department']['return_url']);
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
		if (!$this->request->data && $id){
			$this->request->data= $this->Department->findById($id);
				
		}
		$this->set('title_for_layout', 'Edit department');
		$this->render('form');

	}

	public function org_chart(){
		$this->set('title_for_layout','Library Organization Chart');
		
		$this->set('departments',$this->Department->find('all'));
	//	debug($this->Department->find('threaded',array('conditions'=>array('Department.id'=>'1'))));
	}
	/**
	 * Proceed cautiously with this one, as many of the entires are created and updated elsewhere
	 * Deletes the deletable infomation for an entry.
	 * Requires admin level access
	 * @param int $id
	 */
	public function delete($id=null){
		if (!$id) {
			throw new NotFoundException(__('Invalid department'));
		}
		else{
			if ($this->Department->delete($id)) {
				$this->Session->setFlash(__('Department deleted'));
				$this->redirect(array('action'=>'index'));
			}
			else $this->Session->setFlash(__('Error: unable to delete department.'));
			
		}
	}
}
