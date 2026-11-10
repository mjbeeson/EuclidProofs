#!/usr/bin/php
<?php 
// Strings can be written on two lines by using a period to concatenate the lines.
// We did this in some places to permit this file to be printed for archival purposes.
error_reporting(E_ALL|E_STRICT); 
ini_set('display_errors', true); 
require_once('Axioms.php');



 $v = "(\\$.\\$)";
 $v2 = "(\\$..\\$)";
 $v3 = "(\\$...\\$)";

$formula_patterns = array(  
	array( "~^$v between $v $v\$~", "BE"),
	array( "~^collinear $v $v $v\$~", "CO"),
	array( "~^$v not between $v $v\$~", "NOBE"),
	array( "~^not collinear $v $v $v\$~", "NOCO"),
	array( "~^$v circle center $v radius $v2\$~", "CI"),
	array( "~^$v inside $v\$~", "IN"),
	array( "~^$v outside $v\$~", "OU"),
	array( "~^$v on $v\$~", "ON"),
	array( "~^$v equal $v\$~", "EQ"),
	array( "~^$v2 equal $v2\$~", "EE"),
	array( "~^$v3 equal $v3\$~", "AE"),
	array( "~^$v center circle $v\$~","CE"),
	array( "~^$v3 equilateral\$~", "EL"),
	array( "~^equilateral $v3\$~", "EL"),
	array( "~^$v3 equilateral triangle\$~", "EL"),
	array( "~^$v3 triangle\$~", "TR"),
	array( "~^unequal $v $v\$~", "NE"),
	array( "~^$v not equal $v~", "NE"),
	array( "~^$v unequal $v~", "NE"),
	array( "~$v not equal to $v~", "NE"),
	array( "~^$v2 not equal $v2~", "NN"),   // inequality for lines is NN
	array( "~^$v2 not equal to $v2~", "NN"),
	array( "~^constructed $v3 $v2\$~","CC"),
	array( "~$v2 finite straight line~", "NE"),
	array( "~finite straight line $v2 produced by $v2 $v~", "EX"),
	array( "~$v2 produced by $v2 $v~", "EX"),
	array( "~$v2 $v2 two unequal straight lines~","NOEE"),
	array( "~$v2 greater than $v2~", "GT"),
	array( "~$v2 less than $v2~", "LT"),
	 
   );

function sanitize($x)
// delete words that don't contribute to the logical formula(s) represented by $x$
{  $strikeout = array("/\\\\/","/Thus /", "/it /", "/required /", "/to /", 
	                 // "/ with /",
	                  "/ being /",
					  "/ be /", "/be /", 
					  "/ will /", "/ base /", 
					  "/ the part /",
					  "/ namely /",
					  "/ show that /",
					  "/It /", 
	                  "/ of /", "/ be /", "/ is /","/ are /","/also /", 
	                  "/Also /","/ point /","/ points /", "/the /", "/ angle /", 
	                  "/ Angle /", "/ the /","/ a /","/ an /", "/ such( +)that /","/\./" 
	                 );
   $temp = preg_replace("~ with ~", " and ", $x);
   $temp = preg_replace("~ there ~", " construct ", $temp);
   $temp = trim(preg_replace($strikeout, " ", $temp));
   // Now there can be double spaces, e.g.  if "be the"  occurred
   $temp = preg_replace("( +)", " ", $temp);  // that gets rid of consecutive spaces
   global $v, $v2,$v3;
  // echo "Before ". $temp . "\n";
   $temp = preg_replace("~between $v and $v~", "between \\1 \\2", $temp);
   $temp = preg_replace("~center $v and radius $v2~", "center \\1 radius \\2", $temp);
   $temp = preg_replace("~radius $v2 and center $v~", "center \\2 radius \\1", $temp);
   $temp = preg_replace("~equal to~", "equal",$temp);
   $temp = preg_replace("~$v and $v unequal~", "unequal \\1 \\2", $temp);
   $temp = preg_replace("~$v and $v are equal~", "equal \\1 \\2", $temp);
   $temp = preg_replace("~$v, $v, and $v not collinear~","not collinear \\1 \\2 \\3", $temp);
   $temp = preg_replace("~$v, $v, and $v collinear~","collinear \\1 \\2 \\3", $temp);
   $temp = preg_replace("~$v, $v, and $v distinct~", "unequal \\1 \\2 and unequal \\1 \\3 and unequal \\2 \\3", $temp);
   $temp = preg_replace("~$v3 has been constructed on given finite straight line $v2~", 
                        "constructed " .  "\\1 " .  "\\2", $temp);
   $temp = preg_replace("~^and (.*)~", "\\1",$temp); // delete initial "and "
   return $temp;
}

