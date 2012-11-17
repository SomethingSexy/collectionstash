<?php
header("Pragma: no-cache");
header("Cache-Control: no-store, no-cache, max-age=0, must-revalidate");
header('Content-Type: application/json');
header("X-JSON: " . urlencode($content_for_layout));
$this -> response -> type('json');
echo $content_for_layout;
?>