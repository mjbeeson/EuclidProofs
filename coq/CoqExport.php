#!/usr/bin/php
<?php 
// Strings can be written on two lines by using a period to concatenate the lines.
// We did this in some places to permit this file to be printed for archival purposes.
error_reporting(E_ALL|E_STRICT); 
ini_set('display_errors', true);
require_once('../programs/Axioms.php');

function put_spaces($s)
{
    return(implode(' ',str_split($s))); 
}

function fix_ident($s)
{
  return(str_replace(".","_",str_replace("-", "_", $s))); 
}

function fact_of_string($s)
{
    $beeson_to_geocoq = array(
	  "BE" => "BetS",  //  ABC are in order, that is, B is between A and C
	  "EQ" => "eq",  //  A and B are equal
	  "NE" => "neq",  //  A and B are unequal 
	  "CO" => "Col",  //  A, B, and C are collinear
	  "NC" => "nCol",  //  not collinear
	  "EE" => "Cong",  //  AB is congruent to CD
	  "TR" => "Triangle",  //  ABC is a triangle, same as noncollinear
	  "EA" => "CongA",  //  angle ABC is equal to angle abc
	  "ON" => "OnCirc",  //  A is on circle J
	  "RA" => "Out",  // Ray BA contains point C
	  "IC" => "InCirc",  //  A is inside J
	  "OC" => "OutCirc",  //  A is outside J
	  "CI" => "CI",  // J is the circle of center C and radius AB
	  "CU" => "Cut",  // AB and CD cut each other in E
	  "SU" => "Supp",  // ABC and DBF are supplementary angles
	  "TC" => "Cong_3",  // ABC and abc are congruent triangles
	  "LT" => "Lt",  //  AB < CD,  segment ordering
	  "SS" => "OS",  //  SSCDAB means C and D are on the same side of AB
	  "OS" => "TS",  //  OSCABD  means C and d are on opposite sides of AB
	  "AO" => "LtA",  //  angle ABC < angle DEF
	  "EL" => "equilateral",   //  ABC is equilateral
	  "RR" => "Per",   //  ABC is a right angle
	  "MI" => "Midpoint",    //  MABC means B is a (the) midpoint of AC
	  "IS" => "isosceles",   // ABC is isoceles, i.e. AB=AC and ABC is a triangle.
	  "PA" => "Perp_at",   // PQ perpendicular to AB at C
	  "PE" => "Perp",  // PQ perpendicular to AB
	  "TG" => "TG",  //  AB + CD > EF 
	  "IA" => "InAngle",   // IABCD means D is in the interior of angle ABC
	  "RT" => "RT", // RT missing
	  "IO" => "IO", // IO missing
          "VA" => "VA", // VA missing
          "AS" => "SumA", // AS missing
          "DI" => "Distinct3", // DI missing
          "ME" => "Meet", // ME missing
          "AD" => "AdjacentAngle", // AD missing
          "VE" => "VE", // VE missing
          "EF" => "EF",
          "PG" => "PG",
          "SQ" => "SQ",
          "EF" => "EF",
          "ET" => "ET",
          "TE" => "TE",
          "CR" => "CR",
          "PR" => "Par",
          "TP" => "TP",
	  "RE" => "RE",
	  "ER" => "ER",
	  "RC" => "RC",
	  "BR" => "BR",
	  "BF" => "BF",
	  "FE" => "FE",
	  "FR" => "FR",
	  "TF" => "TF",
	  "TT" => "TT"
          
	);
    if (substr($s,0,2) === "NO")
        {
            return("~ ".fact_of_string(substr($s,2)));
        } 
    if (substr($s,0,2) === "OR")
        {
            return("(".implode(" \/ ",array_map("fact_of_string",explode("|", substr($s,2)))).")");
        }
    if (substr($s,0,2) === "AN")
        {
            return("(".implode(" /\ ",array_map("fact_of_string",explode("+", substr($s,2)))).")");
        }
  
    return($beeson_to_geocoq[substr($s,0,2)] . " ". put_spaces(substr($s,2)));
    }


function def_of_label($labelname)
{
   global $definitions;  
    foreach($definitions as $d)
    {
        if ($d->label===$labelname)
            return($d);
    }
    echo "Error: definition ". $labelname . " not found!";

}
    
function pred_of_label($labelname)
{
            return(fact_of_string(substr(def_of_label($labelname)->hypotheses,0,2)));
}

