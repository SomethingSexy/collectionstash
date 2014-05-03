<?php echo $this -> Html -> script('pages/page.user.settings', array('inline' => false));?>

<?php echo $this -> Html -> scriptBlock('var rawProfile = ' .  json_encode($profile) . ';', array('inline' => false));?>