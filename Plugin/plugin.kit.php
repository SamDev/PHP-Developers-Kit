<?php

/**
 * Plugins System, Allowing you to implement a plugins feature within your Application easily 
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
namespace DevKit {
	
	/**
	 * Plugins Class which resposible for managing the whole plugins system, 
	 * allowing you to configure how system works as well
	 * 
	 * 
	 */
	class Plugins
	{
		
		/**
		 * Holds the actual plugins path to read from
		 * 
		 * @property string
		 */
		private $pluginsPath;
		
		
		/**
		 * Plugins Class Constructor Excutes automatically once an instance initiated. 
		 * 
		 * @param string $path the path where plugins actually located
		 * 
		 * @return void
		 */
		public function __construct($path = false)
		{
			
		}
		
		
		/**
		 * Excutes Automatically once the instance destroyed or at the script end
		 * @return void
		 */
		public function __destruct()
		{
			//Clear everything up
		}
		
		public function __get(){}
		
		public function __set(){}
	
	}
	
	interface IPlugin
	{
		public function install();
		
		public function uninstall();
	}
	
}
