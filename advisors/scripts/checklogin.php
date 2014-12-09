<?php 
session_start();

// needed for CSRF token
include_once('../includes/nocsrf.php');
$tokenForCookie = NoCSRF::generate( 'csrf_token_cookie' );

// needed for prepared DB statements
include_once('db/DBHandler.php');

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
	$db = new DbHandler();
	
	// clean up employee nr
	$empNr = Helpers::cleanData($_POST['employee_nr']);
	$pass = trim($_POST['pass']);
	
	$correctPassword = $db->checkAdvisorLogin($empNr, $pass);
		
	// true if password is correct
	if ( $correctPassword ) 
	{							
			$updateAdvisorRowSuccessful = $db->updateAdvisorLogin($empNr);
			
			// true if update successful
			if ($updateAdvisorRowSuccessful ) 
			{	
				unset($_SESSION["error"]);
				unset($_SESSION["info"]);
				
				$advisorId = $db->getAdvisorId($empNr);
				
				if ($advisorId == -1) 
				{
					// error getting advisor id 
					$_SESSION['error'] = "Error occurred while logging in.";	
				
					// redirect to login page
					header('Location: ../index.php');
					exit();
				}
				
				$expire = time()+3600;
                $domain = 'localhost';
                $secure = true;
                $httponly = true;
								
				// set cookie with random token
        		setcookie("AdvisorCookieToken", $tokenForCookie, $expire, "/", $domain, $secure, $httponly);
				
				// set sessions for sensitive data
				$_SESSION["EmployeeNr"] = $empNr;
				$_SESSION["EmployeeId"] = $advisorId;
				
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