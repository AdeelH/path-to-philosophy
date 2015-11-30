<?php

    // configuration
    require("../includes/config.php");
    include_once("../includes/func.php");
    include_once("../includes/functions.php");
    
    $stats = query("SELECT *
    				FROM stats 
    				ORDER BY clicks DESC");
    // render 
    render("log", ["stats" => $stats]);
?>
