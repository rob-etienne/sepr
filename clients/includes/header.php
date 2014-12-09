<div class="page-header">
	<p class="navbar-text navbar-right">
	  	<h1>Banking Application <small>Secure Programming</small></h1>
    
    
	<p class="navbar-text navbar-right">    
		<a href="scripts/logout.php" class="navbar-link">Logout</a>
    </p>
	
	<?php
    	if (isset($_SESSION['ClientEmail'])) 
    	{    
			// needed for prepared DB statements
			include_once('scripts/db/DBHandler.php');
    	
			$db = new DbHandler();
				
         	$email = $_SESSION['ClientEmail'];
            
           	$result = $db->clientIsActive($email); 
            
            if (count($result) == 0)
            {
            	echo "Not signed in";
        	}
            else
            {
            	echo "Signed in as $email";
        	}
        }
        else 
        {
        	echo "Not signed in!";
        } 	
    ?>
    </p>
</div>
