<?php

class Log
{
	const LOGIN_DIR = '../logs/logins.log';
	const MESSAGE_DIR = '../logs/messages.log';
	
    /*
	Message Logs
	*/
    public function messageFailure($msg, $emplyeeNr)
    {
		$date = date('d.m.Y h:i:s');
		$log = "FAILED:	".$msg."	|	Date:	".$date."	|	EmployeeNr:	".$emplyeeNr."	|IP:	".$_SERVER['REMOTE_ADDR']."\n";
		error_log($log, 3, self::MESSAGE_DIR);
    }
	
	public function messageSuccess($emplyeeNr)
    {
		$date = date('d.m.Y h:i:s');
		$log = "SUCCESS	|	Date:	".$date."	|	EmployeeNr:	".$emplyeeNr."	|IP:	".$_SERVER['REMOTE_ADDR']."\n";
		error_log($log, 3, self::MESSAGE_DIR);
    }
	
	/*
	Login Logs
	*/
	public function loginSuccess($emplyeeNr)
    {
		$date = date('d.m.Y h:i:s');
		$log = "SUCCESS	|	Date:	".$date."	|	EmployeeNr:	".$emplyeeNr."	|	IP:	".$_SERVER['REMOTE_ADDR']."\n";
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
	public function logoutSuccess($emplyeeNr)
    {
		$date = date('d.m.Y h:i:s');
		$log = "SUCCESS	|	Date:	".$date."	|	EmployeeNr:	".$emplyeeNr."	|	IP:	".$_SERVER['REMOTE_ADDR']."\n";
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