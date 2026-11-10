#!/usr/bin/php
<?php 
// Strings can be written on two lines by using a period to concatenate the lines.
// We did this in some places to permit this file to be printed for archival purposes.
error_reporting(E_ALL|E_STRICT); 
ini_set('display_errors', true);
require_once('AxiomsEqualFigures.php');
require_once('array_to_file.php');

class Stack {
 
	public $stk = array();   // OK fine,  it's not really a stack then.
	public $tags = array();   // used for the line numbers associated to the elements of stk
 
	public function __construct() {
	}
 
	public function push($data,$tag) {
		array_push($this->stk, $data);
		array_push($this->tags, $tag);
	//	echo "pushing $data\n";
	}
 
	public function pop() {
		if(count($this->stk) == 0)
		   { echo "Error in pop--can't pop empty stack\n";
			 die();
		   }
		array_pop($this->tags);
		return array_pop($this->stk);
	}
	public function peek() {
		if(count($this->stk) == 0)
		   { return false;
		   }
		return $this->stk[count($this->stk)-1];
	}
	public function peektags(){
		if(count($this->tags)==0)
		    { return false;
			}
		return $this->tags[count($this->tags)-1];
	}
	public function mark($tag) {
		$this->push("marker",$tag);
	}
	public $backtrack_count;
	public function backtrack(){
		$flag = true;
		$prev = "empty";
		$backtrack_count = 0; 
		while($flag)
		   { $t = $this->pop();
			 if($t=="marker")
			    return $prev;
			 $prev = $t;
			 ++$backtrack_count;
		   }
	}
	public function marked(){
	    return in_array("marker", $this->stk);
	}
 
}
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
//___________________________________________________
function exhaustive($a)
// $a$ is an array of propositions.  Return true if these cases are exhaustive.
{ if(count($a) == 2 && contradictory($a[0], $a[1]))
	 return true;
  // example: (EQBC,EQBD,EQCD,EQAC,EQAD,ANNEBC+NEBD+NECD+NEAC+NEAD) 
  // is exhaustive.  It occurs in collinear4.
  $n = count($a);
  foreach($a as $z)
    { if(strlen($z) < 2)
	     { echo "Error in exhaustive\n"; print_r($a); die();
		 }
	}
  if(functor($a[$n-1]) == "AN")
      { $t = explode("+",args($a[$n-1]));
	    if(count($t) != $n-1)
	        return false;
	    for($i=0;$i<$n-1;$i++)
	        if(negation($a[$i]) != $t[$i])
	            return false;
        return true;
      }
  if(functor($a[0]) == "AN")
	  { $t = explode("+",args($a[0]));
		if(count($t) != $n-1)
		    return false;
		for($i=0;$i<$n-1;$i++)
		    if(negation($a[$i+1]) != $t[$i])
		        return false;
	    return true;
	  }  
  return false;
}
//___________________________________________________
function contradictory($p, $q)
// return true if $p$ and $q$ are negations of each other
{  // echo "contradictory? $p $q%\n";
  if(functor($p) == "NO" && args($p) == $q)
	 return true;
  if(functor($q) == "NO" && args($q) == $p)
	 return true;
  if(functor($p) == "NE" && functor($q) == "EQ" && args($p) == args($q))
     return true;
  if(functor($q) == "NE" && functor($p) == "EQ" && args($p) == args($q))
     return true;
  if(functor($p) == "NC" && functor($q) == "CO" && args($p) == args($q))
     return true; 
  if(functor($q) == "NC" && functor($p) == "CO" && args($p) == args($q))
     return true;
  if($p == negation($q) || $q == negation($p))
     return true;
  return false;
}
// __________________________________________________
function lookup_reason($reason)
//  $reason is  "defn:xx", or "lemma:xx",  or "cn:xx", or "proposition:xx", or "postulate:xx", 
//  or "axiom:xx.   Return an object with the mentioned identification that has fields
//  "conclusion" and "hypotheses".
{ global $definitions, $results, $axioms; 
  $v = array_map('trim',explode(":", $reason));
  if(count($v)<2)
     { echo("Error in lookup_reason: $reason\n");
	 }
  $kind = $v[0];
  $name = $v[1];
  if($kind == "defn")
      $target = $definitions;
  if($kind == "lemma" || $kind == "proposition")
      $target = $results;
  if($kind == "axiom" || $kind == "postulate" || $kind == "cn")
      $target = $axioms;
  foreach($target as $x)
     { if($x->label == $name && ($kind == "defn" || $x->kind == $kind))
	     return $x;
     }
  echo("Can't look up $reason in $kind\n");
  die();
}

