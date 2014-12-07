<?php
// Show me all php errors  	
error_reporting(E_ALL);
ini_set("display_errors", 1);
	
// Create connection
$conn = new mysqli("localhost", "sepr_user", "xsFDr4vuZQH2yFAP", "sepr_project");
		
// Check connection
if ($conn->connect_error) 
{
	die("Connection failed: " . $conn->connect_error);
}

// clean up employee nr
$clientId = $_COOKIE["ClientId"];

if ($clientId == null) {
	// couldn't perform update
	$_SESSION['error'] = "You need to enable cookies to view this site.";	
				
	// redirect to login page
	header('Location: index.php');
	exit();
}

$clientId = stripslashes( $clientId );
$clientId = htmlspecialchars($clientId);	
$clientId = mysqli_real_escape_string($conn, $clientId );
	
// get all clients linked to our employee
$sql="select a.* from accounts a where a.client_id = '$clientId' order by a.id";

// run query
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) > 0)
{
	echo "
	<table class='table table-striped'>
	
		<thead>
		  <tr>
			<th>Account</th>
			<th class='text-right'>Balance</th>
			<th class='text-right'>Category / Name</th>
		  </tr>
		</thead>
		<tbody>";
		
	while($row = mysqli_fetch_array($result)) 
	{  
		echo "			  
		<tr>
		  <td><a href='details.php?accountnr=".$row['id']."'>".$row['id']."</a></td>
		  <td class='text-right'>".$row['balance']."</td>
		  <td class='text-right'>".$row['name']."</td>
		</tr>";
	}
	
	echo " </tbody>
		</table>";	
}
else
{
	echo "No accounts found.";
}
?>
