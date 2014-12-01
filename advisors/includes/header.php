<div class="page-header">
	<p class="navbar-text navbar-right">
	  	<h1>Banking Application <small>Secure Programming</small></h1>
    
    
	<p class="navbar-text navbar-right"> 
        <a href="scripts/logout.php" class="navbar-link">Logout</a>
    </p>

	<?php
    	if (isset($_COOKIE['EmployeeNr'])) 
    	{
			// Create connection
			$conn = new mysqli("localhost", "root", "root", "sepr_project");
		
			// Check connection
			if ($conn->connect_error) 
			{
				die("Connection failed: " . $conn->connect_error);
			}
         	
         	$empNr = $_COOKIE['EmployeeNr'];
			
         	$sql = "select * from advisors where employee_nr = '$empNr' and active = '1'";
            
			$result = mysqli_query($conn, $sql);
            
            if (mysqli_num_rows($result) == 0)
            {
            	echo "Not signed in";
        	}
            else
            {
            	echo "Signed in as $empNr";
        	}
        }
        else 
        {
        	echo "Not signed in";
        } 	
    ?>
    </p>
</div>
