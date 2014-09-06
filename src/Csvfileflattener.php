<?php
namespace Csvfileflattener;

use \CsvParser\Parser as Parser;

/**
 * A class to import a CSV into an array, flatten it, and export to another file
 * 
 * @author Alex Brims
 */
class Csvfileflattener
{
	/**
	 * @var Csvfileflattener
	 */
	protected static $_instance;
	
	protected $_data = array();
	
	/**
	 * @var Parser
	 */
	protected $_parser;
	
	/**
	 * Singleton constructor
	 * 
	 * @access protected
	 */
	protected function __construct() {}
	
	/**
	 * Singleton access
	 * 
	 * @return Csvfileflattener
	 */
	public static function instance()
	{
		if ( ! self::$_instance)
		{
			// Need to use this function to make available for subclass
			$class = get_called_class();
			self::$_instance = new $class();
		}
		
		return self::$_instance;
	}
	
	/**
	 * Takes a Parser object and input/output filenames, writes a flattened version
	 * of the input file to the output file.  A Csvfileflattener exception will be
	 * thrown if the import/export can't be completed. 
	 * 
	 * @param  Parser $parser
	 * @param  string $filename
	 * @param  string $output_filename
	 * @return boolean
	 */
	public function process_file(Parser $parser, $filename, $output_filename)
	{
		$data = $this->import_file($parser, $filename);
		
		$flat_data = $this->flatten_array($data);

		// In order to create a multi-line CSV, an additional array layer needs to be added to the flattened data
		$unflat_data = $this->add_array_layer($flat_data);
		
		$this->export_file($parser, $output_filename, $unflat_data);
		
		// If we made it here without an exception then the process is assumed to be successful
		return TRUE;
	}
	
	/**
	 * Imports $filename into an array, using $parser
	 * 
	 * @param  Parser $parser
	 * @param  string $filename
	 * @throws CsvfileflattenerException
	 * @return array
	 */
	public function import_file(Parser $parser, $filename)
	{
		if ( ! file_exists($filename))
			throw new Exception($filename.' does not exist'); 
		
		return $parser->toArray($parser->fromFile($filename));
	}
	
	/**
	 * Flattens the array
	 * 
	 * @param  array $array
	 * @return array:
	 */
	public function flatten_array(array $array)
	{
		return iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($array)), FALSE);
	}
	
	/**
	 * Adds an extra layer to the array for use with Parser::toString
	 * 
	 * @param  array $array
	 * @return array
	 */
	public function add_array_layer(array $array)
	{
		foreach($array as $key => $value)
		{
			$array[$key] = array($value);
		}
		
		return $array;
	}
	
	/**
	 * 
	 * @param  Parser $parser
	 * @param  string $filename
	 * @param  array $data
	 * @return number
	 */
	public function export_file(Parser $parser, $filename, array $data)
	{
		return $parser->toFile($parser->fromArray($data), $filename);
	}
}