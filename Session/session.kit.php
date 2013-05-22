<?php

/**
 * Session Manager Class 
 * 
 * @author SamDev
 * 
 * @license http://creativecommons.org/licenses/by/3.0/deed.en_US  Creative Commons Attribution 3.0
 * 
 * @copyright 2013 SamDev
 * 
 * @package PHP Developers Kit
 * 
 * @version 0.1
 * 
 */
class Session
{
	
	/**
	 * Class Constructor for Starting a new Session
	 * 
	 * @access public
	 * 
	 * @param $forceNew boolean if true then any previous sessions will be ended before starting a new one
	 * 
	 * @since 0.1
	 */
	public function __construct($forceNew = true)
	{
		//Check if there is a session started before
		if(session_id() != '' )
		{
			//Force a new Id
			if($forceNew) {session_regenerate_id(true);}
		}
		else
		{
			//No Session yet, lets start it
			session_start();
		}	
	}
    
	/*******************************************/
		
	public static function id()
	{
		return session_id();
	}

	/*******************************************/
		
	public static function destroy()
	{
		//Session Started
		if(session_id())
		{
			session_destroy();
			$_SESSION = array();
			var_dump(session_name());
		}
	}
    
	/*******************************************/
	
	/**
	 * Check whether session stated or no
	 * 
	 * @return boolean
	 * 
	 * @access protected
	 * 
	 * @since 0.1
	 */
	protected static function started()
	{
		return (session_id() == "") ? false : true;
	}
	
	/*******************************************/
    
	public static function restart()
	{
		session_regenerate_id(true);
		self::destroy();
		
	}
	
	/*******************************************/
	
	/**
	 * Format Session Index Name to allow un-case-sensative feature
	 * 
	 * @param $name string the index name
	 * 
	 * @return string reformatted index
	 * 
	 * @access protected
	 * 
	 * @since 0.1
	 */
	protected static function format($name){return strtoupper(str_replace(' ', '_', $name));}
    
	/*******************************************/
	
	/**
	 * 
	 */
	public static function set($name, $value)
	{
		return session_register(self::format($name), $value);
	}
	
	/*******************************************/
	
	/**
	 * Get value of a specific session variable name
	 * 
	 * @param $name session variable name
	 * 
	 * @access public 
	 * 
	 * @return mixed
	 * 
	 * @since 0.1
	 */
	public static function get($name)
	{
		
	}
	
	/*******************************************/
	
	public static function delete($name)
	{
		if(self::exists($name))
		{
			return session_unregister(self::format($name));
		}
		return false;
	}
    
	/*******************************************/
	
	public static function exists($name)
	{
		return (isset($_SESSION[self::format($name)])) ? true : false;
	}
    
	/*******************************************/
}



