<?php

/**
 * Form Handler, allowing you to handle forms submitted and execute and handler for it
 * 
 * @author SamDev
 * 
 * @license http://creativecommons.org/licenses/by/3.0/deed.en_US  Creative Commons Attribution 3.0
 * 
 * @copyright 2013 SamDev
 * 
 * @package PHP Developers Kit
 * 
 * @subpackage FormKit
 * 
 * @version 0.1
 * 
 */
class FormHandler
{
	
	/**
	 * Define whether engine started or not
	 * 
	 * @property boolean
	 * 
	 * @access private
	 */
	 private static $started = false;
	
	
	/**
	 * Current Form Name ({POST})
	 * 
	 * @property mixed
	 * 
	 * @access private
	 */
	 private static $currentPost = false;
	
	
	/**
	 * Current Form Name ({GET})
	 * 
	 * @property mixed
	 * 
	 * @access private
	 */
	 private static $currentGet = false;
	
	
	/**
	 * Registered Forms along with handlers
	 * 
	 * @property array
	 * 
	 * @access private
	 */
	 private static $registeredForms = array();
	 
	/**
	 * POST-Form Action Field Name
	 * 
	 * @property string
	 * 
	 * @access private
	 */ 
	 private static $actionName = 'action'; 
	 
	 
	/**
	 * POST-Form Action Field Name
	 * 
	 * @property string
	 * 
	 * @access private
	 */ 
	 private static $postActionName = 'FORM_ACTION';
	 
	 /**
	 * GET-Form Action Field Name
	 * 
	 * @property string
	 * 
	 * @access private
	 */ 
	 private static $getActionName = 'action';
	 
	 
	 /**
	  * Session Variable Name, where we save nounces
	  */
	  const SESSION_NAME = 'FORMS_NOUNCE';
	  
	  /**
	  * Session Variable Name, where we save nounces
	  */
	  const OLD_SESSION_NAME = 'OLD_FORMS_NOUNCE';
	 
	
	/**
	 * Constructor, Runs automatically when init. this class
	 * Start Listening for $_POST and $_GET and handle'em
	 * 
	 * @access public
	 * 
	 * @since 0.1
	 * 
	 * @return void 
	 */
	public function __construct()
	{
		
	}
	
	/*************************************************************/
	
	/**
	 * A Static method which run the whole engine and make sure everything is fine
	 * 
	 * 1. Start Session if not started in order to save Sessions later
	 * 
	 * 2. Get the current Submitted Post/Get Form
	 * 
	 * @access public
	 * 
	 * @return void
	 * 
	 * @since 0.1
	 */
	 public static function start()
	 {
	 	if(self::$started){return ture;}
	 	//Lets check on Session
	 	if(strlen(session_id()) == 0) {session_start();}
	 	//Lets Gather Information Needed
	 	self::$currentPost = (isset($_POST[self::$actionName])) ? $_POST[self::$actionName] : false;
		self::$currentGet  = (isset($_GET[self::$actionName])) ? $_GET[self::$actionName] : false;
		register_shutdown_function(array(__CLASS__, '_updateSession'));
		self::$started = true;
	 }
	 
	 /*************************************************************/
	
	/**
	 * Register a handler for POST/GET form
	 * 
	 * 1. Check whether it the actual form which submitted or not, if not then we skip
	 * 
	 * 2. check if handler is callable
	 * 
	 * 3. Prepare submitted Data and send it to the handler and run it
	 * 
	 * @access public
	 * 
	 * @param $formName string Form Identifier, which is the value if a hidden element named form_action(default)
	 * 
	 * @param $handler callback Runs when form Submitted
	 * 
	 * @param $type string form type whether post or get, Optional (default : post)
	 * 
	 * @since 0.1
	 * 
	 * @return boolean
	 */
	 public static function register($formName, $handler, $type = 'post')
	 {
	 	if(!self::$started){self::start();}
	 	$type = strtolower($type);
		//Check if its a Real Form
		if(!in_array($type, array('post','get'))){return false;}
	 	//Lets trick the coder xD
	 	if(($type == 'post') && (!self::$currentPost || self::$currentPost != self::_formatName($formName)))
	 	{return true;}
	 	if( ($type == 'get') && (!self::$currentGet || self::$currentGet != strtolower(($formName))))
	 	{return true;}

	 	//Well, Everything is fine
	 	if(is_callable($handler))
		{
			
			//Prepare Data
			$submittedData = ($type == 'post') ? $_POST : $_GET;
			$form = new Form;
			foreach($submittedData as $key => $value)
			{
				if($key == 'nounce') 
				{
					$form->nounce = $value;
					continue;
				}
				if($key == self::$actionName) {continue;}
				$form->add($key, $value);
			}
			$form->method = strtoupper($type);
			$form->referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
			$form->action = $submittedData[self::$actionName];
			$submittedData = null;
			//Run the Handler
			call_user_func($handler, $form);
			
		}
		//it's Not Valid Callback
		else {
			trigger_error('The callback you registered for the form '.$formName.' isn\'t exists', E_USER_WARNING);
		}
	 }
	 
