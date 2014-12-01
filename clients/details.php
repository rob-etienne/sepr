<?php 
session_start();

// Show me all php errors  	
error_reporting(E_ALL);
ini_set("display_errors", 1);
 
include('includes/htmlhead.php');

// Checks for input fields
if ($_SERVER['REQUEST_METHOD'] == 'GET')
{	
	// check if field is empty
	if(empty($_GET['accountnr']))
    	$errAccNr = 1;
	// check if employee number is integer
	if(function_exists('filter_var') && !filter_var($_GET['accountnr'], FILTER_VALIDATE_INT))
    	$errAccNrVal = 1;
}

if(empty($_POST['accountnr']) || !empty($errAccNr) || !empty($errAccNrVal) || $_SERVER['REQUEST_METHOD'] == 'POST')
{
	if ($_SERVER['REQUEST_METHOD'] == 'GET')
   	{
    	if (!empty($errAccNr))
      	{
        	$_SESSION['error'] = "Please provide an account number.";
			header('Location: ../account.php');
      	}
    	elseif (!empty($errAccNrVal))
      	{
        	$_SESSION['error'] = "The account number is not valid.";
			header('Location: ../account.php');
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
		$conn = new mysqli("localhost", "root", "root", "sepr_project");
				
		// Check connection
		if ($conn->connect_error) 
		{
			die("Connection failed: " . $conn->connect_error);
		}
		
		// clean up employee nr
		$accountNr = $_GET["accountnr"];
		$accountNr = stripslashes( $accountNr );
		$accountNr = htmlspecialchars($accountNr);	
		$accountNr = mysqli_real_escape_string($conn, $accountNr );
			
		// TODO: FIX QUERY
		// get all clients linked to our employee
		$sql="
		select atm.*, t.*, a.* 
			from account_transaction_matches atm, transactions t, accounts a 
				where t.id = atm.transaction_id 
					and a.id = '$accountNr'
						and atm.account_id_to = '$accountNr' or atm.account_id_from = '$accountNr'
							order by t.date_stamp desc";
		
		// run query
		$result = mysqli_query($conn, $sql);
		
		if(mysqli_num_rows($result) > 0)
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
				
			while($row = mysqli_fetch_array($result)) 
			{  
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
			echo "No messages found.";
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

