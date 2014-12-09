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
     * Checking for duplicate advisor by employee nr
     * @param String $empNr employee nr to check in db
     */
    private function isAdvisorExists($empNr) 
	{	
		$stmt = $this->conn->prepare("SELECT id from advisor WHERE employee_nr = ?");	
        $stmt->bind_param("i", $empNr);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }
	
	 /**
     * Checking if advisor is already signed in
     * @param String $empNr employee nr to check in db
     * @return boolean is active true/false
     */
    public function advisorIsActive($empNr) 
	{	
        $stmt = $this->conn->prepare("SELECT active from advisors WHERE employee_nr = ?");	
        $stmt->bind_param("i", $empNr);
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
     * Checking advisor login
     * @param String $empNr advisor login employee nr
     * @param String $password advisor login password
     * @return boolean advisor login status success/fail
     */
    public function checkAdvisorLogin($empNr, $password) {
        
		// fetching advisor by employee nr
        $stmt = $this->conn->prepare("SELECT password_hash FROM advisors WHERE employee_nr = ?");

        $stmt->bind_param("i", $empNr);

        $stmt->execute();

        $stmt->bind_result($password_hash);

        $stmt->store_result();
		
        if ($stmt->num_rows > 0) 
		{
            // Found advisor with the employee nr
            // Now verify the password

            $stmt->fetch();
			
            if (password_verify($password, $password_hash)) 
			{
                // Advisor password is correct
                return true;
            } 
			else 
			{
                // Advisor password is incorrect
                return false;
            }
        } 
		else 
		{
            $stmt->close();
            // Advisor not existed with the employee nr
            return true;
        }
    }
	
	/**
    * Updating advisor login
    */
    public function updateAdvisorLogin($empNr) 
	{
		$timestamp = date('Y-m-d H:i:s');
		$stmt = $this->conn->prepare("update advisors set active = '1', last_sign_in_stamp = ? where employee_nr = ?");
		$stmt->bind_param("si", $timestamp, $empNr);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }
	
	/**
    * Updating advisor logout
    */
    public function updateAdvisorLogout($empNr) 
	{
		$timestamp = date('Y-m-d H:i:s');
		$stmt = $this->conn->prepare("update advisors set active = '0', last_sign_in_stamp = ? where employee_nr = ?");
		$stmt->bind_param("si", $timestamp, $empNr);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }
	
	 /**
     * Get advisor id
     */
    public function getAdvisorId($empNr) {	
		$stmt = $this->conn->prepare("select id from advisors where employee_nr = ?");
        $stmt->bind_param("i", $empNr);
        $stmt->execute();
        $stmt->bind_result($id);
        $stmt->fetch();
        $stmt->close();
        return $id;
    }
    
	/**
     * Get associated clients from advisor
     */
   	public function getAllClients($empNr) {
        $stmt = $this->conn->prepare("SELECT c.id, c.first_name, c.last_name FROM clients c, advisors a WHERE c.advisor_id = a.id AND a.employee_nr = ?");
        $stmt->bind_param("i", $empNr);
        $stmt->execute();
   		$stmt->store_result();
   		$stmt->bind_result($id, $first_name, $last_name);
   		
   		$response["clients"] = array();
   		            
   		while ($stmt->fetch()) 
		{
                    $tmp = array();
                    $tmp["id"] = $id;
                    $tmp["first_name"] = $first_name;
                    $tmp["last_name"] = $last_name;
                    array_push($response["clients"], $tmp);
                }
        
        $stmt->close();
        return $response["clients"];
    }


    /* ------------- `accounts` table method ------------------ */

	/**
     * Adding new account
     * @param String $client_id client id to whom account should be created for
     * @param String $name text for account
     */
    public function addAccount($client_id, $name) {
		
		$new_account_id = $this->generateAccountId();
		
		// insert query
		$stmt = $this->conn->prepare("INSERT INTO accounts(id, name, client_id) VALUES(?,?,?)");
		$stmt->bind_param("isi", $new_account_id, $name, $client_id);
		$result = $stmt->execute();
		$stmt->close();

		if ($result) 
		{
			// account added successfully
			return $new_account_id;
		} else {
			// account failed to add
			return NULL;
		}
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
     * Fetching all messages from employee
     * @param String $empNr id of the employee
     */
    public function getAllMessages($empNr) {
        $stmt = $this->conn->prepare("SELECT m.* FROM messages m, advisors a, clients c WHERE m.client_id = c.id AND c.advisor_id = a.id AND a.employee_nr = ? ORDER BY m.submitted_stamp DESC");
		$stmt->bind_param("i", $empNr);
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
}

?>