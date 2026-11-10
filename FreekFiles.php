#!/usr/bin/php
<?php 
error_reporting(E_ALL|E_STRICT); 
ini_set('display_errors', true);
require_once('Axioms.php');
require_once('array_to_file.php');

/* Code by M. Beeson, last modified 3.5.17 
 This file contains code to produce Definitions.txt, Axioms.txt,
 and Theorems.txt.
 Freek-specified format for definitions: 
 ray     `RA A B C <=> ?X. BE X A C /\ BE X A B`
 outside `OU P J <=> ?C A B E. CI J C A B /\ BE C E P /\ EE C E A B /\ CA B E`
 a single tab on each line, separating the name from the statement.

 Freek-specified format for theorems and axioms
 
proposition     23C     `NE A B /\ NC D C E /\ NC A B P ==> ?X Y. RA A B Y /\ EA X A Y D C E /\ SS X P A B`     Prop23C.prf

So the first two "fields" are the two strings naming the
thing, the third is the statement in HOL Light syntax,
and the fourth is the name of the file that proves the
statement (left empty if the thing is an axiom/postulate).
Mind the three separating tabs in that line.
*/

//___________________________________________________
function functor($p)
// return the first two characters of $p
{  if(strlen($p) < 2)
	   { echo "Error in functor $p\n";
		 die();
	   }
   return substr($p,0,2);
}
//___________________________________________________
function args($p)
{ if(strlen($p) < 3)
	{ echo "Error in args of $p\n";
	  die();
	}
  return substr($p,2,strlen($p)-2);
}



//________________________________________________________________

function format_formula($formula)
// return the desired string representation of the formula without backquotes
{ if(is_array($formula))
	{ $a = $formula;
	  $ans = "";
	  for($i = 0; $i < count($a); $i++)
		 { $t = $a[$i];
		   $ans = $ans . format_formula($t);
		   if($i + 1 < count($a))
			  $ans = $ans . " /\\ ";
	     }
	  return $ans;
     }
  $f = functor($formula);
  $x = args($formula);
  if($f == "AN")
    { $a = explode("+", $x);
	  $ans = "";
	  for($i = 0; $i < count($a); $i++)
	    { $t = $a[$i];
		  $ans = $ans . format_formula($t);
		  if($i + 1 < count($a))
		     $ans = $ans . " /\\ ";
		}
	  return $ans;
	}
  if($f == "OR")
    { $a = explode("|", $x);
	  $ans = "(";  // parenthesize disjunctions 
	  for($i = 0; $i < count($a); $i++)
	    { $t = $a[$i];
		  $ans = $ans . format_formula($t);
		  if($i + 1 < count($a))
		     $ans = $ans . " \\/ ";
		}
	  return $ans . ")";  // add closing paren
	}
  if($f == "NO")
    { return "~(" . format_formula(args($formula)) . ")";
	}
  // Now it's an atomic formula
  $ans = $f . " ";
  for($i = 0; $i < strlen($x); $i++)
     { $ans = $ans . $x[$i];
	   if($i + 1 < strlen($x))
	      $ans = $ans . " ";
	 }
  return $ans;
}

//________________________________________________________________

function format_definitions($defns)
//  return an array containing the formatted definitions
{ $answer = array();
  foreach($defns as $defn)
	{ $line =  $defn->label . "\t`";
	  $vars = $defn->existential;
	  $formula = $defn->conclusion;
	  $line = $line . format_formula($defn->hypotheses);
      $line = $line . " <=> ";
      if($vars != "")
        { $line = $line . "?";
	      for($i = 0; $i < strlen($vars); $i++) 
	        { $line = $line . $vars[$i];
		      if($i + 1 < strlen($vars))
		          $line = $line . " ";
		      else
		          $line = $line . ". ";
		    }
		}
	  $line = $line . format_formula($defn->conclusion) . "`";
	  $answer[] = $line;
	}
   return $answer;
}
//________________________________________________________________

function format_theorems($thms)
//  return an array containing the formatted definitions
{ $answer = array();
  foreach($thms as $thm)
	{ $h = format_formula($thm->hypotheses);	  
	  $line =  $thm->kind . "\t" . $thm->label . "\t`" ;
	  if($h != "")
	      $line = $line . $h  . " ==> ";
	  $vars = $thm->existential;
	  if($vars != "")
        { $line = $line . "?";
	      for($i = 0; $i < strlen($vars); $i++) 
	        { $line = $line . $vars[$i];
		      if($i + 1 < strlen($vars))
		          $line = $line . " ";
		      else
		          $line = $line . ". ";
		    }
	     }
	  $line = $line . format_formula($thm->conclusion) . "`\t";
	  if($thm->kind == "proposition")
	     $filename = "Prop" . $thm->label . ".prf";
	  else if($thm->kind == "lemma")
	     $filename = $thm->label . ".prf";
	  else 
         $filename = "";
      $line = $line . $filename;
	  $answer[] = $line;
	}
   return $answer;
}
//___________________________________________________

// Produce the desired output files

array_to_file("Definitions.txt", "./",format_definitions($definitions));
$axioms = format_theorems($axioms);
$theorems = format_theorems($results);
$answer = $axioms;
foreach($theorems as $x)
   $answer[] = $x;
array_to_file("Theorems.txt", "./", $answer);


	  