// given a string like ORBEABC|BEDEF returns ABCDEF
function variables($s)
{
  if (substr($s,0,2) === "NO")
        {
            return (variables(substr($s,2)));
        }
  else if (substr($s,0,2) === "OR")
        {
            return (implode("",array_map("variables",explode("|", substr($s,2)))));
        }
  else if (substr($s,0,2) === "AN")
        {
            return (implode("",array_map("variables",explode("+", substr($s,2)))));
        }
  else 
        {
            return(substr($s,2));
        }
}

function compute_forall($a)
{
    if (is_array($a->conclusion))
      $aux = array_merge($a->hypotheses,$a->conclusion);
    else
      $aux = array_merge($a->hypotheses,array($a->conclusion));
    $aux = array_unique (str_split( implode("",array_map("variables",$aux))));
    $aux = array_diff($aux, str_split($a->existential));
 //   $aux = array_unique (str_split( implode("",$aux)));
    sort($aux);
    return($aux);
}

function compute_forall_string($a)
{
    return(implode(' ',compute_forall($a)));
}

// list of lemmas for which we use forward reasoning
function forward($name) 
{
    return ($name==="doublereverse" or
	    $name==="collinearorder" or
	    $name==="congruenceflip" or
	    $name==="betweennotequal" or
	    $name==="angledistinct" or
	    $name==="samesidesymmetric" or
	    $name==="NCorder" or
	    $name==="NCdistinct" or
	    $name==="proposition04" or
	    $name==="TGflip" or
	    $name==="lessthannotequal" or
	    $name==="tarskiparallelflip" or
	    $name==="parallelNC" or
	    $name==="parallelflip" or
	    $name==="proposition34" or
	    $name==="ETpermutation" or
	    $name==="EFpermutation" or
	    $name==="crossimpliesopposite" or
	    $name==="proposition_34"
	    );
}

function name_of($a)
{
  return($a->kind . "_" . fix_ident($a->label));
}

$all_depend=array();

// test if a depends on b
function depends_on($a,$b) {
  global $all_depend;
  if (in_array($b,$all_depend[$a]))
    return(true); // direct dependency
  else
    {
      return(in_array(true,array_map(function($x) use ($b) { return (depends_on($x,$b)); }, $all_depend[$a])));		 
    } 
}

// give the new direct dependencies
function new_depends($name) {
    global $all_depend;
    $new_depends=array();
    $direct_depends=$all_depend[$name];
    foreach($direct_depends as $d)
       {
	 $otherdepends=array_diff($direct_depends, [$d]);
	 if  (! in_array(true,array_map(function($x) use ($d) { return (depends_on($x,$d)); },$direct_depends)))
           array_push($new_depends,$d); // else we need this dependency
       }
    return($new_depends);
}

