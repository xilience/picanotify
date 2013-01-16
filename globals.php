<?php  // GLOBAL DEFINITIONS

global $host, $user, $pass, $db, $table, $url;

error_reporting(E_ALL ^ E_STRICT);
ini_set('display_errors',true);

//##// Dev Mode Toggle
$devmode = 1;

$host = "localhost";
$user = "frank";
$pass = "frank";
$db = "picastatus";
$table = "users";

$apikey = "778dd5c98f13edd691d2c14305f8391a";

$url = 'https://picac2cs9.pica.army.mil/Picastatus/Status.aspx';

// Messages
$welcome = "Welcome to the Picatinny Status Notification System.";
$goodbye = "You have been removed from the Picatinny Status Notification System.";

?>

<?php //  STATUS CODES


function errorCode($errorNum)  {

	switch ($errorNum)	{

		case 0:	return "Internal Fail Code";						break;
		case 1:	return "Internal Success Code";						break;
		case 2:	return "Add User Success";							break;
		case 3:	return "User Information Match Found";				break;
		case 4:	return "Update User Success";						break;
		case 5:	return "Remove User Success";						break;
		case 6:	return "";	break;
		case 7:	return "Verify User Success";	break;
		case 8:	return "";	break;
		case 9:	return "BAD API KEY supplied";	break;
		case 10:return "";	break;
		case 11:return "";	break;
		case 12:return "Add User Fail";								break;
		case 13:return "User Information Match Not Found";			break;
		case 14:return "Update User Fail";							break;
		case 15:return "Remove User Fail";							break;
		case 16:return "";	break;
		case 17:return "Verify User Fail";	break;
		case 18:return "DB returned no results";					break;
		case 19:return "URI Too Long";								break;
		case 20:return "Unable to Connect to mySQL";				break;
		case 21:return "Unable to Connect to DB";					break;
		case 22:return "mySQL Query Error";							break;
		case 23:return "Incorrect Number of Parameters Passed";		break;
		case 24:return "Empty Parameters Not Allowed";				break;
		case 25:return "Print Table Success";						break;
		}
}

function writeLog($msg)	{

	$logline = "\r\nPICASTATUS:".date("Y-m-d g:i:s a")." ".$msg;
	$file = "picastatus.log";
	$fh = fopen($file,'a') or die("Failed to open log file.");
	fwrite($fh, $logline);
	fclose($fh);
	
}

/**
Validate an email address, thanks to http://www.linuxjournal.com/
Provide email address (raw input)
Returns true if the email address has the email 
address format and the domain exists.
*/
function validEmail($email)		{
   $isValid = true;
   $atIndex = strrpos($email, "@");
   if (is_bool($atIndex) && !$atIndex)   {
      $isValid = false;
   }
   else   {
      $domain = substr($email, $atIndex+1);
      $local = substr($email, 0, $atIndex);
      $localLen = strlen($local);
      $domainLen = strlen($domain);
      if ($localLen < 1 || $localLen > 64)
      {
         // local part length exceeded
         $isValid = false;
      }
      else if ($domainLen < 1 || $domainLen > 255)
      {
         // domain part length exceeded
         $isValid = false;
      }
      else if ($local[0] == '.' || $local[$localLen-1] == '.')
      {
         // local part starts or ends with '.'
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $local))
      {
         // local part has two consecutive dots
         $isValid = false;
      }
      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
      {
         // character not valid in domain part
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $domain))
      {
         // domain part has two consecutive dots
         $isValid = false;
      }
      else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local))) {
         // character not valid in local part unless 
         // local part is quoted
         if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local))) {
            $isValid = false;
         }
      }
      if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))) {
         // domain not found in DNS
         $isValid = false;
      }
   }
   return $isValid;
}

?>
