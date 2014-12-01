<footer>
	<p>&copy; 2014-2015 | SePr Group Project 
	
	<?php 
	if (isset($_SESSION['msg'])) 
	{
    	echo $_SESSION['msg'];
        $_SESSION['msg'] = "";
    }
	?>
	
	</p>
</footer>