function print_proof($filename, $vars, $fp, $lemmaname)
{
  global $all_depend;
  $indent=0;
  $depend=array();
  $vars=array($vars);
  if(!file_exists($filename))
    {     echo "Expected $filename to exist.\n";
      die();
    }
  $proof = file($filename);
  foreach($proof as $line)
    { $line = trim($line);
      if($line == "") 
	{
	  continue;
	}
      if($line[0] == '%')  // skip commented-out lines
	continue; 
      $a = array(1,2,3,4); 
      if(preg_match("~^case ~", $line))
	{
	  fwrite($fp, str_repeat (" ",$indent) . "{\n");
	  $indent=$indent+1;
	  array_push($vars,array()); // create an array for the variables of this case
	} 
      else if(preg_match("~^cases ~", $line))
        {
	  $assertstring = substr($line,6,strpos($line, ":")-6);
	  fwrite($fp, str_repeat (" ",$indent));
	  fwrite($fp, "assert (");
	  fwrite($fp, fact_of_string($assertstring));
	  fwrite($fp, ").\n");
          fwrite($fp, (str_repeat (" ",$indent)) . "by cases on (");
          $orstring = substr(strstr($line, ":"),1) ;
          $coqorstring =  implode(" \/ ",array_map("fact_of_string",(array_map("trim",explode("|",$orstring)))));
          fwrite($fp, $coqorstring);
          fwrite($fp, ").\n");
        }
      else if(preg_match("~qedcase~", $line))
	{
	  fwrite($fp, str_repeat (" ",$indent) . "close.\n");
	  fwrite($fp, str_repeat (" ",$indent) . "}\n");
	  $indent=$indent-1;
	  array_pop($vars); // variables introduced in the current case are suppressed
	}  
      else if(strstr($line, " "))
	{  
	  preg_match("~(\S*)(\s*)(\S*)~", $line, $a);
	  $prop = $a[1];
	  $reason = trim($a[3]);
	  if($reason== "")
	    fwrite($fp, " by tauto.");
	  $assertvars = str_split(variables($prop));
	  $existsvars = array_unique(array_diff($assertvars,call_user_func_array('array_merge', $vars)));
	  sort($existsvars);
	  $casevars = array_pop($vars);
	  array_push($vars,array_merge($existsvars,$casevars)); // new points are added to variables of the current case
		if (empty($existsvars))
		  {
		    $existsstring = "";
		    $existspre = "";
		    $existspre2 = "";
		    $existspost = "";
		  }
		else
		  {
	  
		    $existsstring = "exists " . implode(" ",$existsvars) . ", ";
		    if (in_array("H", $existsvars))  // rename hypothesis H is we have a point H.
		      $existspre = str_repeat(" ",$indent) . "rename_H H;let Tf:=fresh in\n";
		    else
		      $existspre = str_repeat(" ",$indent) . "let Tf:=fresh in\n";	  
		    $existspre2 = "Tf:";
		    $existspost = ";destruct Tf as [".implode("[",$existsvars).str_repeat("]",count($existsvars)).";spliter";
		  }
	 	$assert= $existspre . str_repeat(" ",$indent) . "assert (". $existspre2 . $existsstring . fact_of_string($prop) . ")";
	 	if($reason == "assumption")
		{
		  fwrite($fp, str_repeat (" ",$indent) . "assert (~ ".fact_of_string($prop).").\n");
		  $indent=$indent+1;
		  array_push($vars,array());
		  fwrite($fp, str_repeat (" ",$indent) . "{\n");
		  fwrite($fp, str_repeat (" ",$indent) . "intro.\n");
		}
                else if ($reason == "reductio")
		  {
		    fwrite($fp, str_repeat (" ",$indent) . "contradict.\n");
                    fwrite($fp, str_repeat (" ",$indent) . "}\n");
		    $indent=$indent-1;
		    array_pop($vars);
		  }
                else if ($reason == "cases")
                  fwrite($fp, "(* cases *)\n"); 
                $reasontype= strstr($reason, ":", true);
                $reasonname= fix_ident(substr(strstr($reason, ":"),1));
                if ($reasontype == "defn")
                {
                    $def=def_of_label($reasonname);
                    if (is_array($def->hypotheses))
                    // we need to distinguish real definitions from axiomatic definitions.
                        {
			  fwrite($fp, $assert . " by (conclude ". $reasonname . ")" . $existspost . ".\n");
                        }
                    else
                        {
                            if ($reasonname=="circle") 
			      fwrite($fp, $assert . " by (conclude circle)" . $existspost . ".\n");
			    else
			      fwrite($fp, $assert . " by (conclude_def ". pred_of_label($reasonname) . ")" . $existspost . ".\n");
                        }
                }
                else if ($reasontype == "proposition" or $reasontype == "postulate" or $reasontype == "cn" or $reasontype == "axiom" or $reasontype == "lemma")
                {
		  if ($reasontype == "proposition" or $reasontype == "lemma")
		    array_push($depend, $reasontype . "_". $reasonname);
		  if (forward($reasonname))
		    {
		      fwrite($fp, $assert . " by (forward_using lemma_" . $reasonname .")" . $existspost . ".\n");  
		    }
		  else
		    {
		      fwrite($fp, $assert . " by (conclude " . $reasontype . "_". $reasonname . ")" . $existspost . ".\n");
		    }
                }
            }

      $all_depend[$lemmaname]=array_unique($depend);
      } 
}

function add_at_the_top($filename,$s)
   {
     $file_data = $s;
     $file_data .= file_get_contents($filename);
     file_put_contents($filename, $file_data);
   }

function print_results()
 { 
    global $results;
    foreach($results as $r)
        {
	  $filename="coq/" . name_of($r) . ".v";
	  $fp = fopen($filename,'w');
	  fwrite($fp, "Lemma ". name_of($r) . " : \n");
	  fwrite($fp, "   forall ". compute_forall_string($r) . ", \n   ");
	  fwrite($fp,implode(" -> ",array_map("fact_of_string",$r->hypotheses)));
	  if ($r->hypotheses)
	    fwrite($fp, " ->\n   ");
	  if ($r->existential != "")
            {
	      fwrite($fp, "exists ". put_spaces($r->existential).  ", ");
            }
	  fwrite($fp,implode(" /\ ",array_map("fact_of_string",$r->conclusion)) . ".\n");
	  fwrite($fp, "Proof.\n");
	  $prooffile = $r->label . ".prf";
	  if($r->kind == "proposition")
	    $prooffile = "Prop" . $prooffile;
	  $prooffile = "../programs/" . $prooffile;
	  fwrite($fp, "intros.\n");
	  print_proof($prooffile,compute_forall($r),$fp,name_of($r));
	  fwrite($fp, "close.\n");
	  fwrite($fp, "Qed.\n\nEnd Euclid.\n");
	  fclose($fp);
	  $requires=implode("",array_map(function ($x) {return ("Require Export GeoCoq.Elements.OriginalProofs.". $x . ".\n"); },new_depends(name_of($r))));
	  if ($requires=="")
	    add_at_the_top($filename, "Require Export GeoCoq.Elements.euclidean_axioms.\n\n");
	  else
	    add_at_the_top($filename, $requires . "\nSection Euclid.\nContext `{Ax:euclidean_neutral_ruler_compass}.\n");
	  echo name_of($r) . " done\n";
        }
 }

 