// __________________________________________________
function unify($p, $q, $initial_unifier = 0)
// unify two propositions.  The matching assigns variables in $q$ to 
// variables in $p$.  Also   $q can be an array of propositions,
// in which case it is converted to a conjunction.
// return false if they do not unify
{ if(!is_string($p))
	 { echo "non-string in unify\n";
	   die();
	 }
  if(is_array($q) && count($q) == 1)
	 $q = $q[0];
  if(!is_string($q))
     { if(!is_array($q))
	      { echo("wrong type in unify\n");
		    echo("Maybe you forgot to list the conclusion of a theorem in Axioms.php.\n");
		    die();
		  }
	   // print_r($q);
	   $temp = "";
	   foreach($q as $x)
	      { $temp = $temp . "+$x";
		  }
	   $q = "AN" . substr($temp,1);
	     //  echo "$p\n$q\n";
	  }
  if(strlen($p) != strlen($q))
	 return false;
  if(strlen($p) < 2)
      { echo "unify called with length less than 2";
        die();
      }
  if(strlen($p) < 2 || strlen($q) < 2)
      { echo "aargh"; die();}
  $f = functor($p);
  $g = functor($q);
  if($f != $g)
	  return false;
  if($f == "NO")
      return unify(args($p),args($q),$initial_unifier);
  $p = args($p);
  $q = args($q);
  if($f == "AN" || $f == "OR")
     { if($f == "AN")
	      { $P = explode("+",$p);
	        $Q = explode("+",$q);
	      }
	   else
	      { $P =  explode("|",$p);
		    $Q =  explode("|",$q);
		    //  echo "hey, unifying disjunctions\n";
		  }
	   // echo "Hey: $p and \n     $q\n";
	   $m = count($P);
	   if($m != count($Q))
	      { echo "Error in unifying $f $p $q\n";
		    die();
		  }
	   $old = $initial_unifier;
	   for($i=0;$i<$m;$i++)
	      { //  print_r($old);
		    $v = unify($P[$i], $Q[$i], $old);
		    // echo $P[$i] . "\n" . $Q[$i] . "\n";
			if(!$v)
				return false;
			$old = $v;
		  }
	   return $v;
	}	    

  if(strlen($p) != arity($f))
	 { echo "wrong arity for $f$\n";
	   die();
     }
  $n = strlen($p);
  if(strlen($q) != $n)
     { echo "ouch!"; 
	   die();
	 }
  if($initial_unifier == 0)
      $match = array();
  else
      { $match = $initial_unifier;
	    // echo "passed\n";
	    // print_r($match);
	    // echo "$p $q\n";
      }
  for($i = 0; $i < $n; $i++)
	   { if(key_exists($q[$i], $match) && $match[$q[$i]] != $p[$i])
		    {  //  echo "failing on " . $i . "\n";
			  return false;  // that variable is already matched to something else
			}
	     	//  echo "Key " . $q[$i] . "\n";
		  $match[$q[$i]] = $p[$i];
	        //	print_r($match);
	   }
  // echo "unifying $p and $q:\n"; 
  // echo "given\n"; print_r($initial_unifier);
 //  echo "returning\n"; print_r($match);
  return $match;
}
 
// __________________________________________________
function apply_unifier($unifier, $input)
// return the result of applying $unifier to $input.

{ if(is_array($input))
	 { $ans = array();
	   foreach($input as $x)
	     $ans[] = apply_unifier($unifier, $x);
	   return $ans;
	 }
  $n = strlen($input);
  $ans = $input;
  if(functor($input) == "NO")
     $start = 4;
  else 
     $start = 2;
  for($i=$start;$i<$n;$i++)
     { if(key_exists($input[$i], $unifier))
	       $ans[$i] = $unifier[$input[$i]];
	   else 
	       { if ($input[$i] == '+' || $input[$i] == '|')
	       		{ $ans[$i] = $input[$i];
		     	  $ans[$i+1] = $input[$i+1];  // copy the next functor
		     	  $ans[$i+2] = $input[$i+2];
		     	  $i += 2;
		     	  $ans[$start] = $input[$start];
		     	  $ans[$start+1] = $input[$start+1];
		   		}
	   		 else
	       		{ $unifier[$input[$i]] = $ans[$i];  // add it to the unifier
		   		}
		   }
	 }
  if(strlen($ans) != strlen($input))
     echo "oops! $ans% $input%\n";
   // echo "apply_unifier $input% returning $ans%\n";
   // print_r($unifier);
  return $ans;
}
 
