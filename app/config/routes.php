<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.app.config
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/views/pages/home.ctp)...
 */
Router::connect('/', array('controller' => 'home', 'action' => 'index'));
/**
 * ...and connect the rest of 'Pages' controller's urls.
 */
Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

//route all controller automatically...this way we can do the custom routing at the bottom...I think this works
$Configure = &Configure::getInstance();
$controllerList = $Configure -> listObjects('controller');

foreach ($controllerList as $controllerName) {
	//map all controllers (apart from app and pages to their name
	if ($controllerName != "App" & $controllerName != "Pages") {

		//route the normal name
		Router::connect('/' . $controllerName . '/:action/*', array('controller' => $controllerName));

		//get the name with first letter lower
		$firstLetterLower = strtolower(substr($controllerName, 0, 1));
		$lowerCaseName = $firstLetterLower . substr($controllerName, 1);

		//route the name with first letter lowered
		Router::connect('/' . $lowerCaseName . '/:action/*', array('controller' => $lowerCaseName));

		Router::connect('/' . $lowerCaseName, array('controller' => $lowerCaseName, 'action' => 'index'));
	}
}

//Rewrite the following routes for more specific detail
// Router::connect('/adminCollectibles/view/:id/:variant', array('controller' => 'adminCollectibles', 'action' => 'view'), array('pass' => array('id', 'variant')));
// 
// Router::connect('/adminCollectibles/approve/:id/:collectibleid', array('controller' => 'adminCollectibles', 'action' => 'approve'), array('pass' => array('id', 'collectibleid')));

// Router::connect('/users', array('controller' => 'users', 'action' => 'index'));
//Router::connect('/collections/stash/:username', array('controller' => 'collections', 'action' => 'stash'), array('pass' => array('username')));

// Router::connect('/:username', array('controller' => 'users', 'action' => 'home'), array('pass' => array('username')));

Router::parseExtensions('json');
?>