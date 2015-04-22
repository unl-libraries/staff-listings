<?php
/**
 * Dynamic content controller.
 *
 * This file will render views from views/subjects/
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
 * Dynamic content controller
 * 
 * @author srickel1
 */

class SubjectsController extends AppController {
	
	var $scaffold;
	/**
	 * Listing of entries
	 * @param string $filter filter by this item (status, etc..)
	 */
	public function index($filter=null){
 		$this->Paginator->settings = array(
 			'recursive'=>1
 		);
		$subjects = $this->Paginator->paginate('Subject');
		foreach ($subjects as $i=>$subject){
			$c=0;
			foreach ($subject['Faculty'] as $faculty){
				$faculty_info = $this->Subject->Faculty->findByUserid($faculty['userid']);
 				$subjects[$i]['Faculty'][$c]['full_name']=$faculty_info['Address']['display_name'];
 				$c++;
			}
		}
		$this->set('subjects',$subjects);
		$this->set('_serialize',array('subjects'));
		$this->set('title_for_layout', 'Subject areas');
	}
	
	public function view_all(){
		
		$subjects = $this->Subject->find('all',array('recursive'=>1));
		foreach ($subjects as $i=>$subject){
			$c=0;
			foreach ($subject['Faculty'] as $faculty){
				$faculty_info = $this->Subject->Faculty->findByUserid($faculty['userid']);
				$subjects[$i]['Faculty'][$c]['full_name']=$faculty_info['Address']['display_name'];
				$c++;
			}
		}
		$this->set('subjects',$subjects);
		$this->set('_serialize',array('subjects'));
		$this->render('index');	
	}
	
	public function beforeFilter() {
		parent::beforeFilter();
		if (!$this->Auth->loggedIn()) {
			$this->Auth->authError = false;
		}
		$this->Auth->allow('view', 'index','print_view','view_all','feed','get_letters');
	}
	
	/**
	 * View a subject entry. If no id sent, show index
	 * @param int $id
	 */
 	public function view($id){
 		if (!$id) $this->redirect(array('controller'=>'subjects','action'=>'index'));
 		else{
 			$this->set('title_for_layout', 'View subject entry');
 			$subject = $this->Subject->findById($id);
 			$c=0;
 			foreach ($subject['Faculty'] as $faculty){
 				$faculty_info = $this->Subject->Faculty->findByUserid($faculty['userid']);
 				$subject['Faculty'][$c]['full_name']=$faculty_info['Address']['full_name'];
 				$c++;
 			}
 			$this->set('subject',$subject);
 		}
 	}
	
	
	function print_view(){
	
		$subjects = $this->Subject->find('all',array('recursive'=>1));
		foreach ($subjects as $i=>$subject){
			$c=0;
			foreach ($subject['Faculty'] as $faculty){
				$faculty_info = $this->Subject->Faculty->findByUserid($faculty['userid']);
				$subjects[$i]['Faculty'][$c]['full_name']=$faculty_info['Address']['full_name'];
				$c++;
			}
		}
		$this->set('subjects',$subjects);
		$this->layout='print';
	}
	
	/**
	 * Search the subjects for an entry
	 * @param string $query
	 */
	public function search($query){
		$this->set('title_for_layout', 'Subject Search');
	}
	
	
	
	/**
	 * providing the json feed
	 *
	 * @param string $letter the letter to filter the result set by
	 *
	 */
	public function feed(){
		$this->Paginator->settings = array(
				'recursive'=>1
		);
		$subjects = $this->Paginator->paginate('Subject');
		foreach ($subjects as $i=>$subject){
			$c=0;
			foreach ($subject['Faculty'] as $faculty){
				$faculty_info = $this->Subject->Faculty->findByUserid($faculty['userid']);
				$subjects[$i]['Faculty'][$c]['Address']=$faculty_info['Address'];
				$c++;
			}
		}	
		$content=array();
		$content['subjects']=$subjects;
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
	
	/**
	 * Edit an entry
	 * Will require admin level access
	 * @param int $id
	 */
	public function edit($id=null){
		
		if (!isset($id)) {
			$this->set('title_for_layout', 'New subject');
		}
		else{
			$this->Subject->id=$id;
			$this->set('title_for_layout', 'Edit subject entry');
		}
		// Has any form data been POSTed?
		if ($this->request->is('post') || $this->request->is('put')) {
			//debug($this->request->data);
			// If the form data can be validated and saved...
			if ($this->Subject->save($this->request->data)) {
				// Set a session flash message and redirect.
				$this->Session->setFlash('Library data Saved!');
				return $this->redirect($this->request->data['Subject']['return_url']);
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

		if (!$this->request->data && isset($id)){
			$this->request->data= $this->Subject->findById($id);
		}
		$faculty = $this->Subject->Faculty->Address->find('list',array('fields'=>array('Address.userid','Address.display_name'),'conditions'=>array('Address.unl_status'=>'faculty')));
		$subject_liaisons=array();
		$subject_liaisons[null]='None';
		foreach ($faculty as $faculty_userid=>$faculty_name){
			$subject_liaisons[$this->Subject->Faculty->field('id',array('userid'=>$faculty_userid))]=$faculty_name;
			
		}
		$this->set('faculty',$subject_liaisons);
				
	}
	
	public function get_letters(){
		foreach (range('a','z') as $starting_letter){
			$content['letters'][$starting_letter]=$this->Subject->find('count',array('conditions'=>array('subject LIKE'=>$starting_letter.'%')));
		}
		$this->autoRender = false; // We don't render a view in this example
		$cb = $_GET['callback'];
		$this->RequestHandler->respondAs('json');
		if ($cb) echo $cb."(".json_encode($content).");";
		else echo json_encode($content);
	}
	/** 
	 * Proceed cautiously with this one, as many of the entires are created and updated elsewhere
	 * Deletes the deletable infomation for an entry.
	 * Requires admin level access
	 * @param int $id
	 */
	public function delete($id){
		
	}
}
