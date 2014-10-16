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
$url = "";

// Number of times to attempt connection / file download
$retries = 3;

// DO NOT EDIT ANYTHING BELOW THIS LINE!
// --------------------------------------

set_time_limit(0);							// If you have a slow internet this is a life saver!
error_reporting(0);							// so only my errors show :)

$pageTryNumber = 1;


// TODO : Clean up the retry code. It's dirty as heck
// TODO : Redo output (Copying/pasting messages is dirty. function it!)
// TODO : function the 404 check on the thread in its own loop! 
// TODO : Show user more stats! (Low Priority)
// TODO : Check if file_get_contents has a timeout that can be set (Maybe make a mode for people with bad internet connections)
// TODO : General code clean up. Been a while since I touched this and I hate it.


if ($retries < $pageTryNumber){ die("FATAL : At least make the # of retries bigger than 0! See config lines in beginning of script!"); }

do {
	echo "INFO  : Grabbing data (Attempt $pageTryNumber of $retries)...\n";

	if ( $html = file_get_contents( $url ) ){

		echo "INFO  : Exploding data...\n";

		preg_match_all( '/(")(\\/)(\\/)(i\\.4cdn\\.org).*?(")/is' , $html, $results );	// Just regex the files we're looking for.

		$results = array_values( array_unique( $results[0] ) );		// Clean results from REGEX

		$totalResults = count( $results );				// Get number of clean results
		
		echo "INFO  : Found $totalResults media file(s)!\n";

		foreach ( $results as $currentResult => $result ){
			
			$currentFileTries = 1;					// Number of tries we have on this file

			$currentResult++;					// So it will show the right number in output, not index;
			$result = 'http:' . trim( $result, '"' );		// Clean out quotes from URL and add HTTP to it

			$filename = basename( $result );				// Get filename from URL.
		
			echo "[$currentResult/$totalResults] $filename ";	// Simple echo to show user current position within thread

			if ( file_exists( $filename ) ) {			// if like me you have a bad internet connection
										// this if statement will be a blessing!
				echo ".. Exists, ignoring!\n";
				continue;

			}

			do {											// do-while loop, for retries. Still uses the user-changeable variable up top!
				try{	
					$fileData = file_get_contents( $result );				// Attempt to grab file from site's CDN
		
					if( $writeResult = file_put_contents( $filename, $fileData ) ){		// Attempt to write result to file
			
						echo ".. OK! (". formatBytes(strlen($fileData)) . ")\n";
						break;

					} else {

						echo ".. ERROR!\n";

					}	
				} catch (Exception $e) {

					echo ".. ERROR! \n";
		
					if ($currentFileTries != $retries ) echo "Retrying.. (Attempt $currentFileTries of $retries )\n";
					
					
				}
				$currentFileTries++;
			} while ($currentFileTries <= $retries);
	
		}

		echo "Done!\n";
		die();

	} else {

		echo "ERROR : Thread possibly 404'd!\n";
		

	}
	if ($pageTryNumber != $retries ) echo "INFO  : Retrying...\n"; else echo "FATAL : Could not connect after $retries tries! Aborting...\n";
	$pageTryNumber++;

} while ( $pageTryNumber <= $retries );

// Source for this function : http://stackoverflow.com/questions/2510434/format-bytes-to-kilobytes-megabytes-gigabytes
// As stated... This is Quick-n-Dirty!

function formatBytes($size, $precision = 2)
{
    $base = log($size) / log(1024);
    $suffixes = array('', 'k', 'M', 'G', 'T');   

    return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
}