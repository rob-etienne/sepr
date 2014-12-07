<?php
session_start();
// Show me all php errors  	
error_reporting(E_ALL);
ini_set("display_errors", 1);

// check if an advisor is logged in
if (isset($_COOKIE['Email']))
{
	// perform logout with db check
	// Create connection
	$conn = new mysqli("localhost", "sepr_user", "xsFDr4vuZQH2yFAP", "sepr_project");
	
	// Check connection
	if ($conn->connect_error) 
	{
		die("Connection failed: " . $conn->connect_error);
	}
	
	// get employee id from cookie
	$email = $_COOKIE["Email"];
	
	// assemble query
	$sql = "select active from clients where active = '0' and email = '$email'";
	
	// run query
	$result = mysqli_query($conn, $sql);
	
	// true if already logged out
	if (mysqli_num_rows($result) == 1) 
	{		
		// set message
		$_SESSION['info'] = "$email is already logged out.";
		
		/* free result set */
		mysqli_free_result($result);
		mysqli_close($conn);
		
		// redirect to index page
		header('Location:../index.php');
		
	} 
	else // log out employee
	{
		// assemble query
		$sql = "update clients set active = '0' where email = '$email'";
	
		// true if update successfull
		if (mysqli_query($conn, $sql)) 
		{		
			// clean up session
			session_destroy();
			session_start();
			
			// performed update
			$_SESSION['info'] = "$email is now logged out.";

			// delete cookie
			setcookie("Email", "", time() - 3600);	
			
			/* free result set */
			mysqli_free_result($result);
			mysqli_close($conn);

			// redirect to login page
			header('Location: ../index.php');
		}
		else
		{
			// couldn't perform update
			$_SESSION['error'] = "Error while logging out $email.";
			
			/* free result set */
			mysqli_free_result($result);
			mysqli_close($conn);		
			
			// redirect to login page
			header('Location: ../index.php');
		}	
	}
}
else
{
	// set message
	$_SESSION['error'] = 'No cookie found.';
	// redirect to login page
	header('Location:../login.php');
}
?>
