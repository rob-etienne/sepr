<?php
session_start();

// Show me all php errors

error_reporting(E_ALL);
ini_set("display_errors", 1);

// Prevention CSRF attacks

include_once ('../includes/nocsrf.php');

// Special checks for input fields

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{

	// check CSRF token

	try
	{

		// Run CSRF check, on POST data, in exception mode, for 3 minutes, in one-time mode.

		if (NoCSRF::check('csrf_token_transaction', $_POST, true, 60 * 3, false));
	}

	catch(Exception $e)
	{

		// CSRF attack detected

		$errCSRF = $e->getMessage();
	}

	// check if fields are empty

	if (empty($_POST['account'])) $errClientAcc = 1;
	if (empty($_POST['accountnr'])) $errAccNr = 1;
	if (function_exists('filter_var') && !filter_var($_POST['accountnr'], FILTER_VALIDATE_INT)) $errAccNrVal = 1;
	if (empty($_POST['amount'])) $errAmount = 1;
	if (function_exists('filter_var') && !filter_var($_POST['amount'], FILTER_VALIDATE_FLOAT)) $errAmountVal = 1;
	if (empty($_POST['purpose'])) $errPurpose = 1;
	if (strlen($_POST['purpose']) > 100) $errPurposeLength = 1;
}

// Check all fields

if (empty($_POST['accountnr']) || empty($_POST['amount']) || empty($_POST['purpose']) || empty($_POST['account']) || !empty($errClientAcc) || !empty($errAccNr) || !empty($errAccNrVal) || !empty($errAmount) || !empty($errAmountVal) || !empty($errPurpose) || !empty($errPurposeLength) || $_SERVER['REQUEST_METHOD'] == 'GET')
{
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		if (!empty($errClientAcc))
		{
			$_SESSION['error'] = "Please select an account for the transaction.";
			header('Location: ../transaction.php');
		}
		elseif (!empty($errAccNr))
		{
			$_SESSION['error'] = "Please enter an account number.";
			header('Location: ../transaction.php');
		}
		elseif (!empty($errAccNrVal))
		{
			$_SESSION['error'] = "Account number is not valid.";
			header('Location: ../transaction.php');
		}
		elseif (!empty($errAmount))
		{
			$_SESSION['error'] = "Please enter an amount.";
			header('Location: ../transaction.php');
		}
		elseif (!empty($errAmountVal))
		{
			$_SESSION['error'] = "Amount is not valid.";
			header('Location: ../transaction.php');
		}
		elseif (!empty($errPurpose))
		{
			$_SESSION['error'] = "Please enter a purpose.";
			header('Location: ../transaction.php');
		}
		elseif (!empty($errPurposeLength))
		{
			$_SESSION['error'] = "Entered purpose is too long, 100 characters max.";
			header('Location: ../transaction.php');
		}
		elseif (!empty($errCSRF))
		{
			$_SESSION['error'] = $errCSRF;
			header('Location: ../transaction.php');
		}
	}
}
else
{

	//	TODO:
	//  1. Add transaction to database (transactions table, make link to account)
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
	$email = stripslashes($email);
	$email = htmlspecialchars($email);
	$email = mysqli_real_escape_string($conn, $email);

	// clean up client id

	$clientId = $_COOKIE["ClientId"];
	$clientId = stripslashes($clientId);
	$clientId = htmlspecialchars($clientId);
	$clientId = mysqli_real_escape_string($conn, $clientId);

	// clean up account number (from)

	$account_id_from = trim($_POST['account']);
	$account_id_from = stripslashes($account_id_from);
	$account_id_from = htmlspecialchars($account_id_from);
	$account_id_from = mysqli_real_escape_string($conn, $account_id_from);

	// clean up last account number (to)

	$account_id_to = trim($_POST['accountnr']);
	$account_id_to = stripslashes($account_id_to);
	$account_id_to = htmlspecialchars($account_id_to);
	$account_id_to = mysqli_real_escape_string($conn, $account_id_to);

	// clean up amount

	$amount = trim($_POST['amount']);
	$amount = stripslashes($amount);
	$amount = htmlspecialchars($amount);
	$amount = mysqli_real_escape_string($conn, $amount);

	// clean up purpose

	$purpose = trim($_POST['purpose']);
	$purpose = stripslashes($purpose);
	$purpose = htmlspecialchars($purpose);
	$purpose = mysqli_real_escape_string($conn, $purpose);

	// check if account_id_to exists

	$sql = "select a.id from accounts a, clients c where a.id='$account_id_to' limit 1";
	$result = mysqli_query($conn, $sql);
	$value = mysqli_fetch_object($result);
	if (!$value == 0) // account exists
	{

		// check if client has an account

		$sql = "select a.id from accounts a, clients c where a.client_id = '$clientId' and c.id = '$clientId' limit 1";
		$result = mysqli_query($conn, $sql);
		$value = mysqli_fetch_object($result);
		if (!$value == 0) // client has an account
		{

			// accounts exists & make transaction

			$sql = "insert into transactions (account_id_to, amount, purpose) values ('$account_id_to', '$amount', '$purpose')";

			// run the query

			$result = mysqli_query($conn, $sql);

			// check result

			if ($result)
			{

				// get transaction id from last query

				$transaction_id = mysqli_insert_id($conn);

				// update account_transaction_matches table

				$sql = "insert into account_transaction_matches (transaction_id, account_id_to, account_id_from) values ('$transaction_id', '$account_id_to', '$account_id_from')";
				$result = mysqli_query($conn, $sql);

				// check result

				if ($result)
				{					
					// assemble query
					$sql = "update accounts a set a.balance = a.balance - '$amount' where a.id = '$account_id_from'";
			
					$result = mysqli_query($conn, $sql);
					
					if($result)
					{
						// assemble query
					  	$sql = "update accounts a set a.balance = a.balance + '$amount' where a.id = '$account_id_to'";
			  
					  	$result = mysqli_query($conn, $sql);
			  
			  			if($result)
						{
							// Transaction sucessfully performed & all tables updated
  
					  		$_SESSION['success'] = "Transaction made to account with number: $account_id_to.";	
						}
					  	else
						{
							// Updating tables failed

							$_SESSION['error'] = "Error occured while updating balance (to). Please contact your financial advisor.";	
						}
					}
					else
					{
						// Updating tables failed

						$_SESSION['error'] = "Error occured while updating balance (from). Please contact your financial advisor.";	
					}
				}
				else
				{

					// Updating tables failed

					$_SESSION['error'] = "Error occured while updating matching. Please contact your financial advisor.";
				}
			}
			else
			{

				// Updating tables failed

				$_SESSION['error'] = "Error occured while making the transaction. Please contact your financial advisor.";
			}
		}
		else

		// client doesn't have an account

		{

			// Updating tables failed

			$_SESSION['error'] = "You don't have an account yet.";
		}
	}
	else
	{

		// Account of beneficiary doesn't exist

		$_SESSION['error'] = "Entered account doesn't exist";
	}

	// close db connection

	mysqli_close($conn);

	// go back to transaction page

	header('Location: ../transaction.php');
}

?>