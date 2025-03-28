<?php
/**
 * GD Reflection Lib Plugin Definition File
 * 
 * This file contains the plugin definition for the GD Reflection Lib for PHP Thumb
 * 
 * PHP Version 5 with GD 2.0+
 * PhpThumb : PHP Thumb Library <http://phpthumb.gxdlabs.com>
 * Copyright (c) 2009, Ian Selby/Gen X Design
 * 
 * Author(s): Ian Selby <ian@gen-x-design.com>
 * 
 * Licensed under the MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @author Ian Selby <ian@gen-x-design.com>
 * @copyright Copyright (c) 2009 Gen X Design
 * @link http://phpthumb.gxdlabs.com
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @version 3.0
 * @package PhpThumb
 * @filesource
 */

/**
 * GD Reflection Lib Plugin
 * 
 * This plugin allows you to create those fun Apple(tm)-style reflections in your images
 * 
 * @package PhpThumb
 * @subpackage Plugins
 */
class GdReflectionLib
{
	/**
	 * Instance of GdThumb passed to this class
	 * 
	 * @var GdThumb
	 */
	protected $parentInstance;
	protected $currentDimensions;
	protected $workingImage;
	protected $newImage;
	protected $options;
	
	public function createReflection ($percent, $reflection, $white, $border, $borderColor, &$that)
	{
		// bring stuff from the parent class into this class...
		$this->parentInstance 		= $that;
		$this->currentDimensions 	= $this->parentInstance->getCurrentDimensions();
		$this->workingImage			= $this->parentInstance->getWorkingImage();
		$this->newImage				= $this->parentInstance->getOldImage();
		$this->options				= $this->parentInstance->getOptions();
		
		$width				= $this->currentDimensions['width'];
		$height				= $this->currentDimensions['height'];
		$reflectionHeight 	= intval($height * ($reflection / 100));
		$newHeight			= $height + $reflectionHeight;
		$reflectedPart		= $height * ($percent / 100);
		
		$this->workingImage = imagecreatetruecolor($width, $newHeight);
		
		imagealphablending($this->workingImage, true);
		
		$colorToPaint = imagecolorallocatealpha($this->workingImage,255,255,255,0);
        imagefilledrectangle($this->workingImage,0,0,$width,$newHeight,$colorToPaint);
		
		imagecopyresampled
		(
            $this->workingImage,
            $this->newImage,
            0,
            0,
            0,
            $reflectedPart,
            $width,
            $reflectionHeight,
            $width,
            ($height - $reflectedPart)
		);
		
		$this->imageFlipVertical();
		
		imagecopy($this->workingImage, $this->newImage, 0, 0, 0, 0, $width, $height);
		
		imagealphablending($this->workingImage, true);
		
		for ($i = 0; $i < $reflectionHeight; $i++) 
		{
            $colorToPaint = imagecolorallocatealpha($this->workingImage, 255, 255, 255, ($i/$reflectionHeight*-1+1)*$white);
			
            imagefilledrectangle($this->workingImage, 0, $height + $i, $width, $height + $i, $colorToPaint);
        }
		
		if($border == true) 
		{
            $rgb 			= $this->hex2rgb($borderColor, false);
            $colorToPaint 	= imagecolorallocate($this->workingImage, $rgb[0], $rgb[1], $rgb[2]);

            imageline($this->workingImage, 0, 0, $width, 0, $colorToPaint); //top line
            imageline($this->workingImage, 0, $height, $width, $height, $colorToPaint); //bottom line
            imageline($this->workingImage, 0, 0, 0, $height, $colorToPaint); //left line
            imageline($this->workingImage, $width-1, 0, $width-1, $height, $colorToPaint); //right line
        }
		
		if ($this->parentInstance->getFormat() == 'PNG')
		{
			$colorTransparent = imagecolorallocatealpha
			(
				$this->workingImage, 
				$this->options['alphaMaskColor'][0], 
				$this->options['alphaMaskColor'][1], 
				$this->options['alphaMaskColor'][2], 
				0
			);
			
			imagefill($this->workingImage, 0, 0, $colorTransparent);
			imagesavealpha($this->workingImage, true);
		}
		
		$this->parentInstance->setOldImage($this->workingImage);
		$this->currentDimensions['width'] 	= $width;
		$this->currentDimensions['height']	= $newHeight;
		$this->parentInstance->setCurrentDimensions($this->currentDimensions);
		
		return $that;
	}
	
	/**
	 * Flips the image vertically
	 * 
	 */
	protected function imageFlipVertical ()
	{
		$x_i = imagesx($this->workingImage);
	    $y_i = imagesy($this->workingImage);

	    for ($x = 0; $x < $x_i; $x++) 
		{
	        for ($y = 0; $y < $y_i; $y++) 
			{
	            imagecopy($this->workingImage, $this->workingImage, $x, $y_i - $y - 1, $x, $y, 1, 1);
	        }
	    }
	}
	
	/**
	 * Converts a hex color to rgb tuples
	 * 
	 * @return mixed 
	 * @param string $hex
	 * @param bool $asString
	 */
	protected function hex2rgb ($hex, $asString = false) 
	{
        // strip off any leading #
        if (0 === strpos($hex, '#')) 
		{
           $hex = substr($hex, 1);
        } 
		elseif (0 === strpos($hex, '&H')) 
		{
           $hex = substr($hex, 2);
        }

        // break into hex 3-tuple
        $cutpoint = ceil(strlen($hex) / 2)-1;
        $rgb = explode(':', wordwrap($hex, $cutpoint, ':', $cutpoint), 3);

        // convert each tuple to decimal
        $rgb[0] = (isset($rgb[0]) ? hexdec($rgb[0]) : 0);
        $rgb[1] = (isset($rgb[1]) ? hexdec($rgb[1]) : 0);
        $rgb[2] = (isset($rgb[2]) ? hexdec($rgb[2]) : 0);

        return ($asString ? "{$rgb[0]} {$rgb[1]} {$rgb[2]}" : $rgb);
    }
}

$pt = PhpThumb::getInstance();
$pt->registerPlugin('GdReflectionLib', 'gd');