	 /*************************************************************/
	 
	 /**
	  * Verify Form Submition upon nounce Code
	  * 
	  * @param object $form
	  * 
	  * @return boolean
	  * 
	  * @access public
	  * 
	  * @since 0.1
	  */
	  public static function verify(Form $form)
	  {
	  	if(!isset($form->nounce)){return false;}
		//Lets check on Session
		if(strlen(session_id()) == 0) {session_start();}
		//Check if there is no nounce for that
		if(!isset($_SESSION[self::OLD_SESSION_NAME][$form->action])){return false;}
		//Check for Nounce
		if($_SESSION[self::OLD_SESSION_NAME][$form->action] != $form->nounce){return false;}
		//Well, it's okay now
		return true;
	  }
	  
	 /*************************************************************/
	 
	 /**
	  * Create Form Hidden fields like form_action, and nounce verify 
	  * 
	  * @access public
	  * 
	  * @param $formName string pick the form name you want
	  * 
	  * @param $nounce boolean create nounce field or no (default: true)
	  * 
	  * @return string form name
	  * 
	  */
	  public static function createHiddenFields($formName, $nounce = true)
	  {
	  	$fields = "";
		if($nounce) {$fields .= self::_generateNounceField(true, $formName)."\n";}
		$fields .= sprintf('<input type="hidden" name="%s" value="%s" />', 
							self::$actionName, self::_formatName($formName))."\n";
		return $fields;
	  }
	  
	 /*************************************************************/
	 
	 /**
	  * Create Form Hidden fields like form_action, and nounce verify 
	  * 
	  * @access public
	  * 
	  * @param $formName string pick the form name you want
	  * 
	  * @param $nounce boolean create nounce field or no (default: true)
	  * 
	  * @return string form name
	  * 
	  */
	  public static function createGetQuery($formName, $data = array(),$nounce = true)
	  {
	  	if(!is_array($data)){$data = array();}
		if($nounce) {$data['nounce']= self::_generateNounceField(true, $formName, false);}
		$data[self::$actionName] = self::_formatName($formName);
		$query = array();
		foreach ($data as $key => $value) {$query[] = implode('=', array($key, $value));}
		$query = implode('&', $query);
		return $query;
	  }
	  
	 /*************************************************************/
	 
	 /**
	  * Helper Method that's format form name in order to add the ability of non-sensitive names
	  * 
	  * @param string $formName
	  * 
	  * @access public
	  * 
	  * @since 0.1
	  * 
	  * @return string
	  */
	  public static function _formatName($formName)
	  {
	  	return strtolower(str_replace(' ', '_', $formName));
	  }
	
	/*************************************************************/
	
	/**
	 * Helper Method that generate a nounce Code
	 * 
	 * @access private 
	 * 
	 * @param boolean $save indicate whether save it or no
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	 private static function _generateNounceField($save = true, $formName = null, $input = true)
	 {
	 	//Generate a new code
	 	$nounce = uniqid('nounce_');
		//Check if user want only the code
		if(!$save){return $nounce;}
		//IF user want to save it
		if(strlen($formName) < 1)
		{
			trigger_error('Empty Form Name', E_USER_WARNING);
			return false;
		}
		
		//Now let's register this nounce and create the field
		if(strlen(session_id()) == 0) {session_start();}
		//Make Sure Nounces List exist
		if(!isset($_SESSION[self::SESSION_NAME])){$_SESSION[self::SESSION_NAME] = array();}
		//Save it
		$_SESSION[self::SESSION_NAME][self::_formatName($formName)] = $nounce;
		//Create the element
		return ($input) ? sprintf('<input type="hidden" name="nounce" value="%s" />', $nounce) : $nounce;
	 }
	 
	 /*************************************************************/
	 
	 /**
	  * Update Session with new Nounces
	  * 
	  * @access public
	  * 
	  * @since 0.1
	  * 
	  * @return void
	  */
	  public static function _updateSession()
	  {
	  	$_SESSION[self::OLD_SESSION_NAME] = $_SESSION[self::SESSION_NAME];
		$_SESSION[self::SESSION_NAME] = array();
	  }
	  
