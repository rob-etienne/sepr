<?php 
   session_start();
   include('includes/htmlhead.php');
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
  			<li class="active">Home</a></li>
  			<li><a href="transaction.php">New Transaction</a></li>
  			<li><a href="messages.php">Messages</a></li>
		</ol>
	
    	<div class="col-md-12">
    		<div class="page-header">
        		<h3>Overview</h3>
      		</div>
            
			<?php include('scripts/accountoverview.php'); ?>
            
		</div> <!-- /col-md-12 -->
    	
    </div> <!-- /row -->   
	
    <hr>
	      
    <?php include('includes/footer.php'); ?>
    
    </div> <!-- /container -->   
        
    <?php include('includes/scripts.php'); ?>
    
</body>

</html>

