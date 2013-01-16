<!doctype html>
<html>

<head>
<title>Picatinny Weather Notification System</title>
<script src="jquery-1.7.1.min.js"></script>
</head>


<?php

include ".\globals.php";

$userinfo = getInfoFromUser();

?>


<?php

function getInfoFromUser() {

  global $apikey;

	if (!isset($_POST['submitbutton'])) {
		echo "<br>Please enter your information below:<br><br>";
		?>
		
		<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" >
		First Name: <br><input type="text" size="20" maxlength="20" name="Fname"><br /><br>
		Last Name: <br><input type="text" size="20" maxlength="36" name="Lname"><br /><br>
		Email or Text Message Address (see below for details): <br>
		<input type="text" size="25" maxlength="40" name="address"><br />
		<br>Choose your notification option: <br>
		<input type="radio" name="radiobutton" value="0" checked> Notify me when Picatinny has any change to OPEN/CLOSED/DELAYED.<br>
		<input type="radio" name="radiobutton" value="1"> Notify me always when Picatinny is CLOSED.<br>
		<input type="radio" name="radiobutton" value="2"> Notify me only when Picatinny changes from OPEN to CLOSED/DELAYED.<br><br>
		<input type="submit" name="submitbutton" value="Add User">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="submit" name="submitbutton" value="Update User">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="submit" name="submitbutton" value="Remove User">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<br /> </form><br />
		
		<?php
	}	
	else {
	
		$user = $_POST["Lname"].$_POST["Fname"];
		$address = $_POST["address"];
		$notify_option = $_POST['radiobutton'];
		
		if ($_POST['submitbutton'] == "Add User")		{ $action = "add"; }
		if ($_POST['submitbutton'] == "Update User")	{ $action = "update"; }
		if ($_POST['submitbutton'] == "Remove User")	{ $action = "rem"; }

		api_connect($action, $user, $address, $notify_option, $apikey);
		
		return array ($action, $user, $address, $notify_option, $apikey);
	}
	
}
	
function api_connect($action="print", $user="", $address="", $notify_option="", $apikey="0") {

	global $devmode;

	if ($devmode)	{
		echo "Action is: $action<br>";
		echo "User is: $user<br>";
		echo "Address is: $address<br>";
		echo "Notify Option is: $notify_option<br>";
		echo "API key is: $apikey<br>";
	}
	
	$url = "http://localhost/xampp/picastatus/api/$apikey/$action/$user/$address/$notify_option";
	$username = "frank";
	$password = "frank";
	
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	$page = curl_exec($ch);
	curl_close($ch);
	
	if ($devmode)	{
		echo "<br><br><br> Result is:<br>";
		print_r($page);
	}
	
	$returned = json_decode($page, true);
	//print_r($returned["parameters"]);
	
	// Build logic to detect the action that was submitted then display a result message to the user.
	//////
	////// if $action == "add" then Display success or fail
	
	
	
}

function verifyUser()	{

	global $apikey;

	if ((!isset($_POST['submitbutton'])) && ($returned["status"][0] == 2)) {
		echo "<br>User successfully added, an email with the verification code has been sent to the address provided.";
		//echo "<br><br><br>Enter code to verify:";
		?>
		
		<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" >
		<br><br>Enter code to verify: <br><input type="text" size="20" maxlength="20" name="userkey"><br /><br>
		
		<input type="submit" name="submituserkey" value="Verify Code">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<br /> </form><br />
		
		<?php
	}	
	else {
	
		$enteredKey = $_POST["userkey"];
		$action = "ver";
		$user = $userinfo[1];
		$address = $enteredKey;

		api_connect($action, $user, $address, $notify_option, $apikey);
		
		return array ($action, $user, $address, $notify_option, $apikey);
	}

}

?> 

</body>
</html>
