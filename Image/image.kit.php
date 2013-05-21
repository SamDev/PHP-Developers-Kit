<?php

namespace DevKit;

class Image
{
	
	private $resource;
	
	private $defaultType = 'PNG';
	
	private $width;
	
	private $height;
	
	private $fonts = array();
	
	private $allowedTypes = array('png' => 'image/png', 'jpeg' => 'image/jpeg', 'jpg' => 'image/jpeg', 'gif' => 'image/gif');
	
	private $currentMime = false;
	
	private $currentFile = false;
	
	public  $color;
	
	
	/*************************************************/
	
	/****************************/
	/* Constructor				*/
	/****************************/
	
	public function __construct($param1, $param2 = null)
	{
		
		//Create Color Instance
		$this->color = new Color();
		
		//Start from Scratch
		$newImage = function() use ($param1, $param2) 
		{
			$this->width = intval($param1);
			$this->height = intval($param2);
			//Create the Canvas 
			$this->resource = imagecreate($this->width, $this->height);
			//Set Background to White as Default
			imagecolorallocate($this->resource, 255, 255, 255);
		};
		
		//Start form Exits Image
		$openImage = function() use ($param1, $param2)
		{
			$this->currentFile = $param1;
			if($data = getimagesize($this->currentFile))
			{
				$this->width = $data[0];
				$this->height = $data[1];
				$this->currentMime = $data['mime'];
				if($fileType = array_search($this->currentMime, $this->allowedTypes))
				{
					switch (strtolower($fileType))
					{
						case 'png' :
							$this->resource = imagecreatefrompng($this->currentFile);
							break;
							
						case 'gif' :
							$this->resource = imagecreatefromgif($this->currentFile);
							break;
							
						case 'jpeg' :
							$this->resource = imagecreatefromjpeg($this->currentFile);
							break;
							
						default :
							$this->resource = imagecreatefrompng($this->currentFile);
							break;
					} #End Switch
				}#End if fileType
			}
		};
			
		//Let's decide which way to work

		//User want to work with exits Image
		if( intval($param1) == 0 && file_exists($param1)){ $openImage(); }
		//Let's create an Empty Workspace
		else{ $newImage(); }
		
	}
	
	/**************************************************************/
	
	private function allocateColor($color)
	{
		
	}
	
	
	private function calucateAlpha($opacity)
	{
		return 127-round(($opacity/100)*127);
	}
	public function rgbToHex(){ }
	
	public function hexToRGB(){ }
	
	public function setBackground(Color $color)
	{
		$color = imagecolorallocatealpha($this->resource, $color->red, $color->green, $color->blue, $color->alpha);
		imagefilledrectangle($this->resource, 0, 0, $this->width, $this->height, $color);
		
	}
	
	public function registerFont($id, $location)
	{
		//Make Sure Font Exists
		if(!file_exists($location)){return false;}
		//Save the font
		$id = strtoupper(str_replace(' ', '_', $id));
		$this->fonts[$id] = $location;
	}
	
	public function addText($text, $size, $position, $color, $font, $angle)
	{
		if(isset($this->fonts[strtoupper(str_replace(' ', '_', $font))]))
		{
			//TTF Text
			
		}
		else
		{
			$color = '';
			//Regular String	
			imagestring($this->resource, $size, $position[0], $position[1], $text, $color);
		}
	}
	
	
	
	public function drawRect(){ }
	
	public function drawEclipse(){ }
	
	public function drawLine(){ }
	
	public function rotate(){ }
	
	public function addEffect($effect, $arg1 = null, $arg2 = null)
	{
		switch (strtoupper($effect))
		{
			
		}
	}
	
	public function render($renderAs = null)
	{
		if(!is_resource($this->resource)){return false;}
		header("Content-type: ".$this->currentMime);
		imagepng($this->resource);
		imagedestroy($this->resource);
	}
	
	public function save($fileLocation)
	{
		
	}
}


class Color
{
	
	public $red = 0;
	
	public $green = 0;
	
	public $blue = 0;
	
	public $alpha = 0;
	
	/********************************/
	/* Basic Colors					*/
	/********************************/
	
	public static $BLACK = array(0,0,0);
	
	public static $WHITE = array(255, 255, 255);
	
	public static $RED = array(255, 0, 0);
	
	public static $GREEN = array(0, 255, 0);
	
	public static $BLUE = array(0, 0, 255);
	
	public static $GREY = array(127, 127, 127);
	
	public static $LIGHT_GREY = array(64, 64, 64);
	
	public static $DARK_GREY = array(192, 192, 192);
	
	
	public function __construct($color = false)
	{
		if(is_array($color) && count($color) >= 3)
		{
			$this->red = (intval($color[0]) <= 255) ? intval($color[0]) : 0;
			$this->green = (intval($color[1]) <= 255) ? intval($color[1]) : 0;
			$this->blue = (intval($color[2]) <= 255) ? intval($color[2]) : 0;
			
			//Check for Alpha
			$this->alpha = (count($color) >= 4) ? 127-round(($color[3]/100)*127) : 0;
			
		}
		
		elseif(is_string($color) && (strlen($color) == 7 || strlen($color) == 4))
		{
			//It's a Hex Color
			
		}
		return $this;
			
	}
	
	public function create($color)
	{
		return new Color($color);
	}
	
	public function __invoke($color)
	{
		return new Color($color);
	}
}



