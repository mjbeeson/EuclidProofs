<?php 
// Strings can be written on two lines by using a period to concatenate the lines.
// We did this in some places to permit this file to be printed for archival purposes.
error_reporting(E_ALL|E_STRICT); 
ini_set('display_errors', true); 
//______________________________________________________________

class Theorem
{	var $kind;  // Proposition or Lemma
	var $label;   // number of proposition or label of lemma
	var $hypotheses;  // array of formulas (in logical form) 
	var $conclusion;  // logical form of the conclusion 
	var $existential;  // empty string or string of names of points to construct
	public function __construct($a,$b,$c,$d,$e="")
	   { $this->kind = $a;
		 $this->label = $b;
		 $this->hypotheses = $c;
		 $this->conclusion = $d;
		 $this->existential = $e;
	   }
	public function  to_array()
	 	{ $ans = array();
		  $ans[] = "$this->kind" . " " .  "$this->label";
		  $ans[] = "Hypotheses:";
		  foreach($this->hypotheses as $h)
		 	$ans[] = $h;
		  $ans[] = "Conclusion:";
		  if($this->existential != "")
		     $ans[] = "Exists " . $this->existential . " such that:";
		  $ans[] = $this->conclusion;
		  return $ans;
		}
}
/*_____________________________________________*/
class Axiom
{	var $kind;     //  cn  (common notion),  axiom,  or postulate 
	var $label;
	var $hypotheses;  // array of formulas
	var $conclusion;  // formula or formulas of the conclusion
	var $existential;  // empty string or string of names of points to construct
	public function __construct($a,$b,$c,$d,$e="")
	   { $this->kind = $a;
		 $this->label = $b;
		 $this->hypotheses = $c;
		 $this->conclusion = $d;
		 $this->existential = $e;
	   }
}
/*____________________________________________________*/
class Definition         //  relations are defined.  Functions are introduced as Skolem symbols in axioms or theorems.
{	var $label;
	var $hypotheses;      //  Definiens, which must be a relation.   
	   //  No function symbols defined.  Example,  midpoint(a,b,m)
	var $english;
	var $conclusion;     //  example, E(a,m,m,b).   Named 'conclusion' to match Theorem and Axiom
	var $existential;
	public function __construct($a,$b,$meaning,$c, $e="")
	   { $this->label = $a;
		 $this->hypotheses = $b;
		 $this->english = $meaning;
		 $this->conclusion = $c;
		 $this->existential= $e;
	   }
}

//___________________________________________________
function arity($f)
// return the expected arity of functor $f
{ $arities = array(  
	  "AC" => 3,  // ABC is acute, i.e., less than a right angle      
	  "BE" => 3,  //  ABC are in order, that is, B is between A and C
	  "TE" => 3,  // non-strict betweenness
	  "EQ" => 2,  //  A and B are equal
	  "NE" => 2,  //  A and B are unequal 
	  "CO" => 3,  //  A, B, and C are collinear
	  "NC" => 3,  //  not collinear
	  "EE" => 4,  //  AB is congruent to CD
	  "TR" => 3,  //  ABC is a triangle, same as noncollinear
	  "EA" => 6,  //  angle ABC is equal to angle abc
	  "ON" => 2,  //  A is on circle J
	  "RA" => 3,  // Ray BA contains point C
	  "IC" => 2,  //  A is inside J
	  "OC" => 2,  //  A is outside J
	  "CI" => 4,  // J is the circle of center C and radius AB
	  "CU" => 5,  // AB cuts CD cut in E
	  "SU" => 5,  // ABC and DBF are supplementary angles
	  "TC" => 6,  // ABC and abc are congruent triangles
	  "LT" => 4,  //  AB < CD,  segment ordering
	  "ME" => 4,  //  line AB meets line CD 
	  "CR" => 4,  // segment AB crosses segment CD
	  "SS" => 4,  //  SSCDAB means C and D are on the same side of AB
	  "OS" => 4,  //  OSCABD  means C and d are on opposite sides of AB
	  "AO" => 6,  //  angle ABC < angle DEF
	  "EL" => 3,   //  ABC is equilateral
	  "RR" => 3,   //  ABC is a right angle
	  "MI" => 3,    //  MABC means B is a (the) midpoint of AC
	  "IS" => 3,   // ABC is isoceles, i.e. AB=AC and ABC is a triangle.
	  "PA" => 5,   // PQ perpendicular to AB at C
	  "PR" => 4,   // AB is parallel to CD
	  "TP" => 4,   // AB is Tarski-parallel to CD
	  "PE" => 4,  // PQ perpendicular to AB
	  "TG" => 6,  //  AB + CD > EF 
	  "TT" => 8,   // AB + CD > EF + GH, used only in I.21  
	  "IA" => 4,   // IABCD means D is in the interior of angle ABC
     "RT" => 6,   // ABC and DEF make together two right angles 
     "AS" => 9,    // ABC+DEF=PQR
	  "PG" => 4,    // ABCD is a parallelogram
	  "SQ" => 4,    // ABCD is a square
	  "EF" => 8,    // ABCD and abcd are equal quadrilaterals; in particular they have equal area
	  "ET" => 6,    // ABC and DEF are equal triangles
	  "RE" => 4,    // ABCD is a rectangle
	  "RC" => 8,    // ABCD and abcd are congruent rectangles
	  "ER" => 8,    // ABCD and abcd are equal rectangles (defined)
	  "TE" => 6,    // defined "equal triangles"
	  "FE" => 8,    //  defined "equal quadrilaterals"
	  "EC" => 2,    // circles J and K are equal
	  "CA" => 4,    // segment AB cuts circle J at E
	  "LC" => 4,    // line AB (produced) cuts circle J 
	  "TA" => 4,    // line AB touches (is tangent to ) circle J at C	  
	  "CT" => 3,    // circles C and K touch at A
	  "CC" => 3,    // circles C cuts circle K at A
	  "ER" => 3   // points A and B are equally distant from center of J

	);
  if(! key_exists($f,$arities))
      { echo "Missing key $f in arities\n";
	    die();
	  }  
  return $arities[$f];
}

//___________________________________________________

// an axiom has kind, label, hypotheses, conclusion, existential (string of points to construct)
//  the "kind" is either "cn" (common notion) or "axiom" or "postulate".

