<?php // MAIN CODE

  global $url;
	//echo("This is not yet complete! getOldStatus not implemented!<br><br>");
	//include "getpicastatus.php";
	include "globals.php";
	$oldStatus = getOldStatus();
	$status = getPicaStatus($url);
	
	print_r($oldStatus);
	
	if (($status[0] == 1) && ($oldStatus[0] == 1)){ //old status and new status are OPEN
		echo "Picatinny is OPEN.\n";
		echo "Message is ".$status[1];
		
		//Picatinny is OPEN, and was open prior.  No change, exit script.
		exit();
	}
	elseif (($status[0] == 0) && ($oldStatus[0] == 1)) { //old status OPEN, new status CLOSED
		echo "Picatinny is CLOSED, yea boi!";	
		echo "Message is ".$status[1];
		
		// notify(0);  // Notify all members
		
	}
	elseif (($status[0] == 1) && ($oldStatus[0] == 0)) { //old status CLOSED, new status OPEN
		echo "Picatinny is OPEN.\n";
		echo "Message is ".$status[1];
	
		//notify(2);  // Notify only those who want to be notified of ANY CHANGE
	
	}
	elseif (($status[0] == 1) && ($oldStatus[0] == 1)) { //old status CLOSED, new status CLOSED
		echo "Picatinny is CLOSED, yea boi!";
		echo "Message is ".$status[1];		
		
		// notify(1);	// Notify only those who chose to be notified of CLOSURE ONLY
	
	}
	else						{		echo "I can't find anything!" ;	}

	//// Now we update the picastatus saved file
	$msg = $status[0].$status[1];
	$file = "lastpicastatus.log";
	$fh = fopen($file,'w+') or die("Failed to open log file.");
	fwrite($fh, $msg);
	fclose($fh);

?>

<?PHP  // SCRIPT FUNCTIONS

// FUNCTION getPicaStatus
// Inputs: URL to Picatinny site
// Outputs: Array
//		[0] status, 1 means OPEN, 0 means CLOSED, 2 means error/unknown
//		[1] time, last time status was updated
//		[2] msg, additional status message

function getPicaStatus($url) {
	// get data from url using curl function
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_CAINFO, getcwd() . "picastatus.cer");
	$page = curl_exec($ch);
	curl_close($ch);

#	echo $page;  //echo the full page to ensure it is read correctly

	$output_array = array();

	// find position of substring for status(just need to check if it exists)
	if (preg_match('/<span id="lblStatusTextHTML">Weather Condition: Picatinny.*(open)*.<\/span>/i',$page))	{ $output_array[0]=1; }
	elseif (preg_match('/<span id="lblStatusTextHTML">Weather Condition: Picatinny.*(closed)*.<\/span>/i',$page))	{ echo $output_array[0]=0; }
	else																{ echo $output_array[0]=2; }
	
	preg_match('/<span id="lblStatusLastUpdated">Last Time Status Changed:.*.<\/span>/',$page, $output_array[1]);
	$output_array[1] = $output_array[1][0];
	preg_match('/<span id="lblStatusMessageHTML">.*.<\/span>/',$page, $output_array[2]);
	$output_array[2] = $output_array[2][0];
	
	return ($output_array);
}

function getOldStatus()  {

	$file = "lastpicastatus.log";
	$fh = fopen($file,'r') or die("Failed to open log file.");
	$read = fread($fh, filesize($file));
	fclose($fh);

	$status[0] = $read[0];
	$status[1] = substr($read,1);

	return $status;

}

?>
