<?php

	include_once('../includes/func.php');
	
	if (!empty($_GET["data"]))
	{
		// in case we're requesting pages too fast and the server gets mad
		sleep(1);
		
		echo json_encode( philosophize($_GET["data"], $_GET["rand"]) );
	}
	else
		echo "error";
	
?>
