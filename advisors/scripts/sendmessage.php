<?php 
session_start();

// for CSRF token check
include_once('../includes/nocsrf.php');
// needed for db communication
include_once('db/DBHandler.php');
// needed for helpers (like clean up data)
include_once('../includes/helpers.php');

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
        if(NoCSRF::check( 'csrf_token_message', $_POST, true, 60*3, false ));
	}
    catch ( Exception $e )
    {
        // CSRF attack detected
        $errCSRF = $e->getMessage();
    }
	
	// check if field is empty
	if(!Helpers::validateInteger($_POST['client']))
    	$errClient = 1;
	// check if subject is valid
	if(!Helpers::validateSubject($_POST['subject']))
    	$errSubject = 1;
	// check if message is valid
	if(!Helpers::validateMessage($_POST['message']))
    	$errMessage = 1;
	// check if file is valid
	//if(!Helpers::validateFileUpload($_FILES['upload']['name']))
    //	$errFile = 1;
			
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
if (empty($_POST['subject']) || empty($_POST['message']) || empty($_POST['client']) || !empty($errClient)  || !empty($errMessage) || !empty($errSubject) || !empty($errCSRF) || !empty($errFile) || $_SERVER['REQUEST_METHOD'] == 'GET')
{
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
   	{
    	if (!empty($errSubject))
      	{
        	$_SESSION['error'] = "Please enter a subject. Min. characters 5 and max. 50. Start with capital letter. Only characters, numbers and these: '.' , ':'";
			header('Location: ../account.php');
      	}
    	elseif (!empty($errMessage))
      	{
        	$_SESSION['error'] = "Please enter a message. Min. characters 5 and max. 150. Start with capital letter. Only characters, numbers and these: '.' , ':'";
			header('Location: ../account.php');
      	}
		elseif (!empty($errFile))
      	{
        	$_SESSION['error'] = $errFile;
			header('Location: ../account.php');
      	}
		elseif (!empty($errCSRF))
      	{
        	$_SESSION['error'] = $errCSRF;
			header('Location: ../account.php');
      	}
   	}
}
else // send message
{	
	// Create connection
	$db = new DbHandler();
	
	// clean up employee nr
	$empNr = Helpers::cleanData($_SESSION['EmployeeNr']);
	
	// clean up subject
	$subject = Helpers::cleanData($_POST['subject']);
	
	// clean up message
	$message = Helpers::cleanData($_POST['message']);
	
	// clean up client id
	$clientId = Helpers::cleanData($_POST['client']);
	
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
			
			// query with link to file uploaded
			$result = $db->addMessage($clientId, $subject, $message, $url);
		}
	}
	else
	{
		// query without file upload
		$result = $db->addMessageWithoutURL($clientId, $subject, $message);
	}
	
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
	
	// go back to account page
	header('Location: ../account.php');

}
 ?>