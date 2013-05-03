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
	 * @version 0.1
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
		 * Hooked Events Array
		 * Event slug as key and actions as array element
		 * 
		 * @property mixed[] Array $hookedEvents
		 */
		 private static $hookedEvents = array();
		 
		
		/**
		 * Hooked Filters Array
		 * 
		 * @property mixed[] Array $hookedFilters
		 *  
		 * @access private
		 */
		 private static $hookedFilters;
		 
		 
		 /**
		  * 
		  */
		  
		  private $secretValue = 'Password ;)';
		
		/**********************************/
		
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
		
		/**********************************/
		
		/**
		 * Excutes Automatically once the instance destroyed or at the script end
		 * @return void
		 */
		public function __destruct()
		{
			//Clear everything up
		}
		
		/**********************************/
		
		public function __get($property){}
		
		/**********************************/
		
		public function __set($property, $newValue){}
		
		/**********************************/
		
		/**
		 * Reformat Hook (Event/Filter) Name in a nicer way and suitable to be a key
		 * 
		 * @param string $hook The Actual Hook Name
		 * 
		 * @return string $hook Formated Name
		 */
		static private function formatHook($hook)
		{
			return strtolower(str_replace(' ', '_', $hook));
		}
		
		/**********************************/
		
		/**
		 * Sort List of Actions depends on Priority
		 * 
		 * @param mixed $x the First Element to compare
		 * 
		 * @param mixed $y the Second Element to compare
		 * 
		 * @return integer
		 */
		static public function sortActions($x, $y)
		{
			if($x['priority'] == $y['priority']){return 0;}
			return ($x > $y) ? 1 : -1;
		}
		
		/**********************************/
		
		/**
		 * Create a Hash for Action
		 * 
		 * @todo Create a Hash for each Action to be able to be removed
		 */
		 static private function hashAction($action)
		 {
		 	
		 }
		 
		
		
		/**********************************/
		
		/**
		 * Hook an Action to and Event to be called once the event happen
		 * 
		 * @param string $event The Event Name
		 * 
		 * @param mixed $action the Actions to be called whether function name or class method
		 * 
		 * @param int $priority Defines in which priority you want this action to be called 
		 * 
		 * @return boolean
		 */
		static public function onEvent($event, $action, $priority = 0)
		{
			//Reformat Event Name
			$event = self::formatHook($event);
			
			//Check if it's Already Hooked Before
			if(!isset(self::$hookedEvents[$event]))
			{self::$hookedEvents[$event] = array();}
			
			//Register it 
			$index = count(self::$hookedEvents[$event]);
			self::$hookedEvents[$event][$index] = array();
			self::$hookedEvents[$event][$index]['action'] = $action;
			self::$hookedEvents[$event][$index]['priority'] = intval($priority);
			
			return true;
		}
		
		/**********************************/
		
		/**
		 * Declare Than an event accured and trigger all actions registered to it
		 * 
		 * @param string $event Event Nice Name
		 * 
		 * @param mixed $args Any args to pass it to registered actions (Optional)
		 * 
		 * @return integer The count of processed Actions
		 */
		static public function doEvent($event, $args = false)
		{
			$event = self::formatHook($event);
			//Check if no actions registered
			if(!isset(self::$hookedEvents[$event]) || count(self::$hookedEvents[$event]) == 0 )
			{return 0;}
			
			$actionsProcessed = 0;
			//Sort Actions depends on Priority
			usort(self::$hookedEvents[$event], array(__CLASS__, 'sortActions'));
			
			//Loop Throw Hooked 
			foreach(self::$hookedEvents[$event] as $index => $data)
			{
				if(is_callable($data['action']))
				{
					call_user_func($data['action'], $args);
					$actionsProcessed++;
				}
			}
		}
		
		/**********************************/
		
		/**
		 * Hook an action to be excuted on a certain Fitler
		 * 
		 * @param string $filter The actual filter name, not case sensative
		 * 
		 * @param callback $action the action which will be called to filter
		 * 
		 * @param integer $priority The proiroty where this fitler should run, the higher priorty the eariler excute
		 * 
		 * @return boolean
		 * 
		 */
		static public function onFilter($filter, $action, $priority = 0)
		{
			//Reformat Filter Name
			$event = self::formatHook($filter);
			
			//Check if it's Already Hooked Before
			if(!isset(self::$hookedFilters[$filter]))
			{self::$hookedFilters[$filter] = array();}
			
			//Register it 
			$index = count(self::$hookedFilters[$filter]);
			self::$hookedFilters[$filter][$index] = array();
			self::$hookedFilters[$filter][$index]['action'] = $action;
			self::$hookedFilters[$filter][$index]['priority'] = intval($priority);
			
			return true;
		}
		
		/**********************************/
		
		/**
		 * 
		 */
		static public function doFilter($filter, $context, $args = null)
		{
			$filter = self::formatHook($filter);
			
			//Check is nothing hooked
			if(!isset(self::$hookedFilters[$filter]) || count(self::$hookedFilters[$filter]) == 0){return $context;}
			
			//Sort Filters Depending on Priorities
			usort(self::$hookedFilters[$filter], array(__CLASS__, 'sortActions'));
			//Loop Through Hooked Fitlers
			foreach(self::$hookedFilters[$filter] as $index => $data)
			{
				if(is_callable($data['action']))
				{
					if($args === null){$context = call_user_func($action, $context);}
					else{$context = call_user_func($action, $context, $args);}
				}
			}
			
			return $context;
		}
		
		 
	
	}
	
	
	/*************************************************************************/
	
	interface IPlugin
	{

		public	function install();
		
		public function uninstall();
		
	}
	
	abstract class APlugin
	{
		
		abstract public function install();
		
		abstract public function uninstall();
		
		public function getName(){echo 'hello';}
	}
	
}
