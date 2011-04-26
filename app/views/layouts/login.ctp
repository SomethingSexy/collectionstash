<?php
/**
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
 * @subpackage    cake.cake.libs.view.templates.layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<!DOCTYPE html>
<head>
  <?php echo $this->Html->charset(); ?>
  <title>Collection Stash</title>
  <?php
    echo $this->Html->meta('icon');

    echo $this->Html->css('jquery.ui.core');
    echo $this->Html->css('jquery.ui.theme');
    echo $this->Html->css('jquery.ui.dialog');
    echo $this->Html->css('jquery.ui.tabs');
    echo $this->Html->css('layout/index');
    echo $this->Html->css('layout/fluid_bdr');
    echo $this->Html->css('layout/col_3_ml');
    echo $this->Html->css('layout/default');
    echo $this->Html->css('redmond');
    
    echo $this->Html->css('cake.generic');
    
    echo $this->Html->css('layout/non_msie');
    echo $this->Html->script('jquery-1.4.2');
    echo $this->Html->script('jquery-ui-1.8.5');
    echo $scripts_for_layout;
  ?>
<script>
$(document).ready(function(){

  $("ul.subnav").parent().append("<span></span>"); //Only shows drop down trigger when js is enabled (Adds empty span tag after ul.subnav*)

  $("ul.topnav li span").click(function() { //When trigger is clicked...

    //Following events are applied to the subnav itself (moving subnav up and down)
    $(this).parent().find("ul.subnav").slideDown('fast').show(); //Drop down the subnav on click

    $(this).parent().hover(function() {
    }, function(){
      $(this).parent().find("ul.subnav").slideUp('slow'); //When the mouse hovers out of the subnav, move it back up
    });

    //Following events are applied to the trigger (Hover events for the trigger)
    }).hover(function() {
      $(this).addClass("subhover"); //On hover over, add class "subhover"
    }, function(){  //On Hover Out
      $(this).removeClass("subhover"); //On hover out, remove class "subhover"
  });

});

//TODO I don't like this here
$(function(){
  $('.form-fields li input').focus(function(){
    $(this).parent('li').addClass('focused');  
  });
  $('.form-fields li input').blur(function(){
    $(this).parent('li').removeClass('focused');  
  });
});

</script> 
</head>
<body>
  <div id="container">
    <div id="header" class="clearfix">
      <div id="header-top">
        <div class="wrapper">
          <div class="box">
            <ul>
              <?php 
               if($isLoggedIn)
               {  ?>
                 <li><?php echo $html->link('Logout', array('action' => 'logout', 'controller' => 'users')); ?></li> 
              <?php  }
               else
               {   ?>
                  <li><?php echo $html->link('Login', array('controller' => 'users','action' => 'login')); ?></li>  
                  <li><?php echo $html->link('Register', array('controller' => 'users','action' => 'register')); ?></li>  
               <?php } ?>
             </ul>
          </div>  
         </div>   
      </div>
      <div id="header-bottom">
        <div class="wrapper">
          <div class="logo">
            <?php echo $html->image('logo/collection_stash_logo_white.png', array('alt' => 'Collection Stash'))?>
          </div>
          <div class="login-wrapper">
            <?php echo $this->Session->flash(); ?>
            <?php echo $content_for_layout; ?>
          </div>
        </div>
      </div>
    </div>
    <div id="stage">

    </div>
  <div id="footer"><span class="links">About | Contact | Donate | Found a bug? </span> <span class="copyright">Collection Stash - Copyright 2010</span></div>
    <?php /**echo $this->element('sql_dump');  
      echo $js->writeBuffer();
    */
      
     ?>
    <?php echo $this->element('sql_dump'); ?>
</body>
</html>