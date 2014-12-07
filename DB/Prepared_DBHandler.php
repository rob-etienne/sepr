<?php

/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 */
class DbHandler {

    private $conn;

    function __construct() {
        require_once dirname(__FILE__) . '/DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }
    
    /* ------------- `advisors` table method ------------------ */
  
    /**
     * Checking advisor login
     */
    public function checkAdvisorLogin($empNr, $password) {
        // fetching advisor by employee nr
        $stmt = $this->conn->prepare("SELECT * FROM advisors WHERE employee_nr = ? AND password_hash = ?");

        $stmt->bind_param("is", $empNr, $password);

        $stmt->execute();

        $stmt->bind_result($password_hash);

        $stmt->store_result();

        if ($stmt->num_rows > 0) 
        {
            // Found advisor with the employee number
            // Now verify the password

            $stmt->fetch();

            $stmt->close();

            if (PassHash::check_password($password_hash, $password)) {
                // Advisor password is correct
                return TRUE;
            } else {
                // Advisor password is incorrect
                return FALSE;
            }
        } else {
            $stmt->close();

            // Advisor not existed with the employee number
            return FALSE;
        }
    }
    
    /**
     * Updating advisor login
     */
    public function updateAdvisorLogin($empNr, $timestamp) {
        $stmt = $this->conn->prepare("UPFDATE advisors SET active = '1', last_sign_in_stamp = ? WHERE employee_nr = ?");
        $stmt->bind_param("si", $timestamp, $empNr);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }
    
    /**
     * Updating advisor logout
     */
    // public function updateAdvisorLogin($empNr) {
        // $stmt = $this->conn->prepare("UPFDATE advisors SET active = '0'WHERE employee_nr = ?");
        // $stmt->bind_param("i", $empNr);
        // $stmt->execute();
        // $num_affected_rows = $stmt->affected_rows;
        // $stmt->close();
        // return $num_affected_rows > 0;
    // }

    /* ------------- `clients` table method ------------------ */

    /**
     * Creating new client
     * @param String $firstName Client first name
     * @param String $lastName Client last name
     * @param String $email Client login email id
     * @param String $password Client login password
     */
    public function createClient($firstName, $lastName, $email, $password) {

        // First check if client already existed in db
        if (!$this->isClientExists($email)) {
            // Generating password hash
				
			// PASSWORD_DEFAULT will always be used to apply the strongest supported hashing algorithm.
			// PHP will choose the encryption to use and it might change in the future 
			// at time of writing it will be using CRYPT_BLWFISH, salt and type of encryption is stores together 
			// with the hash it self
			$options = [
				'cost' => 12
			];
	
			$password_hash = password_hash($password, PASSWORD_DEFAULT, $options);		

            // insert query
            $stmt = $this->conn->prepare("INSERT INTO clients(first_name, last_name, password_hash, email) values(?, ?, ?, ?)");
            $stmt->bind_param("ssss", $firstName, $lastName, $password_hash, $email);

            $result = $stmt->execute();

            $stmt->close();

            // Check for successful insertion
            if ($result) {
                // Client successfully inserted
                //return CLIENT_CREATED_SUCCESSFULLY;
				return true;
            } else {
                // Failed to create client
                //return CLIENT_CREATE_FAILED;
				return false;
            }
        } else {
            // Client with same email already existed in the db
            //return CLIENT_ALREADY_EXISTED;
			return false;
        }

        //return $response;
		return false;
    }

    /**
     * Checking for duplicate client by email address
     * @param String $email email to check in db
     * @return boolean
     */
    private function isClientExists($email) {
		
        //$stmt = $this->conn->prepare("SELECT id from client WHERE email = ?");
		$sql = "SELECT id from client WHERE email = ?";
		
		if( !$stmt = $this->conn->prepare( $sql ) ) {
			
			//echo 'Error: ' . $db->error;
			return false; // throw exception, die(), exit, whatever...			
		}
		
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }
	
	 /**
     * Checking if client is already signed in
     * @param String $email email to check in db
     * @return boolean is active true/false
     */
    public function clientIsActive($email) {
		
        //$stmt = $this->conn->prepare("SELECT id from client WHERE email = ?");
		$sql = "SELECT active from clients WHERE email = ?";
		
		if( !$stmt = $this->conn->prepare( $sql ) ) {
			
			//echo 'Error: ' . $this->conn->error;
			//exit();
			return false; // throw exception, die(), exit, whatever...			
		}
		
        $stmt->bind_param("s", $email);
        $stmt->execute();
		$stmt->bind_result($active);
        $stmt->store_result();
		$stmt->fetch();
        $stmt->close();
		
		if ($active == 1) {
			return true; 
		} else {
			return false;
		}

    }

    /**
     * Checking user login
     * @param String $email User login email id
     * @param String $password User login password
     * @return boolean User login status success/fail
     */
    public function checkClientLogin($email, $password) {
        // fetching client by email
        $stmt = $this->conn->prepare("SELECT password_hash FROM clients WHERE email = ?");

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $stmt->bind_result($password_hash);

        $stmt->store_result();
		
        if ($stmt->num_rows > 0) {
            // Found client with the email
            // Now verify the password

            $stmt->fetch();
			
            if (password_verify($password, $password_hash)) {
			
                // User password is correct
                return TRUE;
            } else {
                // user password is incorrect
                return FALSE;
            }
        } else {
            $stmt->close();

            // client not existed with the email
            return FALSE;
        }
    }
	
	/**
    * Updating client login
    */
    public function updateClientLogin($email) {
	
		$timestamp = date('Y-m-d H:i:s');
		$sql = "update clients set active = '1', last_sign_in_stamp = '$timestamp' where email = ?";
	
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }
	
	 /**
     * Get client id
     */
    public function getClientId($email) {
	
		$sql = "select id from clients where email = ?";
		
		if( !$stmt = $this->conn->prepare( $sql ) ) {
			return -1;	
		}
		
        $stmt->bind_param("s", $email);
        $stmt->execute();
		$stmt->bind_result($id);
        $stmt->store_result();
		$stmt->fetch();
        $stmt->close();

		return $id;
    }


    /* ------------- `accounts` table method ------------------ */

    /**
     * Adding new device
     * @param String $user_id user id to whom device belongs to
     * @param String $name text
     */
    public function addAccount($client_id, $name) {
		
		$new_account_id = $this->generateAccountId();
		
		// insert query
		$stmt = $this->conn->prepare("INSERT INTO accounts(id, name, client_id) VALUES(?,?,?)");
		$stmt->bind_param("sss", $new_account_id, $name, $client_id);
		$result = $stmt->execute();
		$stmt->close();

		if ($result) {
			// account added successfully
			return $new_account_id;
		} else {
			// account failed to add
			return NULL;
		}
	}
	
    /**
     * Updating account
     * @param String $client_id id of the client
     * @param String $account_id id of the account
     * @param String $balance balance of account
     * @param String $name account name
     */
    public function updateAccount($client_id, $account_id, $balance, $name) {
        $stmt = $this->conn->prepare("UPDATE accounts a SET a.balance = ?, a.name = ? WHERE a.id = ? AND a.client_id = c.id AND c.id = ?");
        $stmt->bind_param("iiii", $balance, $name, $account_id, $client_id);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    /**
     * Deleting an account
     * @param String $client_id id of the client
     * @param String $account_id id of the account to delete
     */
    public function deleteAccount($client_id, $account_id) {
        $stmt = $this->conn->prepare("DELETE a FROM accounts a WHERE a.id = ? AND a.client_id = c.id AND c.id = ?");
        $stmt->bind_param("ii", $account_id, $client_id);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    /**
     * Fetching all client accounts
     * @param String $client_id id of the client
     */
    public function getAllClientAccounts($client_id) {
        $stmt = $this->conn->prepare("SELECT a.id, a.name, a.balance FROM accounts a, clients c WHERE a.client_id = c.id AND a.client_id = ?");
        $stmt->bind_param("i", $client_id);
        $stmt->execute();
        
        /* Store the result (to get properties) */
   		$stmt->store_result();
   		
  		/* Bind the result to variables */
   		$stmt->bind_result($id, $name, $balance);
   		
   		$response["accounts"] = array();
   		            
   		while ($stmt->fetch()) 
		{
                    $tmp = array();
                    $tmp["id"] = $id;
                    $tmp["name"] = $name;
                    $tmp["balance"] = $balance;
                    array_push($response["accounts"], $tmp);
                }
        
        $stmt->close();
        return $response["accounts"];
    }
    
	/**
     * Generating random int
     */
    private function generateAccountId() {
        return mt_rand();
    }
    

    /* ------------- `messages` table method ------------------ */

    /**
     * Adding new message
     * @param String $client_id id of the client
     * @param String $subject subject of message
     * @param String $message actual message
     */
    public function addMessage($client_id, $subject, $message, $attachment_url ) {
		
		// insert query
		$stmt = $this->conn->prepare("INSERT INTO messages(subject, message, attachment_url, client_id) VALUES(?,?,?,?)");
		$stmt->bind_param("sssi", $subject, $message, $attachment_url, $client_id);
		$result = $stmt->execute();
		$stmt->close();
	}

    /**
     * Fetching all messages from client
     * @param String $client_id id of the client
     */
    public function getAllMessages($client_id) {
        $stmt = $this->conn->prepare("SELECT m.* FROM messages m WHERE m.client_id = ?");
		$stmt->bind_param("s", $client_id);
        $stmt->execute();
        
        /* Store the result (to get properties) */
   		$stmt->store_result();
   		
  		/* Bind the result to variables */
   		$stmt->bind_result($id, $subject, $message, $submitted_stamp, $client_id);
   		
   		$response["messages"] = array();
   		            
   		while ($stmt->fetch()) 
		{
                    $tmp = array();
                    $tmp["id"] = $id;
                    $tmp["subject"] = $subject;
                    $tmp["message"] = $message;
					$tmp["submitted_stamp"] = $submitted_stamp;
                    $tmp["client_id"] = $client_id;
                    array_push($response["messages"], $tmp);
                }
        
        $stmt->close();
        return $response["messages"];
    }
    
    /* ------------- `transactions` table method ------------------ */

    /**
     * Adding new message
     * @param String $client_id id of the client
     * @param String $subject subject of message
     * @param String $message actual message
     */
    public function addTransaction($account_id_from, $firstName, $lastName, $account_id_to, $amount, $purpose ) {
		
		// insert query
		$stmt = $this->conn->prepare("INSERT INTO transactions(firstName, lastName, account_id_to, amount, purpose) VALUES(?,?,?,?,?)");
		$stmt->bind_param("sssss", $firstName, $lastName, $account_id_to, $amount, $purpose);
		$result = $stmt->execute();
		$stmt->close();
		
		$new_transaction_id = $this->conn->insert_id;
		$res = $this->createAccountTransaction($new_transaction_id, $account_id_to, $account_id_from);
		
		if ($result) {
			// transaction added successfully
			return $new_transaction_id;
		} else {
			// transaction failed to add
			return NULL;
		}
	}

    /**
     * Fetching all messages from client
     * @param String $client_id id of the client
     */
    public function getAllTransactions($client_id, $account_id) {
        $stmt = $this->conn->prepare("SELECT t.* FROM transactions t, accounts a, account_transaction_matches atm WHERE a.client_id = ? AND atm.account_id_from = ? AND t.id = atm.transaction_id");
		$stmt->bind_param("ss", $client_id, $account_id);
        $stmt->execute();
        
        /* Store the result (to get properties) */
   		$stmt->store_result();
   		
  		/* Bind the result to variables */
   		$stmt->bind_result($id, $account_id_to, $amount, $purpose, $date_stamp);
   		
   		$response["transactions"] = array();
   		            
   		while ($stmt->fetch()) 
		{
                    $tmp = array();
                    $tmp["id"] = $id;
					$tmp["account_id_to"] = $account_id_to;
                    $tmp["amount"] = $amount;
                    $tmp["purpose"] = $purpose;
                    $tmp["date_stamp"] = $date_stamp;
                    array_push($response["transactions"], $tmp);
                }
        
        $stmt->close();
        return $response["transactions"];
    }
    

    /* ------------- `account_transaction_matches` table method ------------------ */

    /**
     * Function to assign a transaction to an account
     * @param String $transaction_id id of the transaction
     * @param String $account_id_to id of the receiving account
     * @param String $account_id_from id of the sending account
     */
    public function createAccountTransaction($transaction_id, $account_id_to, $account_id_from) {
        $stmt = $this->conn->prepare("INSERT INTO account_transaction_matches(transaction_id, account_id_to, account_id_from) values(?, ?, ?)");
        $stmt->bind_param("iii", $transaction_id, $account_id_to, $account_id_from);
        $result = $stmt->execute();

        if (false === $result) {
            die('execute() failed: ' . htmlspecialchars($stmt->error));
        }
        $stmt->close();
        return $result;
    }

}

?>