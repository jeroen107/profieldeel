<?php
/**
 * Class definition file for SLIRImage
 *
 * This file is part of SLIR (Smart Lencioni Image Resizer).
 *
 * SLIR is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SLIR is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with SLIR.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright Copyright © 2011, Joe Lencioni
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 * @since 2.0
 * @package SLIR
 */

/**
 * SLIR image class
 *
 * @since 2.0
 * @author Joe Lencioni <joe@shiftingpixel.com>
 * @package SLIR
 */
class SLIRImage
{
  /**
   * Path to this image file
   * @var string
   * @since 2.0
   */
  private $path;

  /**
   * Image data
   * @var string
   * @since 2.0
   */
  private $data;

  /**
   * Image identifier
   * @var resource
   * @since 2.0
   */
  private $image;

  /**
   * MIME type of this image
   * @var string
   * @since 2.0
   */
  private $mime;

  /**
   * Width of image in pixels
   * @var integer
   * @since 2.0
   */
  private $width;

  /**
   * Height of image in pixels
   * @var integer
   * @since 2.0
   */
  private $height;

  /**
   * Width of cropped image in pixels
   * @var integer
   * @since 2.0
   */
  private $cropWidth;

  /**
   * Height of cropped image in pixels
   * @var integer
   * @since 2.0
   */
  private $cropHeight;

  /**
   * Name of the cropper to use
   * @var string
   * @since 2.0
   */
  private $cropper;

  /**
   * IPTC data embedded in image
   * @var array
   * @since 2.0
   */
  private $iptc;

  /**
   * Quality of image
   * @var integer
   * @since 2.0
   */
  private $quality;

  /**
   * Whether or not progressive JPEG output is turned on
   * @var boolean
   * @since 2.0
   */
  private $progressive;

  /**
   * Color to fill background of transparent PNGs and GIFs
   * @var string
   * @since 2.0
   */
  public $background;

  /**
   * Mime types
   * @var array
   * @since 2.0
   */
  private $mimeTypes  = array(
      'jpeg'  => array(
        'image/jpeg'  => 1,
      ),
      'gif' => array(
        'image/gif'   => 1,
      ),
      'png' => array(
        'image/png'   => 1,
        'image/x-png' => 1,
      ),
      'bmp' => array(
        'image/bmp'       => 1,
        'image/x-ms-bmp'  => 1,
      ),
    );

  /**
   * @since 2.0
   */
  final public function __construct()
  {
  }

  /**
   * Destruct method. Clean up memory.
   *
   * @return void
   * @since 2.0
   */
  final public function __destruct()
  {
    unset(
        $this->path,
        $this->data,
        $this->image,
        $this->mime,
        $this->width,
        $this->height,
        $this->cropWidth,
        $this->cropHeight,
        $this->cropper,
        $this->iptc,
        $this->quality,
        $this->progressive,
        $this->background
    );
  }

  /**
   * @param string $name
   * @param mixed $value
   * @since 2.0
   */
  final public function __set($name, $value)
  {
    switch ($name)
    {
      case 'path':
        $this->setPath($value);
          break;

      case 'image':
      case 'mime':
      case 'width':
      case 'height':
      case 'cropWidth':
      case 'cropHeight':
      case 'cropper':
      case 'iptc':
      case 'quality':
      case 'progressive':
      case 'background':
        return $this->$name = $value;
          break;

      default:
        if (property_exists($this, $name)) {
          $this->$name  = $value;
        }
          break;
    } // switch
  }

  /**
   * @since 2.0
   */
  final public function __get($name)
  {
    switch($name)
    {
      case 'data':
        if ($this->data === null) {
          $this->data = $this->getData();
        }
        return $this->data;
          break;

      case 'image':
      case 'mime':
      case 'width':
      case 'height':
      case 'cropWidth':
      case 'cropHeight':
      case 'cropper':
      case 'iptc':
      case 'quality':
      case 'progressive':
      case 'background':
        return $this->$name;
          break;

      default:
        if (property_exists($this, $name)) {
          return $this->$name;
        }
          break;
    }
  }

