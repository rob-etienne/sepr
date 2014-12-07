<?php 
include_once('../../DB/Prepared_DBHandler.php');
session_start();

// for CSRF token check
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
        if(NoCSRF::check( 'csrf_token_registration', $_POST, true, 60*3, false ));
	}
    catch ( Exception $e )
    {
        // CSRF attack detected
        $errCSRF = $e->getMessage();
    }
	
    if(empty($_POST['firstName']))
    	$errFname = 1;
    if(empty($_POST['lastName']))
    	$errLname = 1;
    if(empty($_POST['email']))
    	$errEmail = 1;
    if (function_exists('filter_var') && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
    	$errEmailVal = 1;
    if(empty($_POST['pass']))
    	$errPass = 1;
    if(empty($_POST['passConfirm']))
    	$errPassConfirm = 1;
    if($_POST['pass'] !== $_POST['passConfirm'])
    	$errPassMatch = 1;
		
	/*if (!preg_match('/^[A-Za-z]{1}[A-Za-z0-9]{5,50}$/', $_POST['firstName']))
		$errFnameRegEx = 1;
	if (!preg_match('/^[A-Za-z]{1}[A-Za-z0-9]{5,50}$/', $_POST['lastName']))
		$errLnameRegEx = 1;*/
}

//Check all fields
if (empty($_POST['firstName']) || !empty($errFname) || empty($_POST['lastName']) || !empty($errLname) || empty($_POST['email']) || !empty($errEmail) || empty($_POST['pass']) || !empty($errPass) || empty($_POST['passConfirm']) || !empty($errPassConfirm) || !empty($errPassMatch) || !empty($errEmailVal) || !empty($errCSRF) || $_SERVER['REQUEST_METHOD'] == 'GET')
{
   if ($_SERVER['REQUEST_METHOD'] == 'POST')
   {
	   if (!empty($errFname))
      	{
        	$_SESSION['error'] = "Please enter a first name.";
			header('Location: ../registration.php.php');
			exit();
      	}
    	elseif (!empty($errLname))
      	{
        	$_SESSION['error'] = "Please enter a last name.";
			header('Location: ../registration.php');
			exit();			
      	}
		elseif (!empty($errEmail))
      	{
        	$_SESSION['error'] = "Please enter an email address.";
			header('Location: ../registration.php');
			exit();			
      	}
		elseif (!empty($errEmailVal))
      	{
        	$_SESSION['error'] = "Please enter a valid email address.";
			header('Location: ../registration.php');
			exit();			
      	}
		elseif (!empty($errPass))
      	{
        	$_SESSION['error'] = "Please enter a password.";
			header('Location: ../registration.php');
			exit();			
      	}
		elseif (!empty($errPassConfirm))
      	{
        	$_SESSION['error'] = "Please confirm the password.";
			header('Location: ../registration.php');
			exit();			
      	}
		elseif (!empty($errPassMatch))
      	{
        	$_SESSION['error'] = "The two passwords don't match.";
			header('Location: ../registration.php');
			exit();
      	}
		elseif (!empty($errCSRF))
      	{
        	$_SESSION['error'] = $errCSRF;
			header('Location: ../registration.php');
			exit();
      	}
   }
}
else
{   
	//	TODO:
	//  1. Add client to database (client table, make link to advisor)
	//	2. Send email to user ? 
	//	3. Activation first before login ? (together with 2.)
	
	// clean up first name
	$fname = trim($_POST['firstName']);
	$fname = stripslashes( $fname );
	$fname = htmlspecialchars($fname);
	//$fname = mysqli_real_escape_string($conn, $fname );

	// clean up first name
	$lname = trim($_POST['lastName']);
	$lname = stripslashes( $lname );
	$lname = htmlspecialchars($lname);
	//$lname = mysqli_real_escape_string($conn, $lname );
	
	// clean up email
	$email = trim($_POST['email']);
	$email = stripslashes( $email );
	$email = htmlspecialchars($email);
	//$email = mysqli_real_escape_string($conn, $email );
	
	// get password input
	$pass = $_POST[ 'pass' ];
		
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
		// Sending message failed
		$_SESSION['error'] = "Error occurred while registering.";
	}
		
  	header('Location: ../registration.php');
}
?>
