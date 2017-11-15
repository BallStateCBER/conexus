<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
	public $helpers = array(
		'Js' => array('Jquery'),
		'Html',
		'Form'
	);
	public $components = array(
		'DebugKit.Toolbar',
		'DataCenter.Flash',
        'Security'
	);
	public $categories = array();

	function beforeFilter() {
        $this->Security->blackHoleCallback = 'forceSSL';
        $this->Security->requireSecure();

		App::uses('Category', 'Model');
		$Category = new Category();
		$this->categories = $Category->find('list');

		App::uses('State', 'Model');
		$State = new State();
		$this->states = $State->find('all', array(
			'fields' => array('id', 'name', 'abbreviation'),
			'contain' => false
		));

        define('RELEASE_YEAR', 2017);
	}

	function beforeRender() {
		if ($this->layout == 'default') {
			$this->set(array(
				'states_list' => $this->states,
				'categories_list' => $this->categories
			));
		}
	}

    /**
     * Redirects the current request to HTTPS
     *
     * @return mixed
     */
    public function forceSSL()
    {
        return $this->redirect('https://' . env('SERVER_NAME') . $this->here);
    }
}
