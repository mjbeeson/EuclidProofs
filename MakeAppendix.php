#!/usr/bin/php
<?php 
error_reporting(E_ALL|E_STRICT); 
ini_set('display_errors', true);
require_once('Axioms.php');

/* Code by M. Beeson, last modified 7.28.17
Writes TeX code for the app\\endix of "Proof-checking Euclid".
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


//_______________________________________________________________
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
    { fwrite($fp, $x . "\n");
	}
  fclose($fp);
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
function display_definition($t)
// $t is a definition to be written to stdout in alltt mode
{   if($t->label == "tarski_parallel")
	   echo "tarski\\_parallel";
    else
	   echo $t->label;
    echo "  {\\em " . $t->english . "}" ;
	echo "\n   ";
	if($t->hypotheses == "" || count($t->hypotheses) == 0)
	  { echo $t->conclusion;
		echo "\n";
	  } 
	else
	  { echo "The definition of "; 
		if(is_string($t->hypotheses))
		   { echo $t->hypotheses;
		   }
		else 
	       {  $ct = 0;
		      foreach($t->hypotheses as $h)
	             { echo "$h ";   
		            ++$ct;
		           if($ct == 7) echo "\n   ";
		         }        
	       }
	    echo "\n   ";
	    echo "is: ";
	    if($t->existential != "")
	       { echo "For some " . $t->existential . ", "; 
		   }
	    if(is_string($t->conclusion))
	       { echo $t->conclusion;
		     echo "\n";
		   }
	    else 
	       {$ct = 0;
		     foreach($t->conclusion as $c)
	            {  if($ct == 8) 
					  echo "\n   "; 
		 		   echo "$c ";  
				   ++$ct;     
		        }
	         echo "\n";
	       }
	   }
}

//___________________________________________________
function display_proposition($t)
// $t is an axiom or theorem to be written to stdout in alltt mode
{  if(get_class($t) == "Definition")
	   {
		 display_definition($t);
		 return;    
	   }
	echo $t->label;
	echo "\n   ";
	if(is_string($t->hypotheses && $t->hypotheses == "") || count($t->hypotheses) == 0)
	  { echo "hypotheses: none\n   ";
	  } 
	else
	  { echo "hypotheses: "; 
		if(is_string($t->hypotheses))
		   { echo $t->hypotheses;
		   }
		else 
	       {  $ct = 0;
		      foreach($t->hypotheses as $h)
	             { if($ct == 7)
		 			    echo "\n   ";
		            echo "$h ";   
		            ++$ct;
		           
		         }        
	       }
	    echo "\n   ";
	  }
	echo "conclusion: ";
	if($t->existential != "")
	  { echo "For some " . $t->existential . ", "; 
	  }
	if(is_string($t->conclusion))
	  { echo $t->conclusion;
		echo "\n";
	  }
	else 
	  { $ct = 0;
		foreach($t->conclusion as $c)
	       {  echo "$c ";  
			  ++$ct;
			  if($ct == 4) echo "\n               ";      
		   }
	    echo "\n";
	  }
}


//___________________________________________________

// Produce the desired TeX code on stdout

echo "\\section*{Appendix}\n";
echo "\\subsection*{Common Notions}\n";
echo "\\begin{alltt}\n";
foreach($axioms as $t)
  { if($t->kind != "cn") 
	   break;
	display_proposition($t);
  }
echo "\\end{alltt}\n";
echo "\\subsection*{Definitions}\n";
echo "\\begin{alltt}\n";
foreach($definitions as $defn)
  { display_proposition($defn);
	if($defn->label == "rectangle")
	   break;
  }
echo "\\end{alltt}\n";
echo "\\subsection*{Axioms of betweenness and congruence}\n";
echo "\\begin{alltt}\n";
foreach($axioms as $t)
   { if($t->kind == "cn") continue;
	 if($t->kind == "postulate") break;
	 display_proposition($t);
   } 
echo "\\end{alltt}\n";
echo "\\subsection*{Postulates}\n";
echo "\\begin{alltt}\n";
foreach($axioms as $t)
   {  if($t->kind == "postulate") 
	    display_proposition($t);
   } 
echo "\\end{alltt}\n";
echo "\\subsection*{Axioms for Equal Figures}\n";
echo "\\begin{alltt}\n";
$postulateflag = false;
foreach($axioms as $t)
   { if($t->kind == "postulate") 
	    $postulateflag = true;
	 if($postulateflag == true && $t->kind == "axiom")
	    display_proposition($t);
   } 
echo "\\end{alltt}\n";
/*
echo "\\subsection*{Theorems}\n";
echo "\\begin{alltt}\n";
foreach($results as $t)
    { // display_proposition($t);
	  if($t->label == "48")
	     break;
	}
echo "\\end{alltt}\n";
*/
?>


	  

