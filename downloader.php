<?php

set_time_limit(9999999);	// If you have a slow internet this is a life saver!

// Input URL HERE :
$url = "http://boards.4chan.org/b/thread/547204940";


// DO NOT EDIT ANYTHING BELOW THIS LINE!
echo "Grabbing data...\n";

$html = file_get_contents($url);

echo "Exploding data...\n";

$html = str_replace('"',"",$html);

$html = explode(" ", $html);

$urls = array();

echo "Finding URLs...\n";

foreach ($html as $line){

	if(strpos($line, "i.4cdn.org") > -1 ){
		
		$url =  substr($line, 7);
		$filename = substr(strrchr($line, "/"),1);
		
		$urls[$filename] = $url;

	}
}

unset($html, $line, $filename, $url); // small cleanup

echo "Got " . count($urls) . " Images! Starting Download...\n";
$row = 1;
$total = count($urls);

foreach ($urls as $filename => $url){

	echo "[$row/$total] $filename\n";
	$data = file_get_contents("http://" . $url );
	file_put_contents( "./" . $filename,$data);
	$row++;

}

