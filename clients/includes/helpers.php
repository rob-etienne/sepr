<?php
class Helpers
{
	/*
		GENERAL	- START
	*/ 
	
	// generally for all clean ups
	public static function cleanData($data)
    {
		$data = trim($data);
   		$data = stripslashes($data);
   		$data = htmlspecialchars($data);
   		return $data;
    }
	
	// for client id, employee nr & account nr
	public static function validateInteger($data)
	{
		$cleanedUp = self::cleanData($data);
		
		/*
			integer
			4 digits
		*/
		if(empty($cleanedUp))
		{
			return false;
		}
		else if(strlen($cleanedUp) > 4)
		{
			return false;
		}
		else if(!is_numeric($cleanedUp))
		{
			return false;
		}
		else
		{
			return true;
		}	
		
		// add reg ex check
	}
	
	// for email addresses	
	public static function validateEmail($data)
	{
		$cleanedUp = self::cleanData($data);
			
		if(empty($cleanedUp))
    	{
			return false;
		}
		else if (function_exists('filter_var') && !filter_var($cleanedUp, FILTER_VALIDATE_EMAIL)) 
		{
       		return false;
		}
		else if(!preg_match("/[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+\.[a-zA-Z]{2,4}/", $cleanedUp))
		{
			return false;
		}
		else if(function_exists('checkdnsrr') && !checkdnsrr(preg_replace('/^[^@]++@/', '', $cleanedUp), 'MX'))
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	/*
		GENERAL	- END
	*/ 
	
	////////////////////////////////////////////
	
	/*
		LOGIN - START
	*/
	
	// for client / advisor password
	public static function validatePassword($data)
	{
		$cleanedUp = trim($data);	
		
		// see pw requirements
		
		if(empty($cleanedUp))
    	{
			return false;
		}	
		else
		{
			return true;
		}
		
		// add RegEx check for pw
	}
	
	/*
		LOGIN - END
	*/ 
	
	////////////////////////////////////////////
	
	/*
		REGISTRATION - START
	*/
	
	// for client (first & last) name
	public static function validateName($data)
	{
		$cleanedUp = self::cleanData($data);
		
		/*
			only characters
			within range 3 to 50
			start with capital letter
		*/
		
		if(empty($cleanedUp))
    	{
			return false;
		}	
		else if(!strlen($cleanedUp) >= 3 && !strlen($cleanedUp) < 50)
		{
			return false;
		}
		else if (!preg_match("/^[A-Z]+[a-z]{2,49}/",$cleanedUp)) 
		{
       		return false;
     	}
		else
		{
			return true;
		}
	}
	
	/*
		REGISTRATION - END
	*/ 
	
	////////////////////////////////////////////
	
	/*
		TRANSACTIONS - START
	*/ 
	
	// for amount within transaction
	public static function validateAmount($data)
	{
		$cleanedUp = self::cleanData($data);
		
		/*
			decimal
			within range 5.00 € to 99999.99 €
		*/
		
		if(empty($cleanedUp))
		{
			return false;
		}
		else if(!is_numeric($cleanedUp))
		{
			return false;
		}
		else if(!preg_match("/(?:\d{1,5})?\.(?:\d{1,2})/", $cleanedUp))
		{
			return false;
		}
		else if($cleanedUp < 5.00 && $cleanedUp > 999999.99)
		{
			return false;
		}
		else
		{
			return true;
		}
		
		// revise
		
	}
	
	// for purpose of transaction
	public static function validatePurpose($data)
	{
		$cleanedUp = self::cleanData($data);
		
		/*
			only characters, numbers and whitespaces
			within range 5 to 100
			start with capital letter
		*/
		
		if(empty($cleanedUp))
    	{
			return false;
		}	
		else if(!strlen($cleanedUp) >= 5 && !strlen($cleanedUp) <= 100)
		{
			return false;
		}
		else if (!preg_match("/^[A-Z]+[a-z]+[A-Za-z0-9\s.:]{4,101}/",$cleanedUp)) 
		{
       		return false;
     	}
		else
		{
			return true;
		}
	}
	
	/*
		TRANSACTIONS - END
	*/ 
	
	////////////////////////////////////////////
	
	/*
		MESSAGES - START
	*/
	
	// for subject in messages
	public static function validateSubject($data)
	{
		$cleanedUp = self::cleanData($data);
		
		/*
			only characters, numbers and whitespaces
			within range 5 to 50
			start with capital letter
		*/
		
		if(empty($cleanedUp))
    	{
			return false;
		}	
		else if(!strlen($cleanedUp) >= 5 && !strlen($cleanedUp) <= 50)
		{
			return false;
		}
		else if (!preg_match("/^[A-Z]+[a-z]+[A-Za-z0-9\s.:]{4,51}/",$cleanedUp)) 
		{
       		return false;
     	}
		else
		{
			return true;
		}
		
	}
	
	// for the actual messages within send message
	public static function validateMessage($data)
	{
		$cleanedUp = self::cleanData($data);
		
		/*
			only characters, numbers and whitespaces
			within range 5 to 150
			start with capital letter
		*/
		
		if(empty($cleanedUp))
    	{
			return false;
		}	
		else if(!strlen($cleanedUp) >= 5 && !strlen($cleanedUp) <= 150)
		{
			return false;
		}
		else if (!preg_match("/^[A-Z]+[a-z]+[A-Za-z0-9\s.:]{4,151}/",$cleanedUp)) 
		{
       		return false;
     	}
		else
		{
			return true;
		}
		
	}
	
	// for file upload within send message
	public static function validateFileUpload($data)
	{
		$cleanedUp = self::cleanData($data);
		
		/*
			only image formats (png, jpg, gif)
			file size max 5mb
		*/
	}
	
	/*
		MESSAGES - END
	*/ 
}
?>