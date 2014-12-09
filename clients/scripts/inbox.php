<?php
// Show me all php errors  	
error_reporting(E_ALL);
ini_set("display_errors", 1);

// needed helpers for data clean up and validation
include_once('includes/helpers.php');
// needed for prepared DB statements
include_once('scripts/db/DBHandler.php');

// Create connection
$db = new DbHandler();

// clean up client id
$clientId = Helpers::cleanData($_SESSION["ClientId"]);

// run query
$result = $db->getAllMessages($clientId);

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
