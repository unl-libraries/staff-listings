<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
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
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class Address extends Model {
	public $name = 'Address';
	public $useTable = 'library_people'; //readonly
	public $primaryKey = 'userid';
	public $recursive = 2;
	
	
	
	/**
	 * connects from the readonly view on one-to-one level
	 * to the editable data portion
	 * @var array
	 */
	public $hasOne = array(
			'StaffData'=>array(
					'className'=>'StaffData',
					'foreignKey'=>'userid'
			)
	);
	

	
	public $virtualFields = array(
			'name'=>'CONCAT(Address.last_name,", ",Address.first_name)',
			'display_name'=>'CONCAT(Address.first_name," ",Address.last_name)'
		//'department'=>"IF ((Address.library_sub_dept!='' AND Address.library_sub_dept IS NOT NULL),TRIM(CONCAT (Address.library_dept,' > ', Address.library_sub_dept)),Address.library_dept)",
		
	);
}
