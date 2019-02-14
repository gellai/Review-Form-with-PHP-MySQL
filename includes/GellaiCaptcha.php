<?php
/**
 * Parameters:
 *      mode=raw            // Mode (see usage below)
 *      length=6            // Length of random number
 *      type=png            // Rendering image type
 *      tColor=F0F0F0       // Text colour
 *      bColor=646464       // Background colour
 *      lColor=F0F0F0       // Line colour
 * 
 * Usage:
 *      1. Raw mode
 * 
 *      <img src=classes/GellaiCaptcha.php?mode=raw&length=10&type=jpeg />
 * 
 *      2. Base 64 mode
 * 
 *      <?php
 *          include_onec('classes/GellaiCaptcha.php');
 * 
 *          $param = array(
 *                      'mode'	 => "b64",
 *                      'length' => 5,
 *                      'type'	 => "gif",
 *                      'tColor' => "646464",
 *                      'bColor' => "F0F0F0",
 *                      'lColor' => "949494" );
 *      ?>
 *          
 *      <?php 
 *          echo $gCaptcha->getCaptcha($param); 
 *      ?>
 *
 */


/**
 * Get the first 3 digits of the current PHP Version
 */
$phpVersion = substr(phpversion(), 0, 3);

/**
 * Check PHP Version and check if the session is already started.
 */
if ($phpVersion > 5.4) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
} 
else {
    if (session_id() == '') {
        session_start();
    }
}

/**
 * Main class
 */
class GellaiCaptcha 
{
    const DEF_IMAGE_TYPE = "png";
    const DEF_TXT_COLOR  = "646464";
    const DEF_BG_COLOR   = "F0F0F0";
    const DEF_LINE_COLOR = "949494";
    const DEF_LENGTH     = 6;
    const DEF_MODE       = "b64";
    
    private $_imgType;
    private $_txtColor;
    private $_bgColor;
    private $_lnColor;
    private $_length;
    private $_width;
    private $_height = 27;
    
    private $_randNumber;
    private $_imgInst;
    private $_mode;
    private $_param = array();

    public function __set($name, $value) {
        echo "<p>Property setting is not allowed!</p>";
        exit;
    }
    
	/**
	 * Constructor is mainly used for RAW mode
	 */
    public function __construct() {
        unset($_SESSION['gCaptcha']);

        $this->_setMode();
        
        if($this->_mode == "raw") {
            $this->_setImgType()
                 ->_setRandNumber()   
                 ->_setTxtColor()
                 ->_setBgColor()
                 ->_setLnColor();    
        
            $this->_createCaptchaImage();
        }
    }
    
    /**
     * Get captcha image / Base64 mode 
     * @return type
     */
    public function getCaptcha($param = array()) {
        $this->_param = $param;
        $this->_setMode();
        
        if($this->_mode == "b64") {            
            $this->_setImgType()
                 ->_setRandNumber()   
                 ->_setTxtColor()
                 ->_setBgColor()
                 ->_setLnColor();   
        
            return $this->_getCaptchaB64();
        }
    }
    
    /**
     * Create HTML captcha image tag
     * @return type
     */
    private function _getCaptchaB64() {
        $this->_createImgResource()
             ->_fillImage()
             ->_addLines();
        
        $html = '<img src="data:image/';
        
        ob_start();           
       
        switch($this->_imgType) {
            case 'png':
                $html .= 'png;';
                imagepng($this->_imgInst, null, 0, PNG_NO_FILTER);
                break;
            
            case 'jpg':
                $html .= 'jpeg;';
                imagejpeg($this->_imgInst);
                break;
            
            case 'gif':
                $html .= 'gif;';
                imagegif($this->_imgInst);
                break;
            
            default:
                $html .= 'png;';
                imagepng($this->_imgInst);
                break;
        }
     
        $b64Img = base64_encode(ob_get_contents()); 
        
        imagedestroy($this->_imgInst);  
        ob_end_clean();
        
        $html .= 'base64,' . $b64Img . '" />';
        
        return $html;
    }
    