$axioms = array(
   new Axiom("cn", "equalitytransitive",
  			array("EQAC", "EQBC"),
			"EQAB"
			),
   new Axiom("cn", "congruencetransitive",
			array("EEPQBC", "EEPQDE"),
			"EEBCDE"
			),
	new Axiom("cn","equalityreflexive",
			array(),
			"EQAA"
			),
	new Axiom("cn","congruencereflexive",
			array(),
			"EEABAB"
			),
	new Axiom("cn", "equalityreverse",
			array(),
			"EEABBA"
		   ),
	new Axiom( "cn", "sumofparts",
	             array("EEABab","EEBCbc","BEABC", "BEabc"),
	             array("EEACac")
	           ),
	new Axiom("cn", "stability",
			array("NONEAB"),
			"EQAB"
			),
	new Axiom("cn", "equalitysub",
			array("EQDA", "BEABC"),
			"BEDBC"
			),    // one entry must be here so lookup_reason can work
/* \item\label{cn:equalitysub}
	If $X=Y$ then any betweenness or congruence relations satisfied
	by $X$ are also satisfied by $Y$.  For example, if $\B(X,A,B)$ 
	then also $\B(Y,A,B)$, and if $XA = BC$ then also $YA=BC$.   */

   new Axiom("axiom","betweennessidentity",
             array(),  // no hypotheses
             "NOBEABA"
           ),
   new Axiom("axiom","betweennesssymmetry",
			 array("BEABC"),
			 "BECBA"
			),
	new Axiom("axiom","innertransitivity",
			array("BEABD","BEBCD"),
			"BEABC"
			),
	new Axiom("axiom","connectivity",
			array("BEABD", "BEACD","NOBEABC", "NOBEACB"),
			"EQBC"
			),
			/* If $B$ and $C$ lie on the finite straight line $AD$,  and neither
			is between $A$ and the other,  then they 
			are equal.  */
/*  This axiom is no longer used. 
	new Axiom("axiom","nullsegment1",
			array("EEABCC"),   // Only a null line is equal to a null line
			"EQAB"
			),
*/ 
	new Axiom( "axiom", "nocollapse", /* congruence takes a line to a line */
	             array("NEAB","EEABCD"),
	             array("NECD")
				),
	new Axiom("axiom", "5-line",
			array("EEBCbc",
				  "EEADad",
				  "EEBDbd",
				  "BEABC",
				  "BEabc",
				  "EEABab"
				 ),
			"EEDCdc"
			),
			/*  Let $A$ and $B$ be distinct points, and
			let $C$ be a point with $B$ between $A$ and $C$.
			Let $D$ be a fourth point (which may or may not be collinear with $A$ and $B$).
			Suppose that $b$ is between $a$ and $c$.
			and that the four straight lines $ab$, $bc$, $ad$, and $bd$ are
			equal respectively to the four straight lines $AB$, $BC$, $AD$,
			and $BD$.  Then the fifth lines $dc$
			and $DC$ are also equal.   */
/* This axiom is no longer used 
	new Axiom("postulate", "extension",
			array("NEAB","NECD"),
			array("BEABX", "EEBXCD"),
			"X"  // the point asserted to exist
			),
*/
	// The following is a classical consequence of the 
	// extension postulate, but constructively needed.
	//  It is commented out here so that it does not appear in "Proof-checking Euclid"
	/*
	new Axiom("postulate", "extension2",
			array("NEAB"),
			array("TEABX","EEBXCD"),
			"X"
			),  */
	new Axiom("postulate", "Pasch-inner",
			array("BEAPC", "BEBQC", "NCACB"),
			array("BEAXQ", "BEBXP"),
			"X"
		    ),
	new Axiom("postulate", "Pasch-outer",
			array("BEAPC", "BEBCQ", "NCBQA"),
			array("BEAXQ", "BEBPX"),
			"X"
			),
	// The following is Euclid's extension axiom, weaker than Tarski's 
	// "To produce a finite straight line continuously in a straight line" 
	new Axiom("postulate", "Euclid2",
			array("NEAB"),
			array("BEABX"),
			"X"
			),
	// The following corresponds to Euclid's Postulate 3,
	// "to describe a circle with any given centre and distance" 
	// That is, distance FROM THE CENTER.  Rigid compass draws 
	// through a given point, not with a given radius.  Thus
	// CIXAAB rather than CIXCAB. 
	new Axiom("postulate", "Euclid3", 
			  array("NEAB"), 
			  array("CIXAAB"),
			  "X"
			  ), 
/* This axiom is no longer used.			
			 // Let X be the circle of center C and radius AB
	new Axiom("postulate", "movablecompass", 
			  array("NEAB"), 
			  array("CIXCAB"),  		                  
			  "X"
			  ), 
*/ 	
	new Axiom( "postulate", "line-circle",
				array("CIKCPQ",
					  "ICBK",
					  "NEAB"
					 ),
				array("COABX",
					  "BEABY",  // Y is on the opposite side of B from A  
					  "ONXK",
					  "ONYK",
					  "BEXBY"
					 ),
				"XY"
				  ),
	new Axiom("postulate", "circle-circle",
			array("CIJCRS",  // J circle center C radius RS
				  "ICPJ",    // P inside J
				  "OCQJ",    // Q outside J
				  "CIKDFG", // K circle of center D radius FG
				  "ONPK",
				  "ONQK"   // P and Q lie on K
				 ),
			array("ONXJ", "ONXK"),  // J and K have a point in common
			"X"  
			),
	new Axiom("postulate", "Euclid5",
			array("BErts","BEptq", "BEraq",
				  "EEptqt", "EEtrts",
				  "NCpqs"
				),
			array("BEpaX","BEsqX"),
			"X"
			),
	new Axiom("postulate", "circlePasch",
	// a chord of circle ABC that crosses BC crosses AB or AC or contains A
	   array(
		       "EEFAFB",
					 "EEFAFC",
					 "EEFAFP",
					 "EEFAFQ",
					 "NEAB",
					 "NEAC",
					 "NEBC",
					 "NEPQ",
					 "BEBEC",
					 "BEPEQ",
					 "NEAP",
					 "NEAQ"
		      ),
		 array("ORCRPQAB|CRPQAC")
			),
			
	// following says a circle is given by its center and radius,
	// and the radius is positive.
	// Implicit in Euclid's use of "the centre of the circle CDB". 
	new Axiom("axiom", "circle-center-radius",
			array("CIJABC", 
				  "ONPJ"  // P is on SOME circle named J  
				 ),
			array("EEAPBC")  
			), 
	new Axiom("axiom", "congruentequal",
			array("TCABCabc"),
			array("ETABCabc")
			),
	new Axiom("axiom", "ETpermutation",
			array("ETABCabc"),
			array("ETABCbca","ETABCacb","ETABCbac",
				  "ETABCcba","ETABCcab"
				 )
			),
	new Axiom("axiom", "ETsymmetric",
			array("ETABCabc"),
			array("ETabcABC")
			),
	new Axiom("axiom", "EFpermutation",
			array("EFABCDabcd"),
			array("EFABCDbcda", "EFABCDdcba",
			      "EFABCDcdab", "EFABCDbadc",
				  "EFABCDdabc", "EFABCDcbad",
				  "EFABCDadcb"
				 )
			),
	new Axiom("axiom", "halvesofequals",
			array("ETABCBCD",
				  "OSABCD",
				  "ETabcbcd",
				  "OSabcd",
				  "EFABDCabdc"
				 ),
			array("ETABCabc")
			),		
	new Axiom("axiom", "EFsymmetric",
			array("EFABCDabcd"),
			array("EFabcdABCD")
			),
	new Axiom("axiom", "EFtransitive",
			array("EFABCDabcd",
				  "EFabcdPQRS"
				 ),
			array("EFABCDPQRS")
			),
	new Axiom("axiom", "ETtransitive",
			array("ETABCabc",
				  "ETabcPQR"
				 ),
			array("ETABCPQR")
			),
	new Axiom("axiom", "cutoff1",
	// cut a triangle off a triangle leaving quadrilateral
			array("BEABC", "BEabc",
				  "BEEDC", "BEedc",
				  "ETBCDbcd", "ETACEace"
				 ),
			array("EFABDEabde")
			),
	new Axiom("axiom", "cutoff2",
	// cut a triangle off a quadrilateral with common vertex
			array("BEBCD", "BEbcd",
				  "ETCDEcde", 
				  "EFABDEabde"
				 ),
			array("EFABCEabce")
			),
	new Axiom("axiom", "paste1",
			array("BEABC", "BEabc",
				  "BEEDC", "BEedc",
				  "ETBCDbcd",
				  "EFABDEabde"
				 ),
			array("ETACEace")
			),
	new Axiom("axiom", "deZolt1",
			array("BEBED"),
			array("NOETDBCEBC")
			),
	// deZolt2 says a triangle is not equal to a triangle it contains
	new Axiom("axiom", "deZolt2",
			array("TRABC",
				  "BEBEA",
				  "BEBFC"
				 ),
			array("NOETABCEBF")
			), 
	new Axiom("axiom", "paste2",
	// paste a triangle to a quadrilateral getting a convex quadrilateral
			array("BEBCD", "BEbcd",
				  "ETCDEcde",
				  "EFABCEabce",
				  "BEAMD", "BEBME",  //  ABDE is convex 
				  "BEamd", "BEbme"   // abde is convex 
				 ),
			array("EFABDEabde")
			),
	new Axiom("axiom", "paste3",
	//  paste two triangles together to get a quadrilateral
			array("ETABCabc",
				  "ETABDabd",
				  "BECMD",
				  "ORBEAMB|EQAM|EQMB",
				  "BEcmd",
				  "ORBEamb|EQam|EQmb"
				 ),
			array("EFACBDacbd")
			),
	new Axiom("axiom", "paste4", 
	// paste two quadrilaterals ( one pair are both really triangles)
	// The target quadrilateral is assumed to have crossing diagonals although that CAN be proved;
	// That assumption makes the axiom technically weaker, and much easier to verify in real^2, and 
	// in the single use of this axiom (Prop. 45)  the target is a parallelogram. 
			array("EFABmDFKHG",
				  "EFDBeCGHML",
				  "BEAPC",
				  "BEBPD",
				  "BEKHM",
				  "BEFGL",
				  "BEBmD",
				  "BEBeC",
				  "BEFJM",
				  "BEKJL", 
				 ),
			array("EFABCDFKML")
			)
);

