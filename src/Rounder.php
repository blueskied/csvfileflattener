<?php
namespace Csvfileflattener;

use \CsvParser\Parser as Parser;

/**
 * A class to import a CSV into an array, flatten it, and export to another file
 * 
 * @author Alex Brims
 */
class Rounder extends Csvfileflattener
{
	/**
	 * Rounds any numbers to ints.  Requires a pre-flattened-and-unflatted array
	 * 
	 * @param  array $array
	 * @return array 
	 */
	protected function _round_numbers_to_int(array $array)
	{
		// Go through both layers of the multi-dimensional array and round the numbers
		foreach($array as $key => $value)
		{
			foreach ($value as $subkey => $subvalue)
			{
				if (is_numeric($subvalue))
				{
					$array[$key][$subkey] = round($subvalue);
				}
			}
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
		$data = $this->_round_numbers_to_int($data);
		
		return $parser->toFile($parser->fromArray($data), $filename);
	}
}