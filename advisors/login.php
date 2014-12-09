<?php
session_start();
include('includes/htmlhead.php');

// Show me all php errors  	
error_reporting(E_ALL);
ini_set("display_errors", 1);

// Prevention CSRF attacks
include_once('includes/nocsrf.php');
$tokenLogin = NoCSRF::generate( 'csrf_token_login' );
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
    <div class="col-md-12">
      <?php 
   
      echo '
  <div class="col-sm-offset-3 col-sm-8">
      		<h4>Login</h4>
      		<br/>
  </div>
<form class="form-horizontal" method="POST" action="scripts/checklogin.php" role="form">
 <div class="form-group">
    <label for="InputEmployeeNr" class="col-sm-3 control-label">Employee Number</label>
    <div class="col-sm-8">
      <input type="number" class="form-control" id="InputEmployeeNr" name="employee_nr" value="" placeholder="Enter Employee Number"  required>
    </div>
  </div>
  <div class="form-group">
    <label for="InputPassword" class="col-sm-3 control-label">Password</label>
    <div class="col-sm-8">
    	<input type="password" class="form-control" id="InputPassword" name="pass" value="" placeholder="Enter Password" required >
	</div>
  </div>
  <div class="form-group">
    <div class="col-sm-8">
    	<input type="hidden" class="form-control" name="csrf_token_login" id="csrf_token_login" value="' . $tokenLogin . '">
	</div>
  </div>
 <div class="form-group">
    <div class="col-sm-offset-3 col-sm-8">
      <button type="submit" class="btn btn-default btn-lg btn-block">Login</button>
    </div>
  </div>
</form>';

 ?>
    </div>
    <!-- /col-md-12 --> 
    
  </div>
  <!-- /row -->
  
  <hr>
  <?php include('includes/footer.php'); ?>
</div>
<!-- /container -->

<?php include('includes/scripts.php'); 
?>
</body>
</html>