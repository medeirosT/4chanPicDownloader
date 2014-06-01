<?php


// Input URL HERE :
$url = "http://boards.4chan.org/g/thread/42228391";


// DO NOT EDIT ANYTHING BELOW THIS LINE!
// --------------------------------------

set_time_limit(0);							// If you have a slow internet this is a life saver!

echo "Grabbing data...\n";

if ( $html = file_get_contents( $url ) ){

	echo "Exploding data...\n";

	preg_match_all('/(")(\\/)(\\/)(i\\.4cdn\\.org).*?(")/is', $html, $results);	// Just regex the files we're looking for.

	$results = array_values(array_unique($results[0]));		// Clean results from REGEX

	$totalResults = count($results);				// Get number of clean results

	foreach ($results as $currentResult => $result){
	
		$currentResult++;					// So it will show the right number in output, not index;
		$result = 'http:' . trim($result, '"');			// Clean out quotes from URL and add HTTP to it

		$filename = basename($result);				// Get filename from URL.

		echo "[$currentResult/$totalResults] $filename ";	// Simple echo to show user current position within thread

		try{	
			$fileData = file_get_contents($result);					// Attempt to grab file from site's CDN
		
			if( $writeResult = file_put_contents($filename, $fileData) ){		// Attempt to write result to file
			
				echo ".. OK!\n";

			} else {

				echo ".. ERROR!\n";

			}	
		} catch (Exception $e) {

			echo ".. ERROR : " . $e->getMessage() . "\n";
		}
	
	}

	echo "Done!";

} else {

	echo "ERROR : Thread possibly 404'd!";	

}
