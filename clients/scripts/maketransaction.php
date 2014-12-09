<?php 
session_start();

// needed for CSRF token
include_once('../includes/nocsrf.php');
// needed helpers for data clean up and validation
include_once('../includes/helpers.php');
// needed for db communication
include_once('db/DBHandler.php');
// needed for logging
include_once('../handler/log.php');
// logger
$log = new Log();

// Show me all php errors  	
error_reporting(E_ALL);
ini_set("display_errors", 1);

// Checks for input fields
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
	
	// check if account (from) is valid
	if(!Helpers::validateInteger($_POST['account']))
    	$errClientAcc = 1;
	// check if account (to) is valid
	if(!Helpers::validateInteger($_POST['accountnr']))
    	$errAccNr = 1;
	// check if account is valid
	if(!Helpers::validateAmount($_POST['amount']))
    	$errAmount = 1;
	// check if account is valid
	if(!Helpers::validatePurpose($_POST['purpose']))
    	$errPurpose = 1;
}

// Check all fields again & for errors found
if (empty($_POST['accountnr']) || empty($_POST['amount']) || empty($_POST['purpose']) || empty($_POST['account']) || !empty($errClientAcc) || !empty($errAccNr) || !empty($errAmount) || !empty($errPurpose) || $_SERVER['REQUEST_METHOD'] == 'GET')
{
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		if (!empty($errClientAcc))
		{
			$_SESSION['error'] = "Please select an account for the transaction. (Integer, 4 digits)";
			$log->transactionFailure("No account selected.",$_SESSION['ClientEmail']);
			header('Location: ../transaction.php');
			exit();
		}
		elseif (!empty($errAccNr))
		{
			$_SESSION['error'] = "Please enter a compliant account number. (Integer, 4 digits)";
			$log->transactionFailure("Invalid account number of beneficiary.",$_SESSION['ClientEmail']);
			header('Location: ../transaction.php');
			exit();
		}
		elseif (!empty($errAmount))
		{
			$_SESSION['error'] = "Please enter an amount. Minimum transaction amount is 5.00€ and maximum amount is 1.000.000€. (Decimal, using a dot as delimiter)";
			$log->transactionFailure("Invalid amount.",$_SESSION['ClientEmail']);
			header('Location: ../transaction.php');
			exit();
		}
		elseif (!empty($errPurpose))
		{
			$_SESSION['error'] = "Please enter a purpose. Only characters, whitespaces and these special characters: ... Min. length 5 and max. 100. Should start with capital letter.";
			$log->transactionFailure("Invalid purpose.",$_SESSION['ClientEmail']);
			header('Location: ../transaction.php');
			exit();
		}
		elseif (!empty($errCSRF))
		{
			$_SESSION['error'] = $errCSRF;
			$log->transactionFailure($errCSRF,$_SESSION['ClientEmail']);
			header('Location: ../transaction.php');
			exit();
		}
	}
}
else
{
	// Create connection
	$db = new DbHandler();

	// clean up email address
	$email = Helpers::cleanData($_SESSION["ClientEmail"]);

	// clean up client id
	$clientId = Helpers::cleanData($_SESSION["ClientId"]);

	// clean up account number (from)
	$account_id_from = Helpers::cleanData($_POST['account']);

	// clean up last account number (to)
	$account_id_to = Helpers::cleanData($_POST['accountnr']);

	// clean up amount
	$amount = Helpers::cleanData($_POST['amount']);

	// clean up purpose
	$purpose = Helpers::cleanData($_POST['purpose']);

	// check if account_id_to exists
	$result = $db->FindAccountById($account_id_to);
	
	$value = count($result);
	
	if (!$value == 0) // account exists
	{
		// check if client has an account
		$result = $db->FindAccountById($account_id_from);
        
		$value = count($result);
		
		if (!$value == 0) // client has an account
		{
			// accounts exists & make transaction
			
			// run the query
            $result = $db->addTransaction($account_id_from, $account_id_to, $amount, $purpose);
			
			// check result
			if ($result)
			{
				// get transaction id from last query
				$transaction_id = $result;

				// update account_transaction_matches table
                $result = $db->createAccountTransaction($transaction_id, $account_id_to, $account_id_from);
				
				// check result
				if ($result)
				{
					// update balance on account (from)
			        $bal = $db->FetchAccountBalance($account_id_from);
                    
					$newbal= $bal - $amount;
					
					$result = $db->updateAccount($clientId,$account_id_from,$newbal);
					
					if($result)
					{
						// update balance on account (to)
			            $bal = $db->FetchAccountBalance($account_id_to);
                        
						$bal = $bal + $amount;
                        
						$client_id_to = $db->GetClientIdFromAccNr($account_id_to);
						
			            $result = $db->updateAccount($client_id_to, $account_id_to, $bal);
			  			
						if($result)
						{
							// Transaction sucessfully performed & all tables updated
					  		$_SESSION['success'] = "Transaction made to account with number: $account_id_to.";
							$log->transactionSuccess($_SESSION['success'],$_SESSION['ClientEmail']);
						}
					  	else
						{
							// Updating tables failed
							$_SESSION['error'] = "Error occured while updating balance (to). Please contact your financial advisor.";	
							$log->transactionFailure($_SESSION['error'],$_SESSION['ClientEmail']);
						}
					}
					else
					{
						// Updating tables failed
						$_SESSION['error'] = "Error occured while updating balance (from). Please contact your financial advisor.";	
						$log->transactionFailure($_SESSION['error'],$_SESSION['ClientEmail']);
					}
				}
				else
				{
					// Updating tables failed
					$_SESSION['error'] = "Error occured while updating matching. Please contact your financial advisor.";
					$log->transactionFailure($_SESSION['error'],$_SESSION['ClientEmail']);
				}
			}
			else
			{
				// Updating tables failed
				$_SESSION['error'] = "Error occured while making the transaction. Please contact your financial advisor.";
				$log->transactionFailure($_SESSION['error'],$_SESSION['ClientEmail']);
			}
		}
		else
		{
			// client doesn't have an account
			$_SESSION['error'] = "You don't have an account yet.";
			$log->transactionFailure($_SESSION['error'],$_SESSION['ClientEmail']);
		}
	}
	else
	{
		// Account of beneficiary doesn't exist
		$_SESSION['error'] = "Entered account doesn't exist";
		$log->transactionFailure($_SESSION['error'],$_SESSION['ClientEmail']);
	}
	
	// go back to transaction page
	header('Location: ../transaction.php');
	exit();
}

?>