function print_axioms($fp)
{
    global $axioms;

    foreach($axioms as $a)
        {
	  fwrite($fp,"Axiom ");
	  fwrite($fp,name_of($a));
	  fwrite($fp, " : forall ");
	  fwrite($fp,compute_forall_string($a));
          fwrite($fp, ", ");
	  fwrite($fp, implode(" -> ",array_map("fact_of_string",$a->hypotheses)));
	  if ($a->hypotheses)
	    fwrite($fp, " -> ");
	  if ($a->existential != "")
            {
	      fwrite($fp, "exists ");
	      fwrite($fp, put_spaces($a->existential));
	      fwrite($fp, ", ");
            }
	  if (is_array($a->conclusion))
	    {
	      fwrite ($fp,implode(" /\ ",array_map("fact_of_string",$a->conclusion)));
	    }
	  else 
	    {
	      fwrite ($fp,fact_of_string($a->conclusion));
	    }
	  fwrite ($fp,".\n");
        }
}


function print_defs($fp)
{
    global $definitions;  
    foreach($definitions as $d)
    {
        if (is_array($d->hypotheses))
        {
	  fwrite($fp, "Axiom ". $d->label . " : ");
          fwrite($fp, "forall ");
          fwrite($fp, compute_forall_string($d));
          fwrite($fp, ", ");
          fwrite($fp,implode(" /\ ",array_map("fact_of_string",$d->hypotheses)));
          fwrite($fp, " <-> ");
           if ($d->existential != "")
            {
	      fwrite($fp, "exists ");
	      fwrite($fp,put_spaces($d->existential));
	      fwrite($fp,", ");
            }
           fwrite($fp,implode(" /\ ",array_map("fact_of_string",$d->conclusion)) . ".\n");
        }
        else if($d->label==="circle")
        {
	  fwrite($fp, "Parameter CI : Circle -> Point -> Point -> Point -> Prop.\n");
	  fwrite($fp, "Axiom circle : forall A B C, neq A B -> exists X, CI X C A B.\n");
        }
	else if($d->label==="on")
	{
	  fwrite($fp, "Parameter OnCirc : Point -> Circle -> Prop.\n");
          fwrite($fp, "Axiom ON : OnCirc B J -> CI J A C D -> Cong A B C D.\n");
	} 
        else
        {
	  fwrite ($fp,"Definition ");
	  fwrite ($fp,fact_of_string($d->hypotheses));
	  fwrite($fp, " := ");
	  if ($d->existential != "")
            {
	      fwrite($fp, "exists ");
              fwrite($fp, put_spaces($d->existential));
              fwrite($fp, ", ");
            }
            if (is_array($d->conclusion))
            {
	      fwrite($fp, implode(" /\ ",array_map("fact_of_string",$d->conclusion)));
            }
            else 
            {
	      fwrite($fp,fact_of_string($d->conclusion));
            }
            fwrite($fp, ".\n");
        }   
    }
    
}

$fp = fopen("coq/euclidean_axioms.v",'w');

