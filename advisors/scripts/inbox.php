<?php
// Show me all php errors  	
error_reporting(E_ALL);
ini_set("display_errors", 1);
	
// Create connection
$conn = new mysqli("localhost", "root", "root", "sepr_project");
		
// Check connection
if ($conn->connect_error) 
{
	die("Connection failed: " . $conn->connect_error);
}

// clean up employee nr
$empNr = Helpers::cleanData($_COOKIE["EmployeeNr"]);
	
// get all clients linked to our employee
$sql="select m.* from messages m order by m.submitted_stamp desc";

// run query
$result = mysqli_query($conn, $sql);

if(isset($result))
{
	echo "
	<table class='table table-striped'>
		<thead>
		  <tr>
			<th>Date</th>
			<th>Subject</th>
			<th>Message</th>
			<th>URL</th>
			<th>ClientId</th>
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
		  <td>".$row['client_id']."</td>
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
