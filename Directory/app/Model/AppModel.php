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
class AppModel extends Model {
	function paginate ($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array())
	{
		$args = func_get_args();
		$uniqueCacheId = '';
		foreach ($args as $arg) {
			$uniqueCacheId .= serialize($arg);
		}
		if (!empty($extra['contain'])) {
			$contain = $extra['contain'];
		}
		$uniqueCacheId = md5($uniqueCacheId);
		$pagination = Cache::read('pagination-'.$this->alias.'-'.$uniqueCacheId, 'paginate_cache');
		if (empty($pagination)) {
			$pagination = $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group', 'contain'));
			Cache::write('pagination-'.$this->alias.'-'.$uniqueCacheId, $pagination, 'paginate_cache');
		}
		return $pagination;
	}
	
	function paginateCount ($conditions = null, $recursive = 0, $extra = array()) {
		$args = func_get_args();
		$uniqueCacheId = '';
		foreach ($args as $arg) {
			$uniqueCacheId .= serialize($arg);
		}
		$uniqueCacheId = md5($uniqueCacheId);
		if (!empty($extra['contain'])) {
			$contain = $extra['contain'];
		}
		 
		$paginationcount = Cache::read('paginationcount-'.$this->alias.'-'.$uniqueCacheId, 'paginate_cache');
		if (empty($paginationcount)) {
			$paginationcount = $this->find('count', compact('conditions', 'contain', 'recursive'));
			Cache::write('paginationcount-'.$this->alias.'-'.$uniqueCacheId, $paginationcount, 'paginate_cache');
		}
		return $paginationcount;
	}
}
