<?php
session_start();
// Show me all php errors  	
error_reporting(E_ALL);
ini_set("display_errors", 1);
// needed for helpers (like clean up data)
include_once('../includes/helpers.php');
// needed for prepared DB statements
include_once('db/DBHandler.php');
// needed for logging
include_once('../handler/log.php');
// logger
$log = new Log();

// check if an client is logged in
if (isset($_SESSION['ClientEmail']))
{
	$db = new DbHandler();
	
	// get client id from session
	$email = $_SESSION["ClientEmail"];
	
	$updateClientRowSuccessful = $db->updateClientLogout($email);
			
	// true if update successful
	if ($updateClientRowSuccessful ) 
	{	
		// clean up session
		session_destroy();
		session_start();
			
		// performed update
		$_SESSION['info'] = "$email is now logged out.";

		// log success
		$log->logoutSuccess($email);
		
		// delete cookie
		setcookie("ClientCookieToken", "", time() - 3600);
			
		// redirect to login page
		header('Location: ../index.php');
		exit();
		
	} 
	else // log out client failed
	{
		// couldn't perform update
		$_SESSION['error'] = "Error while logging out $email.";
		// log error
		$log->logoutFailure("Error while logging out $email.");
		// redirect to login page
		header('Location: ../index.php');
		exit();	
	}
}
else
{
	// set message
	$_SESSION['error'] = 'Seems like you are already logged out.';
	// log error
	$log->logoutFailure("Error while logging out $email. Already logged out.");
	// redirect to login page
	header('Location:../login.php');
	exit();
}
?>
