<?php
	include_once('../includes/simple_html_dom.php');
	include_once('../includes/func.php');
	include_once('../includes/functions.php');

	if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
    	passthru(sprintf("php wiki3.php %s", $_POST["page"]));
    	exit();
    }
    
?>
