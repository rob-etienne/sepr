<?php 
include_once('../../DB/Prepared_DBHandler.php');
session_start();

// needed for CSRF token
include_once('../includes/nocsrf.php');

// Show me all php errors  	
error_reporting(E_ALL);
ini_set("display_errors", 1);
	
// Checks for input fields
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	// check CSRF token
	try
    {
        // Run CSRF check, on POST data, in exception mode, for 3 minutes, in one-time mode.
        if(NoCSRF::check( 'csrf_token_regClient', $_POST, true, 60*3, false ));
	}
    catch ( Exception $e )
    {
        // CSRF attack detected
        $errCSRF = $e->getMessage();
    }
	
	// check if field is empty
	if(empty($_POST['email']))
    	$errEmail = 1;
	// check if employee number is integer
	if(function_exists('filter_var') && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
    	$errEmailVal = 1;
	// check if field is empty
    if(empty($_POST['password']))
    	$errPass = 1;
}

//Check all fields
if (empty($_POST['email']) || !empty($errEmail) || !empty($errEmailVal) || empty($_POST['password']) || !empty($errPass) || !empty($errCSRF) || $_SERVER['REQUEST_METHOD'] == 'GET')
{
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
   	{
    	if (!empty($errEmail))
      	{
        	$_SESSION['error'] = "Please enter your email.";
			header('Location: ../index.php');
			exit();
      	}
    	elseif (!empty($errEmailVal))
      	{
        	$_SESSION['error'] = "The email you've entered is not valid.";
			header('Location: ../index.php');
			exit();
      	}
    	elseif (!empty($errPass))
      	{
        	$_SESSION['error'] = "Please enter a password.";
			header('Location: ../index.php');
			exit();
      	}
    	elseif (!empty($errCSRF))
      	{
        	$_SESSION['error'] = $errCSRF;
			header('Location: ../index.php');
			exit();
      	}
   	}
}
else // log in
{	
		
	// clean up employee nr
	$email = trim($_POST['email']);
	$email = stripslashes( $email );
	$email = htmlspecialchars($email);
	//$email = mysqli_real_escape_string($conn, $email );
	
	$db = new DbHandler();
	$correctPassword = $db->checkClientLogin($email, $_POST['password']);
		
	// true if password is correct
	if ( $correctPassword ) 
	{

		// if($db->clientIsActive($email) == false) // true if client not logged in (field active = 1)
		// {
							
			$updateClientRowSuccessful = $db->updateClientLogin($email);
			
			// true if update successful
			if ($updateClientRowSuccessful ) 
			{	
				unset($_SESSION["error"]);
				unset($_SESSION["info"]);
				
				$clientId = $db->getClientId($email);
				
				if ($clientId == -1) {
					// error getting client id 
					$_SESSION['error'] = "Error occurred while logging in.";	
				
					// redirect to login page
					header('Location: ../index.php');
					exit();
				}
				
				//var_dump($_COOKIE);
				//exit();
				
				// set cookie with email
        		setcookie("Email", $email, time()+3600, "/");
				setcookie("ClientId", $clientId, time()+3600, "/");
				
				// redirect to account page
				header('Location: ../account.php');	
				exit();
			} 
			else 
			{
				// couldn't perform update
				$_SESSION['error'] = "Error occured while logging in.";	
				
				// redirect to login page
				header('Location: ../index.php');
				exit();
			}	
		// }
		// else
		// {
		
			// // employee already logged in
			// $_SESSION['error'] = "You are already logged in. If it is not you, please contact your admin asap.";	
			
			// // redirect to login page
			// header('Location: ../index.php');	
			
			// // redirect to account page
			// //header('Location: ../account.php');	
			// //exit();
		// }
	
	} // employee number (or password) incorrect / unknown
	else 
	{
		// Login failed
    	$_SESSION['error'] = "Incorrect email or password." ;
		// redirect to login page
    	header('Location: ../index.php');	
		exit();
	}
}
 ?>