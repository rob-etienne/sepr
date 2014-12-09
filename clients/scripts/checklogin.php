<?php
session_start();

// needed for CSRF token
include_once('../includes/nocsrf.php');
$tokenForCookie = NoCSRF::generate( 'csrf_token_cookie' );

// needed helpers for data clean up and validation
include_once('../includes/helpers.php');

// needed for prepared DB statements
include_once('db/DBHandler.php');

// needed for logging
include_once('../handler/log.php');
// logger
$log = new Log();

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
	
	// check if email is valid
	if(!Helpers::validateEmail($_POST['email']))
    	$errEmail = 1;
	// check if password is valid
    if(!Helpers::validatePassword($_POST['password']))
    	$errPass = 1;
}

// Check all fields again & for errors found
if (empty($_POST['email']) || !empty($errEmail) || empty($_POST['password']) || !empty($errPass) || !empty($errCSRF) || $_SERVER['REQUEST_METHOD'] == 'GET')
{
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
   	{
    	if (!empty($errEmail))
      	{
        	$_SESSION['error'] = "Please enter a valid email. Format looks like name@domain.com.";
			$log->loginFailure("Invalid client email entered.");
			header('Location: ../index.php');
			exit();
      	}
    	elseif (!empty($errPass))
      	{
        	$_SESSION['error'] = "Please enter a compliant password.";
			$log->loginFailure("Invalid client password entered.");
			header('Location: ../index.php');
			exit();
      	}
    	elseif (!empty($errCSRF))
      	{
        	$_SESSION['error'] = $errCSRF;
			$log->loginFailure($errCSRF);
			header('Location: ../index.php');
			exit();
      	}
   	}
}
else // log in
{	
	$db = new DbHandler();
		
	// clean up email
	$email = Helpers::cleanData($_POST['email']);
	$pass = trim($_POST['password']);
	
	$correctPassword = $db->checkClientLogin($email, $pass);
		
	// true if password is correct
	if ( $correctPassword ) 
	{							
			$updateClientRowSuccessful = $db->updateClientLogin($email);
			
			// true if update successful
			if ($updateClientRowSuccessful ) 
			{	
				unset($_SESSION["error"]);
				unset($_SESSION["info"]);
				
				$clientId = $db->getClientId($email);
				
				if ($clientId == -1) 
				{
					// error getting client id 
					$_SESSION['error'] = "Error occurred while logging in. Couldn't get client id by email.";	
					// log error
					$log->loginFailure("Database error. Couldn't get client id by email.");
					// redirect to login page
					header('Location: ../index.php');
					exit();
				}
				
				$expire = time()+3600;
                $domain = 'localhost';
                $secure = true;
                $httponly = true;
								
				// set cookie with random token
        		setcookie("ClientCookieToken", $tokenForCookie, $expire, "/", $domain, $secure, $httponly);
				
				// set sessions for sensitive data
				$_SESSION["ClientEmail"] = $email;
				$_SESSION["ClientId"] = $clientId;
				
				// log success
				$log->loginSuccess($email);
				// redirect to account page
				header('Location: ../account.php');	
				exit();
			} 
			else 
			{
				// couldn't perform update
				$_SESSION['error'] = "Error occured while logging in. Database indicates that you are already logged in.";	
				// log error
				$log->loginFailure("Database error. Already logged in.");
				// redirect to login page
				header('Location: ../index.php');
				exit();
			}
	} // client email (or password) incorrect / unknown
	else 
	{
		// Login failed
    	$_SESSION['error'] = "Incorrect email or password." ;
		// log error
		$log->loginFailure($_SESSION['error']);
		// redirect to login page
    	header('Location: ../index.php');	
		exit();
	}
}
 ?>