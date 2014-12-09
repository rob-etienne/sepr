<?php 
session_start();
include('includes/htmlhead.php');

// Show me all php errors  	
error_reporting(E_ALL);
ini_set("display_errors", 1);

// Prevention CSRF attacks
include_once('includes/nocsrf.php');

$tokenMesClient = NoCSRF::generate( 'csrf_token_messClient' );
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
  			<li class="active">Messages</a></li>
		</ol>
        
    	<div class="col-md-12">
    		<div class="page-header">
        		<h3>Messages</h3>
      		</div>
      		
      		<div class="col-md-6">
      			<form enctype="multipart/form-data" class="form-horizontal" method="POST" action="scripts/sendmessage.php" role="form">
                <div class="form-group">
            <label for="InputSubject" class="col-sm-3 control-label">Subject</label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="InputSubject" maxlength="50" name="subject" value="" placeholder="Enter a Subject" required>
              <p class="text-right">max. 50 characters.</p>
            </div>
          </div>
          <div class="form-group">
            <label for="InputMessage" class="col-sm-3 control-label">Message</label>
            <div class="col-sm-8">
              <textarea class="form-control" rows="3" maxlength="150" id="InputMessage" name="message"  value="" placeholder="Enter a Message" required></textarea>
              <p class="text-right">max. 150 characters.</p>
            </div>
          </div>
          <div class="form-group">
            <label for="InputFile" class="col-sm-3 control-label">File Upload</label>
            <div class="col-sm-8">
              <input class="col-sm-8" type="file" name="upload" id="InputFile">
              <br>
              <p class="text-right">Only JPG, JPEG, PNG & GIF files are allowed.</p>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-8">
              <input type="hidden" class="form-control" name="csrf_token_messClient" id="csrf_token_messClient" value="<?php echo $tokenMesClient ?>">
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-3 col-sm-8">
              <button type="submit" class="btn btn-default btn-lg btn-block">Send</button>
            </div>
          </div>
        </form>
      		</div>
      		
      		<div class="col-md-6">
                  <div>
      		<h4>Message Board</h4>
    	</div>
      			<?php include('scripts/inbox.php'); ?>
      		</div>

		</div> <!-- /col-md-12 -->
    	
    </div> <!-- /row -->   
	
    <hr>
	      
    <?php include('includes/footer.php'); ?>
    
    </div> <!-- /container -->   
        
    <?php include('includes/scripts.php'); ?>
    
</body>

</html>

