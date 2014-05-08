<?php echo $this -> Html -> script('pages/page.user.profile', array('inline' => false));?>

<?php echo $this -> Html -> scriptBlock('var rawProfile = ' .  json_encode($profile) . ';', array('inline' => false));?>
<?php echo $this -> Html -> scriptBlock('var rawFacts = ' .  json_encode($facts) . ';', array('inline' => false));?>
<?php echo $this -> Html -> scriptBlock('var rawCollectibles = ' .  json_encode($collectibles) . ';', array('inline' => false));?>