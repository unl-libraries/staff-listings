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
class Department extends Model {
	public $name = 'Department';
	public $primaryKey ='id';
	public $useTable = 'departments'; //readonly
	public $recursive = 1;
	public $order = 'Department.name';
	public $displayField = "name";

	/**
	 * connects from the readonly view on one-to-one level
	 * to the editable data portion
	 * @var array
	 */
	public $belongsTo = array(
			'Department_above'=>array(
					'className'=>'Department',
					'foreignKey'=>'parent_id',
					'order'=>'Department_above.name'
			)
	);
	public $hasMany = array(
			'Sub_department'=>array(
				'className'=>'Department',
				'foreignKey'=>'parent_id',
				'order'=>'Sub_department.name'
				
		),
			
	);
	
	public $hasAndBelongsToMany = array(
			'Staff'=>array(
					'className'=>'StaffData',
					'joinTable'=>'department_people',
					'foreignKey'=>'department_id',
					'associationForeignKey'=>'staff_id'
			),

	);
	
	/**
	 * Returns a list of the departments 
	 * 
	 * @param int $id id of parent record to start at.  Default to 1 for top entry
	 * @param boolean $threaded Whether to return the list as a threaded list (id of parent=>array(children ids, etc...) or one combined list
	 */
	function getDeptList($id=1,$threaded=false){
		$department_list = array();
		$department = $this->find('threaded',array('conditions'=>array('Department.id'=>$id)));
		
		if ($threaded) $department_list[$id]=array();
		else array_push($department_list,$id);
		foreach ($department as $dept_info){
			if ($threaded && !empty($dept_info['Sub_department'])) {
				foreach ($dept_info['Sub_department'] as $sub_dept)
						$department_list[$id] = array_merge($department_list[$id],$this->getDeptList($sub_dept['id'],$threaded));
			}
			elseif (!empty($dept_info['Sub_department'])){
				foreach ($dept_info['Sub_department'] as $sub_dept) $department_list= array_merge($department_list,$this->getDeptList($sub_dept['id'],$threaded));
			}
		}
		
		return $department_list;
	}
	
	function getChildren(array $dept_list,$threaded=false){
		if (empty($dept_list) || empty($dept_list['Sub_department'])) return null;		
		else{
			$depts = array();
			foreach ($dept_list['Sub_department'] as $child){
				if ($threaded) $depts[$child['Department']['id']]=$this->getChildren($child);
				else array_merge($depts,$child['Department']['id'],$this->getChildren($child));
			}
			return $depts;
		}
	}
	
/** 
 * need to get it in format of 
 * $departments = array(
 * 		1 => 'Dean of Libraries',
 * 		'Dean of Libraries'=>array('
 * 			2 =>'Dean's Office'
 *	 		3 =>'Computing Operations and..'
 *			4 => 'Access Services'=>array(
 *				'5'=>'User Services',
 *				'6'=>'Branch Services')
 *		))
 * @param unknown $results
 * @return multitype:unknown
 */
	
	
}