fwrite($fp, "
Require Import Classical.

(** Some useful tactics *)

Ltac hyp_of_type t :=
 match goal with
| H1:t |- _ => H1
  end.


Ltac mysubst :=
 repeat
( match goal with
 | H1:?a=?b |- _ => (subst a;try revert H1)
 end);intros.
   
Ltac spliter := repeat
match goal with
   | H:(?X1 /\ ?X2) |- _ => induction H
end.

Ltac splits :=
 repeat
 match goal with
  | |- ?x /\ ?y => split
end.

Ltac remove_exists :=
repeat
 match goal with
  | |- exists x, _ => eexists
 end.

Ltac destruct_all := 
repeat
match goal with
   | H:_ |- _ => progress (decompose [ex and] H);clear H
end.

Ltac one_of_disjunct :=
 solve [repeat (assumption || (left;assumption) || right)].

Ltac remove_double_neg :=
repeat
 match goal with
 H: ~ ~ ?X |- _ => apply NNPP in H
end.

Ltac rename_H := let T := fresh in try rename H into T.

Axiom Point : Type.
Axiom Circle : Type.
Definition eq := @eq Point.
Parameter Cong : Point -> Point -> Point -> Point -> Prop.
Parameter BetS : Point -> Point -> Point -> Prop. 
Parameter InCirc : Point -> Circle -> Prop.
Parameter OnCirc : Point -> Circle -> Prop.
Parameter OutCirc : Point -> Circle -> Prop.
Parameter EF : Point -> Point -> Point -> Point -> Point -> Point -> Point -> Point -> Prop.
Parameter ET : Point -> Point -> Point -> Point -> Point -> Point -> Prop.
");
print_defs($fp);
fwrite($fp, "\n");
fwrite($fp,
       "Lemma Col_or_nCol : forall A B C, 
  Col A B C \/ nCol A B C.
Proof.
unfold nCol, Col.
intros.
tauto.
Qed.

Lemma nCol_or_Col : forall A B C, 
  nCol A B C \/ Col A B C.
Proof.
unfold nCol, Col.
intros.
tauto.
Qed.

Lemma eq_or_neq : forall A B,
 eq A B \/ neq A B.
Proof.
intros;unfold neq;tauto.
Qed.

Lemma neq_or_eq : forall A B,
 neq A B \/ eq A B.
Proof.
intros;unfold neq;tauto.
Qed.

Hint Resolve Col_or_nCol nCol_or_Col eq_or_neq neq_or_eq : decidability.

");
fwrite($fp, "
Tactic Notation \"by\" \"cases\" \"on\" constr(t) :=
(let H := hyp_of_type t in decompose [or] H; clear H) ||
   let C := fresh in (assert (C:t) by (auto with decidability || unfold neq in *;tauto);
 decompose [or] C;clear C).
                    
Lemma Col_nCol_False : forall A B C, nCol A B C -> Col A B C -> False.
Proof.
unfold Col, nCol;intuition.
Qed.

Hint Resolve Col_nCol_False.

Lemma nCol_notCol :
 forall A B C, ~ Col A B C -> nCol A B C.
Proof.
intros.
unfold nCol, Col, neq in *.
intuition.
Qed.

Hint Resolve nCol_notCol.

Lemma not_nCol_Col : forall A B C,
  ~ nCol A B C -> Col A B C.
Proof.
intros.
unfold nCol, Col, neq in *.
tauto.
Qed.

Ltac remove_not_nCol :=
repeat
match goal with
 H: ~ nCol ?A ?B ?C |- _ => apply not_nCol_Col in H
end.

Hint Resolve not_nCol_Col.

Lemma nCol_not_Col : forall A B C,
  nCol A B C -> ~ Col A B C.
Proof.
intros.
unfold nCol, Col, neq in *.
tauto.
Qed.

Hint Resolve nCol_not_Col.

Ltac forward_using thm :=
 remove_not_nCol;spliter;splits;
 match goal with
  H: ?X |- _ => apply thm in H;spliter;assumption
 end.

Ltac contradict := 
 (solve [eauto using Col_nCol_False]) || contradiction || (unfold nCol in *;intuition).

Ltac conclude t :=
 spliter;
 remove_double_neg;
 solve [unfold eq in *;mysubst;assumption |
        eauto using t |
        eapply t;eauto |
        eapply t;intuition |
        apply <- t;remove_exists;eauto  |
        unfold neq,eq in *;intuition |
        unfold neq,eq in *;remove_double_neg;congruence |
        apply t;tauto
].

Ltac close := solve [assumption |
                     auto |
                     repeat (split;auto) |
                     unfold neq, nCol in *;
                     try assumption;tauto |
                     remove_exists;eauto 15 
                     (* firstorder *)
                    ].

Ltac conclude_def t := (remove_double_neg;
  (progress (unfold t);  
   solve [remove_exists;eauto 6 |
          remove_exists;splits;eauto  |
          remove_exists;eauto 11 |
          one_of_disjunct |
          intuition
         ])) 
 || 
 solve [unfold t in *;spliter;assumption |
        unfold t in *;destruct_all;assumption |
        unfold t in *;remove_double_neg;destruct_all;remove_exists;eauto 11 
       (* unfold t in *;firstorder *) ].

Ltac find_meet A B C D :=
 match goal with
 H: Col A B ?X, H2: Col C D ?X |- _ => exists X; repeat split;assumption
end.

");
print_axioms($fp);

print_results();
 
?>