	  /*************************************************************/
}
###############################################################################

/**
 * Form Builder, allowing you to build forms easily 
 * 
 * @author SamDev
 * 
 * @license http://creativecommons.org/licenses/by/3.0/deed.en_US  Creative Commons Attribution 3.0
 * 
 * @copyright 2013 SamDev
 * 
 * @package PHP Developers Kit
 * 
 * @subpackage FormKit
 * 
 * @version 0.1
 * 
 */
class FormBuilder {} //Next Version


###############################################################################

/**
 * Form Container, allowing you to work with forms submitted and manipulate its data
 * 
 * @author SamDev
 * 
 * @license http://creativecommons.org/licenses/by/3.0/deed.en_US  Creative Commons Attribution 3.0
 * 
 * @copyright 2013 SamDev
 * 
 * @package PHP Developers Kit
 * 
 * @subpackage FormKit
 * 
 * @version 0.1
 * 
 */
class Form
{
	/**
	 * @property object stdClass
	 * 
	 * @access public
	 */
	 public $data;
	 
	 
	/**
	  * Form Nounce if Exists
	  * 
	  * @property string
	  * 
	  * @access public
	  */
	 public $nounce = false;
	 
	 
	 /**
	  * Form Action
	  * 
	  * @property string
	  * 
	  * @access public
	  */
	 public $action = false;
	 
	 /**
	  * Form Method (GET|POST)
	  * 
	  * @property string
	  * 
	  * @access public
	  */
	 public $method = false;
	 
	 
	 /**
	  * Instance of Validator Class
	  * 
	  * @property object
	  * 
	  * @access public
	  */
	 public $validate = false;
	 
	 /*************************************************************/
	 
	 /**
	  * Constructor, Runs when new Form Created
	  * 
	  * Set data to stdClass in order to add properties to it later
	  * 
	  * Set Validate Property as Instance of Validator Class
	  * 
	  * @access public
	  * 
	  * @return void
	  * 
	  * @since 0.1
	  */
	 public function __construct()
	 {
	 	$this->data = new stdClass;
		
		$this->validate = new Validator($this->data);
	 }
	 
	 /*************************************************************/
	 
	 /**
	  * Handle Properties set
	  * 
	  * Let Code set nounce,action,method and validate only ONCE
	  * 
	  * @param string $key data key
	  * 
	  * @param mixed $value new $value
	  * 
	  * @return void
	  * 
	  * @access public
	  * 
	  * @since 0.1
	  */
	 public function __set($key, $value)
	 {
	 	//Let those properties to be able to set only once
	 	if(in_array($key, array('nounce', 'action', 'method', 'validate')))
		{
			if($this->{$key} != false){return;}
		}
		$this->{$key} = $value;
	 }
	 
	 /*************************************************************/
	 
	 /**
	  * Add a new field to Form
	  * 
	  * @param string $key
	  * 
	  * @param string $value 
	  * 
	  * @return void
	  * 
	  * @access public
	  * 
	  * @since 0.1
	  */
	 public function add($key, $value)
	 {
	 	$this->data->{$key} = $value;
	 }
	 
	 /*************************************************************/
	 
	 /**
	  * Verify Form Submittion via Nounce Validation
	  * 
	  * return false if no nounce or not valid
	  * 
	  * @return boolean
	  * 
	  * @access public
	  * 
	  * @since 0.1
	  */
	 public function verify()
	 {
	 	return FormHandler::verify($this);
	 }
	 
	 /*************************************************************/
	 
	 /**
	  * Return String debug Info when this object used as String
	  * 
	  * @return string
	  * 
	  * @access public
	  * 
	  * @since 0.1
	  */
	 public function __toString()
	 {
	 	$output = '';
		$output .= '<pre>';
		$output .= 'Form Action : '.$this->action.'<br />';
		$output .= 'Form Method : '.$this->method.'<br />';
		$output .= 'Form Status : '.(($this->verify()) ? 'Verified' : 'Not Verified').'<br />' ;
		$output .= 'Form Data : ';
		ob_start();
		print_r(($this->data));
		$output .= ob_get_contents();
		ob_end_clean();
		$output .=  '</pre>';
		
		return $output;
	 }
	 
	 /*************************************************************/
	 
}


###############################################################################

/**
 * Validator, allowing you to check and validate inputs
 * 
 * @author SamDev
 * 
 * @license http://creativecommons.org/licenses/by/3.0/deed.en_US  Creative Commons Attribution 3.0
 * 
 * @copyright 2013 SamDev
 * 
 * @package PHP Developers Kit
 * 
 * @subpackage FormKit
 * 
 * @version 0.1
 * 
 */
