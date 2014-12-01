<?php
  
// Show me all php errors  	
error_reporting(E_ALL);
ini_set("display_errors", 1);

// Prevention CSRF attacks
include_once('includes/nocsrf.php');
$tokenRegClient = NoCSRF::generate( 'csrf_token_regClient' );

?>
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container">
    	<div class="navbar-header">
        	<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            	<span class="sr-only">Toggle navigation</span>
            	<span class="icon-bar"></span>
            	<span class="icon-bar"></span>
            	<span class="icon-bar"></span>
          	</button>
          	<a class="navbar-brand" href="index.php">Online Banking</a>
        </div>
        
        <div class="navbar-collapse collapse">                
          <form method="POST" action="scripts/checklogin.php" class="navbar-form navbar-right" role="form">
            <div class="form-group">
              <input type="email" id="email" name="email" placeholder="Email" class="form-control" required>
            </div>
            <div class="form-group">
              <input type="password" id="password" name="password" placeholder="Password" class="form-control" required>
            </div>
            <input type="hidden" class="form-control" name="csrf_token_regClient" id="csrf_token_regClient" value="<?php echo $tokenRegClient ?>">
            <button type="submit" name="login" value="login" class="btn btn-success">Login</button>
          </form>
        
		</div><!--/.navbar-collapse -->
	</div>
</div>