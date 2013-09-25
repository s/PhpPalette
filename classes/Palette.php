<?php 

/*
* Palette.php
* Said Ozcan
* 25 September 2013 1:47PM
*/

require_once 'Exception/PException.php';


/*
* class Palette
* this class handles saving image, processing image and rendering the output html
*/

class Palette{

	// the original path of the incoming image

	protected $original_path;

	// copied path of incoming image in the project

	protected $copied_image_path;

	// if image is from local disk this will be true

	protected $image_is_locale = false;

	// holds just image name without path

	protected $image_name;

	// this array holds hexadecimal code of every pixel in the image.
	
	protected $histogram = array();

	// holds mime type of incoming image

	protected $mime_type;

	// allowed image mime types to process

	protected $allowed_mime_types = array('image/jpeg','image/png');

	// base template file

	protected $template_file;

	// path of the charts which will be generated

	protected $charts_path;

	// dimensions of the incoming image

	protected $image_dimensions = array();

	// image default width is set to 400.

	protected $image_default_width;
	



	/*
	* method constructor
	* this method sets up the requirements
	* @param (string) original_path
	* @param (string) image_is_locale
	* @return void	
	*/

	public function __construct($original_path, $image_is_locale){

		$this->image_is_locale = $image_is_locale;		

		$this->original_path = $original_path;

		$this->mime_type = mime_content_type($this->original_path);

		$this->template_file = ROOT_DIR.'/outputs/templates/app.html';

		$this->charts_path = ROOT_DIR.'/outputs/charts/';

		$this->image_default_width = 400;



		if ($this->image_is_locale) {
			
			if(is_dir($this->original_path))

				throw new PException('InvalidFileException','Specified path is a directory.',2);
			
			if(!file_exists($this->original_path))

				throw new PException('FileNotFoundException','Specified file does not exist.',3);

			if(!in_array($this->mime_type, $this->allowed_mime_types))

				throw new PException('FileNotSupportedException','Specified file is not allowed.',3);

			$exploded_path = explode(DIRECTORY_SEPARATOR, $this->original_path);

			$this->image_name = uniqid().'-'.$exploded_path[sizeof($exploded_path)-1];

			$this->copied_image_path = ROOT_DIR.'/outputs/data/'.$this->image_name;

			try{
				copy($this->original_path,ROOT_DIR.'/outputs/data/'.$this->image_name);

			}catch(Exception $exc){

				throw new PException('FileMoveException','Specified file can not been copied.',4);
			}

			
		}

	}
	




	/*
	* method process
	* this method processes the image and triggers render() method
	* @param void	
	* @return void	
	*/

	public function process(){		
		
		try{
			
			if ('image/jpeg' == $this->mime_type) {

				$image = ImageCreateFromJpeg($this->copied_image_path);
			}else{
				$image = imagecreatefrompng($this->copied_image_path);
			}
			
			$this->image_dimensions[0] = $image_width = imagesx($image);

			$this->image_dimensions[1] = $image_height = imagesy($image);

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

		$this->render();

	}
	



	/*
	* method get_hex_colors
	* this method sets the histogram with the hexadecimal color codes
	* @param void
	* @return void
	*/

	private function get_hex_colors(){

		foreach($this->histogram as $idx => $val ){

			$red = ($val >> 16) & 0xFF;

			$green = ($val >> 8) & 0xFF;

			$blue = $val & 0xFF;
			
			$hexa = $this->rgb_to_hex(array($red,$green,$blue));
			
			$this->histogram[$idx] = $hexa;
		}

	}




	/*
	* method rgb_to_hex
	* this method finds the hexadecimal color code of given rgb color code
	* @param void
	* @return void
	*/

	private function rgb_to_hex($rgb) {
	   
	   $hex = "#";
	   
	   $hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
	   
	   $hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
	   
	   $hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);

	   return $hex; // returns the hex value including the number sign (#)
	}




	/*
	* method render
	* this method renders a new html file with the all of the data
	* @param void
	* @return void
	*/

	private function render(){

		try{

			$image_name = explode('-',$this->image_name);

			$image_name = $image_name[1];
			
			$template = fopen($this->template_file,'r');
			
			$chart = fopen($this->charts_path.uniqid().'.html','w+');

			$html = fread($template,filesize($this->template_file));

			$replace_keys = array(
							'{ImageName}',
							'{ImageSource}',
							'{ImageWidth}',
							'{UploadAreaWidth}'
						);

			$image_width = $this->image_dimensions[0] > $this->image_default_width ? $this->image_default_width : $this->image_dimensions[0];

			$replace_values = array(
							$image_name,
							$this->copied_image_path,
							$image_width,
							$image_width+5
						);

			$html = str_replace($replace_keys,$replace_values,$html);			

			fwrite($chart, $html);

			fclose($template);

			fclose($chart);

		}catch(Exception $exc){

			throw new PException('TemplateRenderException','An error occured while rendering the template file.');
		}

	}
}