//____________________________________________________
function implies($eq, $r, $p)
// $eq is an equality, $r is an atomic proposition.
// return true if $p$ results from $r$ by substituting 
// one side of $eq for the other in $p
{ if(strlen($eq) != 4)
	 { echo "implies wrongly called on $eq\n";
	   die();
	 }
  if(strlen($r) != strlen($p) 
     || functor($r) != functor($p)
    )
     return false;
 // echo "implies $eq $r $p\n";
  $r = args($r);
  $p = args($p); 
  $n = strlen($r);
  $X = $eq[2];
  $Y = $eq[3];
  for($i=0;$i<$n;$i++)
     { if($r[$i] == $p[$i])
          continue;
       if($r[$i] == $X && $p[$i] == $Y)
          continue;
       if($r[$i] == $Y && $p[$i] == $X)
          continue;
       return false;
     }
  return true;
}
//____________________________________________________
function remove($item, $list)
// return a new array with all items of $list that are not equal to $item
{ $ans = array();
  foreach($list as $x)
     { if($x != $item)
	       $ans[] = $x;
	 }
  return $ans;
}
//___________________________________________________
function negation($x)
{  $f = functor($x);
   if($f == "NO")
     return args($x);
   if($f == "AN")
     { $g = "OR";
	   $p = explode("+",  args($x));
	   foreach($p as $z)
	 	  { $g = $g . negation($z) . "|";
		  }
	   $g = substr($g,0,strlen($g)-1);
	   return $g;
	 } 
   if($f == "OR")
     { $g = "AN";
	   $p = explode("|",  args($x));
	   foreach($p as $z)
	 	  { $g = $g . negation($z) . "+";
		  }
	   $g = substr($g,0,strlen($g)-1);
	   return $g;
	 }
   if($f == "EQ")
      $g = "NE";
   else if($f == "NE")
      $g = "EQ";
   else if($f == "CO")
      $g = "NC";
   else if ($f == "NC")
      $g = "CO";
   else 
      $g = "NO" . $f;
   return $g . args($x);
}
//___________________________________________________
function verify($tocheck, $unifier, $given, $linenumber, $dependencies)
// if the array $given->stk  implies the result of 
// applying $unifier to $tocheck;  return the (extended) unifier used.
// On failure return false.
// Array $dependencies should get a new entry with index $linenumber 
// if verify succeeds. 
{ if(is_string($tocheck) && functor($tocheck) == "OR")
	 { // it's enough to verify one disjunct 
	   $x = explode("|", args($tocheck));
	   foreach($x as $disjunct)
	      { // echo "hey $disjunct\n";
		    $dependencies->mark($linenumber);
		    $ans = verify($disjunct, $unifier, $given, $linenumber, $dependencies);
		    if($ans != false)
		        return $ans;
		    $dependencies->backtrack();
		  }
	   // don't give up, the entire disjunction might be in $given 
	}  
  if(is_string($tocheck))
	{ // echo "trying to verify $tocheck\n";
	  $tocheck = array($tocheck);	  
	}
  $dependencies->mark($linenumber);
  $ans = search(0,$tocheck, $given, $unifier,$linenumber, $dependencies);
  if($ans == false)
     $dependencies->backtrack();
  return $ans;
}

//________________________________________
function true_count($list)
//  don't count members "marker"
{ $ans = 0;
  foreach($list as $x)
    { if($x != "marker")
	    ++$ans;
    }
  return $ans;
}
// _______________________________________
function record_dependency($valid, $dependencies, $linenumber, $nn)
// $linenumber is the zero-based number in the proof-file
// $nn  is the number in the stack of valid propositions now in scope in the Stack object $valid
// Fetch the corresponding linenumber from $valid->tags and push it in the Stack object $dependencies.
{ if(!isset($valid->tags[$nn]))
	 { echo "ooops! $nn not valid for linenumber $linenumber \n"; die();}
  $NN = $valid->tags[$nn];
  $dependencies->push(array($linenumber,$NN),"dummy");
}
//________________________________________
function search($n,$tocheck, $given, $unifier, $linenumber, $dependencies)
// $given is a Stack object.
// Find a unifier extending $unifier
// that unifies $tocheck[$n] with
// some member $given->stk[$nn]  of $given, and return that unifier.  
//  and add the pair ($linenumber, $nn) to $dependencies.
// If none exists, return false.

// Nodes are pairs  ($m, unifier extending $unifier).
// Neighbors of that node are ($m+1, unifier extension)
 
{ if(!is_array($tocheck))
	 { echo "search received a non-array\n.";
	   if(is_string($tocheck)) echo("$tocheck\n");
	   die();
	 }
  if($n == count($tocheck))
     { // done
	   // echo "verified \n"; print_r($tocheck); print_r($unifier);
	   return $unifier;
	 }
  $h = $tocheck[$n];
     /*  echo "search $n of " . count($tocheck) . " $h \n"; 
      print_r($unifier);
      echo "now\n";
      print_r($given);   */
  if(functor($h) == "OR")
     { // it's enough to verify one disjunct
	   $x = explode("|", args($h));
	   foreach($x as $disjunct)
		  { // echo "hey $disjunct\n";
			$dependencies->mark($linenumber);
			$ans = verify($disjunct, $unifier, $given, $linenumber, $dependencies);
			if($ans != false)
			   { $temp2 = search($n+1,$tocheck,$given, $ans, $linenumber, $dependencies);
				 if($temp2 != false)
				    return $temp2;
			   }
			$dependencies->backtrack();
		  }
	}
  $nn =  count($given->stk);
  $t = array_reverse($given->stk);
  foreach( $t as $q)
      {  --$nn;
		if($q == "marker") 
		    { 
			  continue;
			}
	    if(functor($q) != functor($h))
	       continue;
	    $temp = unify($q,$h, $unifier);
	    if($temp == false) 
	      { // echo "Failed to unify $q and $h\n";
	        continue;
	      }
	    record_dependency($given, $dependencies, $linenumber, $nn);
	    $dependencies->mark($linenumber);
	    $temp2 = search($n+1, $tocheck, $given, $temp,$linenumber, $dependencies);
	    if($temp2 != false)
	       { record_dependency($given, $dependencies, $linenumber, $nn);
		     return $temp2;
		   }
	    $dependencies->backtrack();
	  }
  return false;
}
 //__________________________________________________