function hand_translate($x)
/* When the English proposition $x is too difficult for general rules */
{   echo "hand translating $x%\n";
	if($x=='$ABC$ $abc$ two triangles having two sides $AB$ $AC$ equal two sides $ab$ $bc$ respectively')
	     return array("TRABC", "TRabc", "EEABab", "EEABac", "EABACbac");
	if($x == 'triangle $ABC$ equal triangle $abc$')
	     return array("EQABab", "EQACac", "EQBCbc");
	if($x == 'remaining angles equal remaining angles respectively' ||
	   $x == 'that is' ||
	   strstr($x, "subtend")
	  )
	    return "skip";
	echo "hand_translate failed.\n";
	die();
	return "oops";
}

function parse_formula($x)
// $x$ is a string, from the beginning of a line up to but not including the justification. 
// return a formula or  list of formulas.  Each formula is a string.
// The first two characters are the functor;  the rest are the arguments, each one character.
{  $a = array(1,2,3,4,5);
	global $mask;
    $x = trim($x, $mask . ",");
    $err = true;
    global $formula_patterns;
     echo "Parsing formula $x\n";
	foreach ($formula_patterns as $pattern)
	   { $success = preg_match( $pattern[0],  $x, $a);
		 if($success)
		    { $b = array();
			  foreach ($a as $z)
				{ $b[] = substr($z,1,strlen($z)-2);  // omit the dollar signs
				}
			  $err = false;
			  break;
			}
	   }
	if($err && strstr($x, ","))
	   { $t = explode(",", $x);
		 if(count($t) > 1)
		    { $ans = array();
			  foreach ($t as $f)
			     { $ans[] = parse_formula($f);
				 }
			 return $ans;
		   }
	   }
    if($err && strstr($x," and "))   
       { $t = explode(" and ", trim($x));
	     if(count($t) > 1)
	        { $ans = "";
	          // echo $x . "##\n";
	          foreach($t as $formula)
	  	     	{ $ans = "AN" . $ans . parse_formula(trim($formula));
		    	}
		 	  $ans = substr($ans,0,count($ans)-6);
		      // echo "$ans@\n";
	          return $ans;
	 		}
	   }
   if($err) 
      { $z = sanitize($x);
	    if($z != $x)
	       return parse_formula($z);
	    return  hand_translate($x);
	  }
   else
   	  { $ans = $pattern[1];
	    if($ans == "BE" || $ans == "NOBE")
	      { $temp = $b[1];  // fix order of arguments of BE
		    $b[1] = $b[2];
		    $b[2] = $temp;
		  }
		if($ans == "CC")
		  { $ans = "EE";
			$b[1] = substr($b[1],0,2);
		  }
	    for($i = 1; $i< count($b); $i++)
	      { $ans = $ans . $b[$i];
		  }
		if($ans == "EX")   //  ab extended by cd is m,  answer is EXabcdm
		  { $ans = "EX" . $b[1] . $b[2]. $b[3];
		  }
		if($ans == "GT")  // ab > cd  becomes cd < ab 
		  { $ans = "LT" . $b[2] . $b[1];
		  }
	  }
   return $ans;
}

//_______________________________________________________

class Proof
{	var $goal;     // a Theorem object
	var $lines;    // array of Lines
	public function Proof($a, $b)
		{ $this->goal = $a;
		  $this->lines = $b;  
		}
	public function to_array()
	    { $ans = $this->goal->to_array();
		  $ans[] = "Proof:";
		  $ct = 1;
		  foreach($this->lines as $line)
			  {  
				$line->linenumber = $ct;
				++$ct;
				$ans[] = $line->to_string();
			  }
		  return $ans;  
		}
}


$reasons = array("axiom", "prop", "lemma", "cn", "now", "let", "assume", "defn", "old", "pop");
//  these are the values of the $by field in the Line class. 