    /**
     * Retrieve and filter GET
     * @param type $var
     * @return type
     */
    private function _getInput($var) {
        if(array_key_exists($var, $this->_param)) {
            return $this->_param[$var];
        }
        return filter_input(INPUT_GET, $var);
    }
    
    /**
     * Set image type from 'type' parameter
     * @return $this
     */
    private function _setImgType() {
        $type = $this->_getInput('type');
        
        if($type == "png" || $type == "jpg" || $type == "gif") {
            $this->_imgType = $this->_getInput('type');
        }
        else {
            $this->_imgType = self::DEF_IMAGE_TYPE;
        }
        
        return $this;
    }
    
    /**
     * Set the random number length 
     * @return $this
     */
    private function _setRandNumber() {
        $length = (int)$this->_getInput('length');
        
        if($length < 1 || $length > 20) {
            $this->_length = self::DEF_LENGTH;
            $this->_randNumber = rand(10000, 999999);
        }
        else {
            $min = "1";
            $max = "9";
            $i = 1;
            while($i < $length) {
                $min .= "0";
                $max .= "9";
                $i++;
            }
                       
            $this->_length    = $length;
            $this->_randNumber = rand( (int)$min, (int)$max );
        }

        $_SESSION['gCaptcha'] = $this->_randNumber;

        return $this;
    }

    /**
     * Get parameters from $_GET
     */
    private function _getParam() {
        $this->_param['mode'] = isset($_GET['mode']) ? $_GET['mode'] : "";
        $this->_param['length'] = isset($_GET['length']) ? $_GET['length'] : "";
        $this->_param['type'] = isset($_GET['type']) ? $_GET['type'] : "";
        $this->_param['tColor'] = isset($_GET['tColor']) ? $_GET['tColor'] : "";
        $this->_param['bColor'] = isset($_GET['bColor']) ? $_GET['bColor'] : "";
        $this->_param['lColor'] = isset($_GET['lColor']) ? $_GET['lColor'] : "";
    } 

    /**
     * Set mode RAW / BASE64
     */
    private function _setMode() {
        $mode = $this->_getInput('mode');
        
        if($mode == "raw") {
            $this->_mode = $mode;
        }
        else {
            $this->_mode = self::DEF_MODE;
        }
    }

    /**
     * Set text colour from 'tColor' parameter
     * @return $this
     */
    private function _setTxtColor() {
        $tColor = $this->_getInput('tColor');
        
        if(strlen($tColor) == 3 || strlen($tColor) == 6) {
            $this->_txtColor = $this->_getInput('tColor');
        }
        else {
            $this->_txtColor = self::DEF_TXT_COLOR;
        }
        
        return $this;
    }
      
    /**
     * Set background colour from 'bColor' parameter
     * @return $this
     */
    private function _setBgColor() {       
        $bColor = $this->_getInput('bColor');
        
        if(strlen($bColor) == 3 || strlen($bColor) == 6) {
            $this->_bgColor = $this->_getInput('bColor');
        }
        else {
            $this->_bgColor = self::DEF_BG_COLOR;
        }
        
        return $this;
    }
    
    /**
     * Set line colour from 'lColor' parameter
     * @return $this
     */
    private function _setLnColor() {      
        $lColor = $this->_getInput('lColor');
        
        if(strlen($lColor) == 3 || strlen($lColor) == 6) {
            $this->_lnColor = $this->_getInput('lColor');
        }
        else {
            $this->_lnColor = self::DEF_LINE_COLOR;
        }
        
        return $this;
    }
    
    /**
     * Set image resource (width, height)
     * @return $this
     */
    private function _createImgResource() {      
        $this->_width = ($this->_length * 9) + (2 * 6);
        $this->_imgInst = imagecreatetruecolor($this->_width, $this->_height);
        return $this;
    }
    
    /**
     * Return colour resource code
     * @param string $hColor
     * @return int
     */
    private function _getColor($hColor) {      
        $rgb = $this->_hex2rgb($hColor);
        
        return imagecolorallocate($this->_imgInst, $rgb['r'], $rgb['g'], $rgb['b']);
    }
    
