<?php
App::uses('AppController', 'Controller');
class ContactController extends AppController {
	var $name='Contact';
	public function beforeFilter() {
		parent::beforeFilter();
		// Allow only these actions.
		$this->Auth->allow('message');
	
	}
	function message() {
		if ($this->request->is('post')) {
			$this->Contact->set($this->data);
			if ($this->Contact->validates()) {
				//send email using the Email component
				$this->Email->to = 'dpoehlman7@unl.edu';
				$this->Email->subject = 'Message regarding the library directory application from ' . $this->data['Contact']['name'];
				$this->Email->from = $this->data['Contact']['email'];
	
				$this->Email->send($this->data['Contact']['details']);
			}
		}
	}
}
?>
	