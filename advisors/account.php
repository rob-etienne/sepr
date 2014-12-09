<?php 
session_start();
include('includes/htmlhead.php');

// Show me all php errors  	
error_reporting(E_ALL);
ini_set("display_errors", 1);

// Prevention CSRF attacks
include_once('includes/nocsrf.php');
//generate token for form
$tokenMessage = NoCSRF::generate( 'csrf_token_message' );

// needed for helpers (like clean up data)
include_once('includes/helpers.php');

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
      <li><a href="manage.php">Manage</a></li>
    </ol>
    <div class="col-md-12">
      <div class="page-header">
        <h3>Messages</h3>
      </div>
      <div class="col-md-6">
        <form enctype="multipart/form-data" class="form-horizontal" method="POST" action="scripts/sendmessage.php" role="form">
          <div class="form-group">
            <label for="InputClient" class="col-sm-3 control-label">Select Client</label>
            <div class="col-sm-8">
              <?
                // Create connection
            $conn = new mysqli("localhost", "root", "root", "sepr_project");
            
            // Check connection
            if ($conn->connect_error) 
            {
                die("Connection failed: " . $conn->connect_error);
            }
            
		// clean up employee nr
		$empNr = Helpers::cleanData($_COOKIE["EmployeeNr"]);
		
		// get all clients linked to our employee
        $sql="select c.* from clients c, advisors a where a.employee_nr = '$empNr' and c.advisor_id = a.id";
        
        $result = mysqli_query($conn, $sql);
		
		echo "<select name='client' class='form-control' required>"; 
		echo "<option selected disabled value=''>choose</option>";
		
        if(mysqli_num_rows( $result ) > 0)
		{
			while($row = mysqli_fetch_array($result)) 
			{        
			echo "<option value=".$row['id'].">Client ID: ".$row['id'] . " | Name: " . $row['first_name'] . " " . $row['last_name']."</option>"; 
			}
			echo "</select>";
		}
		else
		{
			echo "<option selected disabled value=''>No clients for you.</option>"; 
			echo "</select>";	
		}
		
		mysqli_close($conn);
        ?>
            </div>
          </div>
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
              <input type="hidden" class="form-control" name="csrf_token_message" id="csrf_token_message" value="<?php echo $tokenMessage ?>">
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
    </div>
    <!-- /col-md-12 --> 
    
  </div>
  <!-- /row -->
  
  <hr>
  <?php include('includes/footer.php'); ?>
</div>
<!-- /container -->

<?php include('includes/scripts.php'); ?>
</body>
</html>