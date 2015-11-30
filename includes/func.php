<?php
	
    require_once("constants.php");
	/*
	** Finds 'Philosophy'
	**
	*/
	function philosophize($next_page, $rand)
	{		
		// create URL by concatenating the part of the address common to all articles 
		// with the particular article title
		$url = "http://en.wikipedia.org/wiki/" . $next_page;
		
		// load html into a string
		$aContext = array(
			'http' => array(
				'proxy' => 'tcp://10.2.20.18:9090',
				'request_fulluri' => true,
			),
		);
		$cxContext = stream_context_create($aContext);

		$source = file_get_contents($url, False, $cxContext);

		if (strcasecmp(substr($source, 0, 15), "<!DOCTYPE html>") != 0)
		{
			$source = gzdecoder($source);
		}
		
		if ($source === false)
		{	
			return false;
		}
		
		$title = gettitle($source);
		
		unset($next_page);
		
		// Magic!
		$next = scrape($source, 0);
		
		if($next === false)
		{
			return false;
		}
		
		return ["next" => $next, "title" => $title, "rand" => ($rand == true) ? (str_replace(' ', '_', $title)):null];
	}
	
	/*
	** Scrapes Wikipedia articles for links meeting the Criteria: "the first link in the article text not in parentheses or italics" 
	*/
	function scrape($s, $offset)
    {
    	// locate para
    	$p_start = strpos($s, "<p>", $offset);
		$p_end = strpos($s, "</p>", $p_start);
		
		if ($p_start === false || $p_end === false)
		{
			return false;
		}
		// extract para		
		$p = substr($s, $p_start, $p_end - $p_start);
		
		if ($p === false)
		{	
			return false;
		}
		// get the right link
		$link = find_apt_link($p, 0);
		// if link found return it
		if ($link !== false)
		{	
			return $link;
		}
		// else move on to next paragraph
		else
		{
			return scrape($s, $p_end + 4);
		}
    }
	/*
	** Counts number of open and close parens and <em> tags before the link.
	** If the number of open ones is greater than the closed ones, then the link must lie between one of the pairs.
	** Returns, if found, the link that doesn't lie inside parens or <i> or <table> tags.
	*/
	function find_apt_link($para, $new_link_pos)
    {
        do
		{
			$old_link_pos = $new_link_pos;
			// location of the target link
			$new_link_pos = strpos($para, "href=\"/wiki/", $old_link_pos) + 12;
			if($new_link_pos === false || $new_link_pos == 12)
			{
				return false;
			}
		}
		while(filter($para, "(", ")", $old_link_pos, $new_link_pos)
		|| filter($para, "<i>", "</i>", $old_link_pos, $new_link_pos)
		|| filter($para, "<table>", "</table>", $old_link_pos, $new_link_pos)
		|| exclude($para, $new_link_pos));
		
		return substr($para, $new_link_pos, strpos($para, "\"", $new_link_pos) - $new_link_pos);
    }
     /*
    ** Exclude links falling within parens or some html tags
    */
    function filter($para, $str, $str2 , $old, $new)
    {
    	return (substr_count($para, $str, 0, $new-12) > substr_count($para, $str2, 0, $new-12)
				|| strrpos($para, $str, $old) > strrpos($para, $str2, $old));
    }
    /*
    ** Exclude links with these strings
    */
    function exclude($para, $new)
    {
    	return (strcasecmp(substr($para, $new, 5), "Help:") == 0
				|| strcasecmp(substr($para, $new, 10), "Wikipedia:") == 0
				|| strcasecmp(substr($para, $new, 5), "File:") == 0
				|| strcasecmp(substr($para, $new, 6), "Image:") == 0);
    }
    /*
    ** Searches record of URLs already visited to check if
    ** we've been here before, in case we end up going in circles
    */
    function been_here_before($urls_visited, $address)
    {
    	foreach($urls_visited as $row)
    	{
    		foreach($row as $key => $val)
    		{
    			// if found in the record, return true
    			if ($key == "url" && $val == $address)
    			{
    				return $row;
    			}
    		}
    	}
    	return false;
    }
    
    function gettitle($s)
    {
    	$t_start = strpos($s, "<title>", 0) + 7;
		$t_end = strpos($s, " - Wikipedia, ", $t_start);
		
		if ($t_start === false || $t_end === false)
		{
			return false;
		}
		// extract title		
		$title = substr($s, $t_start,  $t_end - $t_start);
		
		if ($title === false)
		{	
			return false;
		}
		else
		{
			return $title;
		}
    }
    
    function gzdecoder($d)
    {
		$f=ord(substr($d,3,1));
		$h=10;$e=0;
		if($f&4){
		    $e=unpack('v',substr($d,10,2));
		    $e=$e[1];$h+=2+$e;
		}
		if($f&8){
		    $h=strpos($d,chr(0),$h)+1;
		}
		if($f&16){
		    $h=strpos($d,chr(0),$h)+1;
		}
		if($f&2){
		    $h+=2;
		}
		$u = gzinflate(substr($d,$h));
		if($u===FALSE){
		    $u=$d;
		}
		return $u;
	}
	
?>
