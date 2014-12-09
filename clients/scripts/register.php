<?php
session_start();

// needed for CSRF token
include_once('../includes/nocsrf.php');
// needed helpers for data clean up and validation
include_once('../includes/helpers.php');
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
        if(NoCSRF::check( 'csrf_token_registration', $_POST, true, 60*3, false ));
	}
    catch ( Exception $e )
    {
        // CSRF attack detected
        $errCSRF = $e->getMessage();
    }
	
	// check if first name is valid
	if(!Helpers::validateName($_POST['firstName']))
    	$errFname = 1;
	// check if last name is valid
	if(!Helpers::validateName($_POST['lastName']))
    	$errLname = 1;
	// check if email is valid
	if(!Helpers::validateEmail($_POST['email']))
    	$errEmail = 1;
	// check if password is valid
    if(!Helpers::validatePassword($_POST['pass']))
    	$errPass = 1;
	// check if confirmation password is valid
    else if(!Helpers::validatePassword($_POST['passConfirm']))
    	$errPassConfirm = 1;
	// check if both passwords match
    else if($_POST['pass'] !== $_POST['passConfirm'])
    	$errPassMatch = 1;
}

// Check all fields again & for errors found
if (empty($_POST['firstName']) || !empty($errFname) || empty($_POST['lastName']) || !empty($errLname) || empty($_POST['email']) || !empty($errEmail) || empty($_POST['pass']) || !empty($errPass) || empty($_POST['passConfirm']) || !empty($errPassConfirm) || !empty($errPassMatch) || !empty($errCSRF) || $_SERVER['REQUEST_METHOD'] == 'GET')
{
   if ($_SERVER['REQUEST_METHOD'] == 'POST')
   {
	   // redirecting always to registration.php with the respective error message within a session
	   
	   if (!empty($errFname))
      	{
        	$_SESSION['error'] = "Please enter a first name. Only characters are allowed. Should start with a capital letter. Min. length is 3 and max. is 50.";
			$log->registrationFailure("Invalid first name.");
			header('Location: ../registration.php');
			exit();	
      	}
    	elseif (!empty($errLname))
      	{
        	$_SESSION['error'] = "Please enter a last name. Only characters are allowed. Should start with a capital letter. Min. length is 3 and max. is 50.";
			$log->registrationFailure("Invalid last name.");
			header('Location: ../registration.php');
			exit();			
      	}
		elseif (!empty($errEmail))
      	{
        	$_SESSION['error'] = "Please enter an email address.";
			$log->registrationFailure("Invalid email.");
			header('Location: ../registration.php');
			exit();			
      	}
		elseif (!empty($errPass))
      	{
        	$_SESSION['error'] = "Please enter a compliant password. See password requirements.";
			$log->registrationFailure("Invalid password.");
			header('Location: ../registration.php');
			exit();			
      	}
		elseif (!empty($errPassConfirm))
      	{
        	$_SESSION['error'] = "Please re-enter your password.";
			$log->registrationFailure("Invalid confirmation password.");
			header('Location: ../registration.php');
			exit();			
      	}
		elseif (!empty($errPassMatch))
      	{
        	$_SESSION['error'] = "The two passwords don't match.";
			$log->registrationFailure("Mismatch for passwords.");
			header('Location: ../registration.php');
			exit();
      	}
		elseif (!empty($errCSRF))
      	{
        	$_SESSION['error'] = $errCSRF;
			$log->registrationFailure($errCSR);
			header('Location: ../registration.php');
			exit();
      	}
   }
}
else // We are good to go for the DB communication
{
	// clean up first name
	$fname = Helpers::cleanData($_POST['firstName']);

	// clean up first name
	$lname = Helpers::cleanData($_POST['lastName']);
	
	// clean up email
	$email = Helpers::cleanData($_POST['email']);
	
	// clean up password
	$pass = trim($_POST['pass']);
	
	$db = new DbHandler();
	
	$result = $db->createClient($fname, $lname, $email, $pass);	
  	
	// check result
	if($result)
	{
		// Account successfully created
		
		// clean up unused notifications
		unset($_SESSION['error']);
		unset($_SESSION['info']);
	
		// set success message
		$_SESSION['success'] = "Registration successful. An account was opened for you. Log in to get started.";
	}
	else
	{
		// Registration failed
		$_SESSION['error'] = "The email is already taken.";
		$log->registrationFailure($_SESSION['error']);
	}
	
	$log->registrationSuccess($email);
  	header('Location: ../registration.php');
	exit();
}
?>
