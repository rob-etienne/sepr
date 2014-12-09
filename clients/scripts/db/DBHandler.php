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
        if (!$this->isClientExists($email)) 
		{
            // Generating password hash
				
			// PASSWORD_DEFAULT will always be used to apply the strongest supported hashing algorithm.
			// PHP will choose the encryption to use and it might change in the future 
			// at time of writing it will be using CRYPT_BLWFISH, salt and type of encryption is stores together 
			// with the hash it self
			$options = [ 
							'cost' => 11,
							'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM),
						];
	
			$password_hash = password_hash($password, PASSWORD_DEFAULT, $options);		

            // insert query
            $stmt = $this->conn->prepare("INSERT INTO clients(first_name, last_name, password_hash, email) values(?, ?, ?, ?)");
            
			$stmt->bind_param("ssss", $firstName, $lastName, $password_hash, $email);

            $result = $stmt->execute();

            $stmt->close();

            // Check for successful insertion
            if ($result) 
			{
                // Client successfully inserted
				return true;
            } 
			else 
			{
                // Failed to create client
				return false;
            }
        } 
		else 
		{
            // Client with same email already existed in the db
			return false;
        }
		return false;
    }

    /**
     * Checking for duplicate client by email address
     * @param String $email email to check in db
     * @return boolean
     */
    private function isClientExists($email) 
	{	
		$stmt = $this->conn->prepare("SELECT id from client WHERE email = ?");	
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
    public function clientIsActive($email) 
	{	
        $stmt = $this->conn->prepare("SELECT active from clients WHERE email = ?");	
        $stmt->bind_param("s", $email);
        $stmt->execute();
		$stmt->bind_result($active);
        $stmt->store_result();
		$stmt->fetch();
        $stmt->close();
		
		if ($active == 1) 
		{
			return true; 
		} else 
		{
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
		
        if ($stmt->num_rows > 0) 
		{
            // Found client with the email
            // Now verify the password

            $stmt->fetch();
			
            if (password_verify($password, $password_hash)) 
			{
                // User password is correct
                return true;
            } 
			else 
			{
                // user password is incorrect
                return false;
            }
        } 
		else 
		{
            $stmt->close();
            // client not existed with the email
            return true;
        }
    }
	
	/**
    * Updating client login
    */
    public function updateClientLogin($email) 
	{
		$timestamp = date('Y-m-d H:i:s');
		$stmt = $this->conn->prepare("update clients set active = '1', last_sign_in_stamp = ? where email = ?");
		$stmt->bind_param("ss", $timestamp, $email);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }
	
	/**
    * Updating client logout
    */
    public function updateClientLogout($email) 
	{
		$timestamp = date('Y-m-d H:i:s');
		$stmt = $this->conn->prepare("update clients set active = '0', last_sign_in_stamp = ? where email = ?");
		$stmt->bind_param("ss", $timestamp, $email);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }
	
	 /**
     * Get client id
     */
    public function getClientId($email) {	
		$stmt = $this->conn->prepare("select id from clients where email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($id);
        $stmt->fetch();
        $stmt->close();
        return $id;
    }


    /* ------------- `accounts` table method ------------------ */
	
    /**
     * Updating account
     * @param String $client_id id of the client
     * @param String $account_id id of the account
     * @param String $balance balance of account
     * @param String $name account name
     */
    public function updateAccount($client_id, $account_id, $balance) {
        $stmt = $this->conn->prepare("UPDATE accounts a SET a.balance = ? WHERE a.id = ? AND a.client_id = ?");
        $stmt->bind_param("sss", $balance, $account_id, $client_id);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }
    
    /**
     * fetching account balance
     * @param String $client_id id of the client
     * @param String $account_id id of the account
     */
    public function FetchAccountBalance($account_id) {
        $stmt = $this->conn->prepare("Select a.balance From accounts a WHERE a.id = ?");
        $stmt->bind_param("s", $account_id);
        $stmt->execute();
        $stmt->bind_result($balance);
        $stmt->fetch();
        $stmt->close();
        return $balance;
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
    
    /**
      * Fetching one client account by account ID
      * @param Int $Account_Id id of an account
      */
    public function FindAccountById($account_id)
    {
        $stmt = $this->conn->prepare("SELECT a.id FROM accounts a WHERE a.id = ?");
        $stmt->bind_param("i", $account_id);
        $stmt->execute();
        
        /* Store the result (to get properties) */
   		$stmt->store_result();
   		
  		/* Bind the result to variables */
   		$stmt->bind_result($id);
   		
   		$response["accounts"] = array();
   		            
   		while ($stmt->fetch()) 
		{
                    $tmp = array();
                    array_push($response["accounts"], $tmp);
                }
        
        $stmt->close();
        return $response["accounts"];
    }
    
    
    /**
    * Fetch a clients ID by Account Number
    * @param int $Account_Id id of an account
    */
    public function GetClientIdFromAccNr($account_id)
    {
        $stmt = $this->conn->prepare("Select a.client_id From accounts a WHERE a.id = ?");
        $stmt->bind_param("i", $account_id);
        $stmt->execute();
        $stmt->bind_result($client_id);
        $stmt->fetch();
        $stmt->close();
        return $client_id;
    }
    

    /* ------------- `messages` table method ------------------ */

    /**
     * Adding new message
     * @param String $client_id id of the client
     * @param String $subject subject of message
     * @param String $message actual message
     */
    public function addMessage($client_id, $subject, $message, $attachment_url ) 
	{	
		// insert query
		$stmt = $this->conn->prepare("INSERT INTO messages(subject, message, attachment_url, client_id) VALUES(?,?,?,?)");
		$stmt->bind_param("sssi", $subject, $message, $attachment_url, $client_id);
		$result = $stmt->execute();
		$stmt->close();
        return $result;
	}
    
    /**
     * Adding new message
     * @param String $client_id id of the client
     * @param String $subject subject of message
     * @param String $message actual message
     */
    public function addMessageWithoutURL($client_id, $subject, $message ) 
	{
		// insert query
		$stmt = $this->conn->prepare("INSERT INTO messages(subject, message, client_id) VALUES(?,?,?)");
		$stmt->bind_param("ssi", $subject, $message, $client_id);
		$result = $stmt->execute();
		$stmt->close();
		return $result;
	}
    /**
     * Fetching all messages from client
     * @param String $client_id id of the client
     */
    public function getAllMessages($client_id) {
        $stmt = $this->conn->prepare("SELECT m.* FROM messages m WHERE m.client_id = ? ORDER BY m.submitted_stamp DESC");
		$stmt->bind_param("i", $client_id);
        $stmt->execute();
        
        /* Store the result (to get properties) */
   		$stmt->store_result();
   		
  		/* Bind the result to variables */
   		$stmt->bind_result($id, $subject, $message, $attachment_url, $submitted_stamp, $client_id);
   		
   		$response["messages"] = array();
   		            
   		while ($stmt->fetch()) 
		{
                    $tmp = array();
                    $tmp["id"] = $id;
                    $tmp["subject"] = $subject;
                    $tmp["message"] = $message;
                    $tmp["attachment_url"] = $attachment_url;
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
    public function addTransaction($account_id_from, $account_id_to, $amount, $purpose ) {
		
		// insert query
		$stmt = $this->conn->prepare("INSERT INTO transactions(account_id_to, amount, purpose) VALUES(?,?,?)");
		$stmt->bind_param("iss", $account_id_to, $amount, $purpose);
		$result = $stmt->execute();
		$stmt->close();
		
		$new_transaction_id = $this->conn->insert_id;
		$result = $this->createAccountTransaction($new_transaction_id, $account_id_to, $account_id_from);
		
		if ($result) 
		{
			// transaction added successfully
			return $new_transaction_id;
		} 
		else 
		{
			// transaction failed to add
			return NULL;
		}
	}

    /**
     * Fetching all messages from client
     * @param String $client_id id of the client
     */
    public function getAllTransactions($client_id, $account_id) {
        $stmt = $this->conn->prepare("SELECT DISTINCT t.* FROM transactions t, accounts a, account_transaction_matches atm WHERE a.client_id = ? AND atm.account_id_from = ? AND t.id = atm.transaction_id ORDER BY t.date_stamp DESC");
		$stmt->bind_param("ii", $client_id, $account_id);
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
                    $tmp["account_id_from"] = $account_id;
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

        if (false === $result) 
		{
            die('execute() failed: ' . htmlspecialchars($stmt->error));
        }
        $stmt->close();
        return $result;
    }

}

?>