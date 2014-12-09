<?php 
session_start();

// needed for CSRF token
include_once('../includes/nocsrf.php');
// needed helpers for data clean up and validation
include_once('../includes/helpers.php');

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
			header('Location: ../registration.php');
      	}
    	elseif (!empty($errLname))
      	{
        	$_SESSION['error'] = "Please enter a last name. Only characters are allowed. Should start with a capital letter. Min. length is 3 and max. is 50.";
			header('Location: ../registration.php');
      	}
		elseif (!empty($errEmail))
      	{
        	$_SESSION['error'] = "Please enter a valid email address. In the format like name@domain.com.";
			header('Location: ../registration.php');
      	}
		elseif (!empty($errPass))
      	{
        	$_SESSION['error'] = "Please enter a compliant password. See password requirements.";
			header('Location: ../registration.php');
      	}
		elseif (!empty($errPassConfirm))
      	{
        	$_SESSION['error'] = "Please re-enter your password.";
			header('Location: ../registration.php');
      	}
		elseif (!empty($errPassMatch))
      	{
        	$_SESSION['error'] = "The two passwords don't match.";
			header('Location: ../registration.php');
      	}
		elseif (!empty($errCSRF))
      	{
        	$_SESSION['error'] = $errCSRF;
			header('Location: ../registration.php');
      	}
   }
}
else // We are good to go for the DB communication
{
	// Create connection
	$conn = new mysqli("localhost", "root", "root", "sepr_project");
	
	// Check connection
	if ($conn->connect_error) 
	{
    	die("Connection failed: " . $conn->connect_error);
	}
	
	// clean up first name
	$fname = Helpers::cleanData($_POST['firstName']);
	$fname = mysqli_real_escape_string($conn, $fname );

	// clean up first name
	$lname = Helpers::cleanData($_POST['lastName']);
	$lname = mysqli_real_escape_string($conn, $lname );
	
	// clean up email
	$email = Helpers::cleanData($_POST['email']);
	$email = mysqli_real_escape_string($conn, $email );
	
	// clean up password
	$pass = trim($_POST['pass']);
	
	// query without file upload
	$sql = "insert into clients(first_name, last_name, email, password_hash) values ('$fname', '$lname', '$email', '$pass')";
		
	// run the query
	$result = mysqli_query($conn, $sql);
   
  	// check result
	if($result)
	{
		// Message sucessfully added
		
		// clean up unused notifications
		unset($_SESSION['error']);
		unset($_SESSION['info']);
	
		// set success message
		$_SESSION['success'] = "Registration successfull. An account was opened for you. Log in to get started.";
	}
	else
	{
		// Sending message failed
		$_SESSION['error'] = "The email is already taken.";
	}
	
	// close db connection	
	mysqli_close($conn);
		
  	header('Location: ../registration.php');
}
?>
