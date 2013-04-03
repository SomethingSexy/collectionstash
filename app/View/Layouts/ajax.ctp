<?php
header("Pragma: no-cache");
header("Cache-Control: no-store, no-cache, max-age=0, must-revalidate");
header('Vary: Accept');
if (isset($_SERVER['HTTP_ACCEPT']) && (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
	header('Content-type: application/json; charset=UTF-8');
} else {
	header('Content-type: text/plain; charset=UTF-8');
}
// this is fucking evil apparently and was causing the header to be too big in
// some browsers which would force an empty reponse, leaving here so I know never to do this
//header("X-JSON: " . urlencode($content_for_layout));
$this -> response -> type('json');
echo $content_for_layout;
?>