$definitions = array(
	new Definition( "unequal", "NEAB", 
					"A and B are distinct points",
					array("NOEQAB")
				  ),
/*	new Definition( "T", "TEABC", 
					"B is non-strictly between A and C",
					array("NOANNEAB+NEBC+NOBEABC")
				  ), */
	new Definition( "collinear", "COABC", "A, B, and C are collinear",  
					array("OREQAB|EQAC|EQBC|BEBAC|BEABC|BEACB")
	              ),
	new Definition( "noncollinear", "NCABC",
					"A, B, and C are not collinear",
					array("NEAB", "NEAC", "NEBC", "NOBEABC", "NOBEACB", "NOBEBAC")
				  ),
	new Definition( "inside", "ICPJ", 
					"P is inside (some) circle J of center C and radius AB",
					array( "CIJUVW",  // J is the circle of center U, radius VW
					      // "OREQPU|ANBEUYX+EEUXVW+EEUPUY"   changed to the next line, 5.29.22, which required 
								// minor corrections to  the proofs of Prop02, Prop12, localextension, ondiameter.
								// Nested propositional connectives are not in the spirit of Euclid. 
								"OREQPU|LTUPVW"
					     ),
					"UVW"
					),
	new Definition( "outside", "OCPJ", 
					"P is outside (some) circle J of center U and radius VW",
					array( "CIJUVW", 
						   "BEUXP", 
						   "EEUXVW"
						 ),
					"XUVW"
					),
	new Definition( "on", "ONBJ", 
					"B is on (some) circle J of center U and radius XY", 
	                array("CIJUXY", "EEUBXY", "NEXY"),
	                "XYU"
	              ),
	new Definition( "equilateral", "ELABC",
					"ABC is equilateral",
	 				array("EEABBC","EEBCCA")
				  ),
	new Definition( "triangle", "TRABC", 
					"ABC is a triangle",
					array("NCABC")
				  ),
	new Definition( "ray", "RAABC", 
					"C lies on ray AB",
	                array( "BEXAC", "BEXAB"), // introducing X
	                "X"
	              ),
	new Definition( "lessthan", "LTABCD", 
					"AB is less than CD",
	                array("BECXD", "EECXAB"),
					"X"
	               ), 
    new Definition( "midpoint", "MIABC", 
 					"B is the midpoint of AC",
					array("BEABC", "EEABBC")
				   ),
	new Definition( "equalangles", "EAABCabc", 
					"Angle ABC is equal to angle abc",
					array("RABAU", "RABCV", "RAbau","RAbcv", "EEBUbu", 
					      "EEBVbv", "EEUVuv", "NCABC"
				    ),
					"UVuv"
				  ),
/*
	new Definition( "adjacentangles", "ADABCDEF",
					"ABC and DEF are adjacent angles",
	 				array("EQBE", "BEABF","RABDC")
				  ), 
	new Definition( "verticalangles", "VAABCDEF",
					array("EQBE", "BEABD", "BECBF")
				  ), */
	new Definition( "supplement", "SUABCDF",    
					"DBF is a supplement of ABC",
					array("RABCD", "BEABF")
				  ),
	new Definition( "rightangle", "RRABC",
					"ABC is a right angle",
					array( "BEABX", "EEABXB", "EEACXC", "NEBC"),
					"X"
				  ),
	new Definition( "perpat","PAPQABC",  
	                "PQ is perpendicular to AB at C and NCABP",
					array("COPQC",
						  "COABC",
						  "COABX",
						  "RRXCP"
						),
					"X"
				  ),
	new Definition( "perpendicular", "PEPQAB",
					"PQ is perpendicular to AB",
					array("PAPQABX"),
					"X"
				  ),
	new Definition( "interior", "IAABCP", 
					"P is in the interior of angle ABC",
	 				array("RABAX","RABCY","BEXPY"),
					"XY"
                  ),
    new Definition( "oppositeside", "OSPABQ",
					"P and Q are on opposite sides of AB",
					array("BEPXQ", "COABX", "NCABP"),
					"X"
				  ),
/*   Points $P$ and $Q$ {\em lie on opposite sides of line $AB$}
	if there is a point $E$ between $P$ and $Q$ and collinear with $AB$,
	and $P$ is not collinear with $AB$.  */
	new Definition( "sameside", "SSPQAB",
					"P and Q are on the same side of AB",
					array("COABU", "COABV", "BEPUX", "BEQVX", "NCABP", "NCABQ"),
						"XUV"
				  ),
		/*  P and Q are on the same side of AB if there are points $R$, $U$, and $V$ such that $U$ is
	     collinear with $A$ and $B$, $V$ is collinear with $A$ and $B$,
		and $U$ is between $P$ and $R$ and $V$ is between $Q$ and $R$, 
		and $P$ and $Q$ are not collinear with $AB$.  */
	new Definition( "isosceles", "ISABC",
					"ABC is isosceles with base BC",
					array("TRABC", 
						  "EEABAC"
						 )
				  ),
	new Definition( "cut", "CUABCDE",  
					"AB cuts CD in E",
					array("BEAEB", 
						  "BECED",
						  "NCABC",
						  "NCABD"
						 )
				  ),
	new Definition( "trianglecongruence", "TCABCabc",
					"Triangle ABC is congruent to abc",
					array("EEABab",
						  "EEBCbc",
						  "EEACac",
						  "TRABC"
						 )
				  ),
	new Definition( "anglelessthan", "AOABCDEF",
					"Angle ABC is less than angle DEF",
					array("BEUXV",
						  "RAEDU",
						  "RAEFV",
						  "EAABCDEX"
						 ),
					 "UXV"
				  ),
	new Definition( "togethergreater", "TGABCDEF",
	                "AB and CD are together greater than EF",
					array("BEABX",
						  "EEBXCD",
						  "LTEFAX"
						 ),
					"X"
					), 
	new Definition( "togetherfour", "TTABCDEFGH",   
					"AB,CD are together greater than EF, GH",
					array("BEEFX",
						  "EEFXGH",
						  "TGABCDEX"
						 ),
					"X"
					),  
	new Definition( "tworightangles", "RTABCDEF",
					"ABC and DEF make together two right angles",
					array("SUXYUVZ",
						  "EAABCXYU",
						  "EADEFVYZ"
						 ),
					"XYZUV"
				   ),   
	new Definition( "meet",  "MEABCD",
					"AB meets CD",
					array("NEAB", "NECD", 
					 	  "COABX", "COCDX"
					 	 ),
					"X"
					),
	new Definition( "cross", "CRABCD",
					"AB crosses CD",
					array("BEAXB", 
						  "BECXD"
						 ),
					"X"
					),
	new Definition( "tarski_parallel",  "TPABCD",
					"AB and CD are Tarski parallel",
					array("NEAB", "NECD", 
						  "NOMEABCD",  // AB doesn't meet CD 
						  "SSCDAB"  // CD on same side of AB
						 )
				  ), 
	new Definition( "parallel", "PRABCD",
					"AB and CD are parallel",
					array("NEAB", "NECD",
						  "COABU", "COABV", "NEUV",
						  "COCDu", "COCDv", "NEuv",
						  "NOMEABCD",
						  "BEUXv", "BEuXV"
					 	 ),
						 "UVuvX"
				  ),
	new Definition( "anglesum", "ASABCDEFPQR", 
					"ABC and DEF are together equal to PQR",	
					array("EAABCPQX",
						  "EADEFXQR",
						  "BEPXR"
						 ),
					"X"
				   ),
	new Definition( "parallelogram", "PGABCD", 
					"ABCD is a parallelogram",
					array("PRABCD", "PRADBC")
				  ),
	new Definition( "square", "SQABCD",  
					"ABCD is a square",
	   // equilateral and right-angled, according to Euclid
	   // he used THIS definition but stated a definition with ONE right angle
					array("EEABCD",
						  "EEABBC",
						  "EEABDA",
						  "RRDAB",
						  "RRABC",
						  "RRBCD",
						  "RRCDA"
						)
				   ),
	new Definition( "rectangle", "REABCD",
					"ABCD is a rectangle",
					array("RRDAB",
						  "RRABC",
						  "RRBCD",
						  "RRCDA",
						  "CRACBD"
						)
					),
	new Definition( "segmentcutscircle", "CAABJE",
	                "AB cuts circle J at E",  // from inside to outside
	                array("CIJCPQ",
	                      "ONEJ",
	                      "BEAEB",
	                      "ICAJ",
						  "OCBJ"
						 )
				  ),
	new Definition( "linecutscircle", "LCABJ",
					"AB (produced) cuts circle J",
					array("CICJCPQ",
						  "COABX", 
						  "COABY",
						  "COABZ",
						  "CAXYJZ"
						 ),
					     "CPQXYZ"
					),
	new Definition( "tangentline", "TAABJE",
	                "line AB touches circle J at E",  
	                array("CIJCPQ",
						  "ONEJ",
						  "BEAEB",
						  "NOLCABJ"
						 ),
					     "CPQ"
					),						  
	// The following definitions will be used to define 
	// "equal triangles" and "equal quadrilaterals"
	new Definition( "congruentrectangles", "RCABCDabcd",
					"ABCD and abcd are congruent rectangles",
					array("REABCD",
						  "REabcd",
						  "EEABab",
						  "EEBCbc"
						 )
					),
	new Definition( "equalrectangles", "ERABCDabcd",
	  				"ABCD and abcd are equal rectangles",
					// because they are complements
	  				//  in a larger "gnomon" rectangle
					array("RCABCDXYZU",
						  "RCabcdxYzu",
						  "BExYZ",
						  "BEXYz",
						  "BEWUw" // diagonal of gnomon
						 ),
					"XYZUxzuwW"
					),
	new Definition( "baserectangle", "BRABCDE",
					"ABCD is a base rectangle of triangle BCE",
					array("REBCDE",
						  "CODEA"
						 )
					),
	new Definition( "figurerectangle", "FRABCDEFGH",
					"EFGH is a figure rectangle of quadrilateral ABCD",
					// and ABCD does not degerate to a triangle
					array("REEFGH",
						  "BEEBF",
						  "BEHDG",
						  "BRCBDGF",
						  "BRABDHE"
						 )
					),
	new Definition( "equaltriangles", "TEABCabc",
					"ABC and abc are equal triangles",
	//  rectangles containing the triangles are equal
		  			array("REABXY", "REabxy",
						  "COXYC", "COxyc",
						  "ERABXYabxy"
						 ),
					"XYxy"
					),
	new Definition( "equalfigures", "FEABCDabcd",
					"ABCD and abcd are equal quadrilaterals",
					array("OSABCD", "OSabcd",
						  "FRABCDXYZU", "FRabcdxyzu",
						  "ERXYZUxyzu"
						  ),
				    "XYZUxyzu"
				   ),
	new Definition( "equaltrianglefigure", "TFABCabcd",
					"triangle ABC is equal to quadrilateral abcd",
					array("BRABCXY",
						  "FRabcdxyzu",
						  "ERBCXYxyzu"
						 ),
					"XYxyzu"
					),
	new Definition( "circlecut", "CCJKB",  
	// first used in III.5,  and never defined by Euclid
	// we define it as Euclid uses it in III.5
					"circle J cuts circle K  at B",
					array("ONBJ",
					      "ONBK",
					      "ONXK",
					      "ONYK",
						  "ONxJ",
						  "ONyJ",
					      "CIJEPQ",
						  "CIKepq",
						  "BEEXy",
						  "BEExY"
					     ),
					"XYUV"
				   ), 
	new Definition("acute","ACABC", 
					"angle ABC is less than a right angle", 
					array("RRXYZ", 
							"AOABCXYZ"
							), 
					"XYZ"
            	),
);


