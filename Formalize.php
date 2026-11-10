#!/usr/bin/php
<?php 
// Strings can be written on two lines by using a period to concatenate the lines.
// We did this in some places to permit this file to be printed for archival purposes.
error_reporting(E_ALL|E_STRICT); 
ini_set('display_errors', true); 
require_once('Euclid.php');
 

function valid_label($x)
// x should be a string with one colon.  Before the colon
// should come cn, defn, axiom, postulate, proposition, or lemma.
// After the colon should come the name of a valid thing of the specified kind.
// Return true if the name is valid, false if not.
{ global $axioms, $lemmas, $definitions;
  $temp = explode(":",$x);
  if(count($temp) != 2)
     return true;
  $kind = $temp[0];
  $label = $temp[1];
  foreach($axioms as $axiom)
     { if($axiom->kind == $kind && $axiom->label == $label)
	      return  true;
	 }
  foreach($lemmas as $lemma)
	 { if($lemma->kind == $kind && $lemma->label == $label)
		  return  true;
	 }
  foreach($definitions as $defn)
	 { if($defn->label == $label)
		  return  true;
	 }
  return false;
}
	
function check_proof($P)
// $P is a Proof object. This is not really a proof-checker,
// it only checks that there exist definitions, propositions, axioms, etc. that are
// mentioned in the justifications of $P.

{ foreach($P->lines as $line)
	{ if(! valid_label($line->label))
		 echo "Invalid label " . $line->label . "\n";
	}
}

make_files();
$files = scandir("./",1);
$textfiles = array();
foreach($files as $file)
   { if(!strstr($file, ".txt"))
	      continue;
	 echo "made $file\n";
	 $textfiles[] = $file;
   }
/* sort($textfiles);
$N = 5;
for($i=0;$i<$N;$i++)
   { if($i==1) continue;
	$currentfile = $textfiles[$i];
	 echo "Working on $currentfile\n";
	$P = formalize_proof($currentfile);
	check_proof($P);  // and echo any error messages 
	$proof = $P->to_array();
	$outfile = preg_replace("~.txt~", ".prf", $currentfile);
	echo "Creating $outfile\n";
	array_to_file($outfile, ".", $proof); 
  } */
   



?>