class Line   // one line of a proof
{	public $linenumber;  // number of this line, starting from 1
	public  $formula;    // just a string. Negation is a minus sign.  Conjunction is a comma.
	                 //  examples  Babbc,  -Eabcd,  finite_straight_line(ab),  angle(abc), abc < def, abc = def, a=b
	                 //  FINISH THIS.  Definitions can introduce new relations
	public $by;          //  "Axiom", "Prop", "Lemma", "Post.",  "C.N.",  "now", "let", or "hypothesis"
	public  $label;     //  label of the justification  example,  lemma:layoff,  or defn:collinear, or postulate:Pasch
	public  $reasons;     //  array of the lines that contain the hypotheses of the justification

	public function to_string()
		{ $ans = $this->linenumber ." ";
		  $ans = $ans . $this->formula;
		  if($this->by != "now" && $this->by != "none")
		      $ans = $ans . " " .  $this->by;
		  $ans= $ans . " " . $this->label; 
		  return $ans;
		}
	public function parse_line($line)
	{   
		global $mask;
		$line = trim($line, $mask . "," . ".");
		$intro = '(Then |Therefore |Therefore, |But |Also |Again, |Again |it follows that |follows that |Since |and |And |Now |Now, |Assume |Let)';
		global $v, $v2;	   
		$line_pattern0 = "~^Let $v be the circle of center $v and radius $v2~";
   		$line_pattern1 = "~^$intro" . "(.*)~";
   		$line_pattern2 = "~^$intro" . "(.*)justification{" . "(.*)}~";
		$line_pattern3 = "~(.*)justification{" . "(.*)}~";
		$line_pattern4 = "~^Let $v (.*)~";
		$line_pattern5 = "~there $v (.*)~";
   		$a = array(1,2,3);
	   // echo "Calling parse_line on $line\n";
		$success = preg_match($line_pattern0, $line, $a);
		if($success)
			{ $this->by = "new";
			  $this->formula = "CI" . $a[1] . $a[2] . $a[3];
			  $this->by = "new " . $a[1];
			  $this->label = "new";
			  // echo "Here160\n";
			  return;
		    }
        $success = preg_match($line_pattern4, $line, $a);
        if($success)
 			{ $temp = preg_replace("~\\$~","",$a[1]);  // strip dollar signs
	          $this->by = "new " . $temp;
			  if(strstr($line, "circle "))  // that space is important because we don't want to count "circle-circle"
			                                // in a justification!  
				 $line = $a[1] . $a[2];
			  else 
				 $line = $a[2];
			}
		else
			{ $this->by = "none";  // later deal with Case 1,  \qedcase, \qefcase, etc.
			}
		if(preg_match($line_pattern5, sanitize($line),$a))
		    { $this->formula = parse_formula($a[2]);
			  $stripped =  preg_replace("~\\$~", "", $a[1]);
			  $this ->existential = $stripped;
			  $this ->by = "new " . $stripped;
			  return; 
			}
		//  Next check for "Case 1"  etc.
		if(preg_match("~Case (., )(.*)~", $line, $a))
			{ // echo("recursion to " . $a[2] . "\n");
			  $this->parse_line($a[2]);
			  $this->by = "assume";
			  $this->label = "Case " . $a[1];
			  return;
			}
		else if(preg_match("~(qefcase|qedcase)~", trim($line), $a))
		    { $this->by = "pop";
			  $this->label = "discharge last assumption";
			  return;
			}	
		// echo "attempting to parse $line\n";
 		if(strstr($line, "justification"))
            { $success2 = preg_match($line_pattern2, $line, $a);
			  if($success2)
	          	 { $this->label = $a[3];
	          	   $line = $a[2];
	             }
			  else
				 { $success2 = preg_match($line_pattern3, $line, $a);
				   if(!$success2)
				     { echo "assertion 4 failed in parse_line\n"; 
				   	   echo "while trying to parse\n $line \n";
				       die();
				     }
			      $this->label = $a[2];
			      $line = $a[1];
			    }
	        }
        else 
            {
	          $this->label = "none";
	        }
	    $success = preg_match($line_pattern1, $line, $a);
	    if($success)
	        $formula = $a[2];
	    else
	        $formula = $line;
	    $formula = sanitize($formula);
        if(strstr($formula, "and"))
			{ // since it's already been sanitized, this is a conjunction	 
			  $conjunction = explode(" and ", $formula);
			  if(count($conjunction) < 2)
			     { echo("assertion 2 failed in parse_line\n");
				   echo "$formula\n";
				   die();
				 }
			  $this->formula = "AN" . parse_formula($conjunction[0]) . parse_formula($conjunction[1]);
			  for($i = 2; $i < count($conjunction); $i++)
			     { $this->formula = $this->formula . parse_formula($conjunction[$i]);
				 }
			  // echo "@@" . $this->formula . "@@\n";
			}
		else if(strstr($formula, "or"))
			{ // since it's already been sanitized, this is a disjunction	 
			  $disjunction = explode(" or ", $formula);
			  if(count($disjunction) < 2)
				  { echo("assertion 2b failed in parse_line\n");
					echo "$formula\n";
					die();
				   }
			  $this->formula = "OR" . parse_formula($disjunction[0]) . parse_formula($disjunction[1]);
			  for($i = 2; $i < count($disjunction); $i++)
				   { $this->formula = $this->formula . parse_formula($disjunction[$i]);
				   }
			  // echo "@@" . $this->formula . "@@\n";
			}
		else //  Now it's not a disjunction or conjunction
			{ // echo "#$formula#\n";
			  $this ->formula = parse_formula(sanitize($formula));
			  // echo "##" . $this->formula . "##\n";
			}
		if(strstr($this->label,"label"))
		    {  //  e.g.  \label{defn:circle}
			  $parts =  explode("{", $this->label);
			  if(count($parts) != 2)
				 { echo("Assertion 3 failed in parse_line\n");
				   die();
				 }
			  $this->label = substr($parts[1], count($parts[1]));
			}
		// Now fix up the label,  which at present might be "I.1" or "\ref{defn:circle}"
		if(strstr("$this->label", ":"))
		   { $temp = explode("{", $this->label);
			 $this->label = substr($temp[1], 0,strlen($temp[1])-1);
		   }
 	}
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
//_______________________________________________________________
function extract_justification($line)
{ if (strstr($line, "justification"))
     { $q = explode("justification", $line);
       $line = $q[1];
     }
  $a = preg_split("~({|})~",$line);
  foreach($a as $x)
     if(strstr($x,":"))
         return $x;
  return "oops";
}
//_______________________________________________________________
function formalize_proof($infile)
// $infile contains a piece of TeX constituting the statement and proof of a 
//  lemma or proposition from EuclidFreed.tex
  // First pass by the informal description of the theorem.
  // then get the hypotheses and the conclusion.
  // Then the rest of the lines constitute the proof, but 
  // each line that doesn't end in a period or a closing brace '}' needs
  // to be joined with the next line.
  // after that call parse_line on each line to produce the 'object proof'.
  // Finally write the formalized hypothesis, conclusion, 
  // and the object proof into a Proof object and return that.

{ $lines = file($infile);
  $outlines = array();
  $firstline = trim($lines[0]);
  global $v, $v2, $v3;
  $a = array(1,2,3,4,5);
  $t = new Theorem(1,2,3,4,5);
  if($firstline == "\\newlemma")
     { $t->kind = "Lemma";
	   $t->label = substr($lines[1],"label{");
	   $t->label = substr($t->label,0, count($t->label)-1);  // discard trailing close brace
	   $lines[1] = $lines[0] =  "";   // so they won't be there any more
	 }
  else if($x = strstr($firstline,"Proposition" ))
     { $t->kind = "Proposition";
	   $a = array(1,2);
	   preg_match("~Proposition (\d*)~",$x,$a);
	   $t->label =  $a[1];
	   $lines[0] = "";   // erase it
	 }
  else  // $infile was created automatically so the label is not in the file but in the filename
     { if(strstr($infile, "lemma"))
	       { $t->kind = "Lemma";
		     $temp = strstr($infile,"_");
		     $t->label = substr($temp,1,strlen($temp)-1);
		   }
		else if (strstr($infile, "Prop"))
		   { $t->kind = "Proposition";
			 $t->label = substr($infile,4,strlen($infile)-4);
		   }
		else
		   { echo("Assertion failed in formalize_proof\n");
	   		 die();
	       }
	 }
  $outlines[] = $t->kind . " " . $t->label;
  $i=0;
  foreach($lines as $line)
     { if(strstr($line, "}"))
           { $lines[$i] = "";
	         break;  // the brace closed the {\em  marking off the informal description
		   }
	   $lines[$i] = "";   // erase that line
	   $i++;
	 }
  $hyp = array();
  $oonclusion = array();
  $proof = array();
  $hypflag = true;
  $conclusionflag = $proofflag = $parseconclusionflag = false;
  $previous = "";
  $mask = "\\ \t\n\r\0\x0B";
  foreach($lines as $line)
	{ $line = trim($line);
	  if($line == "" || strstr($line, "medskip" ) || strstr($line, "smallskip")  // skip blank and TeX-only lines
		 || strstr($line, "It remains") || strstr($line, "footnote")
		)
		 { if($conclusionflag)
			  { $conclusionflag = false;
			  }
		   continue;
		 }
	  if($line == "STOP") 
	     { echo "AAAAARGH!!\n";
		   die();   // for debugging 
		 }
	  if(strstr($line, "required") || (!$proofflag && strstr($line, "I say")))
	     { $hypflag = false;
		   $conclusionflag = true;
		 }
	  if($conclusionflag)
	     { if(
		        preg_match("~It is required to show that (.*)~",$line,$a) ||
				preg_match("~It required show that (.*)~",$line,$a) 
		     )
		       { $line = $a[1]; // discard "It is required to show that
			   }
		   $line = trim($line);
		   $conclusion[] = $line;
		   // echo "Adding $line to conclusion\n";
		 }
	  if($parseconclusionflag) //  && (trim($line,"\\ \t\n\r\0\x0B") == "" || strstr($line,"Let")))
	     { $conclusionflag = false;
		   $proofflag = true;
		   // Now parse the conclusion(s) 
		   $temp = "";
		   foreach($conclusion as $x)
		     { $temp = "$temp ". $x;
			 }
		   $temp = sanitize($temp);

		  // echo "got here1: $temp%\n";
		   $success = preg_match("~(construct |there )$v(.*)~",$temp,$a);
		   if($success)
		       { 
			      //echo "got here2 $temp\n" . $a[2] . $a[3] . "\n";
			     $parsed_conclusion = parse_formula($a[2] .  $a[3]);
			     if($parsed_conclusion == "oops")
			         { echo "Can't parse 8 conclusion\n";
					   echo $a[2] . $a[3] . "\n";
					   die();
					 }
				 $existential = preg_replace("~\\$~","",$a[2]);
		   	  }
		   else 
		      { $success = preg_match("~(construct |there )(.*)$v3~",$temp,$a);
			    if($success)
			       { $parsed_conclusion = parse_formula(trim($a[3] . " " .  $a[2]));
			         if($parsed_conclusion == "oops")
			         	{ echo "Can't parse 8a conclusion\n";
					   	  echo $a[2] . $a[3] . "\n";
					   	  die();
					    }
					 $existential = preg_replace("~\\$~","",$a[3]); 
				  }
		   	  }
		   if(! $success)
		      { $parsed_conclusion = parse_formula($temp);
		  		if($parsed_conclusion == "oops")
			         { echo "Can't parse 9 conclusion\n";
					   echo $temp . "\n";
					   die();
					 }
				 $existential = "";
			  }
			// echo "Parsed conclusion is: " . $parsed_conclusion . "\n";
		   $parseconclusionflag = false;
		 }
	  //echo "###$line%##\n";
	  if($proofflag)
	     { $line = trim($line, $mask);
		    echo "###$line###$previous\n";
		    if(preg_match("~^I say that (.*)~",$previous,$a))
		       { $previous = strstr($previous, "I say that ");
			     // Now $line should begin a proof by contradiction of $a[1]  
			     //  so $line should begin with "If "  or with "For, if "
			     $goal = sanitize($a[1]);
				 $TheLine = new Line();
				 $TheLine->by = "goal";
				 $TheLine->label = "";
				 $TheLine ->formula = parse_formula($goal);
				 if(!is_a($TheLine,"Line"))
				    echo "oops!\n";
				 $proof[] =  $TheLine;
				 // echo "8: " . $TheLine->to_string() . "\n";
				 $previous = $line;
				 // echo "new goal: " . $goal . "\n" . $TheLine->formula . "\n";
				 continue;
			   } 
			if( strstr($previous,"reductio"))
			   { $TheLine = new Line();
				 $TheLine->by = "pop";
				 $TheLine->label= "discharge last assumption";
				 $proof[] = $TheLine;
				 $previous = $line; 
				 continue;
				}
			if(preg_match("~^(If | For, if )(.*)then (.*)~",$previous,$a))
		       { $TheLine = new Line();
				 $TheLine->by = "assumption";
				 $assumption = sanitize(trim($a[2], $mask . ","));
				 $TheLine->formula = parse_formula($assumption);
				if(!is_a($TheLine,"Line"))
				    echo "oops!\n";
				 $proof[] =  $TheLine;
				 // echo "7: " . $TheLine->to_string() . "\n";
				 $previous = $a[3];  // and keep going, i.e. 'continue'  is not wanted here.
			  }
		   if(preg_match("~^Suppose (.*)~", $previous,$a))
		      {  $TheLine = new Line();
			     $TheLine->by = "assumption";
			     $assumption = sanitize(trim($a[1], $mask));
				 $TheLine->formula = parse_formula($assumption);
				 $proof[] = $TheLine;
				 $previous = $line;
				 continue;
			  }
		   if(preg_match("~^Therefore (.*), as I said.\$~",$previous,$a))
		      {  $TheLine = new Line();
			     $TheLine->by = "pop";
			     $formula = sanitize($a[1]);
			     $TheLine->formula = parse_formula($formula);
			     $proof[] =  $TheLine;
			     // echo "6: " . $TheLine->to_string() . "\n";
			     $previous = $line;
			     continue;
			  } 
		   if( preg_match("~^Let $v be the circle of center $v and radius $v2.~", $previous,$a))   
			  {  //echo "hey! $previous\n";
				 $TheLine = new Line();
				 $temp = "CI" . $a[1] . $a[2] . $a[3];
				 // now remove dollar signs
				 $TheLine->formula = preg_replace("~\\$~", "",$temp);
				 $TheLine->by = "new " . preg_replace("~\\$~","",$a[1]);
				 $TheLine->label = "new " . preg_replace("~\\$~","",$a[1]);
				 $proof[] = $TheLine;
				 $previous = $line;
				 continue;
			   }
		   	if( preg_match("~^Let $v be the circle (of|with) center $v and radius $v2.~", $previous,$a))   
				  {  // echo "hey! $previous\n";
					 $TheLine = new Line();
					 $temp = "CI" . $a[2] . $a[4] . $a[3];
					 // now remove dollar signs
					 $TheLine->formula = preg_replace("~\\$~", "",$temp);
					 $TheLine->label = "new " . preg_replace("~\\$~","",$a[1]);
					 $TheLine->by = "new" . preg_replace("~\\$~","",$a[1]);
					 $proof[] = $TheLine;
					 $previous = $line;
					 continue;
				   }
			if( preg_match("~Since (.*), (.*)~", $previous, $a))
			      { 
				     $first = parse_formula($a[1]);
				     $FirstLine = new Line();
				     $FirstLine->formula = $first;
				     $FirstLine->label = "none";
				 	 $FirstLine->by = "none";
			         $previous = $a[2];  
			         continue;
			       }  
		   if($line == "") 
		       continue;  // skip blank lines
		   if(strstr($line, "common"))
		      { $temp = preg_replace("~Then circles $v and $v have a point $v in common~", 
		                        "Let \\3  \\3 on \\1 and \\3 on \\2.", $line);
		        $line = $temp;
		      }
		   // should we join $previous and $line into one line?
		   if($previous == "")
		      { $previous = $line;
			    continue;
			  }
		   if($previous != "")
		        $lastchar = $previous[strlen($previous)-1];
	       else 
	            $lastchar = 'A';   //  something not equal to period.
		   if(strpos($line, "justification") === 0 )  // $line begins with "justification.   
		      { $TheLine = new Line();
			    $TheLine->parse_line($previous);
			    $TheLine->label = extract_justification($line);
			    $proof[] = $TheLine;
			    // echo "5: " . $TheLine->to_string() . "\n";
			    $previous = "";
			    continue;
			   }
		   if(strstr($previous, "since")) // add the part after "since" as its own line and delete it from $previous
		                                  // and then add the part before "since".
		       { $e = explode("since", $previous);
			     //echo "@#$previous#@\n";
			     if(count($e) != 2)
			        { echo "assertion 5 failed\n";
				      echo "$previous\n";
				      die();
				    }   
			     $last = $e[1][strlen($e[1])-1];
			     if($last == '.' || $last == ',')
			         $e[1] = substr($e[1],0, strlen($e[1])-1);
			     $TheLine = new Line();
			     $TheLine->parse_line($e[1]);  // the part after "since"
			 	 $proof[] = $TheLine;
			     // echo "1: " . $TheLine->to_string() . "\n";
			     $TheLine->by = "now";
			     if(!strstr($e[0],"\$"))
			        { // no variables, no proposition there.  Example "Now,"
				      $previous = $line;
				      continue;
				    }
			     if($temp != "")
			        { $TheLine = new Line();
			          $TheLine->parse_line($temp);  // the part before "since"
					  $proof[] = $TheLine;
			   		  // echo "2: " . $TheLine->to_string() . "\n";
			        }
			     $previous = $line;
			     continue;
			   }
		   if( 
		      (strstr($previous, "justification") 
		       || strstr($line, "justification") 
			   || $lastchar == '.' || $line == "qed" || $line == "qef") 
		       || $lastchar == ','
		       || 
		         ( preg_match("~^(Since | Therefore, since | Again, since | Now, since )(.*)$~", $previous,$a)
	               && $lastchar == ','
	             ) 
	           || 
	             preg_match("~^and (.*)~", $line)  // $line begins with "and "
			   || $previous == "qedcase" || $previous == "qefcase" || $previous == "reductio"
		      ) // don't join
			   { $TheLine = new Line();
			     $TheLine->parse_line($previous);
			     $proof[] = $TheLine;
			     if(substr($TheLine->formula,0,2) == "EX")
			        $TheLine->label = "postulate:extension";
				 // echo "3: " . $TheLine->to_string() . "\n";
				 $previous = $line;
				 continue;
			  }  
		   //  so join, as $previous doesn't end in a period and contains no justification.
		   $TheLine = new Line();
		   $temp = trim($previous . " $line", $mask);   
		   // echo "###$previous*$line###\n";
		   $a = array(1,2,3);
		   if(preg_match("~^(Now | Now,)(.*)~", $temp, $a))  // and no justification
			   { 
			     $temp2 = explode(",", $temp);
				 for($i=1;$i<count($temp2);$i++)
				     { $TheLine = new Line();
					   //echo $temp2[i] . "\n";
					   $TheLine->parse_line(sanitize($temp2[$i]));
					   $TheLine->by = "now";
					   $proof[] = $TheLine;
					 }
		   		 $previous = "";
			     continue;				  
			   }
		   $TheLine->parse_line($temp);
		   $proof[] = $TheLine;
		    // echo "4: " . $TheLine->to_string() . "\n";
		   $previous = ""; 
	     }			
    } 
  $t->Theorem($t->kind,$t->label,$hyp,$parsed_conclusion, $existential);
  for($i=0;$i<count($proof);$i++)
      if(!is_a($proof[$i],"Line"))
         echo $i . "not a line\n";

  $P = new Proof($t, $proof);
  return $P;  
}

//___________________________________________________
$linetests = array(
	        'Let $J$ be the circle with center $A$ and radius $AB$.',
			'Let $K$ be the circle with center $B$ and radius $AB$.',
			'Then $B$ is inside $K$ \justification{Def. \ref{defn:inside}}',
			'and $B$ is on $J$.   \justification{Def. \ref{defn:circle}}', 
			'Let $D$ be a point such that $AD$ is equal to $AB$ and $A$ is between $B$ and $D$.  \justification{Post.~\ref{postulate:extension}}',
			'Then $D$ is outside $K$. \justification{Def. \ref{defn:outside}}'
			);
			
/*
foreach($formula_tests as $test)
    { echo parse_formula($test) . "\n";
	}
*/
function Test1(){
$ct = 1;	
foreach($linetests as $test)
    { $L = new Line(1,2,3,4,5);
	  //echo "Trying to parse #$test#\n";
	  $L->parse_line($test);
	  $L->linenumber = $ct;
	  ++$ct;
	  echo $L->toString() . "\n";
	}
}
//_______________________________________________________
function make_proof($filename)
// $filename contains one English proof, e.g. Proposition1.txt
// print the symbolic proof to stdout.
{ $P = formalize_proof($filename);
  $proof = $P->to_array();
  foreach($proof as $line)
	 { echo $line ."\n";
	 } 
}
//_______________________________________________________
function make_files()
// read EuclidFreed.tex and make text files containing
// the English proofs of the lemmas and propositions,
// suitable for input to make_proof.
{ $text = file("../EuclidFreed.tex");
  $N = count($text);
  $i = 0;
  $lemmanumber = 0;
  $propositionnumber = 0;
  global $mask;
  $tex = "(\\begin|\\end|label|caption|\\center|medskip|smallskip)";
  while(! strstr($text[$i],"\\newlemma"))
      $i++;  // now we're at "\def\newlemma"
  $i++;
  while(! strstr($text[$i],"\\newlemma"))
	   $i++ ; 
   // now we're at the first lemma
   while($i< $N)
      {  ++$lemmanumber;
	     ++$i;
	     $lemmaname = strstr($text[$i],"lemma:");
	     while( $i < $N && !strstr($text[$i],"required"))
	         { ++$i;  
		     }
		 ++$i;
	     while( $i < $N && trim($text[$i]) != "")
	         ++$i;
         if($i == $N) 
             break;
	    
		 if($lemmanumber < 10)
		    $lemmanumeral = "0" . $lemmanumber;
		else
		    $lemmanumeral = $lemmanumber;
	     $lemmaname = preg_replace("/:/",$lemmanumeral . "_",$lemmaname);
       	 $lemmaname = trim(preg_replace("/}/","",$lemmaname));
	       //  echo "$lemmanumber  $lemmaname   \n";
	     $temp = array();
	     $done  = false;
	     while($i < $N && !strstr($text[$i],"\\newlemma"))
	         { $next  = trim($text[$i]);
		       $a = array(1,2);
		       if(!$done && !preg_match($tex,$next,$a)) // skip Tex commands and everything after qed or qef
		           $temp[] = $next; 
		       // else echo "skipped $next\n";  
		       if(!$done && ($next == "\\qed" || $next == "\\qef"))
		           { array_to_file("$lemmaname.txt","./",$temp); 
			         // echo "Making $lemmaname\n";
			         $done = true;
			       }
		       $i++;
		     }
		 if($i == $N)
		    break;
	  }
    echo "Done with lemmas, starting propositions\n";
    $i = 0;
    while(!strstr($text[$i],"{Proposition"))
       ++$i;
   // now we're at the first proposition
   while($i< $N)
      {  preg_match("~([0-9]+)~", $text[$i], $a); 
	     $propositionnumber = $a[1];
	     ++$i;
	     if($propositionnumber < 10)
	        $filename = "Prop" . "0" . $propositionnumber. ".txt";
	     else 
	        $filename = "Prop" . $propositionnumber . ".txt";
	     $temp = array();
	     $done  = false;
	     $started = false;
	     while($i < $N && !strstr($text[$i],"{Proposition"))
	         { $next  = trim($text[$i]);
		       if($next == "\\end{document}")
		           return;	 
			   while(!$started &&  $i < $N && !strstr($text[$i],"required"))
		         { ++$i;  
			     }
			   ++$i; 
		       while(!$started && $i < $N && trim($text[$i]) != "")
		         ++$i;
	           if($i == $N) 
	             break; 
	           $started = true; 
               echo "%$i\n";    
		       if(!$done && !preg_match($tex,$next)) // skip Tex commands and everything after qed or qef
		           $temp[] = $next; 
		       if(!$done && ($next == "\\qed" || $next == "\\qef"))
		           { array_to_file($filename,"./",$temp); 
			         $count = count($temp);
			        // echo "Printing $filename, which has $count lines\n";
			         $done = true;
					 $started = false;
			       }
		       $i++;
		     }
		 if($i == $N)
		    break;
	  }
    
}


		 

?>