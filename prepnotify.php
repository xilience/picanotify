<?php  // SCRIPT INFO, INCLUDES, GLOBALS
// prepnotify.php
// Pull list of recipients from database

include "globals.php";

?>
  
<?php // MAIN SCRIPT

$result = getNotifyList(2);
if ($result[0] != 1)	{echo "<br>Error code returned: "; print_r($result[1]);}
else echo "<br>Success!<br><br>";

print_r($result[2]);


function getNotifyList($notifyType) {
	
	global $host, $user, $pass, $db, $table;
	
	// open connection
	$connection = mysql_connect($host, $user, $pass); 
	if (!$connection)	{	return array(20,errorCode(20));	}

	// select database
	$db_selected = mysql_select_db($db, $connection);
	if (!$db_selected)	{	return array(21,errorCode(21));	}

	// create query
	$query = sprintf("SELECT * FROM $table");
				
	// execute query
	$result = mysql_query($query);
	if (!$result)	{	return array (22,"Error in query: $query. ".mysql_error());	}
			
	// if no rows returned, exit with error
	if (mysql_num_rows($result) == 0)
	{	
		mysql_free_result($result);
		mysql_close($connection);		
		return array (18, errorCode(18));
	}

	$userlist = array();
	while ($row = mysql_fetch_row($result)) {
		if (($row[4] == 0) || ($row[4] == $notifyType)){	array_push($userlist,$row[2]);	}
	}
		
	mysql_free_result($result);
	mysql_close($connection);
	return array (1, errorCode(1),$userlist);

}

?>
