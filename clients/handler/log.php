<?php

class log
{
    const TRANSACTION_DIR = '../logs/transactions.log';
	const LOGIN_DIR = '../logs/logins.log';
	const REGISTRATION_DIR = '../logs/registrations.log';
	const MESSAGE_DIR = '../logs/messages.log';
	
	/*
	Transaction Logs
	*/
    public function transactionFailure($msg, $email)
    {
		$date = date('d.m.Y h:i:s');
		$log = "FAILED:	".$msg."	|	Date:	".$date."	|	User:	".$email."	|IP:	".$_SERVER['REMOTE_ADDR']."\n";
		error_log($log, 3, self::TRANSACTION_DIR);
    }
	
	public function transactionSuccess($msg, $email)
    {
		$date = date('d.m.Y h:i:s');
		$log = "SUCCESS	|	Date:	".$date."	|	User:	".$email."	|IP:	".$_SERVER['REMOTE_ADDR']."\n";
		error_log($log, 3, self::TRANSACTION_DIR);
    }
	
	/*
	Registration Logs
	*/
	public function registrationFailure($msg)
    {
		$date = date('d.m.Y h:i:s');
		$log = "FAILED:	".$msg."	|	Date:	".$date."	|	IP:	".$_SERVER['REMOTE_ADDR']."\n";
		error_log($log, 3, self::REGISTRATION_DIR);
    }
	
	public function registrationSuccess($email)
    {
		$date = date('d.m.Y h:i:s');
		$log = "SUCCESS	|	Date:	".$date."	|	EMAIL:	".$email."	|	IP:	".$_SERVER['REMOTE_ADDR']."\n";
		error_log($log, 3, self::REGISTRATION_DIR);
    }
	
    /*
	Message Logs
	*/
    public function messageFailure($msg, $email)
    {
		$date = date('d.m.Y h:i:s');
		$log = "FAILED:	".$msg."	|	Date:	".$date."	|	User:	".$email."	|IP:	".$_SERVER['REMOTE_ADDR']."\n";
		error_log($log, 3, self::MESSAGE_DIR);
    }
	
	public function messageSuccess($email)
    {
		$date = date('d.m.Y h:i:s');
		$log = "SUCCESS	|	Date:	".$date."	|	User:	".$email."	|IP:	".$_SERVER['REMOTE_ADDR']."\n";
		error_log($log, 3, self::MESSAGE_DIR);
    }
	
	/*
	Login Logs
	*/
	public function loginSuccess($email)
    {
		$date = date('d.m.Y h:i:s');
		$log = "SUCCESS	|	Date:	".$date."	|	EMAIL:	".$email."	|	IP:	".$_SERVER['REMOTE_ADDR']."\n";
		error_log($log, 3, self::LOGIN_DIR);
    }
	
	public function loginFailure($msg)
    {
		$date = date('d.m.Y h:i:s');
		$log = "FAILED:	".$msg."	|	Date:	".$date."	|	IP:	".$_SERVER['REMOTE_ADDR']."\n";
		error_log($log, 3, self::LOGIN_DIR);
    }
	
	/*
	Logout Logs
	*/
	public function logoutSuccess($email)
    {
		$date = date('d.m.Y h:i:s');
		$log = "SUCCESS	|	Date:	".$date."	|	EMAIL:	".$email."	|	IP:	".$_SERVER['REMOTE_ADDR']."\n";
		error_log($log, 3, self::LOGIN_DIR);
    }
	
	public function logoutFailure($msg)
    {
		$date = date('d.m.Y h:i:s');
		$log = "FAILED:	".$msg."	|	Date:	".$date."	|	IP:	".$_SERVER['REMOTE_ADDR']."\n";
		error_log($log, 3, self::LOGIN_DIR);
    }
}


?>