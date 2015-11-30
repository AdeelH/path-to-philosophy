<?php

	include_once('../includes/functions.php');

	if (!empty($_POST))
	{
		$sql = json_decode($_POST["pages"]);
		$clicks = count($sql);
		for($i = $clicks; $i > 0; $i--)
		{
			$sql[$clicks - $i] = "('" . $sql[$clicks - $i] . "', " . $i . ")";			
		}

		query(sprintf("INSERT INTO stats(page, clicks) 
					   VALUES %s
					   ON DUPLICATE KEY 
					   UPDATE clicks = VALUES(clicks)", 
					   implode(",", $sql)));
		echo "Recorded";
	}
	
?>
