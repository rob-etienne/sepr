<?php
session_start();
// Show me all php errors  	
error_reporting(E_ALL);
ini_set("display_errors", 1);

// check if an advisor is logged in
if (isset($_COOKIE['EmployeeNr']))
{
	// perform logout with db check
	// Create connection
	$conn = new mysqli("localhost", "root", "root", "sepr_project");
	
	// Check connection
	if ($conn->connect_error) 
	{
		die("Connection failed: " . $conn->connect_error);
	}
	
	// get employee id from cookie
	$empNr = $_COOKIE["EmployeeNr"];
	
	// assemble query
	$sql = "select active from advisors where active = '0' and employee_nr = '$empNr'";
	
	// run query
	$result = mysqli_query($conn, $sql);
	
	// true if already logged out
	if (mysqli_num_rows($result) == 1) 
	{		
		// set message
		$_SESSION['info'] = "$empNr is already logged out.";
		
		/* free result set */
		mysqli_free_result($result);
		mysqli_close($conn);
		
		// redirect to index page
		header('Location:../index.php');
		
	} 
	else // log out employee
	{
		// assemble query
		$sql = "update advisors set active = '0' where employee_nr = '$empNr'";
	
		// true if update successfull
		if (mysqli_query($conn, $sql)) 
		{		
			// clean up session
			session_destroy();
			session_start();
			
			// performed update
			$_SESSION['info'] = "$empNr is now logged out.";

			// delete cookie
			setcookie("EmployeeNr", "", time() - 3600);	
			
			/* free result set */
			mysqli_free_result($result);
			mysqli_close($conn);

			// redirect to login page
			header('Location: ../index.php');
		}
		else
		{
			// couldn't perform update
			$_SESSION['error'] = "Error while logging out $empNr.";
			
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
