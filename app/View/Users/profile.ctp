<?php echo $this -> Html -> script('pages/page.user.profile', array('inline' => false));?>

<?php echo $this -> Html -> scriptBlock('var rawProfile = ' .  json_encode($profile) . ';', array('inline' => false));?>
<?php echo $this -> Html -> scriptBlock('var rawFacts = ' .  json_encode($facts) . ';', array('inline' => false));?>
<?php echo $this -> Html -> scriptBlock('var rawPermissions = ' .  json_encode($permissions) . ';', array('inline' => false));?>