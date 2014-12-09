<?php
session_start();
// Show me all php errors  	
error_reporting(E_ALL);
ini_set("display_errors", 1);
// needed for helpers (like clean up data)
include_once('../includes/helpers.php');
// needed for prepared DB statements
include_once('db/DBHandler.php');

// check if an advisor is logged in
if (isset($_SESSION['EmployeeNr']))
{
	$db = new DbHandler();
	
	// get client id from session
	$empNr = $_SESSION["EmployeeNr"];
	
	$updateAdvisorRowSuccessful = $db->updateAdvisorLogout($empNr);
	
	// true if update successful
	if ($updateAdvisorRowSuccessful ) 
	{	
		// clean up session
		session_destroy();
		session_start();
			
		// performed update
		$_SESSION['info'] = "$empNr is now logged out.";

		// delete cookie
		setcookie("AdvisorCookieToken", "", time() - 3600);
			
		// redirect to login page
		header('Location: ../index.php');
		
	} 
	else // log out advisor failed
	{
		// couldn't perform update
		$_SESSION['error'] = "Error while logging out $email.";
			
		// redirect to login page
		header('Location: ../index.php');	
	}
}
else
{
	// set message
	$_SESSION['error'] = 'Seems like you are already logged out.';
	// redirect to login page
	header('Location:../login.php');
}
?>
