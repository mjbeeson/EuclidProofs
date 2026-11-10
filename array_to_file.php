<?php
function array_to_file($filename, $directory, $array)
// open the file, write the strings in $array (which is an array of strings)
// one string per line, and close the file.  Put the file in the specified
// directory.  Create the directory and/or file if they don't exist.  Overwrite
// the file if it does exist.
{ if(!file_exists($directory))
	 mkdir($directory);
  $pathname = $directory . "/" . $filename;
  $fp = fopen($pathname, 'w');
  foreach($array as $x)
    { if(!is_string($x))
	    { echo "Error in array_to_file, non-string in array.\n"; 
		  print_r($x);
		  die();
		}
	  fwrite($fp, $x . "\n");
	}
  fclose($fp);
}
?>