    /**
     * Flood fill image resource with background colour
     * Add string with colour to the image resource
     * @return $this
     */
    private function _fillImage() {
        imagefill($this->_imgInst, 0, 0, $this->_getColor($this->_bgColor));
        
        $xPos = ($this->_width - ($this->_length * 9)) / 2;
        imagestring($this->_imgInst, 5, $xPos, 6, $this->_randNumber, $this->_getColor($this->_txtColor));
        
        return $this;
    }
    
    /**
     * Add diagonal lines to image resource
     * @return $this
     */
    private function _addLines() {
        $aX = 10;
        $aY = 0;
        $bX = 0;
        $bY = 10;
        
        do {
            imageline($this->_imgInst,  $aX, $aY,  $bX, $bY, $this->_getColor($this->_lnColor));
            $aX += 10;
            $bY += 10;
            
            if($bY == $this->_height) {
                $bX += 10;
            }
            elseif($bY > $this->_height) {
                $bX += $bY - $this->_height;
                $bY = $this->_height;
            }
            
            if($aX == $this->_width) {
                $aY += 10;
            }
            elseif($aX > $this->_width) {
                $aY += $aX - $this->_width;
                $aX = $this->_width;
            }
            
        } while ($bX < $this->_width);
        
        $cX = 0;
        $cY = $this->_height - 10;
        $dX = 10;
        $dY = $this->_height;
        
        do {
            imageline($this->_imgInst,  $cX, $cY,  $dX, $dY, $this->_getColor($this->_lnColor)); 
            $cY -= 10;
            $dX += 10;
            
            if($cY == 0) {
                $cX += 10;
            }
            elseif($cY < 0) {
                $cX += 0 - $cY;
                $cY = 0;
            }
            
            if($dX == $this->_width) {
                $dY -= 10;
            }
            elseif($dX > $this->_width) {
                $dY -= $dX - $this->_width;
                $dX = $this->_width;
            }
            
        } while($cX < $this->_width);

        return $this;
    }

    /**
     * Convert HEX colour to RGB
     *
     * @param type $hexColor
     * @return array
     */
    private function _hex2rgb($hexColor) {
        $rgb = array(
            "r" => 0,
            "g" => 0,
            "b" => 0
        );

        if (strlen($hexColor) != 3 && strlen($hexColor) != 6) {
            return $rgb;
        }

        if (strlen($hexColor) == 3) {
            $rgb['r'] = hexdec(substr($hexColor, 0, 1) . substr($hexColor, 0, 1));
            $rgb['g'] = hexdec(substr($hexColor, 1, 1) . substr($hexColor, 1, 1));
            $rgb['b'] = hexdec(substr($hexColor, 2, 1) . substr($hexColor, 2, 1));
        } 
        elseif (strlen($hexColor) == 6) {
            $rgb['r'] = hexdec(substr($hexColor, 0, 2));
            $rgb['g'] = hexdec(substr($hexColor, 2, 2));
            $rgb['b'] = hexdec(substr($hexColor, 4, 4));
        } 
        else {
            return $rgb;
        }
        
        return $rgb;
    }
    
    /**
     * Create the captcha image
     */
    private function _createCaptchaImage() {
        $this->_createImgResource()
             ->_fillImage()
             ->_addLines();
                   
        header("Cache-Control: no-cache, must-revalidate");
        
        switch($this->_imgType) {
            case 'png':
                header('Content-type: image/png');
                imagepng($this->_imgInst);
                break;
            
            case 'jpg':
                header('Content-type: image/jpeg');
                imagejpeg($this->_imgInst);
                break;
            
            case 'gif':
                header('Content-type: image/gif');
                imagegif($this->_imgInst);
                break;
            
            default:
                header('Content-type: image/png');
                imagepng($this->_imgInst);
                break;
        }
     
        imagedestroy($this->_imgInst);
    }
}

$gCaptcha = new GellaiCaptcha();