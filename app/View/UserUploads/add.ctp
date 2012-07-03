<?php
if (isset($fake) && $fake) {
	echo '<textarea>' . $this -> Js -> object($aUpload) . '</textarea>';
} else {
	echo $this -> Js -> object($aUpload);
}
?>