<?php 
session_start();

// needed for CSRF token
include_once('../includes/nocsrf.php');

// needed for helpers (like clean up data)
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
        if(NoCSRF::check( 'csrf_token_login', $_POST, true, 60*3, false ));
	}
    catch ( Exception $e )
    {
        // CSRF attack detected
        $errCSRF = $e->getMessage();
    }
	
	// check if employee nr is valid
	if(!Helpers::validateInteger($_POST['employee_nr']))
    	$errEmpNr = 1;
	// check if password is valid
    if(!Helpers::validatePassword($_POST['pass']))
    	$errPass = 1;
}

//Check all fields
if (empty($_POST['employee_nr']) || !empty($errEmpNr) || empty($_POST['pass']) || !empty($errPass) || !empty($errCSRF) || $_SERVER['REQUEST_METHOD'] == 'GET')
{
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
   	{
    	if (!empty($errEmpNr))
      	{
        	$_SESSION['error'] = "Please enter your employee number. (Integer, 4 digits)";
			header('Location: ../login.php');
		}
    	elseif (!empty($errPass))
      	{
        	$_SESSION['error'] = "Please enter a compliant password.";
			header('Location: ../login.php');
      	}
    	elseif (!empty($errCSRF))
      	{
        	$_SESSION['error'] = $errCSRF;
			header('Location: ../login.php');
      	}
   	}
}
else // log in
{	
	// perform login with db check
	// Create connection
	$conn = new mysqli("localhost", "root", "root", "sepr_project");
	
	// Check connection
	if ($conn->connect_error) 
	{
    	die("Connection failed: " . $conn->connect_error);
	}
	
	// clean up employee nr
	$empNr = Helpers::cleanData($_POST['employee_nr']);
	$pass = Helpers::cleanData($_POST['pass']);
	$pass = md5($pass);
	
	// assemle query 
	$sql = "select * from advisors where employee_nr = '$empNr' and password_hash = '$pass'";
	
	// run the query
	$result = mysqli_query($conn, $sql);

	// true if employee exists
	if ( $result && mysqli_num_rows( $result ) == 1 ) 
	{
		// assign vaues from db to row
		$row = mysqli_fetch_assoc($result);
		
		if(!$row['active'] == 1) // true if employee not logged in (field active = 1)
		{
			// get values
		    $empNr = $row['employee_nr'];
        	$pass = $row['password_hash'];
			
			/* free result set */
    		mysqli_free_result($result);
		
			// update active field + update last login stamp
			// get timestamp
			$timestamp = date('Y-m-d H:i:s');
			// assemble query
			$sql = "update advisors set active = '1', last_sign_in_stamp = '$timestamp' where employee_nr = '$empNr'";
			
			$result = mysqli_query($conn, $sql);
			
			// true if update successfull
			if ($result ) 
			{	
				unset($_SESSION["error"]);
				unset($_SESSION["info"]);
				
				// set cookie
        		setcookie("EmployeeNr", $empNr, time()+3600, "/");
				
				// redirect to account page
				header('Location: ../account.php');	
			} 
			else 
			{
				// couldn't perform update
				$_SESSION['error'] = "Error occured while logging in.";	
				
				// redirect to login page
				header('Location: ../login.php');
			}	
		}
		else
		{
			// employee already logged in
			$_SESSION['error'] = "You are already logged in. If it is not you, please contact your admin asap.";	
			
			// redirect to login page
			header('Location: ../login.php');	
		}
	
		// close db connection
		mysqli_close($conn);
	} // employee number (or password) incorrect / unknown
	else 
	{
		// Login failed
    	$_SESSION['error'] = "Incorrect employee number or password.";
		// redirect to login page
    	header('Location: ../login.php');	
	}
}
 ?>