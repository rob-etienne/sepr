<?php 
session_start();

// for CSRF token check
include_once('../includes/nocsrf.php');

// Show me all php errors  	
error_reporting(E_ALL);
ini_set("display_errors", 1);

// Checks for input fields
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$fileToUpload = false;
		
	// check CSRF token	
	try
    {
        // Run CSRF check, on POST data, in exception mode, for 3 minutes, in one-time mode.
        if(NoCSRF::check( 'csrf_token_messClient', $_POST, true, 60*3, false ));
	}
    catch ( Exception $e )
    {
        // CSRF attack detected
        $errCSRF = $e->getMessage();
    }
	
	// check if field is empty
	if(empty($_POST['subject']))
    	$errSubject = 1;
	// check if field is empty
    if(empty($_POST['message']))
    	$errMessage = 1;
	// check if message has 150 characters max.
	if(strlen($_POST['message']) > 150)
		$errMessLength = 1;
	// check if subject has 50 characters max.
	if(strlen($_POST['subject']) > 50)
		$errSubLength = 1;
		
	// Regular Expressions checks
	// Subject: should start with letter, max of 50, only letters and numbers
	// TODO: Allow certain special characters like: [?], [!], [.], [,], [€], [$], and whitespaces 
	if (!preg_match('/^[A-Za-z]{1}[A-Za-z0-9]{5,50}$/', $_POST['subject']))
		$errRegExSub = 1;
	// Message: should start with letter, max of 150, only letters and numbers
	// TODO: Allow certain special characters like: [?], [!], [.], [,], [€], [$], and whitespaces 
	if (!preg_match('/^[A-Za-z]{1}[A-Za-z0-9]{5,150}$/', $_POST['message']))
		$errRegExMess = 1;
			
	// check file upload
	if($_FILES['upload']['name'])
	{
		$target_dir = "../uploads/";
		$target_file = $target_dir . basename($_FILES["upload"]["name"]);
		$uploadOk = 1;
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
		
		// Check if image file is a actual image or fake image
		if(isset($_POST["submit"])) {
			$check = getimagesize($_FILES["upload"]["tmp_name"]);
			if($check !== false) {
				$errFile =  "File is an image - " . $check["mime"] . ".";
				$uploadOk = 1;
			} else {
				$errFile =  "File is not an image.";
				$uploadOk = 0;
			}
		}
		
		// Check if file already exists
		if (file_exists($target_file)) {
			$errFile =  "Sorry, file already exists.";
			$uploadOk = 0;
		}
		// Check file size (max file size = 5mb)
		if ($_FILES["upload"]["size"] > 5000000) {
			$errFile =  "Sorry, your file is too large.";
			$uploadOk = 0;
		}
		// Allow certain file formats
		if($imageFileType != "jpg" && $imageFileType != "JPG" && $imageFileType != "png" && $imageFileType != "PNG" && $imageFileType != "jpeg" && $imageFileType != "JPEG" && $imageFileType != "gif" && $imageFileType != "GIF" ) 
		{
			$errFile =  "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
			$uploadOk = 0;
		}
		// Check if $uploadOk is set to 0 by an error
		if (!$uploadOk == 0) 
		{
			$fileToUpload = true;
		}
	}
}

//Check all fields
if (empty($_POST['subject']) || empty($_POST['message']) || !empty($errMessage) || !empty($errRegExSub) || !empty($errRegExMess) || !empty($errSubject) || !empty($errMessLength) || !empty($errSubLength) || !empty($errCSRF) || !empty($errFile) || $_SERVER['REQUEST_METHOD'] == 'GET')
{
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
   	{
    	if (!empty($errSubject))
      	{
        	$_SESSION['error'] = "Please enter a subject.";
			header('Location: ../messages.php');
      	}
    	elseif (!empty($errMessage))
      	{
        	$_SESSION['error'] = "Please enter a message.";
			header('Location: ../messages.php');
      	}
		elseif (!empty($errMessLength))
      	{
        	$_SESSION['error'] = "Message is too long. 150 characters max.";
			header('Location: ../messages.php');
      	}
		elseif (!empty($errSubLength))
      	{
        	$_SESSION['error'] = "Subject is too long. 50 characters max.";
			header('Location: ../messages.php');
      	}
		elseif (!empty($errRegExSub))
      	{
        	$_SESSION['error'] = "Subject should start with a letter, 50 characters max., and only letters and numbers are permitted.";
			header('Location: ../messages.php');
      	}
		elseif (!empty($errRegExMess))
      	{
        	$_SESSION['error'] = "Message should start with a letter, 150 characters max., and only letters and numbers are permitted.";
			header('Location: ../messages.php');
      	}
		elseif (!empty($errCSRF))
      	{
        	$_SESSION['error'] = $errCSRF;
			header('Location: ../messages.php');
      	}
		elseif (!empty($errFile))
      	{
        	$_SESSION['error'] = $errFile;
			header('Location: ../messages.php');
      	}
   	}
}
else // send message
{	
	// input message in db
	// Create connection
	$conn = new mysqli("localhost", "root", "root", "sepr_project");
	
	// Check connection
	if ($conn->connect_error) 
	{
    	die("Connection failed: " . $conn->connect_error);
	}
	
	// clean up email address
	$email = $_COOKIE["Email"];
	$email = stripslashes( $email );
	$email = htmlspecialchars($email);	
	$email = mysqli_real_escape_string($conn, $email );
	
	// clean up client id
	$clientId = $_COOKIE["ClientId"];
	$clientId = stripslashes( $clientId );
	$clientId = htmlspecialchars($clientId);	
	$clientId = mysqli_real_escape_string($conn, $clientId );
	
	// clean up subject
	$subject = trim($_POST['subject']);
	$subject = stripslashes( $subject );
	$subject = htmlspecialchars($subject);
	$subject = mysqli_real_escape_string($conn, $subject );
	
	// clean up message
	$message = trim($_POST['message']);
	$message = stripslashes( $message );
	$message = htmlspecialchars($message);
	$message = mysqli_real_escape_string($conn, $message );
	
	// check if file is attached to form
	if($fileToUpload)
	{
		// try to move/upload file
		if (!move_uploaded_file($_FILES["upload"]["tmp_name"], $target_file)) 
		{
			$errFile =  "Sorry, there was an error uploading your file.";
		}
		else
		{
			// clean up attachment url
			$url = stripslashes( $target_file );
			$url = htmlspecialchars($url);
			$url = mysqli_real_escape_string($conn, $url );
			
			// query with link to file uploaded
			$sql = "insert into messages (subject, message, attachment_url, client_id) VALUES ('$subject', '$message','$url', '$clientId')";
		}
	}
	else
	{
		// query without file upload
		$sql = "insert into messages (subject, message, client_id) VALUES ('$subject', '$message', '$clientId')";
	}
		
	// run the query
	$result = mysqli_query($conn, $sql);
	
	// check result
	if($result)
	{
		// Message sucessfully added
		
		// clean up unused notifications
		unset($_SESSION['error']);
		unset($_SESSION['info']);
	
		// set success message
		$_SESSION['success'] = "Message sent.";
	}
	else
	{
		// Sending message failed
		$_SESSION['error'] = "Error occured while sending message.";
	}
	
	// close db connection	
	mysqli_close($conn);
	
	// go back to account page
	header('Location: ../messages.php');

}
 ?>