class Validator 
{
	
	/**
	 * Default Data Container to use it as reference
	 * 
	 * @property object
	 * 
	 * @access private
	 */
	private $dataContainer = null;
	
	/*************************************************************/
	
	/**
	 * Constructor, Set default data and check on data container
	 * 
	 * @param object $data
	 * 
	 * @access public 
	 * 
	 * @return void
	 * 
	 * @since 0.1
	 */
	public function __construct($data = null)
	{
		if(is_a($data, 'stdClass'))
		{
			$this->dataContainer = $data;
		}
	} 
	
	/*************************************************************/
	
	/**
	 * Helper Method, Try to guess what input user want to validate is real value or reference to data container
	 * 
	 * @param mixed $input
	 * 
	 * @access private
	 * 
	 * @since 0.1
	 * 
	 * @return mixed
	 */
	private function getReal($input)
	{
		if(!is_a($this->dataContainer, 'stdClass')){return $input;}
		return (isset($this->dataContainer->{$input})) ? $this->dataContainer->{$input} : $input;
	}
	
	/*************************************************************/
	
	/**
	 * Check if Input Length is Same as limit or Between two limits
	 * 
	 * @param mixed $data
	 * 
	 * @param int $limit
	 * 
	 * @param int $limit2 (optional)
	 * 
	 * @access public
	 * 
	 * @since 0.1
	 *  
	 * @return boolean
	 */
	public function length($data, $limit, $limit2 = null) 
	{
		$data = $this->getReal($data);
		if(is_null($limit2))
		{
			return (strlen($data) == $limit) ? true : false; 
		}
		return (strlen($data) <= intval($limit2) && strlen($data) >= intval($limit));
	}
	
	/*************************************************************/
	
	/**
	 * Check if current input has min limit
	 * 
	 * @param mixed $data
	 * 
	 * @param int $min
	 * 
	 * @return boolean
	 * 
	 * @access public
	 * 
	 * @since 0.1
	 */
	public function minLength($data, $min)
	{
		$data = $this->getReal($data);
		return (strlen($data) >= $min) ? true : false;
	}
	
	/*************************************************************/
	
	/**
	 * Validate input upon max limit
	 * 
	 * @param mixed $data
	 * 
	 * @param int $max
	 * 
	 * @return boolean
	 * 
	 * @access public
	 * 
	 * @since 0.1
	 */
	public function maxLength($data, $max)
	{
		$data = $this->getReal($data);
		return (strlen($data) <= $max) ? true : false;
	}
	
	/*************************************************************/
	
	/**
	 * Validate input and check if its a Real Email or not
	 * 
	 * @param mixed $data
	 * 
	 * @return boolean
	 * 
	 * @access public
	 * 
	 * @since 0.1
	 * 
	 */
	public function email($data) 
	{
		$data = $this->getReal($data);
		return  (preg_match('/^([a-zA-Z0-9\._]+)@([a-zA-Z0-9-\.]+)\.([a-zA-Z]{2,4})$/', $data) == 0) ? false : true;
	}
	
	/*************************************************************/
	
	/**
	 * Validate this input as a number
	 * 
	 * @param mixed $data
	 * 
	 * @return boolean
	 * 
	 * @access public
	 * 
	 * @since 0.1
	 */
	public function number($data)
	{
		$data = $this->getReal($data);
		return (intval($data) == $data) ? true : false;
	}
	
	/*************************************************************/
	
	/**
	 * Validate Input as URL and return true or false
	 * 
	 * @param mixed $data
	 * 
	 * @return boolean
	 * 
	 * @access public
	 * 
	 * @since 0.1
	 */ 
	public function url($data)
	{
		$data = $this->getReal($data);
		
	}
	
	/*************************************************************/
	
	/**
	 * Validate Input using custom Pattern
	 * 
	 * @param mixed $data
	 * 
	 * @param string $pattern
	 * 
	 * @return boolean
	 * 
	 * @access public
	 * 
	 * @since 0.1
	 */
	public function pattern($data, $pattern) { }
	
	/*************************************************************/
	
	/**
	 * Validate Input Using Custom Callback
	 * 
	 * @param mixed $data
	 * 
	 * @param callback $callback
	 * 
	 * @return mixed
	 * 
	 * @access public
	 * 
	 * @since 0.1 
	 */
	 public function callback($data, $callback)
	 {
	 	$data = $this->getReal($data);
		return call_user_func($callback);
	 }
	
	/*************************************************************/
}
