<?php
session_start();
include ('includes/htmlhead.php');

// needed helpers for data clean up and validation
include_once('includes/helpers.php');

// Show me all php errors
error_reporting(E_ALL);
ini_set("display_errors", 1);

// Prevention CSRF attacks
include_once ('includes/nocsrf.php');

// token for transaction form
$tokenTransaction = NoCSRF::generate('csrf_token_transaction');
?>

<body>

	<div class="container">

   	<?php
include ('includes/header.php');
 ?>

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
  			<li class="active">New Transaction</li>
  			<li><a href="messages.php">Messages</a></li>
		</ol>
	
    	<div class="col-md-12">
    		<div class="page-header">
        		<h3>New Transaction</h3>
      		</div>
      		
        	<div class="col-sm-offset-3 col-sm-8">
      		<h4>Beneficiary of Transaction</h4>
      		<br/>
    	</div>
    <form class="form-horizontal" method="POST" action="scripts/maketransaction.php" role="form">
   	<div class="form-group">
            <label for="InputClient" class="col-sm-3 control-label">Select your account</label>
            <div class="col-sm-8">
              <?php

// Create connection

$conn = new mysqli("localhost", "sepr_user", "xsFDr4vuZQH2yFAP", "sepr_project");

// Check connection

if ($conn->connect_error)
{
	die("Connection failed: " . $conn->connect_error);
}

// clean up employee nr

$clientId = $_COOKIE["ClientId"];
$clientId = stripslashes($clientId);
$clientId = htmlspecialchars($clientId);
$clientId = mysqli_real_escape_string($conn, $clientId);

// get all clients linked to our employee

$sql = "select a.* from accounts a, clients c where c.id = '$clientId' and a.client_id = c.id";
$result = mysqli_query($conn, $sql);
echo "<select name='account' class='form-control' required>";
echo "<option selected disabled value=''>choose</option>";

if (mysqli_num_rows($result) > 0)
{
	while ($row = mysqli_fetch_array($result))
	{
		echo "<option value=" . $row['id'] . ">Account ID: " . $row['id'] . " | Name: " . $row['name'] . " | Current balance: " . $row['balance'] . "</option>";
	}

	echo "</select>";
?>
            
            </div>
          </div>
 <div class="form-group">
    <label for="inputAccountNr" class="col-sm-3 control-label">Account Number</label>
    <div class="col-sm-8">
      <input type="number" class="form-control" id="inputAccountNr" name="accountnr"  value="" placeholder="Enter Account Nr" required>
    </div>
  </div>
 <div class="form-group">
    <label for="inputAmount" class="col-sm-3 control-label">Amount</label>
    <div class="col-sm-8">
      <input type="text" class="form-control" id="inputAmount" name="amount"  value="" placeholder="Enter Amount" required>
    </div>
  </div>
  <div class="form-group">
    <label for="inputPurpose" class="col-sm-3 control-label">Purpose</label>
    <div class="col-sm-8">
    	<textarea class="form-control" rows="3" maxlength="100" id="inputPurpose" name="purpose"  value="'" placeholder="Enter Transaction Purpose" required></textarea>
    	<p class="text-right">max. 100 characters.</p>
	</div>
  </div>
  <div class="form-group">
	<div class="col-sm-8">
  		<input type="hidden" class="form-control" name="csrf_token_transaction" id="csrf_token_transaction" value="<?php
	echo $tokenTransaction ?>">
    </div>
  </div>
 <div class="form-group">
    <div class="col-sm-offset-3 col-sm-8">
      <button type="submit" class="btn btn-default btn-lg btn-block">Next</button>
    </div>
  </div>
</form>

<?php
}
else
{
	echo "<option selected disabled value=''>You don't have any accounts yet.</option>";
	echo "</select>";
}

mysqli_close($conn);
?>
                 		
		</div> <!-- /col-md-12 -->
    	
    </div> <!-- /row -->   
	
    <hr>
	      
    <?php
include ('includes/footer.php');
 ?>
    
    </div> <!-- /container -->   
        
    <?php
include ('includes/scripts.php');
 ?>
    
</body>

</html>