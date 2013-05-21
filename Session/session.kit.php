<?php

namespace DevKit;
{
	class Session
	{
		
		/**
		 * Start a new Session
		 */
		public function __construct()
		{
			session_start();
			
		}
			
		public static function id()
		{
			return session_id();
		}
		
		public static function destroy()
		{
			//Session Started
			if(session_id())
			{
				//session_unset();
				session_destroy();
				//$_SESSION = array();
			}
		}
		
		protected static function started(){}
		
		public static function restart()
		{
			self::destroy();
			
		}
		
		
		/**
		 * Format Session Index Name to allow un-case-sensative feature
		 * 
		 * @param $name string the index name
		 * 
		 * @return string reformatted index
		 * 
		 * @since 0.1
		 */
		protected static function format($name){return strtoupper(str_replace(' ', '_', $name));}
		
		
		/**
		 * 
		 */
		public static function set($name, $value)
		{
			return session_register(self::format($name), $value);
		}
		
		
		/**
		 * Get value of a specific session variable name
		 * 
		 * @
		 */
		public static function get($name){}
		
		public static function delete($name)
		{
			if(self::exists($name))
			{
				return session_unregister(self::format($name));
			}
			return false;
		}
		
		public static function exists($name)
		{
			return (isset($_SESSION[self::format($name)])) ? true : false;
		}
	}
}


