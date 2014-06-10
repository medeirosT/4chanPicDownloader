<?php

/**
* 
* The MIT License (MIT)
* 
* Copyright (c) 2014 Tiago Roque Medeiros
* 
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:
* 
* The above copyright notice and this permission notice shall be included in all
* copies or substantial portions of the Software.
* 
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
* SOFTWARE.
* 
*/

// Input URL HERE :
$url = "<INSERT FULL 4CHAN URL HERE>";


// DO NOT EDIT ANYTHING BELOW THIS LINE!
// --------------------------------------

set_time_limit(0);							// If you have a slow internet this is a life saver!

echo "Grabbing data...\n";

if ( $html = file_get_contents( $url ) ){

	echo "Exploding data...\n";

	preg_match_all( '/(")(\\/)(\\/)(i\\.4cdn\\.org).*?(")/is' , $html, $results );	// Just regex the files we're looking for.

	$results = array_values( array_unique( $results[0] ) );		// Clean results from REGEX

	$totalResults = count( $results );				// Get number of clean results

	foreach ( $results as $currentResult => $result ){
	
		$currentResult++;					// So it will show the right number in output, not index;
		$result = 'http:' . trim( $result, '"' );		// Clean out quotes from URL and add HTTP to it

		$filename = basename( $result );				// Get filename from URL.

		echo "[$currentResult/$totalResults] $filename ";	// Simple echo to show user current position within thread

		try{	
			$fileData = file_get_contents( $result );				// Attempt to grab file from site's CDN
		
			if( $writeResult = file_put_contents( $filename, $fileData ) ){		// Attempt to write result to file
			
				echo ".. OK! (". strlen($fileData)  . " bytes)\n";

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
