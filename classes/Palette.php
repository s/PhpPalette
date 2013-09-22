<?php 

require_once 'Exception/PException.php';

class Palette{

	protected $original_path;

	protected $image_is_locale = false;

	protected $image_name;

	protected $histogram = array();

	protected $mime_type;

	protected $copied_image_path;

	protected $allowed_mime_types = array('image/jpeg','image/png');

	public function __construct($original_path, $image_is_locale){

		$this->image_is_locale = $image_is_locale;		

		$this->original_path = $original_path;

		$this->mime_type = mime_content_type($this->original_path);

		if ($this->image_is_locale) {
			
			if(is_dir($this->original_path))

				throw new PException('InvalidFileException','Specified path is a directory.',2);
			
			if(!file_exists($this->original_path))

				throw new PException('FileNotFoundException','Specified file does not exist.',3);

			if(!in_array($this->mime_type, $this->allowed_mime_types))

				throw new PException('FileNotSupportedException','Specified file is not allowed.',3);

			$exploded_path = explode(DIRECTORY_SEPARATOR, $this->original_path);

			$this->image_name = $exploded_path[sizeof($exploded_path)-1];

			$this->copied_image_path = ROOT_DIR.'/outputs/data/'.$this->image_name;

			try{				
				
				copy($this->original_path,ROOT_DIR.'/outputs/data/'.$this->image_name);

			}catch(Exception $exc){

				throw new PException('FileMoveException','Specified file can not been copied.',4);
			}

			
		}

	}

	public function draw(){		
		
		try{
			
			if ('image/jpeg' == $this->mime_type) {

				$image = ImageCreateFromJpeg($this->copied_image_path);
			}else{
				$image = imagecreatefrompng($this->copied_image_path);
			}
			
			$image_width = imagesx($image);

			$image_height = imagesy($image);

			$histogram = array();

			for ($i=0; $i < $image_width; $i++) { 
				
				for ($j=0; $j < $image_height ; $j++) { 
					
					$rgb = ImageColorAt($image, $i, $j);						

            		$histogram[$rgb] +=1;
                           		
				}

			}

			for ($i=0; $i < 5 ; $i++) { 
				
				$max_value = max($histogram);

				$max_index_array = array_keys($histogram, $max_value);

				$max_index = $max_index_array[0];

				$this->histogram[$i] = $max_value;

				unset($histogram[$max_index]);

			}
			
			$this->get_hex_colors();
			
			
		}catch(Exception $exc){

			throw new PException('ImageProcessingException','An error occured while processing.',5);

		}		

	}

	private function get_hex_colors(){

		foreach($this->histogram as $idx => $val ){

			$red = ($val >> 16) & 0xFF;

			$green = ($val >> 8) & 0xFF;

			$blue = $val & 0xFF;
			
			$hexa = $this->rgb_to_hex(array($red,$green,$blue));
			
			$this->histogram[$idx] = $hexa;
		}

	}
	private function rgb_to_hex($rgb) {
	   
	   $hex = "#";
	   
	   $hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
	   
	   $hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
	   
	   $hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);

	   return $hex; // returns the hex value including the number sign (#)
	}
}

?>