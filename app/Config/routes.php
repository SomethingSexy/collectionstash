<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
Router::connect('/', array('controller' => 'home', 'action' => 'index'));
/**
 * ...and connect the rest of 'Pages' controller's urls.
 */

Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

/**
 * Renamed a url so keep it alive here.
 */
// Logged in user home
Router::connect('/user/home', array('controller' => 'users', 'action' => 'home'));
// Logged in user home
Router::connect('/user/home/activity', array('controller' => 'users', 'action' => 'activity'));
// Logged in user home
Router::connect('/user/home/history', array('controller' => 'users', 'action' => 'history'));
// Logged in user home
Router::connect('/user/home/notifications', array('controller' => 'users', 'action' => 'notifications'));

//public user profile, right now this routes to stash but this might route to a profile eventually
Router::connect('/user/:id', array('controller' => 'users', 'action' => 'view'), array('pass' => array('id')));
// public user stash
Router::connect('/user/:id/stash', array('controller' => 'users', 'action' => 'profile'), array('pass' => array('id')));
//public user profile
Router::connect('/user/:id/wishlist', array('controller' => 'users', 'action' => 'profile'), array('pass' => array('id')));
// public sale
Router::connect('/user/:id/sale', array('controller' => 'users', 'action' => 'profile'), array('pass' => array('id')));
//public photos
Router::connect('/user/:id/photos', array('controller' => 'users', 'action' => 'profile'), array('pass' => array('id')));
//public stash comments
Router::connect('/user/:id/comments', array('controller' => 'users', 'action' => 'profile'), array('pass' => array('id')));
//public history
Router::connect('/user/:id/history', array('controller' => 'users', 'action' => 'profile'), array('pass' => array('id')));

Router::connect('/stash/*', array('controller' => 'users', 'action' => 'profile'));
Router::connect('/collectibles/catalog/*', array('controller' => 'collectibles', 'action' => 'search'));

//old replaced by public
Router::connect('/stash/comments/*', array('controller' => 'users', 'action' => 'profile'));
//old replaced by public
Router::connect('/wishlist/*', array('controller' => 'users', 'action' => 'profile'));
//old replaced by public
Router::connect('/stashes/view/*', array('controller' => 'users', 'action' => 'profile'));
Router::connect('/stashs/view/*', array('controller' => 'users', 'action' => 'profile'));
//old replaced by public
Router::connect('/sale/*', array('controller' => 'users', 'action' => 'profile'));

// maintaining ths old manufactures since I can't spell
Router::connect('/manufactures/view/*', array('controller' => 'manufactures', 'action' => 'index'));
// since I can't spell right
Router::connect('/manufacturer/*', array('controller' => 'manufactures', 'action' => 'index'));

Router::connect('/artist/*', array('controller' => 'artists', 'action' => 'index'));
// logged in user profile
Router::connect('/settings', array('controller' => 'profiles', 'action' => 'index'));
Router::connect('/settings/profile', array('controller' => 'profiles', 'action' => 'index'));
Router::connect('/settings/stash', array('controller' => 'profiles', 'action' => 'index'));
Router::connect('/profile/*', array('controller' => 'users', 'action' => 'profile'));





/**
 * Load all plugin routes.  See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
CakePlugin::routes();

/**
 * Load the CakePHP default routes. Remove this if you do not want to use
 * the built-in default routes.
 */
require CAKE . 'Config' . DS . 'routes.php';

Router::parseExtensions('json');
?>