$results = array(
	new Theorem( "lemma", "equalitysymmetric",
	 			 array("EQBA"),
				 array("EQAB")
			   ),
	new Theorem( "lemma", "inequalitysymmetric",
				array("NEAB"),
				array("NEBA")
			   ),
	new Theorem( "lemma", "congruencesymmetric",
			 	 array("EEBCAD"),
				 array("EEADBC")
			   ),
	new Theorem( "lemma", "congruencetransitive",
				 array("EEABCD", "EECDEF"),
				 array("EEABEF")
				),
	new Theorem( "lemma", "3.6a",
				array("BEABC", "BEACD"),
				array("BEBCD")
				),
	new Theorem( "lemma", "3.6c",
				array("BEABD", "BEBCD"),
				array("BEABC")
				),
	new Theorem( "lemma", "betweennotequal",
	             array("BEABC"),
	             array("NEBC", "NEAB", "NEAC")
	           ),
	new Theorem( "lemma", "extensionunique",
				 array( "BEABE",
						"BEABF",
						"EEBEBF"
					  ),
				 array( "EQEF")
			   ),
	new Theorem( "lemma", "doublereverse",
				array("EEABCD"),
				array("EEDCBA", "EEBADC")
				),
	new Theorem( "lemma", "congruenceflip",
				array("EEABCD"),
				array("EEBADC", "EEBACD", "EEABDC")
			   ),
    // following special case of extension needs only line-circle, 
   // not even Euclid2
	new Theorem ("lemma", "localextension",
	             array("NEAB", "NEBQ"),
	             array("BEABX", 
	                   "EEBXBQ"
	                  ),
	            "X"
            	),	
	new Theorem( "lemma", "betweennesspreserved",
				array("EEABab", 
					  "EEACac", 
					  "EEBCbc",
					  "BEABC"
					  ),
				array("BEabc")
				),
	new Theorem( "lemma", "differenceofparts",
				array("EEABab",
					  "EEACac",
					  "BEABC",
					  "BEabc"
					 ),
				array("EEBCbc")
			   ),
	new Theorem( "lemma", "3.7a",
				array("BEABC", "BEBCD"),
				array("BEACD")
				),
	new Theorem( "lemma", "3.5b",
				array("BEABD", "BEBCD"),
				array("BEACD")
				),
	new Theorem( "lemma", "3.6b",
				array("BEABC", "BEACD"),
				array("BEABD")
				),
	new Theorem( "lemma", "3.7b",
				array("BEABC", "BEBCD"),
				array("BEABD")
				),
	new Theorem( "lemma", "partnotequalwhole",
				array("BEABC"),
				array("NOEEABAC")
			   ),
	new Theorem( "lemma", "NCdistinct",
				array("NCABC"),
				array("NEAB","NEBC","NEAC",
					  "NEBA","NECB","NECA"
					 )
				),
	new Theorem ( "proposition", "01",
				array( "NEAB"),
				array( "ELABX",
				       "TRABX"
				     ),
				"X"  // the point to construct
				),
	new Theorem ( "proposition", "02",
				array( "NEAB", "NEBC"),
				array( "EEAXBC"),
				"X"
				), 
	new Theorem ("lemma", "extension",
	             array("NEAB", "NEPQ"),
	             array("BEABX", 
	                   "EEBXPQ"
	                  ),
	            "X"
            	),
	new Theorem( "lemma", "lessthancongruence2",
				array("LTABCD",
					  "EEABEF"
					 ),
				array("LTEFCD"
				     )
				),
	new Theorem( "lemma", "lessthancongruence",
				array("LTABCD",
				 	  "EECDEF"
					 ),
				array("LTABEF")
				),
	new Theorem( "lemma", "outerconnectivity",
				array("BEABC",
					  "BEABD",
					  "NOBEBCD",
					  "NOBEBDC"
					 ),
				array("EQCD")
				),
	new Theorem( "lemma", "trichotomy1",
				array("NOLTABCD",
					  "NOLTCDAB",
					  "NEAB",
					  "NECD"
					 ),
				array("EEABCD")
				),
	new Theorem( "lemma", "interior5",
				array("BEABC",
					  "BEabc",
					  "EEABab",
					  "EEBCbc",
					  "EEADad",
					  "EECDcd"
				      ),
				array("EEBDbd")
			    ),
	new Theorem( "lemma", "collinear1",
				array("COABC"),
				array("COBAC")
				),
	new Theorem( "lemma", "collinear2",
				array("COABC"),
				array("COBCA")
				),			
	new Theorem( "lemma", "collinearorder",
				array("COABC"),
				array("COBAC","COBCA","COCAB","COACB","COCBA")
				),
	new Theorem( "lemma", "NCorder",
				array("NCABC"),
				array("NCBAC","NCBCA","NCCAB","NCACB","NCCBA")
				),
	new Theorem( "lemma", "collinear4",
				array("COABC",
					  "COABD",
					  "NEAB"
					 ),
				array("COBCD")
				), 
	new Theorem( "lemma", "collinear5",
				array("NEAB",
					  "COABC",
					  "COABD",
					  "COABE"
					 ),
				array("COCDE")
				),
	new Theorem( "lemma", "NChelper",
				array("NCABC", "COABP", "COABQ", "NEPQ"),
				array("NCPQC")
				),
	new Theorem( "lemma", "fiveline",
				array("COABC",
					  "EEABab",
					  "EEBCbc",
					  "EEADad",
					  "EECDcd",
					  "EEACac",
					  "NEAC"
				      ),
				array("EEBDbd")
			    ),
	new Theorem( "lemma", "twolines",
				array("CUABCDE",
					  "CUABCDF",
					  "NCBCD"
					 ),
				array("EQEF")
				),
	new Theorem( "lemma", "ray2",
				array("RAABC"),
				array("NEAB")
			   ),
	new Theorem( "lemma", "ray",
				array( "RAABP",
					   "NEPB",
					   "NOBEAPB"
					 ),
				array( "BEABP")
				),
	new Theorem( "lemma", "ray1",
		       array( "RAABP"),
			   array( "ORBEAPB|EQBP|BEABP")
			   ),
	new Theorem( "lemma", "ray3",
	           array("RABCD", "RABCV"),
	           array("RABDV")
	 			),
	new Theorem( "lemma", "raystrict",
				array("RAABC"),
				array("NEAC")
			   ),
	new Theorem( "lemma", "ray4",
	            array("ORBEAEB|EQEB|BEABE", "NEAB"),
				array("RAABE")
				),
	new Theorem( "lemma", "ray5",
				array("RAABC"),
				array("RAACB")
				),
	new Theorem( "lemma", "rayimpliescollinear",
				array("RAABC"),
				array("COABC")
				),
	new Theorem( "lemma", "tworays",
				array("RAABC", "RABAC"),
				array("BEACB")
				),
	new Theorem( "lemma", "twolines2",
				array("NEAB", "NECD", 
					  "COPAB", "COPCD",
					  "COQAB", "COQCD",
					  "NOANCOACD+COBCD"
					 ),
				array("EQPQ")
				), 
	new Theorem( "lemma", "supplements",
				array("EAABCabc",
					  "SUABCDF",
					  "SUabcdf"
					 ),
				array("EADBFdbf")
			   ),
	new Theorem( "lemma", "supplementsymmetric",
				array("SUABCED"),
				array("SUDBECA")
			   ),
	new Theorem( "lemma", "collinearitypreserved",
				array("COABC",
					  "EEABab",
					  "EEACac",
					  "EEBCbc"
					 ),
				array("COabc")
			   ),
	new Theorem( "lemma", "trichotomy2",
				array("LTABCD"),
				array("NOLTCDAB")
			   ),
	new Theorem( "lemma", "lessthannotequal",
				array("LTABCD"),
				array("NEAB", "NECD")
			   ),
	new Theorem( "lemma", "layoff",
				array("NEAB",
					  "NECD"
					 ),
				array("RAABX", // X lies on ray(A,B)
				      "EEAXCD"
				     ),
				"X"  // the point to construct 
			   ),
	new Theorem( "lemma", "layoffunique",
				array("RAABC", 
					  "RAABD",
					  "EEACAD"
					 ),
				array("EQCD")
			   ),
	new Theorem( "lemma", "lessthanbetween",
				array("LTABAC", 
					  "RAABC"
					 ),
				array("BEABC")
				),
	new Theorem( "lemma", "lessthantransitive",
				array("LTABCD",
					  "LTCDEF"
					 ),
				array( "LTABEF")
				),
	new Theorem( "lemma", "lessthanadditive",
				array("LTABCD",
					  "BEABE",
					  "BECDF",
					  "EEBEDF"
					 ),
				array("LTAECF")
				),
	new Theorem( "lemma", "subtractequals",
				array("BEABC",
					  "BEADE",
					  "EEBCDE",
					  "BEACE"
					 ),
				array("BEABD")
				),
	new Theorem( "lemma", "crossbar",
				array("TRABC",  // ABC is a triangle 
					  "BEAEC",
					  "RABAU",
					  "RABCV"
					 ),
				array("RABEX",
					  "BEUXV"
					 ),
				"X"  // the point to construct
			    ),
	new Theorem( "lemma", "ABCequalsCBA",
				array("NCABC"),
				array("EAABCCBA")
			   ),
	new Theorem( "lemma", "equalanglessymmetric",
				array("EAABCabc"),
				array("EAabcABC")
			 	),
	new Theorem( "lemma", "angledistinct",
				array("EAABCabc"),
				array("NEAB", "NEBC", "NEAC", "NEab", "NEbc", "NEac")
			   ),
	new Theorem( "lemma", "4.19",
			array( "BEADB",
				   "EEACAD",
				   "EEBDBC"
				 ),
			array("EQCD")
			),
	new Theorem( "lemma", "collinearbetween",
				array("COAEB", 
					  "COCFD",
					  "NEAB", 
					  "NECD",
					  "NEAE",
					  "NEFD",
					  "NOMEABCD",
					  "BEAHD",  
					  "COEFH"
					 ),
				array( "BEEHF")
				),  
	new Theorem( "lemma", "9.5a",
				array("OSPABC","BERPQ","NCRQC", "COABR"),
				array("OSQABC")
					),
	new Theorem( "lemma", "9.5b",
				array("OSPABC","BERQP","NCCPR","COABR"),
				array("OSQABC")
				),
	new Theorem( "lemma", "9.5",
				array("OSPABC","RARQP","COABR"),
				array("OSQABC")
				),
	new Theorem( "lemma", "samesidereflexive",
				array("NCABP"),
				array("SSPPAB")
				),
	new Theorem( "lemma", "samesidesymmetric",
				array( "SSPQAB"),
				array( "SSQPAB",
					   "SSPQBA",
					   "SSQPBA"
					 )
				),
	new Theorem( "lemma", "oppositesidesymmetric",
				array("OSPABQ"),
				array("OSQABP")
				),
	new Theorem( "lemma", "oppositesideflip",
				array("OSPABQ"),
				array("OSPBAQ")
				),
	new Theorem( "lemma", "samesidecollinear",
				array("SSPQAB", 
				 	  "COABC", 
					  "NEAC"
			    ),
				array("SSPQAC")
				),
	new Theorem( "lemma", "equalanglesNC",
				array("EAABCabc"),
		 		array("NCabc")
				),
	new Theorem( "lemma", "ondiameter",
				array("CIKFPQ",
					  "EEFDPQ",
					  "EEFMPQ",
					  "BEDFM",
					  "BEDNM"
					 ),
				array("ICNK")
				),
	new Theorem ( "proposition", "03",
				array( "LTCDAB",
					   "EEEFAB"
					 ),
				array( "BEEXF",
					   "EEEXCD"
					 ),
				"X"   // the point to construct
				),
	new Theorem( "proposition", "04",
				array("EEABab",
					  "EEACac",
					  "EABACbac"
					 ),
				array("EEBCbc",
					  "EAABCabc",
					  "EAACBacb"
					 )
				),
	new Theorem( "lemma", "equalangleshelper",
				array("EAABCabc",
					  "RAbap",
					  "RAbcq"
					 ),
				array("EAABCpbq")
				),
	new Theorem( "lemma", "equalanglestransitive",
				array("EAABCDEF", 
					  "EADEFPQR"
					 ),
				array("EAABCPQR")
				),
	new Theorem( "lemma", "equalanglesreflexive",
				array("NCABC"),
				array("EAABCABC")
				),
	new Theorem( "lemma", "equalanglesflip",
				array("EAABCDEF"),
				array("EACBAFED")
				),
	new Theorem( "lemma", "supplements2",
				array("RTABCPQR",
					  "EAABCJKL",
					  "RTJKLDEF"
					 ),
				array("EAPQRDEF",
					  "EADEFPQR"
					 )
				),
	new Theorem( "lemma", "RTsymmetric",
				array("RTABCDEF"),
				array("RTDEFABC")
			   ),
	new Theorem( "lemma", "RTcongruence",
				array("RTABCDEF",
					  "EAABCPQR"
					 ),
				array("RTPQRDEF")
			   ),
	new Theorem( "lemma", "midpointunique",
				array("MIABC", 
					  "MIADC"
					 ),
				array("EQBD")
				),
	new Theorem( "lemma", "rightangleNC",
				array("RRABC"),
				array("NCABC")
				),
	new Theorem( "lemma", "8.2",
				array("RRABC"),
				array("RRCBA")
			   ),
	new Theorem( "lemma", "rightreverse",
				array("RRABC",
					  "BEABD",
					  "EEABBD"
					 ),
				array("EEACDC")
			   ),
	new Theorem ( "lemma", "8.3",
				 array( "RRABC", 
						"RABCD"
					  ),
				 array("RRABD")
				),
	new Theorem ( "lemma", "equaltorightisright",
				array( "RRABC", // ABC is a right angle
					   "EAabcABC"
					 ),
				array( "RRabc")
				),
	new Theorem( "lemma", "altitudebisectsbase",
				array("BEAMB",
					  "EEAPBP",
					  "RRAMP"
					 ),
				array("MIAMB")
			   ),
	new Theorem( "lemma", "collinearright",
				array("RRABD","COABC", "NECB"),
				array("RRCBD")
				),
	new Theorem( "lemma", "droppedperpendicularunique",
				array("RRAMP",
					  "RRAJP",
					  "COAMJ"
					 ),
				array("EQMJ")
				),
	new Theorem( "lemma", "8.7",
				array("RRCBA"),
				array("NORRACB")
				),
	new Theorem( "lemma", "angleorderrespectscongruence",
				array("AOABCDEF",
					  "EAPQRDEF"
					 ),
				array("AOABCPQR")
				),
	new Theorem( "lemma", "angleordertransitive",
				array("AOABCDEF", 
					  "AODEFPQR"
					 ),
				array("AOABCPQR")
				),
	new Theorem( "lemma", "angleorderrespectscongruence2",
				array("AOABCDEF",
					  "EAabcABC"
					 ),
				array("AOabcDEF")
				),
	new Theorem( "proposition", "15",  // vertical angles are equal
				array("BEAEB",
					  "BECED",
					  "NCAEC"
					 ),
				array("EAAECDEB",
					  "EACEBAED"
					 )
				),
	new Theorem( "lemma", "supplementinequality",
				array("SUABCDF",
					  "SUabcdf",
					  "AOabcABC"),
				array("AODBFdbf")
				),
	new Theorem( "proposition", "05",
				array("ISABC"),
				array("EAABCACB")
				),
	new Theorem( "proposition", "05b",
				array("ISABC",
					  "BEABF",
					  "BEACG"
					 ),
				array("EACBFBCG")
				),
	new Theorem( "proposition", "10",  // midpoint existence
				array("NEAB"),
				array("BEAXB", 
					  "EEXAXB"
					 ),
				"X"
				),
	new Theorem( "lemma", "planeseparation",
				array("SSCDAB",  // C and D on the same side of AB
					  "OSDABE"  // D and E on opposite sides of AB
 					 ),
				array("OSCABE")
				),
	new Theorem( "lemma", "samesidetransitive",
				array("SSPQAB", "SSQRAB"),
				array("SSPRAB")
				),
	new Theorem( "lemma", "samesideflip",
				array("SSPQAB"),
				array("SSPQBA")
				),
	new Theorem( "lemma", "samenotopposite",
				array("SSABCD"),
				array("NOOSACDB")
				), 
	new Theorem( "lemma", "sameside2",
				array( "SSEFAC",
					   "COABC",
					   "RABFG"
					 ),
				array( "SSEGAC")
				),
	new Theorem( "proposition", "12",  // dropped perpendicular
				array("NEAB",
					  "NCABC"
				     ),
				array("PACXABX"),
				"X"
			   ),
	new Theorem( "proposition", "13", 
				array("BEDBC",
					  "NCABC"
					 ),
				array("RTCBAABD")
			   ),	
	new Theorem( "proposition", "07",  // triangle uniqueness
				array("NEAB",
					  "EECADA",
					  "EECBDB",
					  "SSCDAB"
					 ),
				array("EQCD")
				),
	new Theorem( "lemma", "crossbar2",
			array("AOHGAHGP",
				  "SSAPGH",
				  "RAGHS",
				  "RAGPT"
				 ),
			array("BETXS",
				  "RAGAX"
				 ),
			"X"
			),	
	new Theorem( "lemma", "angletrichotomy",
				array( "AOABCDEF"),
				array( "NOAODEFABC")
				),
	new Theorem( "proposition", "06a",
				array("TRABC",
					  "EAABCACB"
					 ),
				array("NOLTACAB")
				),
	new Theorem( "proposition", "06",
				array("TRABC",
					  "EAABCACB"
					 ),
				array("EEABAC")
				),
	new Theorem( "proposition", "08",
				array("TRABC",
					  "TRDEF",
					  "EEABDE",
					  "EEACDF",
					  "EEBCEF"
					 ),
				array("EABACEDF",
				      "EACBAFED",
				      "EAACBDFE"
				     )
				),
	new Theorem( "proposition", "09",  // bisect an angle
				array( "NCBAC"),
				array( "EABAXXAC",
				 	   "IABACX"
				 	 ),
				"X"
				),
	new Theorem( "proposition", "11",  // erected perpendicular
				array("BEACB"),
				array("RRACX"),
				"X"
				),
	new Theorem( "lemma", "pointreflectionisometry",
				array("MIABC",
					  "MIPBQ",
					  "NEAP"
					 ),
				array("EEAPCQ")
			   ),
	new Theorem( "lemma", "rightreflection", // point reflection of right angle is right
				array("RRABC",
					  "MIAEa",
					  "MIBEb",
					  "MICEc"
					 ),
				array("RRabc")
				),
	new Theorem( "lemma", "notperp",
				array("BEACB",
					  "NCABP"
					 ),
				array("NCABX",
					  "SSXPAB",
					  "NORRACX"  
					 ),
				"X"
				),
	new Theorem( "proposition", "11B",  // erected perpendicular on given side
				array("BEACB", "NCABP"),
				array("RRACX","OSXABP"),
				"X"
				),
	new Theorem( "lemma", "linereflectionisometry",
				array("RRBAC",
				      "RRABD",
				      "BECAE",
				 	  "BEDBF",
					  "EEACAE",
					  "EEBDBF"
					 ),
				array("EECDEF")
				),
	new Theorem( "lemma", "10.12",
				array("RRABC",
					  "RRABH",
					  "EEBCBH"
					 ),
				array("EEACAH")
			   ),
	new Theorem( "lemma", "erectedperpendicularunique",
				array("RRABC",
					  "RRABE",
					  "SSCEAB"
					 ),
				array("RABCE")
				), 
	new Theorem( "proposition", "14",
				array("RTABCDBE",
					  "RABCD",
					  "OSEDBA"
					 ),
				array("SUABCDE", "BEABE")
				),
	new Theorem( "proposition", "16", // exterior angle theorem
				array("TRABC",
					  "BEBCD"
					 ),
				array("AOBACACD",
					  "AOCBAACD"
					 )
			 	),
	// in prop 17, if the sum of two angles exists, it 
	// is an angle, automatically less than two right angles.
	new Theorem( "proposition", "17",
				array("TRABC"),
				array("ASABCBCAXYZ"),
				"XYZ"
				),				
	new Theorem( "proposition", "18", // greater side subtends greater angle
				array("TRABC",
					  "LTABAC"
					 ),
				array("AOBCAABC")
				),
	new Theorem( "proposition", "19",  // greater angle subtended by greater side
				array("TRABC",
					  "AOBCAABC"
					 ),
				array("LTABAC")
				),
	new Theorem( "proposition", "20",   // triangle inequality
				array("TRABC"),
				array("TGBAACBC")
			   ),
	new Theorem( "lemma", "together",
				array("TGAaBbCc",
					  "EEDFAa",
					  "EEFGBb",
					  "BEDFG",
					  "EEPQCc"
					 ),
				array("LTPQDG",
				      "NEAa",
				      "NEBb",
				      "NECc"
				     )
				),
	new Theorem( "lemma", "together2",
				array("TGAaCcBb",
					  "EEFGBb",
					  "RAFGM",
					  "EEFMAa",
					  "RAGFN",
					  "EEGNCc"
					),
				array("RAMFN")
				),
	new Theorem("lemma","lessthanbetween2",
	array("LTEDEP", 
				"BEEFD",
				"BEEFP"
			),
	array("BEEDP")
				),
	new Theorem( "lemma", "togethersubtract",
				array("TGEFFCEC","EEECED","BEEFD"),
				array("LTFDFC")
			),
	new Theorem( "lemma", "TGsymmetric",
				array("TGAaBbCc"),
				array("TGBbAaCc")
				),
	new Theorem( "lemma", "TGflip",
				array("TGAaBbCc"),
				array("TGaABbCc",
					  "TGAaBbcC"
					  )
				),
	new Theorem( "lemma", "21helper",
				array("TGBAAEBE",
					  "BEAEC"
					 ),
				array("TTBAACBEEC")
			    ),
	new Theorem( "lemma", "TTorder",
				array("TTABCDEFGH"),
				array("TTCDABEFGH")
				),
	new Theorem( "lemma", "TTflip",
				array("TTABCDEFGH"),
				array("TTBADCEFGH")
				),
	new Theorem( "lemma", "TTtransitive",
				array("TTABCDEFGH",
					  "TTEFGHPQRS"
					),
				array("TTABCDPQRS")
				),
	new Theorem( "lemma", "TTflip2",
				array("TTABCDEFGH"),
				array("TTABCDHGFE")
				),
	new Theorem( "proposition", "21", 
				array("TRABC",
					  "BEAEC",
					  "BEBDE"
					 ),
				array("TTBAACBDDC",
				      "AOBACBDC"
				     )
				),
	new Theorem( "proposition", "22",  // triangle construction
				array("TGAaBbCc",
					  "TGAaCcBb",
					  "TGBbCcAa",
					  "NEFE"
					 ),
				array("EEFXBb",
					  "EEFYAa",
					  "EEXYCc",
					  "RAFEX",
					  "TRFXY"
					 ),
				"XY"
				),
	new Theorem( "lemma", "Euclid4",  //  all right angles equal
				array("RRABC", 
					  "RRabc"
					 ),
				array("EAABCabc")
			   ),
	new Theorem( "lemma", "legsmallerhypotenuse",
				array("RRABC"),
				array("LTABAC", 
					  "LTBCAC"
					 )
				),
	new Theorem( "proposition", "23",
				array("NEAB",
					  "NCDCE"
					 ),
				array("RAABY",
					  "EAXAYDCE"
					 ),
				"XY"
				),
	new Theorem( "proposition", "23B",
				array("NEAB", 
					  "NCDCE",
					  "NCABP"
					 ),
				array("RAABY",
					  "EAXAYDCE",
					  "OSXABP"
					 ),
				"XY"
				),	
	new Theorem( "proposition", "23C",
				array("NEAB", 
					  "NCDCE",
					  "NCABP"
					 ),
				array("RAABY",
					  "EAXAYDCE",
					  "SSXPAB"
					 ),
				"XY"
				),	  
    new Theorem("proposition", "24",
				array("TRABC", "TRDEF",
					  "EEABDE", "EEACDF",
					  "AOEDFBAC"
					 ),
				array("LTEFBC")
				),
	new Theorem("lemma", "angletrichotomy2",
				array("NCABC", "NCDEF", 
					  "NOEAABCDEF", 
					  "NOAODEFABC"
					 ),
				array("AOABCDEF")
				),
	new Theorem("proposition", "25",
				array("TRABC", "TRDEF",
					  "EEABDE", "EEACDF",
					  "LTEFBC"
					 ),
				array("AOEDFBAC")
			 	),
	new Theorem("proposition", "26A",
				array( "TRABC", "TRDEF",
					   "EAABCDEF",
					   "EABCAEFD",
					   "EEBCEF"
					 ),
				array("EEABDE","EEACDF","EABACEDF")
				),
	new Theorem("lemma", "26helper",
				array( "TRABC",
					   "EAABCDEF",
					   "EABCAEFD",
					   "EEABDE"
					 ),
				array("NOLTEFBC")
				),
	new Theorem("proposition", "26B",
				array( "TRABC", "TRDEF",
					   "EAABCDEF",
					   "EABCAEFD",
					   "EEABDE"
					 ),
				array("EEBCEF","EEACDF","EABACEDF")
				),
	new Theorem( "lemma", "parallelsymmetric",
	 			array("PRABCD"),
				array("PRCDAB")
			   ),
	new Theorem( "lemma", "paralleldef2A",
				array("TPABCD"),
				array("PRABCD")
				),
	new Theorem( "lemma", "parallelcollinear1",
				array("TPABcd", "BECcd"),
				array("TPABCd")
				),
	new Theorem( "lemma", "tarskiparallelflip",
				array("TPABCD"),
				array("TPBACD", "TPABDC", "TPBADC")
			   ),
	new Theorem( "lemma", "parallelcollinear2",
				array("TPABcd", "BEcCd"),
				array("TPABCd")
				),	
	new Theorem( "lemma", "parallelcollinear",
				array("TPABcd", "COcdC", "NECd"),
				array("TPABCd")
				),
	new Theorem( "lemma", "paralleldef2B",
				array("PRABCD"), 
				array("TPABCD")
			   ),
	new Theorem( "lemma", "parallelflip",
				array("PRABCD"),
				array("PRBACD", "PRABDC", "PRBADC")
			   ),	
	new Theorem( "lemma", "collinearparallel",
				array("PRABcd","COcdC", "NECd"),
				array("PRABCd")
				),
	new Theorem( "lemma", "parallelNC",
				array("PRABCD"),
				array("NCABC", "NCACD", "NCBCD", "NCABD")
				),
	new Theorem( "lemma", "collinearparallel2",
				array("PRABCD", "COCDE", "COCDF", "NEEF"),
				array("PRABEF")
				),
	new Theorem( "proposition", "27",
				array("BEAEB",
					  "BECFD",
					  "EAAEFEFD",
					  "OSAEFD"
					 ),
				array("PRABCD")
				),
	new Theorem( "proposition", "27B", // 4-point version
				array("EAAEFEFD", "OSAEFD"),
				array("PRAEFD")
				),
	new Theorem( "proposition", "28A",
				array("BEAGB",
					  "BECHD",
					  "BEEGH",
					  "EAEGBGHD",
				      "SSBDGH"
				     ),
				array("PRABCD")
				), 
	new Theorem( "proposition", "28B",
				array("BEAGB",
					  "BECHD",
					  "RTBGHGHD",
				      "SSBDGH"
				     ),
				array("PRABCD")
				), 
	new Theorem( "proposition", "28C",
				array("RTBGHGHD",
					  "SSBDGH"
					 ),
				array("PRGBHD")
				),
	new Theorem( "proposition", "28D",
				array("BEEGH",
					  "EAEGBGHD",
				      "SSBDGH"
				     ),
				array("PRGBHD")
				), 
	new Theorem( "proposition", "31",
				array("BEBDC", "NCBCA"),
				array("BEXAY",
					  "EAYADADB",
					  "EAYADBDA",
					  "EADAYBDA",
					  "EAXADADC",
					  "EAXADCDA",
					  "EADAXCDA",
					  "PRXYBC",
					  "EEXADC",
					  "EEAYBD",
					  "EEAZZD",
					  "EEXZZC",
					  "EEBZZY",
					  "BEXZC",
					  "BEBZY",
					  "BEAZD"
					 ),
				"XYZ"
			   ),
	new Theorem( "proposition", "31short",
				array("BEBDC", "NCBCA"),
				array("BEXAY","EAXADADC","PRXYBC","BEXZC","BEAZD"),
				"XYZ"
				),
	new Theorem( "proposition", "29",
				array( "PRABCD", 
					   "BEAGB", 
					   "BECHD", 
					   "BEEGH",  
					   "OSAGHD"
					 ),
				array("EAAGHGHD", 
					  "EAEGBGHD",
					  "RTBGHGHD"
					 )
				),
	new Theorem( "proposition", "29B",
				array("PRAGHD", "OSAGHD"),
				array("EAAGHGHD")
			   ),
	new Theorem( "proposition", "29C",
				array("PRGBHD", "SSBDGH","BEEGH"),
				array("EAEGBGHD", 
					  "RTBGHGHD"
					 )
				),	
	new Theorem( "lemma", "crossimpliesopposite",
				array("CRABCD", "NCACD"),
				array("OSACDB",
					  "OSADCB",
					  "OSBCDA",
					  "OSBDCA"
				     )
				),
	new Theorem( "lemma", "crisscross",
				array("PRACBD",
					  "NOCRABCD"
					  ),
				array("CRADBC")
				),			
	new Theorem( "proposition", "30A",
				array("PRABEF", 
					  "PRCDEF",
					  "BEGHK",
					  "BEAGB",
					  "BEEHF",
					  "BECKD",
					  "OSAGHF",
					  "OSFHKC"
					 ),
				array("PRABCD")
				),
	new Theorem( "lemma", "30helper",
				array("PRABEF",
					  "BEAGB",
					  "BEEHF",
					  "NOCRAFGH"
					 ),
				array("CRAEGH")
				),
	new Theorem( "proposition", "30",
				array("PRABEF", 
					  "PRCDEF", 
					  "BEGHK",
					  "COABG",
					  "COEFH",
					  "COCDK",
					  "NEAG",
					  "NEEH",
					  "NECK"
					 ),
				array("PRABCD")
				),
	new Theorem( "proposition", "30B",
				array("PRABEF", 
					  "PRCDEF", 
					  "BEGKH",
					  "BEAGB",
					  "BEEHF",
					  "BECKD",
					  "OSAGHF",
					  "OSCKHF"
					 ),
				array("PRABCD")
				),
	new Theorem( "proposition", "32",
				array("TRABC",
					  "BEBCD"
					 ),
				array("ASCABABCACD")
			    ),
	new Theorem( "proposition", "33",
				array("PRABCD",
					  "EEABCD",
					  "BEAMD",
					  "BEBMC"
					 ),
				array("PRACBD",
					  "EEACBD"
					 )
				),
	new Theorem( "lemma", "diagonalsmeet",
				array("PGABCD"),
				array("BEAXC", "BEBXD"),
				"X"
				),
	new Theorem( "lemma", "TCreflexive",
				array("TRABC"),
				array("TCABCABC")
			   ),
	new Theorem( "lemma", "EFreflexive",
	            array("BEapc", "BEbpd","NCabc"),
	            array("EFabcdabcd")
	           ),
	new Theorem( "lemma", "parallelPasch",
				array("PGABCD", "BEADE"),
				array("BEBXE", "BECXD"),
				"X"
			   ),
	new Theorem( "lemma", "parallelbetween",
				array("BEHBK",
					  "PRMBHL",
					  "COLMK"
					 ),
				array("BELMK")
				),
	new Theorem( "proposition", "34",
				array("PGACDB"),
				array("EEABCD",
					  "EEACBD",
					  "EACABBDC",
					  "EAABDDCA",
					  "TCCABBDC"
					 )
				),
	new Theorem( "lemma", "diagonalsbisect",
				array("PGABCD"),
				array("MIAXC", "MIBXD"),
				"X"
				),
	new Theorem( "lemma", "trapezoiddiagonals",
	           array("PGABCD", "BEAED"),
	           array("BEBXD","BECXE"),
	           "X"
	           ),
	new Theorem("lemma", "ETreflexive",
			array("TRABC"),
			array("ETABCABC")
			),
	new Theorem( "lemma", "PGflip",
				array("PGABCD"),
				array("PGBADC")
			   ),
	new Theorem( "lemma", "PGsymmetric",
				array("PGABCD"),
				array("PGCDAB")
				),
	new Theorem( "lemma", "PGrotate",
				array("PGABCD"),
				array("PGBCDA")
				),
	new Theorem( "proposition", "33B",
				array("PRABCD",
					  "EEABCD",
					  "SSACBD"
					 ),
				array("PRACBD",
					  "EEACBD"
					 )
				),
	new Theorem( "lemma", "Playfairhelper",
				array("PRABCD", 
					  "PRABCE",
					  "CRADBC",
					  "CRAEBC"
					 ),
				array("COCDE")
			),
	new Theorem( "lemma", "Playfairhelper2",
				array("PRABCD", "PRABCE","CRADBC"),
				array("COCDE")
				),	
	new Theorem( "lemma", "Playfair",
				array("PRABCD", "PRABCE"),
				array("COCDE")
				),
	new Theorem( "lemma", "triangletoparallelogram",
				array("PRDCEF", "COEFA"),
				array("PGAXCD",
					  "COEFX"
					 ),
				"X"
				),
	new Theorem( "lemma", "35helper",
				array("PGABCD",
					  "PGEBCF",
					  "BEADF",
					  "COAEF"
					 ),
				array("BEAEF")
			   ),
	new Theorem( "proposition", "35A",
				array("PGABCD",
					  "PGEBCF",
					  "BEADF",
					  "COAEF"
					 ),
				array("EFABCDEBCF")
				),
	new Theorem( "proposition", "35",
				array("PGABCD",
					  "PGEBCF",
					  "COADE",
					  "COADF"
					 ),
				array("EFABCDEBCF")
				),
	new Theorem( "proposition", "36A",
				array("PGABCD",
					  "PGEFGH",
					  "COADE",
					  "COADH",
					  "COBCF",
					  "COBCG",
					  "EEBCFG",
					  "BEBMH",
					  "BECME"
					 ),
				array("EFABCDEFGH")
			   ),
	
	new Theorem( "proposition", "36",
				array("PGABCD",
					  "PGEFGH",
					  "COADE",
					  "COADH",
					  "COBCF",
					  "COBCG",
					  "EEBCFG"
					 ),
				array("EFABCDEFGH")
			   ),
	new Theorem("proposition", "37",
				array("PRADBC"),
				array("ETABCDBC")
				),
	new Theorem("proposition", "38",
				array("PRPQBC",
					  "COPQA",
					  "COPQD",
					  "EEBCEF",
					  "COBCE",
					  "COBCF"
					  ),
				array("ETABCDEF")
				),
	new Theorem("proposition", "39A",
				array( "TRABC",
					   "ETABCDBC",
					   "BEAMC",
					   "RABDM"
					  ),
				array( "PRADBC")
				),
	new Theorem("proposition", "39",
				array( "TRABC",
					   "TRDBC",
					   "SSADBC",
					   "ETABCDBC",
					   "NEAD"
					 ),
				array("PRADBC")
				),
	new Theorem( "proposition", "40",
				array("EEBCHE",
					  "ETABCDHE",
					  "TRABC",
					  "TRDHE",
					  "COBCH",
					  "COBCE",
					  "SSADBC",
					  "NEAD"
					 ),
				array("PRADBC")
				),
	new Theorem( "proposition", "41",
				array("PGABCD",
					  "COADE"
					 ),
				array("ETABCEBC")
				),
	new Theorem( "proposition", "42",
				array("TRABC",
					  "NCJDK",
					  "MIBEC"
					 ),
				array("PGXECZ",
					  "EFABECXECZ",
					  "EACEXJDK",
					  "COXZA"
					 ),
				"XZ"
				),
	new Theorem( "proposition", "42B",
				array("TRabc",
					  "MIbec",
					  "NCJDK",
					  "MIBEC",
					  "EEECec",
					  "NCREC"
					 ),
				array("PGXECZ",
					  "EFabecXECZ",
					  "EACEXJDK",
					  "SSRXEC"
					 ),
				"XZ"
				), 
	new Theorem( "proposition", "43",
				array("PGABCD",
					  "BEAHD",
					  "BEAEB",
					  "BEDFC",
					  "BEBGC",
					  "BEAKC",
					  "PGEAHK",
					  "PGGKFC"
					 ),
				array("EFKGBEDFKH")
				),
	new Theorem( "proposition", "43B",
				array("PGABCD",
					  "BEAHD",
					  "BEAEB",
					  "BEDFC",
					  "BEBGC",
					  "PGEAHK",
					  "PGGKFC"
					 ),
				array("PGEKGB")
				),
	// 44A starts with a parallelogram, not a triangle like 44
	new Theorem( "proposition", "44A",
				array("PGBEFG",
					  "EAEBGJDN",
					  "BEABE"
					 ),
				array("PGABXY",
					  "EAABXJDN",
					  "EFBEFGYXBA",
					  "BEGBX"
					  ),
				"XY"
				),	
	new Theorem( "proposition", "44",
				array("TRabc",   // given triangle
					  "NCJDN",   // given angle
					  "NCABR"
					 ),
				array("PGABXY",
				      "EAABXJDN",
				      "EFabZcABXY",
				      "MIbZc",
					  "OSXABR"
				  	 ),
				"XYZ"
				),
	new Theorem( "proposition", "45",
				array("NCJEN",
					  "NCABD",
					  "NCCBD",
					  "BEAOC",
					  "BEBOD",
					  "NERK",
					  "NCKRS"
					 ),
				array("PGXKZU",
					  "EAXKZJEN",
					  "EFXKZUABCD",
					  "RAKRZ",
					  "SSXSKZ"
					 ),
				"XZU"
				),
	new Theorem( "proposition", "46", 
	 // existence of square on given side of AB
	 // In addition, this square is a parallelogram
				array("NEAB","NCABR"),
				array("SQABXY",
					  "OSYABR",
					  "PGABXY" 
				     ),
				"XY"
				),
	new Theorem( "lemma", "righttogether", 
				array("RRGAB",
					  "RRBAC",
					  "OSGBAC"
					 ),
				array("RTGABBAC",
					  "BEGAC"
					 )
				),
	new Theorem( "lemma", "twoperpsparallel",
				array("RRABC",
					  "RRBCD",
					  "SSADBC"
					 ),
				array("PRABCD")
				),
	new Theorem( "lemma", "squareparallelogram",
				array("SQABCD"),
				array("PGABCD")
				),
	new Theorem( "lemma", "squareunique",
				array("SQABCD",
					  "SQABCE"
					 ),
				array("EQED")
				),
	new Theorem( "lemma", "altitudeofrighttriangle",
				array("RRBAC",
					  "RRAMp",
					  "COBCp",
					  "COBCM"
					 ),
				array("BEBMC")
				),
	new Theorem( "lemma", "squareflip",
				array("SQABCD"),
				array("SQBADC")
				),
	new Theorem( "proposition", "47A",
				array("TRABC",
					  "RRBAC",
					  "SQBCED", 
					  "OSDCBA"
					 ),
				array("PGBXYD",
					  "BEBXC",
					  "PGXCEY",
					  "BEDYE",
					  "BEYXA",
					  "RRDYA"
					  ),
				"XY"
				),
	new Theorem( "lemma", "angleaddition",
				array("ASABCDEFPQR",
					  "EAABCabc",
					  "EADEFdef",
					  "ASabcdefpqr"
					 ),
				array("EAPQRpqr")
				),
	new Theorem( "proposition", "47B",
				array("TRABC",
					  "RRBAC",
					  "SQABFG", 
					  "OSGBAC",
					  "SQBCED", 
					  "OSDCBA"
					 ),
				array("PGBXYD",
					  "BEBXC",
					  "PGXCEY",
					  "BEDYE",
					  "BEYXA",
					  "RRDYA",
					  "EFABFGBXYD"
					  ),
				"XY"
				),
	new Theorem( "proposition", "47",
				array("TRABC",
					  "RRBAC",
					  "SQABFG", "OSGBAC",
					  "SQACKH", "OSHCAB",
					  "SQBCED", "OSDCBA"
					 ),
				array("PGBXYD",
					  "BEBXC",
					  "PGXCEY",
					  "BEDYE",
					  "EFABFGBXYD",
					  "EFACKHXCEY"
					  ),
				"XY"
				),
//  Above we proved squareparallelogram; but now 
//  we need more. 
	new Theorem( "lemma", "rectangleparallelogram",
				array("REABCD"),
				array("PGABCD")
				),
	new Theorem( "lemma", "supplementofright",
				array("SUABCDF",
					  "RRABC"
					 ),
				array("RRFBD","RRDBF")
				),
	new Theorem( "lemma", "PGrectangle",
	// parallelogram with one right angle is a rectangle
				array("PGACDB",
					  "RRBAC"
					 ),
				array("REACDB")
				),
	new Theorem( "lemma", "squarerectangle",
				array("SQABCD"),
				array("REABCD")
				),
	new Theorem( "lemma", "rectanglereverse",
				array("REABCD"),
				array("REDCBA")
			   ),
	new Theorem( "lemma", "rectanglerotate",
				array("REABCD"),
				array("REBCDA")
			   ),
	new Theorem( "lemma", "squaresequal",
				array("EEABab",
					  "SQABCD",
					  "SQabcd"
					 ),
				array("EFABCDabcd")
			   ),
	new Theorem( "lemma", "paste5",
	   // paste two quadrilaterals getting a quadrilateral.
	   // the second one is assumed to be a rectangle so we 
	   // can prove this from paste2
			array("EFBMLDbmld",
				  "EFMCELmcel",
				  "BEBMC", "BEbmc",
				  "BEELD", "BEeld",
				  "REMCEL", "REmcel"
				 ),
			array("EFBCEDbced")
			),	
//  Prop 48 requires the notion of two squares 
//  together equalling one square.  It also requires
//  Prop 48A below (which Euclid takes for granted).
//  Prop48A  is the only theorem that depends on deZolt2.  


	new Theorem( "proposition", "48A",
				array("SQABCD",
					  "SQabcd",
					  "EFABCDabcd"
					 ),
				array("EEABab")
				),
	new Theorem( "proposition", "48",
				array("TRABC",
					  "SQABFG", 
					  "SQACKH", 
					  "SQBCED", 
					  "BEBMC", 
					  "BEELD",
					  "EFABFGBMLD",
					  "EFACKHMCEL",
					  "REMCEL"
					 ),
				array("RRBAC")
				),
	new Theorem( "lemma", "collinearmidpoint",  // proved 6.27.18 
				array("COABM",
					  "NEAB",
					  "EEAMMB"
					 ),
				array("BEAMB", "MIAMB")
			   ),  
	new Theorem("lemma", "fivehelper",    // proved 6.20.26
				array("ASABCDEFPQR",     // that is ABC,DEF together are equal to PQR,
						"RTABCUVW"        //  ABC, UVW  are together equal to two right angles, 
						),
				array("AODEFUVW")       //  DEF < UVW 
				),   
	new Theorem("lemma","anglesumcongruence",    // proved 6.22.26 
				array("ASABCDEFPQR", 
						"EAPQRUVW" 
						), 
				array("ASABCDEFUVW") 
				),
	new Theorem("lemma", "anglesumswap",
				array("ASABCDEFUVW"), 
				array("ASDEFABCUVW")
				),		
	new Theorem("lemma", "PGhelper",  
				array("PREQSF",  
						"OSSEFQ"  
						), 
				array("BEEYF", 
						"BESYQ"
						), 
				"Y"
				),	 
	new Theorem("lemma","equaltriangle", 
				array("TRABC", 
						"TRADC", 
						"SSBDAC",  
						"EABACDAC",
						"EEABAD",
				),
				array("EQBD") 
				),
	new Theorem("lemma", "EuclidOriginal" ,  // Euclid's version of the parallel postulate
				array("BEAEB",
				      "BECFD",  
				      "OSAEFD",
				      "ASBEFEFDUVW"   // interior angles on the right side of EF make less than two right angles 
						                // making less than two right angles means the two angles together make some angle.
						), 
				array("BEAEX", 
					  "BECFX"         
				     ), 
				"X"
				),
	new Theorem( "proposition", "III.01",
	/* D is the midpoint of chord AB, CE perpendicular to AB at D, 
	   C and E on the circle, then the midpoint F of CE is the center.  */
				array("CIJGPQ",
					  "ONAJ",
					  "ONBJ",
					  "NEAB",
					  "MIADB",
					  "ONCJ",
					  "ONEJ",
					  "PACEABD",
					  "SSCGAB",
					  "MICFE",
					  "BECDE"
					 ),
				array("EQGF")
			   ),
    /* Two circles with the same name have the same center */
  new Theorem( "lemma","centerunique",  
               array("CIJFPQ", "CIJGpq"),
               array("EQFG")
             ),

	new Theorem( "lemma", "radiiequal",
				array("CIJCPQ",
					  "ONAJ",
					  "ONBJ"
					  ),
				array("EECACB")
				),
/* Two circles with the same name have the same radius */
	new Theorem( "lemma","radiusunique",  
				array("NEPQ",
					  "NEpq",
					  "CIJFPQ", 
					  "CIJGpq"
						),
				array("EEPQpq")
				),
	new Theorem( "lemma", "oncircle",
				array("CIJEPQ", 
				      "NEPQ",
					  "ONAJ"
						 ),
				array("EEEAPQ")
	      ),
	new Theorem( "lemma", "insidecircle",
				array("CIJEPQ", 
				      "NEPQ",
					  "ICAJ"
						 ),
				array("OREQAE|LTEAPQ")
	      ),
	new Theorem( "lemma", "outsidecircle",
				array("CIJEPQ", 
				      "NEPQ",
					  "OCAJ"
						 ),
				array("LTPQEA")
	      ),
	new Theorem("lemma","centernotoncircle",
				array("CIJEPQ","ONAJ"),
				array("NEEA")
				),
	new Theorem( "proposition", "III.02",
	//  points on chord AB lie inside the circle 
			   array("CIJDPQ",
					 "ONAJ", 
					 "ONBJ",
					 "BEAEB"
					),
			   array("ICEJ")
			  ),
	new Theorem( "proposition", "III.03A",
	//  radius that bisects chord AB is perpendicular to the chord
			   array("CIJEEA",
					 "ONCJ",
					 "ONDJ",
					 "ONAJ",
					 "ONBJ",
					 "MIAFB",
					 "NCABE"
					),
				array("RRAFE",
					  "RRBFE"
					 )
			    ),
	new Theorem( "proposition", "III.03B",
	// radius perpendicular to a chord  bisects the chord 
			   array("CIJEEA",
					 "ONCJ",
					 "ONDJ",
					 "ONAJ",
					 "ONBJ",
					 "RRAFE",
					 "RRBFE",
					 "BEAFB",
					 "NCABE"
					),
				array("MIAFB")
			    ),
	new Theorem("lemma", "circleNC",
	// three distinct points on a circle are not collinear 
				array(  "EEFAFB",
						"EEFAFC",
						"NEAF",
						"NEAB",
						"NEAC",
						"NEBC"
						),
				array("NCABC")
			),
	new Theorem("lemma","III.04helper",
				array("EEFAFB",
						"EEFAFC",
						"EEFAFD",
						"BEAEC",
						"BEBED",
						"NCABC"
						),
						array("ORCRACFB|CRACFD|BEAFC")
				),
	new Theorem("lemma","cutsymmetric",
				array("CUABCDE"),
				array("CUCDABE")
				),
	new Theorem("lemma","insidenoton",
				array("CIJFAB",
				      "ICPJ",
					  "NEAB"
						 ),
				array("NOONPJ")
			),
	new Theorem( "lemma", "nothreeoncircle",
	//  no more than two points of AB lie on a circle
				array("CIJFPQ",
						"NEPQ",
						"ONAJ", 
						"ONBJ",
						"ONCJ",
						"COCAB",
						"NEAB",
						"NEAC"
					),
			   array("EQBC")
			  ),
	new Theorem("lemma", "III.02converse",
				array("CIJERS",
						"NERS",
						"ONAJ",
						"ONBJ",
						"NEAB",
						"COABC",
						"ICCJ"
						 ),
				array("BEACB")
				 ), 
	 new Theorem( "lemma", "outsidesecant",
	 			array("CIJEPQ",
						"NEPQ", 
						"ONBJ",
						"ONCJ",
						"NEBC",
						"COBCX",
						"NEXB",
						"NEXC",
						"NOBEBXC"
						),
				array("LTPQEX")
				), 
	new Theorem("lemma", "insideoutside",
				array("CIJEPQ",       // J is circle, center E, radius PQ
						"NEPQ",          // radius nonzero
						"ICAJ",          // A inside J
						"ONBJ",          // B on circle J
						"BEABC"          // C past B from A
						),
				array("OCCJ")          // C is outside J
				),
	new Theorem( "proposition", "III.04",
		// if chords AB and CD bisect each other at E, then E is the center
			   array("CIJFFA",
					 "EEFAFB",
					 "EEFAFC",
					 "EEFAFD",
					 "NCACF",
					 "NCBDF",
					 "CUABCDE",
					 "MIAEC",
					 "MIBED"
					),
				array("EQEF")
				),
	new Theorem("lemma", "angleorderNC",
				array("AOABCDEF"),
				array("NCABC","NCDEF")
			),
	new Theorem( "proposition", "III.05",
	//  Two circles that cut each other do not have the same center; or,
	//  Two circles with the same center with a point in common have all radii equal
	//  But Euclid assumes also BEEFG, "Let EFG be drawn through at random."
	//  If we don't assume it as a hypothesis, then the two circles might be in different 
	//  planes without any contradiction.  We could interpret "two circles cut one another"
	//  to mean there's a point inside one and outside the other.   That justifies this formulation.
				array("CIJEEC",
					  "CIKHHC",
					  "ONCJ",
						"ONCK",
						"ONFJ",
						"ONGK",
						"BEEFG",
					 ),
				array("NEEH")
			  ),
	//  III.05 as stated here subsumes both III.05 and III.06 in Euclid, so we don't prove III.06 separately.
	new Theorem("proposition", "III.07A",
				array("CIJEED",  //  E is the center and EB the radius of J
						"BEAED",  // AD is a diameter
						"BEEFD",   // as shown in Euclid's diagram
						"ONAJ",
						"ONBJ",
						"ONCJ",
						"AODECDEB"   // see commentary in Heath; this is the version corresponding to Euclid's proof
				     ),
				array("LTFDFC",
						"LTFCFB",
						"LTFBFA",
						"LTFDFA"
					)
		    ),
	new Theorem("proposition", "III.07C",  
				array("CIJEED",  //  E is the center and EB the radius of J
						"BEAED",  // AD is a diameter
						"BEEFD",   
						"ONAJ",
						"ONBJ",
						"NCADB"
				     ),
				array(  
						"LTFBFA",
						"LTFDFB",
						"LTFDFA"
					)
		    ),
new Theorem("lemma", "cyclicquad",
				array("EEFAFB",
						"EEFAFC",
						"EEFAFD",
						"NEAB",
						"NEAC",
						"NEAD",
						"NEBC",
						"NEBD",
						"NECD",
						"NEFA",
						"NOCRADBC",
						"NOCRABCD"
						),
				array("CRACBD"
						)
			),
new Theorem("proposition", "III.07Bhelper",
				array("CIJEED",  //  E is the center and EB the radius of J
						"BEAED",  // AD is a diameter
						"BEAEF",   // as shown in Euclid's diagram
					 	"ONGJ",
						"ONHJ",
						"ONAJ",
						"NEGH",
						"EEFGFH",
						"NCAHD"
						),
				array("NOCRAGHD"
					 )
		    ),
new Theorem("lemma", "collinearchord",
				array("CIJEPQ",
						"NEPQ",
						"ONAJ", 
						"ONDJ",
						"ONGJ",
						"NEAD",
						"COADG",
					),
				array("OREQGA|EQGD")
			),
new Theorem("proposition", "III.07Bhelper2",
				array("CIJEED",
					  "NEEG",
					  "ONGJ",
					  "ONHJ",
					  "NEGH",
					  "BEAED",
					  "ONAJ",
					  "BEAEF",
					  "EEFGFH"
					),
					array("NOANCOADG+COADH")
			),
new Theorem("proposition", "III.07B-NC",
				array("CIJEED",
				      "ONKJ",
					  "ONHJ",
					  "NEKH",
					  "BEAED",
					  "ONAJ",
					  "ONDJ",
					  "BEEFD",
					  "EEFKFH",
					  "NCADH"
					),
				array("NCADK")
			),
new Theorem("lemma", "crosssymmetric",
				array("CRABCD"),
				array("CRCDAB")
			),
new Theorem("lemma", "crossflip",
				array("CRABCD"),
				array("CRBACD", "CRABDC", "CRBADC")
			),
new Theorem("proposition","III.07Bhelper3",
			array("CIJEED",
				  "NEEG",
				  "ONGJ",
				  "ONHJ",
				  "ONKJ",
				  "NEGH",
				  "BEAED",
				  "ONAJ",
				  "ONDJ",
				  "BEEFD",
				  "EEFGFH",
				  "EEFKFH",
				  "NCADG",
				  "NCADH",
				  "NEKH"
				),
				array("EQKG")
			),
new Theorem("proposition", "III.07B",
			array("CIJEED",  //  E is the center and EG the radius of J
					"BEAED",
					"BEEFD",
					"NEEG",
					"NEFE",  // F is not the center
					"ONGJ",
					"ONKJ",
					"ONHJ",
					"ONAJ",
					"ONDJ",
					"NEGH",
					"EEFGFH",
					"EEFGFK"
			     ),
			array("OREQKG|EQKH"
				 )
		    ),  
new Theorem("proposition", "III.08A",
			array("CIJMMA",
					"BEDMA",
					"OCDJ",
					"EEMAME",
					"EEMAMF",
					"CRFMED",
					"NCDME",
					"NCDMF"
					),
			array("ANLTEDAD+LTFDED" 
					),
			), 
new Theorem("lemma", "togethersplit" ,
			array("TGABBCAC",
				   "EEABAD",
				   "BEADC", 
				   "NEAB"     // should be derivable, but seems hard to do it 
					), 
			array("LTDCBC"
					) 
			),   
new Theorem( "lemma", "ttcancel",
			array("TTABCDEFGH",
					"EECDGH"
					),
			array("LTEFAB")
			),
new Theorem("proposition", "III.08B",
			array("CIJMMA",
					"OCDJ",
					"BEDGM",
					"EEMAMG",
					"EEMAMK",
					"EEMAML",
					"BEDKQ",
					"BEMQL",
					"NCDQL"
					),
			array("ANLTDGDK+LTDKDL"
					)
			),	  
new Theorem("proposition", "III.08C",
			array("CIJMMA",       // M center, MA radius
					"OCDJ",          // D outside
					"BEDGM",         // G between D and M (G is near pole)
					"EEMAMG",        // G on circle
					"EEMAMK",        // K on circle
					"EEMAML",        // L on circle
					"EEMAMk",        // lowercase k on circle
					"EEMAMl",        // lowercase l on circle
					"BEDKk",         // K is entry point of secant Dk
					"BEDLl",         // L is entry point of secant Dl
					"EEDKDL",        // DK ≅ DL 
					"BEWUK",         // W,U,V witness K and L on same side of DM
					"BEWVL",
					"BEDUV", 
					"BEDVM",        // L is on the convex circumference 
					"NCDMK", 
					"NCDML",
					"BEMVG",        // V is inside the circle
					),
			array("EQKL")          // therefore K = L
			),  
new Theorem("lemma", "halfangleisacute", 
			array("ASABCDEFPQR",   
					"EAABCDEF"       // So ABC is half of PQR
					), 
			array("ACABC")	        //  Then ABC is acute, i.e., less than a right angle
			 ),  
new Theorem("lemma", "anglesofrightareacute", 
			array("RRABC"), 
			array("ACBAC",  
					"ACBCA",  
					"ACCAB",  
					"ACACB" 
				  )
			), 
new Theorem("lemma","acuteplusright", 
			array("ACABC",  
					"RRDEF" 
					), 
			array("ASABCDEFXYZ"),
			"XYZ"
			),
new Theorem("lemma","acuteplusacute",
			array("ACABC",  
				  "ACDEF" 
			  	), 
			array("ASABCDEFXYZ"),
			"XYZ"
			),
new Theorem("proposition", "IV.5",
			array("NCABC"),
			array("CIJXXA",
				  "ONBJ",
				  "ONCJ",
				  "EEXAXB",
				  "EEXAXC"
			 	),
			"XJ"
			),
new Theorem("lemma","Pasch",  
			array("TRABC",  
					"BEBEC", 
					"NCBCF" 
					),
			array("COEFX", "ORBEAXB|BEAXC|EQXA"
					), 
			"X"
			), 
new Theorem("lemma","sidedichotomy",
			array("NEAB", 
					"NEPQ",
					"NCABP", 
					"NCABQ" 
					), 
				array( "ORSSPQAB|OSPABQ"
				)
			), 
new Theorem( "lemma", "erectedperpendicularunique3",
			array("RRABC",
					"RRABE"
				 ),
			array("COBCE")
			),
new Theorem("proposition", "III.09", 
			array("CIJMMA",       // M center, MA radius)
					"ONAJ", 
					"ONBJ",  
					"ONCJ",
					"NEAB", 
					"NEAC", 
					"NEBC",
					"EEDADB", 
					"EEDADC", 
					),
			array("EQDM")
			),


				
/*

new Theorem( "lemma", "rectanglediagonalsequal",
             array("REABCD"),
             array("EEACBD")
           ), 
      
new Theorem( "lemma", "congruentrectanglesareequal",
			array("RCABCDabcd"),
			array("ERABCDabcd")
		   ),
new Theorem( "lemma", "baserectangleexists",
			array("TRABC"),
			array("REABXY",
				  "COXYC"
				 ),
			"XY"
			),
	new Theorem( "lemma", "tworectangles",
				array("REABCD",
					  "REPQEF",
					  "EEPQAB",
					  "EFABCDPQEF"
					 ),
				array("EEADPF")
				),
	new Theorem( "lemma", "TE37",
				array("BRABCDE"),
				array("TEABCDBC")
				),
	new Theorem( "lemma", "congruent_equal",
				array("TCABCabc"),
				array("TEABCabc")
				),
*/					
  
/*
	new Theorem( "lemma", "segment-circle",
			array("BEAPB",  // P lies on AB
			      "BECPU", "EECURS",  // P lies inside the circle of center C and radius RS
				  "BEAQB",  // Q lies on AB
				  "BECVQ", "EECVRS"),  // Q lies outside the circle of center C and radius RS
			array("BEAXB", "EECXRS"),  // X lies on AB and also on the circle 
			"X"
			) 
	*/
);
//_____________________________________
function result_number($x)
// get the index in the $results array of the item with label $x.
{ global $results;
  $N = count($results);
  for($i = 0; $i < $N; $i++)
     { if($results[$i]->label == $x)
	      return $i;
	 }
  echo "Error in result_number with $x\n";
  die();
}
//____________________________________________
function precedes($first, $second)
// return true if $first is the label (e.g.  defn:ray)
// of a result that comes before $second,  which is the 
// label of a lemma, or is "Prop##"  for some proposition number.
// Any definition comes before any lemma or proposition.
{  $x = explode(":", $first);
   if(count($x) != 2)
      { echo "Error in precedes with $first\n"; 
	    die();
	  }
   $label = $x[1];
   $kind = $x[0];
   if($kind == "axiom" || $kind == "postulate" || $kind == "defn" || $kind == "cn")
       return true;
   // echo $label . " " . $second . "\n";
   $i = result_number($label);
   if(strstr($second, "Prop"))
       $second = substr($second,4);  // e.g. 12
   $j = result_number($second);
   return $i < $j;
}
?>

