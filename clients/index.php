<?php 
   session_start();
   include('includes/htmlhead.php');
 ?>

<body>

    <div class="container">

   <?php include('includes/nav.php'); ?>
		   
    <div class="jumbotron">
        <h1>Start Online Banking?</h1>
        <p>When you don't have an account yet, please register to perform your transactions online.<br/>Otherwise, just sign in with your username and password in the form in the header.</p>
        <p><a class="btn btn-primary btn-lg" href="registration.php" role="button">Register &raquo;</a></p>
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
        
    <hr>
	      
    <?php include('includes/footer.php'); ?>
    
    </div> <!-- /container -->   
        
    <?php include('includes/scripts.php'); ?>
    
</body>

</html>

