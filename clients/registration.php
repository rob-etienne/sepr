<?php 
session_start();
include('includes/htmlhead.php');
   
// Show me all php errors  	
error_reporting(E_ALL);
ini_set("display_errors", 1);

// Prevention CSRF attacks
include_once('includes/nocsrf.php');
$tokenRegistration = NoCSRF::generate( 'csrf_token_registration' );

?>

<body>

    <div class="container">

   <?php include('includes/nav.php'); ?>

    <div class="row">
        
        <div class="col-md-12">
    		<div class="page-header">
          		<h1>Registration</h1>
      		</div>   
            
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
      		
        	<form class="form-horizontal" method="POST" action="scripts/register.php" role="form">
 <div class="form-group">
    <label for="InputFirstName" class="col-sm-3 control-label">First Name</label>
    <div class="col-sm-8">
      <input type="text" class="form-control" id="InputFirstName" name="firstName" value="" placeholder="Enter First Name" required>
    </div>
  </div>
 <div class="form-group">
    <label for="InputLastName" class="col-sm-3 control-label">Last Name</label>
    <div class="col-sm-8">
      <input type="text" class="form-control" id="InputLastName" name="lastName" value="" placeholder="Enter Last Name" required>
    </div>
  </div>
  
 <div class="form-group">
    <label for="inputEmail" class="col-sm-3 control-label">Email</label>
    <div class="col-sm-8">
      <input type="email" class="form-control" id="inputEmail" name="email"  value="" placeholder="Enter Email" required>
    </div>
  </div>
  <div class="form-group">
    <label for="inputPassword" class="col-sm-3 control-label">Password</label>
    <div class="col-sm-8">
      <input type="password" class="form-control" id="inputPassword" name="pass"  value="" placeholder="Enter Password" required>
    </div>
  </div>
  <div class="form-group">
    <label for="inputPasswordConfirm" class="col-sm-3 control-label">Confirm Password</label>
    <div class="col-sm-8">
      <input type="password" class="form-control" id="inputPasswordConfirm" name="passConfirm"  value="" placeholder="Confirm Password" required>
    </div>
  </div>
  <div class="form-group">
	<div class="col-sm-8">
  		<input type="hidden" class="form-control" name="csrf_token_registration" id="csrf_token_registration" value="<?php echo $tokenRegistration ?>">
    </div>
  </div>
 <div class="form-group">
    <div class="col-sm-offset-3 col-sm-8">
      <button type="submit" class="btn btn-default">Submit</button>
    </div>
  </div>
</form>		
        </div>
      </div>
	
    <hr>
	      
    <?php include('includes/footer.php'); ?>
    
    </div> <!-- /container -->   
        
    <?php include('includes/scripts.php'); ?>
    
</body>

</html>

