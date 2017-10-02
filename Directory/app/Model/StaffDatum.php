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
class StaffDatum extends Model {
	public $name = 'StaffDatum';
	public $useTable = 'library_data'; 
	public $primaryKey = 'id';
	public $recursive = 2;
	//public $displayField = 'full_name';
	
	//relationships
	
	/**
	 * Many to many relationships for the staff data
	 * @var array
	 */
	public $hasAndBelongsToMany = array(
		'Subjects'=>array(
			'className'=>'Subject',
				'joinTable'=>'subject_assignments',
				'foreignKey' => 'person_id',
				'associationForeignKey'=>'subject_id'
		),
		'Department'=>array(
			'className'=>'Department',
			'joinTable'=>'department_people',
			'foreignKey'=>'staff_id',
			'associationForeignKey'=>'department_id'
		)
		
	);
	
	
	public $hasMany = array(
			'ExternalLinks'=>array(
					'className'=>'ExternalLink',
					'foreignKey'=>'library_data_id'
			)
	);
	
	/**
	 * connects to the readonly view on one-to-one level
	 * to pull in additional non-editable data
	 * @var array
	 */
	public $belongsTo = array(
		'Address'=>array(
			'className'=>'Address',
			'foreignKey'=>'userid',
			'order'=>'last_name'			
			),
		
	);
	
	

}