  /**
   * @param string $path
   * @param boolean $loadImage
   * @since 2.0
   */
  public function setPath($path, $loadImage = true)
  {
    $this->path = $path;

    if ($loadImage === true) {
      // Set the image info (width, height, mime type, etc.)
      $this->setImageInfoFromFile();

      // Make sure the file is actually an image
      if (!$this->isImage()) {
        header('HTTP/1.1 400 Bad Request');
        throw new RuntimeException('Requested file is not an accepted image type: ' . $this->fullPath());
      } // if
    }
  }

  /**
   * @return float
   * @since 2.0
   */
  final public function ratio()
  {
    return $this->width / $this->height;
  }

  /**
   * @return float
   * @since 2.0
   */
  final public function cropRatio()
  {
    if ($this->cropHeight != 0) {
      return $this->cropWidth / $this->cropHeight;
    } else {
      return 0;
    }
  }

  /**
   * @return integer
   * @since 2.0
   */
  final public function area()
  {
    return $this->width * $this->height;
  }

  /**
   * @return string
   * @since 2.0
   */
  final public function fullPath()
  {
    return SLIRConfig::$documentRoot . $this->path;
  }

  /**
   * Checks the mime type to see if it is an image
   *
   * @since 2.0
   * @return boolean
   */
  final public function isImage()
  {
    if (substr($this->mime, 0, 6) == 'image/') {
      return true;
    } else {
      return false;
    }
  }

  /**
   * @since 2.0
   * @param string $type Can be 'JPEG', 'GIF', 'PNG', or 'BMP'
   * @return boolean
   */
  final public function isOfType($type = 'JPEG')
  {
    $method = "is$type";
    if (method_exists($this, $method) && isset($imageArray['mime'])) {
      return $this->$method();
    }
  }

