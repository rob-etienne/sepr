<?php
// Show me all php errors  	
error_reporting(E_ALL);
ini_set("display_errors", 1);
include_once('db/DBHandler.php');
include_once('includes/helpers.php');

$db = new DbHandler();

// get client id
$clientId = Helpers::cleanData($_SESSION["ClientId"]);
	
$result = $db->getAllClientAccounts($clientId);

if(count($result) > 0)
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
		
	//if($row = $result[0])
    for ($x = 0; $x < count($result); $x++)
	{  
        $row = $result[$x];
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
