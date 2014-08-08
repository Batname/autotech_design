<?php 
/**
 * ------------------------------------------------------------------------
 * JM Slideshow module for magento
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */
 

if( !class_exists('JoomlArt_JmSlideshow_Helper_Jmimage') ){
	
	/**
	 * JM Image Class, using for render thumb image or crop image from orginal image.
 	 *
 	 * @author    joomlart.com (@see http://joomlart.com, @email: webmaster@joomlart.com)
 	 * @copyright Copyright (C) August - 2009, J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 	 * @license	 GNU/GPL
 	 * @version 1.0
 	 */
	class JoomlArt_JmSlideshow_Helper_Jmimage extends Mage_Core_Helper_Abstract {
		
		/**
		 * Identifier of the cache path.
		 *
		 * @access private
		 * @param string $__cachePath
		 */
		private  $__cachePath;
		
		/**
		 * Identifier of the path of source.
		 *
		 * @access private
		 * @param string $__imageBase
		 */
		private $__imageBase;
		
		/**
		 * Identifier of the image's extensions
		 *
		 * @access public
		 * @param array $types
		 */
		private $types = array();
	
		/**
		 * Identifier of the quantity of thumnail image.
		 *
		 * @access public
		 * @param string $__quality
		 */
		private $__quality = 90;
		
		/**
		 * Identifier of the url of folder cache.
		 *
		 * @access public
		 * @param string $__cacheURL
		 */
		private $__cacheURL;
		
		
		/**
		 * Identifier of the url of folder cache.
		 *
		 * @access public
		 * @param string $__noImage
		 */
		private $__noImage = null;
		
		private $__thumbmode = 'crop';
		
		/**
		 * constructor 
		 */
		function __construct()
		{
			$this->types = array( 1 => "gif", "jpeg", "png", "swf", "psd", "wbmp" );	
			$this->__imageBase =  Mage::getBaseDir().DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR;
			$this->__cachePath = $this->__imageBase.'resized'.DIRECTORY_SEPARATOR;
			$this->__cacheURL = 'media/resized/';
		}
		/**
		 * constructor 
		 */
		public function JAImage()
		{
			$this->__construct();
		}
		/**
		 * get a instance of JAImage object.
		 *
		 * This method must be invoked as:
		 * 		<pre>  $jaimage = &JAImage::getInstace();</pre>
		 *
		 * @static.
		 * @access public,
		 */
		public function &getInstance()
		{
			static $instance = null;
			if( !$instance )
			{
				$instance = new JAImage();
			}
			return $instance;
		}
		/**
		 * crop or resize image
		 * 	
		 *
		 * @param string $image path of source.
		 * @param integer $width width of thumnail
		 * @param integer $height height of thumnail
		 * @param boolean $aspect whether to render thumnail base on the ratio
		 * @param boolean $crop whether to use crop image to render thumnail.
		 * @access public,
		 */
		public function resize( $image, $width, $height, $crop=true,  $aspect=true ){
			// get image information
			if( !$width || !$height ) return '';
		
			$imagSource =Mage::getBaseDir().DIRECTORY_SEPARATOR. str_replace( '/', DIRECTORY_SEPARATOR,  $image );
			if( !file_exists($imagSource) || !is_file($imagSource) ){ return ''; }
			$size = getimagesize( $imagSource );
			// if it's not a image.
			if( !$size ){ return ''; }
			
			 // case 1: render image base on the ratio of source.
		 	$x_ratio = $width / $size[0];
			$y_ratio = $height / $size[1];
			
			// set dst, src
			$dst = new stdClass(); 
			$src = new stdClass();
			$src->y = $src->x = 0;
			$dst->y = $dst->x = 0;
		
			if ($width > $size[0])
				$width = $size[0];
			if ($height > $height)
				$height = $size[1];
				
		
			if ( $crop ) 
			{	// processing crop image 	
				$dst->w = $width;
				$dst->h = $height;
				if ( ($size[0] <= $width) && ($size[1] <= $height) ) 
				{
					$src->w = $width;
					$src->h = $height;
				} 
				else 
				{
					if ($x_ratio < $y_ratio)
					{
						$src->w = ceil ( $width / $y_ratio );
						$src->h = $size[1];
					} 
					else
					{
						$src->w = $size[0];
						$src->h = ceil ( $height / $x_ratio );
					}
				}
				$src->x = floor ( ($size[0] - $src->w) / 2 );
				$src->y = floor ( ($size[1] - $src->h) / 2 );
			}
			else
			{ // processing resize image.
				$src->w = $size[0];
				$src->h = $size[1];
				if( $aspect ) 
				{ // using ratio
					if ( ($size[0] <= $width) && ($size[1] <= $height) )
					{
						$dst->w = $size[0];
						$dst->h = $size[1];
					} else if ( ($size[0] <= $width) && ($size[1] <= $height) ) 
					{
						$dst->w = $size[0];
						$dst->h = $size[1];
					} 
					else if ( ($x_ratio * $size[1]) < $height ) 
					{
						$dst->h = ceil ( $x_ratio * $size[1] );
						$dst->w = $width;
					} 
					else {
						$dst->w = ceil ( $y_ratio * $size[0] );
						$dst->h = $height;
					}
				} else { // resize image without the ratio of source.
					$dst->w = $width;
					$dst->h = $height;
				}
			}
			//
			$ext =	substr ( strrchr ( $image, '.' ), 1 ); 
			$thumnail =  substr ( $image, 0, strpos ( $image, '.' )) . "_{$width}_{$height}.".$ext; 
			$imageCache   = $this->__cachePath .  str_replace( '/', DIRECTORY_SEPARATOR, $thumnail );
		
			if( file_exists($imageCache) )
			{
				$smallImg = getimagesize ( $imageCache );
				if ( ($smallImg [0] == $dst->w && $smallImg [1] == $dst->h)  )
				{
					return  $this->__cacheURL. $thumnail;
				}
			} 
		
			if( !file_exists($this->__cachePath) && !mkdir($this->__cachePath) )
			{
				return '';
			}
			
			if( !$this->makeDir( $image ) ) {
				return '';
			}
			
			// resize image
			$this->_resizeImage( $imagSource, $src, $dst, $size, $imageCache ); 
			
			return  $this->__cacheURL. $thumnail;					
		}
		
		/**
		 *  check the folder is existed, if not make a directory and set permission is 755
		 *
		 *
		 * @param array $path
		 * @access public,
		 * @return boolean.
		 */
		public function makeDir( $path )
		{
			$folders = explode ( '/',  ( $path ) );
			$tmppath = $this->__cachePath;	
			for( $i = 0; $i < count ( $folders ) - 1; $i ++) 
			{
				if (! file_exists ( $tmppath . $folders [$i] ) && ! mkdir ( $tmppath . $folders [$i], 0755) )
				{
					return false;
				}	
				$tmppath = $tmppath . $folders [$i] . DIRECTORY_SEPARATOR;
			}		
			return true;
		}
				
		/**
		 *  process render image 
		 *
		 * @param string $imageSource is path of the image source.
		 * @param stdClass $src the setting of image source
		 * @param stdClass $dst the setting of image dts 
		 * @param string $imageCache path of image cache ( it's thumnail).
		 * @access public,
		 */
		protected function _resizeImage( $imageSource, $src, $dst, $size, $imageCache )
		{
			// create image from source.
			$extension = $this->types[$size[2]];
			$image = call_user_func( "imagecreatefrom".$extension, $imageSource );
			
			if( function_exists("imagecreatetruecolor") 
								&& ($newimage = imagecreatetruecolor($dst->w, $dst->h)) )
			{
				
				if( $extension == 'gif' || $extension == 'png' )
				{
					imagealphablending ( $newimage, false );
					imagesavealpha ( $newimage, true );
					$transparent = imagecolorallocatealpha ( $newimage, 255, 255, 255, 127 );
					imagefilledrectangle ( $newimage, 0, 0, $dst->w, $dst->h, $transparent );
				}
				
				imagecopyresampled ( $newimage, $image, $dst->x, $dst->y, $src->x, $src->y, $dst->w, $dst->h, $src->w, $src->h );
			} 
			else
			{
				$newimage = imagecreate ( $width, $height );
				imagecopyresized ( $newimage, $image, $dst->x, $dst->y, $src->x, $src->y, $dst->w, $dst->h, $size[0], $size[1] );
			}
	 	
			switch( $extension )
			{
				case 'jpeg' :
					call_user_func( 'image'.$extension, $newimage, $imageCache, $this->__quality );	
					break;
				default:
					call_user_func( 'image'.$extension,$newimage, $imageCache );
					break;	
			}
			// free memory
			imagedestroy ( $image );
			imagedestroy ( $newimage );
		}
		
		/**
		 *
		 */
		public function getNoImage( $srcFile )
		{
			$this->noImage = $srcFile;
			return $this;
		}
		/**
		 * set quality image will render.
		 */
		public function setQuality( $number = 9 )
		{
			$this->__quality = $number;
			return $this;
		}
		
		/**
		 * check the image is a linked image from other server.
		 *
		 *
		 * @param string the url of image.
		 * @access public,
		 * @return array if it' linked image, return false if not
		 */
		public function isLinkedImage( $imageURL )
		{
			$parser = parse_url($imageURL);
			// return  strpos( JURI::base (), $parser['host'] ) ?false:$parser;
		}
		
		/**
		 * check the file is a image type ?
	 	 *
	 	 * @param string $ext
	 	 * @return boolean.
		 */
		public function isImage( $ext = '' )
		{
			return in_array($ext, $this->types);
		}
		
		/**
		 * check the image source is existed ?
		 *
		 * @param string $imageSource the path of image source.
		 * @access public,
		 * @return boolean,
		 */
		public function sourceExited( $imageSource ) 
		{
			
			if( $imageSource == '' || $imageSource == '..' || $imageSource == '.' )
			{
				return false;
			}
		//	$imageSource = str_replace ( JURI::base (), '', $imageSource );
			$imageSource = rawurldecode ( $imageSource );
			return ( file_exists (Mage::getBaseDir() . '/' . $imageSource ) );	
		}
		
		public function setConfig( $thumbnailMode, $ratio=true )
		{
			$this->__thumbmode = $thumbnailMode;
			
			if( $thumbnailMode != 'none' )
			{
				$this->__isCrop = $thumbnailMode == 'crop' ? true:false;
				$this->__isResize = $ratio;
			}
			return $this;
		}
		
		public function resizeThumb( $image, $width, $height )
		{
			if( $this->__thumbmode == 'none' || empty($this->__thumbmode) )
			{
				return $image;	
			}
			return $this->resize( $image, $width, $height, $this->__isCrop, $this->__isResize  );
		}
		/**
		 * check the image source is existed ?
		 *
		 * @param string $imageSource the path of image source.
		 * @access public,
		 * @return boolean,
		 */
		public function parseImage( $text ) 
		{
			$regex = "/\<img.+src\s*=\s*\"([^\"]*)\"[^\>]*\>/";
			preg_match ( $regex, $text, $matches );
			$images = (count ( $matches )) ? $matches : array ();
			$image = count ( $images ) > 1 ? $images [1] : '';
			return $image;
		}
	}
}
?>