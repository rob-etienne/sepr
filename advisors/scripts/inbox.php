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
$empNr = Helpers::cleanData($_SESSION["EmployeeNr"]);
	
// run query
$result = $db->getAllMessages($empNr);

if(count($result) > 0)
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
		
	for ($x = 0; $x < count($result); $x++)
	{  
        $row = $result[$x];
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
