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
$clientId = stripslashes( $clientId );
$clientId = htmlspecialchars($clientId);	
$clientId = mysqli_real_escape_string($conn, $clientId );
	
// get all clients linked to our employee
$sql="select m.* from messages m where m.client_id = '$clientId' order by m.submitted_stamp desc";

// run query
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) > 0)
{
	echo "
	<table class='table table-striped'>
		<thead>
		  <tr>
			<th>Date</th>
			<th>Subject</th>
			<th>Message</th>
			<th>URL</th>
		  </tr>
		</thead>
		<tbody>";
		
	while($row = mysqli_fetch_array($result)) 
	{  
		echo "			  
		<tr>
		  <td>".$row['submitted_stamp']."</td>
		  <td>".$row['subject']."</td>
		  <td>".$row['message']."</td>
		  <td>".$row['attachment_url']."</td>
		</tr>";
	}
	
	echo " </tbody>
		</table>";	
}
else
{
	echo "No messages found.";
}
?>