function check_hypotheses($given, $filename)
// and die if there is a problem
 //  these hypotheses better be the ones listed in the $results array in Axioms.php
   //  So we will check that next, before checking the proof itself
   //  is this a proposition or a lemma?
 { global $results;
   if(strstr($filename, "Prop"))
       $kind = "proposition";
   else 
       $kind = "lemma";
   $hyp = false;
   $lemmaname = substr($filename,0,strlen($filename)-4);  // drop the ".prf" at the end
   if(strstr($lemmaname, "Prop"))
      $lemmaname = substr($lemmaname,4);  // take the part after "Prop"
   foreach($results as $r)
       { if($r->kind == $kind && $r->label == $lemmaname)
	        { $hyp = $r->hypotheses;
		      $con = $r->conclusion;
		      break;
		    }
	   }
	if($hyp === false)  
	// if $hyp is an empty array it is == false but not === false
	   { echo "$filename missing in results array.\n";
		 die();
	   }
	if(!is_array($hyp) || count($given) != count($hyp))
	   { echo "Problem with hypotheses of $filename\n";
		 echo count($given) . " " . count($hyp) . "\n";
		 print_r($given);
		 die();
	   }
	$n = count($hyp);
	for($i=0;$i<$n;$i++)
	   { if ($hyp[$i] != $given[$i])
		    { echo "Problem with hypothesis $i (of $n) of $filename\n";
			  echo $r->label . "\n";
			  echo $hyp[$i] . " " . $given[$i] . "\n";
			  die();
			}
	   }
	//  OK,  everything OK with the hypotheses.  
}
 
//___________________________________________________
function check_conclusion($lastline, $filename)
  /* $lastline should be the conclusion of the theorem,
     or if the conclusion is an array, it should be the 
     conjunction of the members of that array.  
     check_conclusion dies if that is not the case.
  */
{ global $results;
   if(strstr($filename, "Prop"))
       $kind = "proposition";
   else 
       $kind = "lemma";
   $con = false;
   $lemmaname = substr($filename,0,strlen($filename)-4);  // drop the ".prf" at the end
   if(strstr($lemmaname, "Prop"))
      $lemmaname = substr($lemmaname,4);  // take the part after "Prop"
   foreach($results as $r)
       { if($r->kind == $kind && $r->label == $lemmaname)
	        { $hyp = $r->hypotheses;
		      $con = $r->conclusion;
		      $new = $r->existential; 
		      break;
		    }
	   }
	if($con === false)  
	// if $con is an empty array it is == false but not === false
	   { echo "$filename missing in results array.\n";
		 die();
	   }
	$lastline = trim($lastline);
	if(strstr($lastline, " "))
	   { $t = explode(" ", $lastline);
		 $lastline = $t[0];  // discard the justification
	   }
	$unifier = unify($lastline,$con,array());
	if($unifier === false)
	   { echo "Problem in conclusion of $filename\n";
		 echo "$lastline\n"; print_r($con);
		 die();
	   }
	foreach($unifier as $var => $value)
	   { if(strstr("XYZUVWxyzuvw", $var)==false && $var != $value)
		     { echo "Problem in conclusion of $filename with $var and $value\n";
			   echo "$lastline\n"; print_r($con);
			   print_r($unifier);
			   die();
			 } 
		}
	// print_r($unifier);
}   
// _________________________________________________

function vars_in($given)
// create a string listing  all variables in the formula or 
// list of formulas $given
// and return the result.  Duplicates are OK.
{ if(is_string($given))
	{ if(functor($given) == "AN")
		return vars_in(explode("+",args($given)));
	  if(functor($given) == "OR")
	    return vars_in(explode("|",args($given)));
	  if(functor($given)== "NO")
	    return vars_in(args($given));
	  return args($given);
	}
 if(!is_array($given))
    { echo("Error in vars, wrong type.\n");
	  die();
	}
 $ans = "";
 foreach($given as $formula)
   { if($formula == "marker")
	    continue;
	 $ans = $ans . vars_in($formula); 
   }
 return $ans;
}
//_________________________________________________

function check_fresh($prop, $unifier, $vars, $given)
// $vars is a list of variables 
// $check that the values assigned to those variables by $unifier
// do not occur in $given.  If they do,  print an error message and die.
{ $oldvars = vars_in($given);
 // echo "Calling check_fresh with $vars\n";
 // echo "oldvars = $oldvars\n";
  foreach($unifier as $var=>$value)
	{ if( strstr($vars, $var)!== FALSE && strstr($oldvars,$value)!== FALSE)
		{ echo("Fresh variable condition violated when deducing $prop with $var = $value\n");
		  print_r($given);
		  die();
		}
//	  echo "$var = $value\n";
    }
}