  /**
   * @since 2.0
   * @return boolean
   */
  final public function isJPEG()
  {
    if (isset($this->mimeTypes['jpeg'][$this->mime])) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * @since 2.0
   * @return boolean
   */
  final public function isGIF()
  {
    if (isset($this->mimeTypes['gif'][$this->mime])) {
      return true;
    } else {
      return false;
    }
  }

  final public function isBMP()
  {
    if (isset($this->mimeTypes['bmp'][$this->mime])) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * @since 2.0
   * @return boolean
   */
  final public function isPNG()
  {
    if (isset($this->mimeTypes['png'][$this->mime])) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * @since 2.0
   * @return boolean
   */
  final public function isAbleToHaveTransparency()
  {
    if ($this->isPNG() || $this->isGIF()) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * @since 2.0
   * @return boolean
   */
  private function isCroppingNeeded()
  {
    if ($this->cropWidth !== null && $this->cropHeight != null && ($this->cropWidth < $this->width || $this->cropHeight < $this->height)
    ) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * @since 2.0
   * @return boolean
   */
  private function isSharpeningDesired()
  {
    if ($this->isJPEG()) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * @since 2.0
   * @return void
   */
  private function setImageInfoFromFile()
  {
    $info = $this->getImageInfoFromFile();

    $this->mime   = $info['mime'];
    $this->width  = $info['width'];
    $this->height = $info['height'];
    if (isset($info['iptc'])) {
      $this->iptc   = $info['iptc'];
    }
  }

  /**
   * Retrieves information about the image such as width, height, and IPTC info
   *
   * @since 2.0
   * @return array
   */
  private function getImageInfoFromFile()
  {
    $info = getimagesize($this->fullPath(), $extraInfo);

    if ($info == false) {
      header('HTTP/1.1 400 Bad Request');
      throw new RuntimeException('getimagesize failed (source file may not be an image): ' . $this->fullPath());
    }

    $info['width']  =& $info[0];
    $info['height'] =& $info[1];

    // IPTC
    if (is_array($extraInfo) && isset($extraInfo['APP13'])) {
      $info['iptc'] = iptcparse($extraInfo['APP13']);
    }

    return $info;
  }

  /**
   * @since 2.0
   * @return void
   */
  final public function createBlankImage()
  {
    $this->image  = imagecreatetruecolor($this->width, $this->height);
  }

  /**
   * @since 2.0
   * @param string $path path to BMP file
   * @return resource
   * @link http://us.php.net/manual/en/function.imagecreatefromwbmp.php#86214
   */
  public function imagecreatefrombmp($path)
  {
    // Load the image into a string
    $read = file_get_contents($path);

    $temp = unpack('H*', $read);
    $hex  = $temp[1];
    $header = substr($hex, 0, 108);

    // Process the header
    // Structure: http://www.fastgraph.com/help/bmp_header_format.html
    if (substr($header, 0, 4) == '424d') {
      // Get the width 4 bytes
      $width  = hexdec($header[38] . $header[39] . $header[36] . $header[37]);

      // Get the height 4 bytes
      $height = hexdec($header[46] . $header[47] . $header[44] . $header[45]);
    }

    // Define starting X and Y
    $x  = 0;
    $y  = 1;

    // Create newimage
    $image  = imagecreatetruecolor($width, $height);

    // Grab the body from the image
    $body = substr($hex, 108);

    // Calculate if padding at the end-line is needed
    // Divided by two to keep overview.
    // 1 byte = 2 HEX-chars
    $bodySize    = (strlen($body) / 2);
    $headerSize  = ($width * $height);

    // Use end-line padding? Only when needed
    $usePadding = ($bodySize > ($headerSize * 3) + 4);

    // Using a for-loop with index-calculation instaid of str_split to avoid large memory consumption
    // Calculate the next DWORD-position in the body
    for ($i = 0; $i < $bodySize; $i += 3) {
        // Calculate line-ending and padding
        if ($x >= $width) {
          // If padding needed, ignore image-padding
          // Shift i to the ending of the current 32-bit-block
          if ($usePadding) {
            $i += $width % 4;
          }

          // Reset horizontal position
          $x  = 0;

          // Raise the height-position (bottom-up)
          ++$y;

          // Reached the image-height? Break the for-loop
          if ($y > $height) {
            break;
          }
        }

      // Calculation of the RGB-pixel (defined as BGR in image-data)
      // Define $iPos as absolute position in the body
      $iPos = $i * 2;
      $r    = hexdec($body[$iPos + 4] . $body[$iPos + 5]);
      $g    = hexdec($body[$iPos + 2] . $body[$iPos + 3]);
      $b    = hexdec($body[$iPos] . $body[$iPos + 1]);

      // Calculate and draw the pixel
      $color  = imagecolorallocate($image, $r, $g, $b);
      imagesetpixel($image, $x, $height - $y, $color);

      // Raise the horizontal position
      ++$x;
    }

    // Unset the body / free the memory
    unset($body);

    // Return image-object
    return $image;
  }

  /**
   * @since 2.0
   * @return void
   */
  final public function createImageFromFile()
  {
    try {
      if ($this->isJPEG()) {
        $this->image  = imagecreatefromjpeg($this->fullPath());
      } else if ($this->isGIF()) {
        $this->image  = imagecreatefromgif($this->fullPath());
      } else if ($this->isPNG()) {
        $this->image  = imagecreatefrompng($this->fullPath());
      } else if ($this->isBMP()) {
        $this->image  = $this->imagecreatefrombmp($this->fullPath());
      }
    } catch (Exception $e) {
      // Try an alternate catch-all method
      $this->image  = imagecreatefromstring(file_get_contents($this->fullPath()));
    }
  }

  /**
   * Turns on transparency for image if no background fill color is
   * specified, otherwise, fills background with specified color
   *
   * @param boolean $isBackgroundFillOn
   * @since 2.0
   * @return void
   */
  final public function background($isBackgroundFillOn, $image = null)
  {
    if (!$this->isAbleToHaveTransparency()) {
      return;
    }

    if ($image === null) {
      $image  = $this->image;
    }

    if (!$isBackgroundFillOn) {
      // If this is a GIF or a PNG, we need to set up transparency
      $this->transparency($image);
    } else {
      // Fill the background with the specified color for matting purposes
      $this->fillBackground($image);
    } // if
  }

  /**
   * @since 2.0
   * @return void
   */
  private function transparency($image)
  {
    imagealphablending($image, false);
    imagesavealpha($image, true);
  }

  /**
   * @since 2.0
   * @return void
   */
  private function fillBackground($image)
  {
    $background = imagecolorallocate(
        $image,
        hexdec($this->background[0].$this->background[1]),
        hexdec($this->background[2].$this->background[3]),
        hexdec($this->background[4].$this->background[5])
    );

    imagefilledrectangle($image, 0, 0, $this->width, $this->height, $background);
  }

  /**
   * @since 2.0
   * @return void
   */
  final public function interlace()
  {
    if ($this->progressive) {
      imageinterlace($this->image, 1);
    }
  }

  /**
   * @since 2.0
   * @return integer
   */
  private function getWidth()
  {
    if ($this->cropWidth === null) {
      return $this->width;
    } else {
      return $this->cropWidth;
    }
  }

  /**
   * @since 2.0
   * @return integer
   */
  private function getHeight()
  {
    if ($this->cropHeight === null) {
      return $this->height;
    } else {
      return $this->cropHeight;
    }
  }

  /**
   * @return integer
   * @since 2.0
   */
  private function getQuality()
  {
    if ($this->isJPEG()) {
      return $this->quality;
    } else if ($this->isPNG() || $this->isGIF()) {
      // We convert GIF to PNG, and PNG needs a compression level of 0 (no compression) through 9
      return round(10 - ($this->quality / 10));
    }
  }

  /**
   * Determines if the image can be converted to a palette image
   *
   * @since 2.0
   * @return array colors in image, otherwise false if image is not palette
   */
  private function isPalette()
  {
    $colors = array();

    // Loop over all of the pixels in the image, counting the colors and checking their alpha channels
    for ($x = 0, $width = $this->getWidth(); $x < $width; ++$x) {
      for ($y = 0, $height = $this->getHeight(); $y < $height; ++$y) {
        $color      = imagecolorat($this->image, $x, $y);

        if (isset($colors[$color])) {
          // This color has already been checked, move on to the next pixel
          continue;
        }

        $colors[$color] = true;

        if (count($colors) > 256) {
          // Too many colors to convert to a palette image without losing quality
          return false;
        }

        // Get the alpha channel of the color
        $alpha  = ($color & 0x7F000000) >> 24;

        // What is the threshold for visibility in an alpha channel? (out of 127)
        if ($alpha > 1 && $alpha < 126) {
          return false;
        }
      }
    }

    return $colors;
  }

  /**
   * @since 2.0
   * @return void
   * @link http://us.php.net/manual/ro/function.imagetruecolortopalette.php#44803
   */
  private function trueColorToPalette($dither, $ncolors)
  {
    $palette  = imagecreate($this->getWidth(), $this->getHeight());
    imagecopy($palette, $this->image, 0, 0, 0, 0, $this->getWidth(), $this->getHeight());
    $this->image  = $palette;
    $this->mime   = 'image/png';

    /* For some reason, ImageTrueColorToPalette produces horrible results for true color images that have less than 256 colors. http://stackoverflow.com/questions/5187480/imagetruecolortopalette-losing-colors

    $colorsHandle = ImageCreateTrueColor($this->getWidth(), $this->getHeight());
    ImageCopy($colorsHandle, $this->image, 0, 0, 0, 0, $this->getWidth(), $this->getHeight());
    ImageTrueColorToPalette($this->image, $dither, $ncolors);
    ImageColorMatch($colorsHandle, $this->image);
    ImageDestroy($colorsHandle);
    */
  }

  /**
   * @since 2.0
   * @return void
   */
  final public function optimize()
  {
    $colors = $this->isPalette();
    if ($colors !== false) {
      $this->trueColorToPalette(false, count($colors));
    }
  }

  /**
   * Gets the name of the class that will be used to determine the crop offset for the image
   *
   * @since 2.0
   * @param string $className Name of the cropper class name to get
   * @return string
   */
  private function getCropperClassName($className = null)
  {
    if ($className !== null) {
      return $className;
    } else if ($this->cropper !== null) {
      return $this->cropper;
    } else {
      return SLIRConfig::$defaultCropper;
    }
  }

  /**
   * Gets the class that will be used to determine the crop offset for the image
   *
   * @since 2.0
   * @param string $className Name of the cropper class to get
   * @return SLIRCropper
   */
  final public function getCropperClass($className = null)
  {
    $cropClass  = strtolower($this->getCropperClassName($className));
    $fileName   = SLIRConfig::$pathToSLIR . "/croppers/$cropClass.class.php";
    $class      = 'SLIRCropper' . ucfirst($cropClass);

    if (!file_exists($fileName)) {
      throw new RuntimeException('The requested cropper could not be found: ' . $fileName);
    }

    require_once $fileName;

    return new $class();
  }

  /**
   * Crops the image
   *
   * @since 2.0
   * @param boolean $isBackgroundFillOn
   * @return boolean
   * @todo improve cropping method preference (smart or centered)
   */
  final public function crop($isBackgroundFillOn)
  {
    if (!$this->isCroppingNeeded()) {
      return true;
    }

    $cropper  = $this->getCropperClass();
    $offset   = $cropper->getCrop($this);
    return $this->cropImage($offset['x'], $offset['y'], $isBackgroundFillOn);
  }

  /**
   * Performs the actual cropping of the image
   *
   * @since 2.0
   * @param integer $leftOffset Number of pixels from the left side of the image to crop in
   * @param integer $topOffset Number of pixels from the top side of the image to crop in
   * @param boolean $isBackgroundFillOn
   * @return boolean
   */
  private function cropImage($leftOffset, $topOffset, $isBackgroundFillOn)
  {
    // Set up a blank canvas for our cropped image (destination)
    $cropped  = imagecreatetruecolor(
        $this->cropWidth,
        $this->cropHeight
    );

    $this->background($isBackgroundFillOn, $cropped);

    // Copy rendered image to cropped image
    imagecopy(
        $cropped,
        $this->image,
        0,
        0,
        $leftOffset,
        $topOffset,
        $this->width,
        $this->height
    );

    // Replace pre-cropped image with cropped image
    imagedestroy($this->image);
    $this->image  = $cropped;
    unset($cropped);

    return true;
  }

  /**
   * Sharpens the image
   *
   * @param integer $sharpness
   * @since 2.0
   */
  final public function sharpen($sharpness)
  {
    if ($this->isSharpeningDesired()) {
      imageconvolution(
          $this->image,
          $this->sharpenMatrix($sharpness),
          $sharpness,
          0
      );
    }
  }

  /**
   * @param integer $sharpness
   * @return array
   * @since 2.0
   */
  private function sharpenMatrix($sharpness)
  {
    return array(
      array(-1, -2, -1),
      array(-2, $sharpness + 12, -2),
      array(-1, -2, -1)
    );
  }

  /**
   * @since 2.0
   * @return array
   */
  final public function cacheParameters()
  {
    return array(
      'path'        => $this->fullPath(),
      'width'       => $this->width,
      'height'      => $this->height,
      'cropWidth'   => $this->cropWidth,
      'cropHeight'  => $this->cropHeight,
      'iptc'        => $this->iptc,
      'quality'     => $this->getQuality(),
      'progressive' => $this->progressive,
      'background'  => $this->background,
      'cropper'     => $this->getCropperClassName(),
    );
  }

  /**
   * @since 2.0
   * @return string
   */
  private function getData()
  {
    ob_start(null);
      if (!$this->output()) {
        return false;
      }
      $data = ob_get_contents();
    ob_end_clean();

    return $data;
  }

  /**
   * @since 2.0
   * @return boolean
   */
  private function output($filename = null)
  {
    if ($this->isJPEG()) {
      return imagejpeg($this->image, $filename, $this->getQuality());
    } else if ($this->isPNG()) {
      return imagepng($this->image, $filename, $this->getQuality());
    } else if ($this->isGIF()) {
      return imagegif($this->image, $filename, $this->getQuality());
    } else {
      return false;
    }
  }

  /**
   * @since 2.0
   * @return integer
   */
  final public function fileSize()
  {
    return strlen($this->data);
  }

  /**
   * @since 2.0
   * @return boolean
   */
  final public function destroyImage()
  {
    return imagedestroy($this->image);
  }

}