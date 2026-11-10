#!/usr/bin/php
<?php 
// This file analyzes the dependencies of the proofs
error_reporting(E_ALL|E_STRICT); 
ini_set('display_errors', true);
require_once('Axioms.php');
require_once('array_to_file.php');




function analyze($filename)
// $filename should be without extension, such as "Prop01"
//  containing lines like 24, 5, 17  meaning line 24 depends on lines 5, 17.
{ $path = "./dependencies/" . $filename . ".prfdep";
  $dependencies = file($path);
  $proof = file($filename . ".prf");
  $nlines = count($proof);
  // following loop decreases nlines to not count any final blank lines in the file
  for($i=$nlines-1; $i > 0; $i--)
     { $x = trim($proof[$i]);
	   if($x == "")
	      { --$nlines;
		    continue;
		  }
	   break;
	 }
  $used = array();
  foreach($dependencies as $x)
     { $temp = explode(" ", $x);
	   $n = count($temp);
	   for($i = 1; $i < $n; $i++)
	      $used[] = $temp[$i];
	 } 
  $reduced = array_unique($used);
  $ans =  "unused in " . $filename . ".prf" . ":    ";
  $outputflag = false;
  for($i = 1; $i < $nlines; ++$i)  // "<", not "<=",  otherwise last line counts as "unused" 
    { if(!in_array($i,$reduced))
	     { $ans = $ans . $i;
		   $theline = trim($proof[$i-1]);
		   if($theline == "")
		      { // blank line
			    $ans = $ans . "b ";
			    continue;
			  }
		   if($theline[0]== '%')
		      { // it's a commented line
			    $ans = $ans . "% ";
			    continue;
			  } 
		   if(strstr($proof[$i],"equalitysub"))
		      { // The "unused" line is right before an equalitysub, so it's probably 
			    // a deliberate repetition
			   $ans = $ans . "* ";
			   continue;
			  }
		   if(
		      (strstr($proof[$i],"defn") 
			   || strstr($proof[$i], "lemma")
		       || strstr($proof[$i], "proposition")
		      ) 
		      && strstr($proof[$i-1],"AN")
		      )
		      {  // The "unused" line is a conjunction right before using a 
			    // definition or lemma, so it is probably intentional
			     $ans = $ans . "# ";
			     continue;
			  }
		   $ans = $ans . " ";
		   $outputflag = true;
		 }
	}
  if($outputflag)
     echo $ans . "\n";
  return $outputflag;
}
//_________________________________________________
$ct = 0;
 foreach($results as $theorem)
   { $name = $theorem->label;
	 if($theorem->kind == "proposition")
	    $name = "Prop" . $name;
	 if($theorem->kind == "lemma" || $theorem->kind == "proposition")
	    { $flag = analyze($name);
		  if($flag)
		     ++$ct;
		  if( $name == "Prop48") 
		      break;
		}
   }
 echo $ct . " proofs left to fix.\n";


//analyze("sumofparts");
	  
	     