//____________________________________________________
function process_dependencies($filename, $dependencies)
// $dependencies is a Stack object containing entries (one, two),
//  indicating that line number 'one'  depends on line number 'two'.
// $dependencies also contains occurrences of literal string "marker".
//  This function produces a new array indexed by linenumber whose entries
//  are strings like 14 5 4 8, meaning line 14 depends on 5, 4, and 8.
{  $temp = array();
   foreach($dependencies->stk as $x)
      { if($x == "marker")
	       continue;
	    assert(is_array($x));
	    $ell = $x[0];
	    $j = $x[1];
	    assert(is_integer($ell)); 
	    assert(is_integer($j));
	    if(!isset( $temp[$ell]))
	       $temp[$ell] = array();
	    $temp[$ell][] = $j;
	  }
   $ans = array();
   foreach($temp as $x=>$y)
     { sort($y);
	   $nn = count($y);
	   $yy = array();
	   for($i = 0; $i < $nn; $i++)
	     { if($i > 0 && $y[$i] == $y[$i-1])
		      continue; // skip duplicates
		   $yy[] = $y[$i];
		 }
	   $w = $x + 1;
	   foreach($yy as $zerobased)
	      { $onebased = $zerobased + 1;
		    $w = $w . " " . $onebased;
		  }
	   $ans[] = $w;
	 }
   return $ans;
}
 
	    
		 
			      		
//____________________________________________________
function check_proof($filename)
// check if the proof in $filename is correct.
// return true if it is, false if not.
// Does not check whether it has the correct assumptions and 
// conclusions specified in $lemmas or $propositions.
{ $valid = new Stack();  // propositions proved 
  $variables = new Stack();
  $goals = new Stack();
  $caselists = new Stack();
  $inference_count = 0;  // number of inferences
  $dependencies = new Stack();
    //  $dependencies[14] = (4,5,8)  means line 14 depends on lines 4,5,8
  if(!file_exists($filename))
      { echo "Expected $filename to exist.\n";
	    die();
	  }
  $proof = file($filename);
  if(count($proof) == 0)
      { echo "$filename is empty\n";
	    die();
	  } 
  $n = count($proof);
  for($i = $n-1; $i > 0; $i--)
    { if(trim($proof[$i]) != "")
	    { $lastline = $proof[$i];
		  break; 
		} 
	}
  if(!isset($lastline))
     { echo "Trouble with lastline of $filename\n";
	   die();
	}
  check_conclusion($lastline, $filename);
  /* $lastline should be the conclusion of the theorem,
     or if the conclusion is an array, it should be the 
     conjunction of the members of that array.  
     check_conclusion dies if that is not the case.
  */
  
  $hypflag = 1;   // read the hypotheses
  $a = array(1,2,3,4); 
  $linenumber = -1;
  foreach($proof as $line)
     { ++$linenumber;  // used only to track dependencies 
	   $line = trim($line);
	   //  echo "Checking $line\n";
	   if($line == "") 
	      { if($hypflag == 1)
		       $hypflag = -1;  // first blank line before any justified line , signals end of hypotheses 
		    continue;    //  skip blank lines (which occur sometimes at the end)
		  }
	   if($line[0] == '%')  // skip commented-out lines
	      continue; 
	   if($hypflag!= 0)
	      { if(strstr($line, " ") || $hypflag == -1)
		        { $hypflag = 0;   // done reading hypotheses 
			      check_hypotheses($valid->stk, $filename);  // and die if there's a problem
			    }
			else
				{ $valid->push($line, $linenumber);   // assume this line
				  continue;
				}
		  } 
	   if( preg_match("~^cases~", $line))
		  {   
			$p = array_map('trim',explode(":", $line));
			$thecases = "OR" . $p[1];
			  // echo "$thecases\n";
			 // print_r($valid);
			preg_match("~^cases( *)(.*)~",$p[0],$a);
			$goals->push($a[2],$linenumber);  // the thing to be proved by cases
			 //  echo "setting goal = " . $a[2] ."\n";
			if(isset($list_of_cases) && count($list_of_cases)!= 0)
			    { // this is a nested cases, so store the old caselist
				  $caselists->push($list_of_cases,$linenumber);
				  // echo "Pushing caselist \n";
				  // print_r($list_of_cases);
				}
			$list_of_cases = array_map('trim',explode("|", $p[1]));
			if(in_array($thecases, $valid->stk))
				{ $j = array_search($thecases, $valid->stk);
				  record_dependency($valid, $dependencies, $linenumber, $j);
				  continue;  // OK, proceed to the cases
				}
			if(!exhaustive($list_of_cases))
			    { echo "cases not checked to be exhaustive\n";
				  print_r($list_of_cases);
				  die();
				}
			continue;  // OK, proceed to the cases
		  }
	   if(strstr($line, "|"))
		  {  // if a disjunction  occurs to set up a proof by cases, then the cases should be exhaustive
			 $cases = explode("|", $line);
			 $t = array();
			 foreach($cases as $x)
			    { $t[] = trim($x);
				}
			 if(exhaustive($t))
			     continue;  // these cases are exhaustive
			 //  echo "failed to check the cases are exhaustive\n";
			 // don't die,  maybe this line can be checked normally
		  }
	   if(preg_match("~^case ~", $line, $a))
		  { $p = explode(":", $line);
			$valid->mark($linenumber);
			$currentcase = trim($p[1]);
			$valid->push($currentcase,$linenumber);
			// echo "pushing $currentcase\n";
			continue;
		  }
	   if(preg_match("~qedcase~", $line))
		  { if(count($goals->stk) == 0) 
			   { echo "error at line 688\n"; die();
			   }
			if($valid->peek() != $goals->peek())
			   { echo("Can't check case $currentcase\n");
				 echo "because " . $goals->peek() . "doesn't match " . $valid->peek() . "\n";
				 // print_r($valid);
				 $valid->backtrack();
				 die();
			   }
			$list_of_cases = remove($currentcase, $list_of_cases);
			//	echo "Removed $currentcase from the caselist leaving\n";
			//  print_r($list_of_cases);
			record_dependency($valid, $dependencies, $linenumber,count($valid->stk)-1);
			$valid->backtrack();
			// echo "backtracking $currentcase\n";
			// print_r($valid);
			continue;  // go on to the next line
		  }
	   if(strstr($line, " "))
		  {  preg_match("~(\S*)(\s*)(\S*)~", $line, $a);
			 $prop = $a[1];
		 	 $reason = trim($a[3]);
			 if($reason == "")
			    { echo "Ooops, blank reason in $line\n";
				  echo "$prop%\n";
				  die();
				}
			 // echo "$reason\n";
			 if($reason == "reductio") 
			     { $p = $valid->peek();  // previous line
				   // look for something that $p contradicts
				   $flag = false;
				   $nn=-1;
				   foreach($valid->stk as $r)
					  { ++$nn;
						if(contradictory($p, $r))
				          { $mm = count($valid->stk)-1;
							$valid->push($prop,$linenumber);
					        record_dependency($valid, $dependencies, $linenumber, $nn);       
					        record_dependency($valid, $dependencies, $linenumber, $mm);
					        $flag = true;
					        break;
					      }
					  }
				   if(!$flag)
				      { echo("reductio error at $line\n");
					    echo("$p not contradictory\n");
					    print_r($valid);
					    die();
					  }
				    $lastassumption = $valid -> backtrack();
				    // check that $prop is the negation
				    // of the last assumption
				    if(negation($prop) != $lastassumption &&
				       negation($lastassumption) != $prop &&
				       $prop != "NO" . $lastassumption &&
				       $lastassumption != "NO" . $prop
				      )
				      { echo("reductio error at $line\n");
					    echo "Conclusion is not negation of assumption.\n";
					    echo "$prop versus $lastassumption\n";
					    die();
					  }
					// Now $linenumber has to depend on the assumption, 
					// but the assumption is already off the $valid stack,
					// so it can't be done with record_dependency.
					$assumption_linenumber = $valid->tags[count($valid->stk)-1]+1;
					$dependencies->push(array($linenumber,$assumption_linenumber),"dummy");
				    $linenumber += $valid->backtrack_count;
				    $valid -> push($prop,$linenumber);
				    // echo "pushing $prop\n";
				    // print_r($valid);
				    ++$inference_count;
				    continue;  // this line has been checked
				 }
		 	 if($reason == "cases")
			    { if($prop != $goals->peek())
			 		 { echo "goal " . $goals->peek() . "does not match claim $prop in cases\n";
				       die();
				     }
				  $valid->push($prop,$linenumber);
				  if(!empty($list_of_cases))
				     { echo "Not all cases checked for " .  $goals->peek() . "\n";
					   print_r($list_of_cases);
					   die();
					 } 
				  $goals->pop();
				  // echo "popping goal\n";
				  $temp = $caselists->peek();
				  if($temp)
				     { $list_of_cases = $temp;
					   $caselists->pop();
					   $currentcase = $list_of_cases[0];
					   // echo "popping cases\n";
					   // print_r($list_of_cases);
					 }
				  ++$inference_count;
				  // this line should be marked as depending on all the relevant qedcase lines
				  // But those lines are no longer in $valid->stk, so we go to the proof itself
				  // also this line depends on the lines like "case 2"  and the opening "cases" line,
				  // for if not then those lines get labeled "unused". 
				  $scopedepth = 0;
				  for($i=$linenumber-1;$i>0;$i--)
	                 { $x = $proof[$i];
					   if( $scopedepth == 0 && (strstr($x, "case ") || strstr($x,"qedcase")))
					      { $dependencies->push(array($linenumber,$i),"dummy");
					        // zero-based line numbers are stored
						  }
					   if( $scopedepth == 0 && strstr($x,"cases") && strstr($x, ":"))
					      { $dependencies->push(array($linenumber,$i),"dummy");
						    break;
						  }
					   if(strstr($x,"cases"))
					      { if(strstr($x, ":"))
						       --$scopedepth;
						    else
						       ++$scopedepth;
						  }
					 }
				  continue;  // this line checks OK 
				}
			if($reason == "assumption") // starting a proof by contradiction
			    { $valid->mark($linenumber);
				  $valid->push($prop,$linenumber);
				  continue;
				}
			if(strstr($reason, ":"))
			   { $a = array(1,2,3);
				 preg_match("~(.*)\.prf~", $filename, $a);
				 $proofname = $a[1];
				 if(!precedes($reason, $proofname))
				    { echo "Proof of $proofname illegally cites $reason\n";
					  die();
					}
				 ++$inference_count;
			   } 
			$authority = lookup_reason($reason);
			if($authority->label == "equalitysub")
			   { //  see if any equality in $valid->stk  implies $prop 
				 $err = true;
				 // echo "prop = $prop\n";
				 $nn = -1;  // used for dependencies
				 foreach($valid->stk as $q)
				     { // echo "q = $q\n";
					   ++$nn;
					   $mm = -1;
					   if(functor($q) != "EQ")
					   		continue;					  
					   foreach($valid->stk as $r)
					      { ++$mm;
						    if(implies($q,$r,$prop))
					           { $err = false;
						         break;
						       }
						  }
					   if($err==false)
					      break;
					 }
				 if($err)
				     { echo "Can't verify $prop by equalitysub\n";
					   die();
					 }
				 $valid->push($prop,$linenumber);
				 record_dependency($valid,$dependencies,$linenumber,$nn);
				 record_dependency($valid,$dependencies,$linenumber,$mm);
				 continue;
			   }		  
			if( functor($prop) != "AN" 
			   && functor($prop) != "OR" 
			   && is_array($authority->conclusion)
			   )
			   { $unifier = false;
				 $flag89 = false;
				 $conclusion = $authority->conclusion;
				 foreach($authority->conclusion as $x)
				    { $unifier = unify($prop, $x);
					     // echo "Unifying $prop and $x  with \n";
					     // print_r($unifier);
					     // if($unifier == false) echo "failed\n";
					  if($unifier != false && $authority->existential != "")
						 { 
							check_fresh($prop, $unifier, $authority->existential, $valid->stk);
						 }
					  if($unifier != false)
					     { $new_unifier = verify($authority->hypotheses, $unifier, $valid, 
						                         $linenumber, $dependencies);
						   if($new_unifier != false)
						      { 
							    $flag89 = true; // this line is checked
							    break;
							  }
						 }	
				    /*  if($unifier == false)
					     echo "Can't unify $prop and any conclusion of " . $authority->label. "\n";
					  else
					     { echo "Can't verify hypotheses of " . $authority->label . "\n";	
					       echo "where the conclusion is $prop and $x\n";
					     }	*/			
					}
				 if($flag89)
				   { $valid->push($prop,$linenumber); 
					 // echo "pushing $prop\n";
					 continue;  // this line is checked
				   }
				 if($unifier == false && get_class($authority) == 'Definition')
					{ // definitions can be used 'backwards' 
					  $unifier = unify($prop, $authority->hypotheses);
					  if($unifier == false)
					     echo "Can't unify $prop with " . $authority->hypotheses . "\n";
					  else
					    { $new_unifier = verify($authority->conclusion, $unifier, $valid,
						                        $linenumber, $dependencies);
						  if($new_unifier != false)
					         { $valid->push($prop,$linenumber);  // this line is checked
						        /* echo "hey $prop\n";
						        print_r($unifier);
						        print_r($new_unifier);
						        print_r($authority->conclusion);  */
						       continue;
						     }
					      else if(is_array($authority->conclusion) && count($authority->conclusion) == 1 
					              && functor($authority->conclusion[0]) == "OR"
					             )
					         { $temp = explode("|", args($authority->conclusion[0]));
						       $flag72 = false;
						       foreach($temp as $x)
						           { $new_unifier = verify($x, $unifier, $valid,$linenumber, $dependencies);
							         if($new_unifier)
							              { $flag72 = true;
											$valid->push($prop,$linenumber);  // this line is checked
								            break;
								          }
								   }
							   if($flag72)
							       continue;
							 }
					       echo "Can't verify conclusion of " . $authority->label . "\n";
					    }
					}
				if($unifier == false || $new_unifier == false)
				    {  echo "AARGH! failed to check $prop " . $authority->label . "\n";
					   print_r($valid);
					   die();
			        }
								 
			  }	 
			else	       
			   { $unifier = unify($prop, $authority->conclusion);
				 if($unifier == false)
				    { if(get_class($authority) == "Definition" &&
					     functor($prop) == "AN" &&
					     is_array($authority->hypotheses)
					    )
					    { $unifier = unify($prop, $authority->hypotheses);
					      $new_unifier = verify($authority->conclusion, $unifier, $valid,
												$linenumber, $dependencies);
					      if($new_unifier != false)
					         { $valid->push($prop,$linenumber);
						       continue;
						     }
						}  
					  echo  "Can't unify $prop with conclusion of " . $authority->label . "\n";
					  print_r($authority->conclusion);
					  echo "\n"; print_r($authority);
					  echo "Hypotheses:"; print_r($authority->hypotheses);
				      die();
				    }
				 //  echo "Hey! $prop\n"; 
				 check_fresh($prop,$unifier,$authority->existential,$valid->stk);
				 $new_unifier = verify($authority->hypotheses, $unifier,$valid,$linenumber, $dependencies);
				 if($new_unifier != false)
				    { $valid->push($prop,$linenumber);
				      continue;
					}
				 echo "Can't verify hypotheses of  " . $authority->label .
				    " with conclusion $prop\n";
				 if(is_array($authority->hypotheses))
				    { echo "Namely,\n";
					  foreach($authority->hypotheses as $z)
					      { echo "$z\n";
						  }
					  echo "after applying some extension of this unifier:\n";
                      print_r($unifier);
					} 
				 else 
				    { echo "Namely,\n"; echo $authority->hypotheses; echo "\n";
					  echo "after applying some extension of this unifier:\n";
	                      print_r($unifier);
					}
				 die();
			   }	
		 }	
	   else  // there's no justification given for this line
		  { // check that $prop is in $valid or follows from
			// something in $valid
			$notyet = true;
			$prop = $line;
			$unifier = array();  // an empty array
			if(functor($prop)=="AN")
			   { // first check if it follows by deMorgan's law from something in $valid
				 $premise = "NO" . negation($prop);  
				 if(in_array($premise, $valid->stk))
				    { $valid->push($line,$linenumber);
					  record_dependency($valid,$dependencies,$linenumber,array_search($premise,$valid->stk));
					  continue;
					}
				 $r = explode("+", args($prop));
				 $dep = array();
				 foreach($r as $s)
				    { $notyet = true;
					  $nn = -1;
					  foreach($valid->stk as $q)
					    { ++$nn;
						  if($s == $q)
						    { $notyet = false;
							  $dep[] = $nn;
						      break;  // $s is verified
						    }
					    }
					  if($notyet)
					     { echo "Could not check $s in $line\n";
						   die();
						 }
				    }
				 $valid->push($line,$linenumber);
				 foreach($dep as $d)
				    record_dependency($valid, $dependencies, $linenumber, $d);
				 continue;
			   }
			else
			   { $nn = -1;
				 foreach($valid->stk as $q)
					{ ++$nn;
					  if($prop != $q && functor($q) != "AN")
				     	 continue;
				      if(functor($q) == "AN")
				         { $r = explode("+", args($q));
					       $flag44 = false;
					       foreach($r as $s)
					          { if($prop == $s)
						            { $flag44 = true;
									  record_dependency($valid, $dependencies, $linenumber, $nn);
							          break;
							        }
						      }
						   if($flag44== false)
						       continue;
						 }
				  	  $notyet = false;
				      $valid->push($prop,$linenumber);  // it's a repeat of an earlier line
				      record_dependency($valid, $dependencies, $linenumber, $nn); 
				       //  so it depends on the line that is being repeated.
					   // echo "pushing $prop\n";
				  	  break;  // $prop is verified
					}
				 if($notyet && functor($prop) == "OR")
				    { // it's enough if one of the disjuncts is in $valid->stk
					  $flag44 = false;
					  $disjuncts = explode("|",args($prop));
					  foreach($disjuncts as $x)
					     { // echo "checking $x\n";
						   $nn = -1;
						   foreach($valid->stk as $q)
						      { ++$nn;
							    if($x == $q)
							       { $valid->push($prop,$linenumber); // $prop is verified
								     $flag44=true;
								     record_dependency($valid, $dependencies, $linenumber, $nn);
								     break;
								   }
							  }
						   if($flag44)
							   break;
						 }
					 if($flag44)
					    continue;
					}
							
				  if($notyet)
					{ if(functor($prop) == "OR")
					     { $previous = $valid->peek();
						   if(functor($previous) == "OR")
						       { $R = explode("|", args($previous));
							     // is every member of $disjuncts in $R$?
							     $flag63 = false;
							     foreach($disjuncts as $x)
							         { if(!in_array($x, $R))
								          { $flag63 = true;
									        break;
									      }
									}
								 if($flag63)
								    { echo("Dying at line 1178\n");
									  print_r($valid);
									  echo "$previous  $prop\n";
									  die();
									}
								 // Now see if every member of $R$ is
								 // either in $disjuncts or its negation is in $valid->stk;
								 $flag64 = false;
								 foreach($R as $x)
								    { if(in_array($x, $disjuncts))
									     continue;
									  if(in_array(negation($x), $valid->stk))
									     { record_dependency($valid, $dependencies, $linenumber,
										           array_search(negation($x), $valid->stk));
									 	   continue;
									     }
									  $flag64 = true;
									  break;
									}
								 if($flag64)
								    { echo "Dying at line 1195 with $x\n";
									  print_r($R);
									  print_r($disjuncts);
									  die();
									}
								 $nn = count($valid->stk)-1;
								 record_dependency($valid, $dependencies,$linenumber, $nn);
								 $valid->push($prop,$linenumber);  // checked it successfully
								 // echo "pushing $prop\n";
								 continue;
							   }
						}
					  echo "could not check $prop in $line\n";	
						print_r($valid);	    	     
				  	  die();
					}
			   }
			
		  }
		//  echo "checked $line\n";
		//  $Now $line is successfully checked
	 }	 
	// all done.  But there should now be no marks in $valid
	if($valid->marked())
	   { echo("Final stack contains a mark\n");
		 print_r($valid);
		 die();
	   }
	echo "Proof checked OK. ";   
	echo $inference_count; echo " inferences. ";
	$Dep = process_dependencies($filename, $dependencies);
	array_to_file($filename . "dep", "dependencies", $Dep);
	}
	
//_____________________________________________________
ini_set('memory_limit', '1024M'); 
$count = 0;
foreach($results as $lemma)
{ $testfile = $lemma->label . ".prf";
  if($lemma->kind == "proposition")
      $testfile = "Prop" . $testfile;
  echo "Checking $testfile\n";
  check_proof($testfile);
  ++$count; 
  echo ("That makes $count proofs.\n");
}



  
 

