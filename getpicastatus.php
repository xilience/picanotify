<?PHP

// FUNCTION getPicaStatus
// Inputs: URL to Picatinny site
// Outputs: Array
//  	[0] status, 1 means OPEN, 0 means CLOSED, 2 means error/unknown
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

?>
