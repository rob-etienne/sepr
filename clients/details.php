<?php 
session_start();

// Show me all php errors  	
error_reporting(E_ALL);
ini_set("display_errors", 1);
 
// needed helpers for data clean up and validation
include_once('includes/helpers.php');
include_once('scripts/db/DBHandler.php');

include('includes/htmlhead.php');

// Checks for input fields
if ($_SERVER['REQUEST_METHOD'] == 'GET')
{	
	// check if account (to) is valid
	if(!Helpers::validateInteger($_GET['accountnr']))
    	$errAccNr = 1;
}

if(empty($_POST['accountnr']) || !empty($errAccNr) || $_SERVER['REQUEST_METHOD'] == 'POST')
{
	if ($_SERVER['REQUEST_METHOD'] == 'GET')
   	{
    	if (!empty($errAccNr))
      	{
        	$_SESSION['error'] = "Please enter a compliant account number. (Integer, 4 digits)";
			header('Location: ../account.php');
			exit();
      	}
   	}	
}

?>

<body>

	<div class="container">

   	<?php include('includes/header.php'); ?>

	<div class="row">
    
    	<?php
			// display error messages
			if (isset($_SESSION['error'])) 
	    	{
				echo '
			<div class="panel panel-danger">
			  <div class="panel-heading">
				<h3 class="panel-title">Error</h3>
			  </div>
			  <div class="panel-body">
			   ' . $_SESSION['error'] . '
			  </div>
			</div>';
			
				unset($_SESSION['error']);
			
        	} // or info messages
			elseif (isset($_SESSION['info'])) 
	    	{
				echo '
			<div class="panel panel-info">
			  <div class="panel-heading">
				<h3 class="panel-title">Info</h3>
			  </div>
			  <div class="panel-body">
			   ' . $_SESSION['info'] . '
			  </div>
			</div>';
				
				unset($_SESSION['info']);
        	
			} // or success messages
			elseif (isset($_SESSION['success'])) 
	    	{
				echo '
			<div class="panel panel-success">
			  <div class="panel-heading">
				<h3 class="panel-title">Success</h3>
			  </div>
			  <div class="panel-body">
			   ' . $_SESSION['success'] . '
			  </div>
			</div>';
			
				unset($_SESSION['success']);			
        	
			}
			else
			{
				// do nothing
			}
		?>
		
		<ol class="breadcrumb">
  			<li><a href="account.php">Home</a></li>
  			<li><a href="transaction.php">New Transaction</a></li>
  			<li><a href="messages.php">Messages</a></li>
		</ol>
	
    	<div class="col-md-12">
    		<div class="page-header">
        		<h3>Transactions for account <?php echo $_GET['accountnr'] ?></h3>
      		</div>
      		
      	<?php 
		
		// Create connection
		$db = new DbHandler();
			
		// clean up employee nr
		$accountNr = Helpers::cleanData($_GET["accountnr"]);
		$clientId = $_SESSION['ClientId'];
		
		// run query
		$result = $db->getAllTransactions($clientId ,$accountNr);
		
		if(count($result) > 0)
		{
			echo "
			<table class='table table-striped'>
				<thead>
				  <tr>
					<th>Date</th>
					<th>Account Number ( from -> to)</th>
					<th>Purpose</th>
					<th class='text-right'>Amount</th>
				  </tr>
				</thead>
				<tbody>";
				
			for($x = 0; $x < count($result); $x++)
            {
                $row = $result[$x];
				
				echo "			  
				<tr>
				  <td>".$row['date_stamp']."</td>
				  <td>".$row['account_id_from']. " ---> " .$row['account_id_to']."</td>
				  <td>".$row['purpose']."</td>
				  <td class='text-right'>".$row['amount']."</td>
				</tr>";
			}
			
			echo " </tbody>
				</table>";	
		}
		else
		{
			echo "No transactions found.";
		}
		
		?>
      		
		</div> <!-- /col-md-12 -->
    	
    </div> <!-- /row -->   
	
    <hr>
	      
    <?php include('includes/footer.php'); ?>
    
    </div> <!-- /container -->   
        
    <?php include('includes/scripts.php'); ?>
    
</body>

</html>

