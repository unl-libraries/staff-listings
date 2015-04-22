<?php
/**
 * Dynamic content controller.
 *
 * This file will render views from views/users/ and control
 * access
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
 *
 */
class UsersController extends AppController {
	
	var $scaffold;
	/**
	 * Listing of entries
	 * @param string $filter filter by this item (status, etc..)
	 */
	
	public function beforeFilter() {
		parent::beforeFilter();
		// Allow only the view and index actions.
		$this->Auth->allow('login','password_reminder');
	}

	
	public function login() {
		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				return $this->redirect($this->Auth->redirectUrl());
				// Prior to 2.3 use `return $this->redirect($this->Auth->redirect());`
			} else {
				$this->Session->setFlash(__('Username or password is incorrect'), 'default', array(), 'auth');
			}
		}
	}
	
	public function logout() {
		return $this->redirect($this->Auth->logout());
	}
	
	public function index(){			
		$this->set('users',$this->Paginator->paginate('User'));
		$this->set('title_for_layout', 'Admin users');
	}
	public function view($id){
		if (!$id) $this->redirect('index');
		else{
			$this->set('user',$this->User->findById($id));
		}
	}
	
	
	public function edit($id) {
		if (!$id) {
			throw new NotFoundException(__('Invalid entry'));
		}
		$this->User->id = $id;
		// Has any form data been POSTed?
		if ($this->request->is('post') || $this->request->is('put')) {
			
			// If the form data can be validated and saved...
			if ($this->User->save($this->request->data)) {
				// Set a session flash message and redirect.
				$this->Session->setFlash('User information Saved!');
				return $this->redirect('/users');
			}
			else $this->Session->setFlash('Error on saving');
		}
		
		// If no form data, find the user to be edited
		// and hand it to the view.
		$this->request->data= $this->User->findById($id);
		
	}
	
	public function add(){
		if ($this->request->is('post')) {
			// If the form data can be validated and saved...
			if ($this->User->save($this->request->data)) {
				// Set a session flash message and redirect.
				$this->Session->setFlash('User information Saved!');
				return $this->redirect('/users');
			}
		}
		
	}
	
	/* password functions */
	
	public function password_reminder($token=null){
		$authorized=false;
		if ($token) {			
			$this->set('token',$token);
			$this->Session->setFlash("Please verify your email.",'default', array(), 'good');
		}
		elseif ($this->request->is('post') && !empty($this->request->data['User']['email'])){
			$email = $this->request->data['User']['email'];			
			if (isset($this->request->data['User']['token']) && ($this->request->data['User']['token'] !='')){
				//check against the database
				$db_token = $this->User->field('token',array('email' => $email));
				if (($db_token!='' && $db_token != NULL) && ($this->request->data['User']['token'] == $db_token)){
										
					//allow the password reset form					
					$user_id = $this->User->field('id',array('email'=>$email));		
					$this->request->data=$this->User->findById($user_id);								
					if ($this->Auth->login($this->request->data['User']) ){						
						   $this->Session->setFlash('You are temporarily logged in to change your password '.$this->Auth->user('username'),'default',array(),'good');
						   return $this->redirect(array('action'=>'edit',$user_id));
					}
					else $this->Session->setFlash("Not logged in for some unknown reason. *sigh*");
								
					
				}
				else { $this->set('authorized',false); $this->Session->setFlash('Error - token may have expired or be incorrect.');}
			}
			else {
				/* the reset url in case */
				$key = Security::hash(String::uuid(),'sha1',true);
				
				$url = Router::url( ($this->here), true ).'/'.$key;
				$message = "Hello, someone has requested help with the password for the account associated with this email on libdirectory.unl.edu \r\n \r\n";
				$user = $this->User->findByEmail($email);
				if (!empty($user)){
					//store the key for validation when they click the link to reset
					$this->User->save(array('id'=>$user['User']['id'],'token'=>$key));
					$message .= "Your username is: ".$user["User"]["username"].".\r\n";
					if (!empty($user["User"]["password_hint"])) $message .="Your password hint is: ". $user["User"]["password_hint"].".\r\n \r\n  You may try logging in again at http://libdirectory.unl.edu/users/login \r\n \r\n";
					else $message .=" You did not supply a password hint. \r\n";
					$message .= "If you requested this information and either do not have a password hint, or the hint does not help you remember your password, you can reset your password using this link $url. "."\r\n \r\n";
					$message .= "If you still experience problems, please contact srickel1@unl.edu";
					if (isset($user['User']['email'])) {
						$headers = 'From: srickel1@unl.edu' . "\r\n" .
								'Reply-To: srickel1@unl.edu' . "\r\n" .
								'X-Mailer: PHP/' . phpversion();
						if (mail($user['User']['email'], "Lib directory password reminder",$message,$headers)) $this->Session->setFlash("An email has been sent to your address with help on your password.");
						else $this->Session->setFlash("There was a problem sending mail, please check with the site admin.<br />");
					}
				}
				else $this->Session->setFlash("No user found.  If you believe this is an error, please contact srickel1@unl.edu with a desciption of the problem.");
			}
		}
		
		else {
			/* show the form to submit their email*/
		}
	}
	

}
