<?php

class CaptchaController extends \Mvc\Controller
{
	public function __construct()
    {
        $args = func_get_args();
        call_user_func_array(array($this, 'parent::__construct'), $args);
        
        if($this->Identity == null)
        {
            die("IdentityFramework not found");
        }
        else if($this->Entity == null)
        {
            die("EntityFramework not found");
        }
	}
	
	public function Index()
	{
		ob_clean();
		
		$SecurityCode = $this->GenerateCaptchaCode();
		
		$width = 80;
		$height = 15;
		$font = 10;

		$image = imagecreate($width, $height);
		$background = imagecolorallocate($image, 255, 255, 255);
		$text_colour = imagecolorallocate($image, 0, 0, 0);
		$line_colour = imagecolorallocate($image, 255, 0, 0);
		$text_width = imagefontwidth($font) * strlen($SecurityCode);
		$text_height = imagefontheight($font);
		
		imagestring($image, $font, rand(0, $width - $text_width), rand(0, $height - $text_height), $SecurityCode, $text_colour);
		
		imagesetthickness($image, 1);
		$rand = rand(2, 3);

		for($i = 0; $i < $rand; $i++)
		{
			imageline($image, 0, rand(0, $height), $width, rand(0, $height), $text_colour);
		}

		header("Content-type: image/png");
		imagepng($image);
		imagecolordeallocate($line_color);
		imagecolordeallocate($text_color);
		imagecolordeallocate($background);
		imagedestroy($image);

		exit;
	}
}

?>