<?php echo $this -> Html -> script('pages/page.user.profile', array('inline' => false));?>
<?php echo $this -> Html -> scriptBlock('var rawProfile = ' .  json_encode($profile) . ';', array('inline' => false));?>
<?php echo $this -> Html -> scriptBlock('var rawFacts = ' .  json_encode($facts) . ';', array('inline' => false));?>
<?php echo $this -> Html -> scriptBlock('var rawPermissions = ' .  json_encode($permissions) . ';', array('inline' => false));?>
<?php echo $this -> Html -> scriptBlock('var rawReasons = ' .  json_encode(Set::extract('/CollectibleUserRemoveReason/.', $reasons)) . ';', array('inline' => false));?>
<?php echo $this -> Html -> scriptBlock('var rawFilters = ' .  json_encode($filters) . ';', array('inline' => false));?>
<?php echo $this -> Html -> scriptBlock('var rawActivity = ' .  json_encode($activity) . ';', array('inline' => false));?>
<?php echo $this -> Html -> scriptBlock('var serverTime = "' . date('Y-m-d H:i:s') .'";', array('inline' => false));  ?>
<?php
if(isset($stashFacts)){
	echo $this -> Html -> scriptBlock('var rawStashFacts = ' .  json_encode($stashFacts) . ';', array('inline' => false));
} else {
	echo $this -> Html -> scriptBlock('var rawStashFacts = {};', array('inline' => false));
}?>
<?php
if(isset($comments)){
	echo $this -> Html -> scriptBlock('var rawComments = ' .  json_encode($comments) . ';', array('inline' => false));
}?>