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
	  "BE" => 3,  //  ABC are in order, that is, B is between A and C
	  "BR" => 5,  //  ABCD is a base rectangle of triangle BCE
	  "FR" => 8,  //  ABCD is a figure rectangle of PQRS
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
	  "CU" => 5,  // AB and CD cut each other in E
	  "SU" => 5,  // ABC and DBF are supplementary angles
	  "TC" => 6,  // ABC and abc are congruent triangles
	  "LT" => 4,  //  AB < CD,  segment ordering
	  "ME" => 4,  //  line AB meets line CD 
	  "CR" => 4,  // segment AB meets segment CD
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
      "DA" => 6,    // ABC is double PQR 
	  "PG" => 4,    // ABCD is a parallelogram
	  "SQ" => 4,    // ABCD is a square
	  "EF" => 8,    // ABCD and abcd are equal quadrilaterals 
	  "ET" => 6,    // ABC and DEF are equal triangles
	  "RE" => 4,    // ABCD is a rectangle
	  "RC" => 8,    // ABCD and abcd are congruent rectangles
	  "ER" => 8,    // ABCD and abcd are equal rectangles (defined)
	  "EC" => 2,    // circles J and K are equal
	  "CA" => 4,    // segment AB cuts circle J at E
	  "LC" => 3,    // line AB (produced) cuts circle J 
	  "TA" => 4,    // line AB touches (is tangent to ) circle J at C	  
	  "CT" => 3,    // circles C and K touch at A
	  "CC" => 3,    // circles C cuts circle K at A
	  "CQ" => 4,    // ABCD is a convex quadrilateral
	  "QL" => 4,     //  ABCD is a quadrilateral, convex or really a triangle 
	  "QT" => 4,    // ABCD is a quadrilateral that is really a triangle 
	  "TF" => 7,    // abc is a triangle equal to quadrilateral PQRS 
	  "PN" => 8     // proportional 
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
	// following says a circle is given by its center and radius,
	// and the radius is positive.
	// Implicit in Euclid's use of "the centre of the circle CDB". 
	new Axiom("axiom", "circle-center-radius",
			array("CIJABC", 
				  "ONPJ"  // P is on SOME circle named J  
				 ),
			array("EEAPBC")  
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
					"P is inside (some) circle J of center U and radius VW",
					array( "CIJUVW",  // J is the circle of center U, radius VW
					       "OREQPU|ANBEUYX+EEUXVW+EEUPUY"
					     ),
					"XYUVW"
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
	                array("CIJUXY", "EEUBXY"),
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
/*   Points $P$ and $Q$  lie on opposite sides of line $AB$
	if there is a point $E$ between $P$ and $Q$ and collinear with $AB$,
	and $P$ is not collinear with $AB$.  */
    new Definition( "oppositeside", "OSPABQ",
					"P and Q are on opposite sides of AB",
					array("BEPXQ", "COABX", "NCABP"),
					"X"
				  ),
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
	new Definition( "doubleangle", "DAABCPQR",
					"angle ABC is double angle PQR",
					array("EAABXXBC",
						  "BEAXC",
						  "EAABXPQR"
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
	new Definition( "convexquad", "CQABCD",
					"ABCD is a convex quadrilateral",
					array("BEAXC",
						  "BEBXD",
						  "NEAB",
						  "NEAD",
						  "NEBC"
						 ),
					"X"
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
						  "CODAE"
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
	new Definition( "equaltriangles", "ETABCabc",
					"ABC and abc are equal triangles",
	//  rectangles containing the triangles are equal
		  			array("REABXY", "REabxy",
						  "COXYC", "COxyc",
						  "ERABXYabxy"
						 ),
					"XYxy"
					),
	new Definition( "equalfigures", "EFABCDabcd",
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
					"circle J cuts circle K  at B",
					array("ONJB",
					      "ONKB",
					      "ONKX",
					      "ONKY",
					      "ICJX",
					      "OCJY"
					     ),
					"XY"
				   ),
	new Definition( "proportion", "PNABCDabcd",
					"AB:CD = ab:cd",
					array("RRXZY",
						  "RAZXx",
						  "RAZYy",
						  "EEABZX",
						  "EEACZY",
						  "EEAcZy",
						  "EEAbZx",
						  "ORPRXYxyANEQXxEQYy"
						 ),
					"XYZxy"
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
					  "NCACD"
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
	new Theorem( "lemma", "lessthanadditive2",
				array("LTABab",
					  "BEABC",
					  "LTBCbc",
					  "BEabc"
					 ),
				array("LTACac"
				)
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
	new Theorem( "lemma", "crossbar3",   // 3.17 in SST,  needed for Euclid III.20 
				array("NCACa",
				      "BEABC",
				      "BEabC",
				      "BEApa"
				     ),
				array("BEpXC",
				      "BEBXb"
				     ),
				"X"
			   ), 
	new Theorem( "lemma", "crossbar4",   
				array("NCACa",
				      "BEABC",
				      "BEabC",
				      "BEBqb"
				     ),
				array("BEAXa",
				      "BECqX"
				     ),
				"X"
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
	new Theorem( "lemma", "rightcollinear",
				array("RRABC",
					  "RRCBD",
					  "OSABCD"
					 ),
				array("BEABD")
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
	new Theorem( "lemma", "acutebetween",
				array("RRDFB",
					  "COBFC",
					  "AODBCBFD",
					  "NCDBC"
					 ),
				array("RABFC")
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
	new Theorem( "lemma", "halfanglelessthanright",
				array("BEAHC",
					  "NCABC",
					  "EAABHHBC",
					  "RRPSR"
					 ),
				array("AOABHPSR"
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
	new Theorem( "lemma", "collinearmidpoint",
				array("COABM",
					  "NEAB",
					  "EEAMMB"
					 ),
				array("BEAMB", "MIAMB")
			   ),
	new Theorem( "lemma", "35helper",
				array("PGABCD",
					  "PGEBCF",
					  "BEADF",
					  "COAEF"
					 ),
				array("BEAEF")
			   ),
			
/* This begins the formalization of "On the Notion of Equal Figures in Euclid"  */  

    new Theorem( "lemma", "rightangleSSA",
				array("TRABC", 
				      "TRabc",
					  "RRABC",
					  "RRabc",
					  "EEACac",
					  "EEBCbc"
					 ),
				array( "EACABcab"
				      //"EEABab",   postponed the other two conclusions
					  //"EAACBacb"
					 )
				),
    new Theorem( "lemma", "incenter",
				array("TRABC",
				      "EAABHHBC",
				      "EABCJJCA",
					  "BEAHC",     // ensuring that the bisector is in the plane of the triangle
					  "BEAJB"
				     ),
				array("BECXJ",
				      "BEBXH",
				      "EABAXXAC",
					  "BEAXY",
					  "BEBYC"
				     ),
				"XY"
				),
	new Theorem( "lemma", "sumofequalsisdouble",
				// if angles 1 and 2 are equal then their sum is the double of angle 1
				array("EAABCabc",
					  "ASABCabcPQR"
					 ),
				array("DAPQRABC"
			         )
			    ),
	new Theorem( "lemma", "doublesum",
				 array("ASABCABCPQR"),
				 array("DAPQRABC")
			   ),
	new Theorem( "lemma", "doublesum2",
	 			 array("DAPQRABC"),
				 array("ASABCABCPQR")
			   ),
	new Theorem( "lemma", "angleaddition",
				 array("ASABCDEFPQR",
					   "EAABCabc",
					   "EADEFdef",
					   "ASabcdefpqr"
					   ),
				 array("EAPQRpqr")
			   ),
	new Theorem( "lemma", "angleaddition2",
				 array("ASABCDEFPQR",
				       "EAPQRpqr"
				      ),
				 array("ASABCDEFpqr")
			   ),
	new Theorem( "lemma", "anglesumrespectscongruence",
				 array("ASABCDEFPQR",
					   "EAABCabc",
					   "EADEFdef"
					  ),
				 array("ASabcdefPQR")
			   ),					
// we need algebraic properties of angle addition to prove sumsofdoubles,  which Euclid uses in III.20 without justification.
    new Theorem( "lemma", "anglesumNC",
				 array("ASABCDEFPQR"),
				 array("NCPQR")
			   ),
    new Theorem( "lemma", "anglesumcommutative",
				 array("ASABCDEFabe"),
				 array("ASDEFABCeba",
				       "ASDEFABCabe"
				      )
			   ),
	new Theorem( "lemma", "anglesumassociative",
				 array("ASABCDEFPQR",
					   "ASPQRTUVSYZ",
					   "ASDEFTUVGHJ"
					  ),
				 array("ASABCGHJSYZ")
			   ),
	new Theorem( "lemma", "anglesumassociative2",
				 array("ASDEFGHJXYZ",     // 2 + 3 = XYZ
					   "ASABCXYZRST",     // 1 + (2+3) = RST
					   "ASABCDEFUVW",      // 1 + 2 = UVW
					   "ASGHJABCLMN"       // 3+1 = LMN 
					  ),              
				 array("ASUVWGHJRST")     // (1+2) + 3 = RST
			   ),	
	new Theorem( "lemma", "anglesumassociative3",
				 array("ASDEFGHJpqt",     // 2 + 3 = XYZ
					   "ASABCpqtabd"     // 1 + (2+3) = RST
					  ),              
				 array("ASABCDEFUVW"),      // 1 + 2 = UVW,     // (1+2) + 3 = RST
				 "UVW"
			   ),	
    new Theorem( "lemma", "anglebisectorunique",    // 2x = 2y implies x = y, for angles
				 array("DAPQRABC",
					   "DAPQRabc"
					  ),
				 array("EAABCabc")
			   ),
	new Theorem( "lemma", "sumsofdoubles",  // 2(x+y) = 2x + 2y  
				 array("DAFEBFAB",
					   "DAFECFAC",
					   "ASBEFFECBEC",
					   "ASBAFFACBAC",
					   "BEAEH",
					   "BEBHC",
					   "BEAEF"
					  ),
				 array("DABECBAC")
				),	
	new Theorem( "lemma", "insideisosceles",   // similar to III.02  but without circles
	            array("TREGC",
	                  "EEEGEC",
	                  "BEGQC"
	                 ),
	            array("LTEQEC")
	           ), 	  
	new Theorem( "lemma", "outsideisosceles",  
	            array("TREBD",
	                  "EEEBED",
	                  "BEPBD"
	                 ),
	            array("LTEDEP")
	           ),
	new Theorem( "proposition", "III.20A",   // Euclid's first case, center in interior 
				array("NEAE",
					  "EEEAEC",
					  "EEEAEB",
					  "NECB",
					  "NEAB",
					  "NCABC",
					  "NCEAB",
					  "NCEAC",
					  "BEAEH",
					  "BEBHC"
					 ),
				array("DABECBAC")
				),
	new Theorem( "lemma", "anglesubtraction",
	            array("ASPQRABCpqr",
	                  "ASPQRabcpqr",
	                 ),
	            array("EAABCabc")
	           ),
	new Theorem( "proposition", "III.20B",   // Euclid's second case, center in exterior
				array("EEEDEC",
					  "EEEDEB",
					  "NCDBC",
					  "NCEDB",
					  "NCEDC",
					  "BEEPC",
					  "BEBPD",
					  "BECQG",
					  "BEEQB",
					  "BEGED"
					 ),
				array("DABECBDC")
				),
	new Theorem( "proposition", "III.20C",  //  We fix Euclid's missing hypothesis with diameter KEM     
				array("EEEDEC",
					  "EEEDEB",
					  "NCDBC",
					  "NCEDB",
					  "NCEDC",
					  "EEEKEM",
					  "BEKEM",       // KEM is a diameter  
					  "BEDUB",       // B and C are on the opposite side of KEM  from D
					  "BEDVC",       // which says angle BDC is contained in the large circumference BC
					  "COKMU",
					  "COKMV",
					  "BEVUE"
					 ),
				array("DABECBDC")
				),
	new Theorem( "proposition", "III.20",  //  the general case; dropping hypothesis BEVUE from III.20C   
				array("EEEDEC",
					  "EEEDEB",
					  "NCDBC",
					  "NCEDB",
					  "NCEDC",
					  "EEEKEM",
					  "BEKEM",       // KEM is a diameter  
					  "BEDUB",       // B and C are on the opposite side of KEM  from D
					  "BEDVC",       // which says angle BDC is contained in the large circumference BC
					  "COKMU",
					  "COKMV"
					 ),
				array("DABECBDC")
				),
	new Theorem( "lemma", "corcoran",
	            array("OREQPA|EQPB",
	                  "OREQPA|EQPC",
	                  "NEAB",
	                  "NEAC",
	                  "NCABC"
	                 ),
	            array("EQPA")
	           ),             
	new Theorem( "proposition", "III.21", // but stated without circles.   Euclid's proof is flawed, see Heaths' commentary.
	            array("NEFA",
					  "EEFAFB",
					  "EEFAFE",
					  "EEFAFD",
					  "NEBD"
					),
				array("EABADBED")
			   ),
	new Theorem( "proposition", "IV.5",  // three noncollinear points determine a circle
				array("NCABC"),
				array("EEXAXB",
					  "EEXAXC",
					  "EEXBXC"
					 ),
				"X"
				), 
	new Theorem( "lemma", "cyclicquadrilateral",
				 array( "BEAOC",
					    "BEBOC",
					    "EAOABODC"
					  ),
				 array( "CIXYYA",  // X is a circle of center Y with radius YA
						"ONAX",
						"ONBX",
						"ONCX",
						"ONDX"
					  ),
				"XY"
				), 	           
/* Now we're going to develop the theory of proportions using PN.  There are ten theorems to prove. */
    new Theorem( "lemma", "PNsymmetric",
				array("PNpqrsPQRS"
		             ),
		        array("PNPQRSpqrs")
	           ),
	new Theorem( "lemma", "PNflip",
				 array("PNpqrsPQRS"
			      ),
			     array("PNrspqRSPQ")
			    ),
	new Theorem( "lemma", "PNtransitive",
				array("PNABCDPQRS",
					  "PNPQRSabcd"
					 ),
			    array("PNABCDabcd")
			   ),
	new Theorem( "lemma", "fourth proportional exists",
				array("NEAB", "NECD", "NEPQ"),
			    array("PNABCDPQXY"
			         ),
			    "XY"
			   ),
	new Theorem( "lemma", "fourth proportional unique",
			    array("PNABCDPQRS", 
			          "PNABCDPQrs"
			         ),
		        array("EERSrs")
		       ),
	new Theorem( "theorem", "interchange",
				array("PNABCDPQRS"),
				array("PNABPQCDRS"
				     )
			   ),
	new Theorem( "lemma", "proportionallegs",
				array("TRABC",
					  "TRabc",
					  "RRABC",
					  "RRabc",
					  "PNBAbaBCbc"
					 ),
				array("EABACbac",
				      "EABCAbca"
				     )
			   ),
	new Theorem( "theorem", "fundamental_theorem_of_proportion",
				array("RAACc",
				      "RAABb",
				      "PRAaBb"
				     ),
				array("PNABAbACAc"
				     )
			   ),
	new Theorem( "lemma", "similar_implies_proportional",
	            array("TRABC",
	                  "TRabc",
	                  "EABACbac",
	                  "EAACBacb"
	                 ),
	            array("PNABACabac")
	           ),
    new Theorem( "lemma", "proportional_hypotenuses_and_legs",
				array("TRABC",
					  "TRabc",
					  "RRABC",
					  "RRabc",
					  "PNABabACac"
					 ),
				array("EACABcab")
				),
	/*  Now we move on to the section on equal rectangles */
	
	new Theorem( "lemma", "ERproportion1",   // half of Lemma 5 in the paper
				array("ERABCDabcd"),
				array("PNABabcdCD",
					  "PNABcdabCD"
					 )
				),
	new Theorem( "lemma", "ERproportion2",  // the converse
				array("PNABabcdCD"),
				array("ERABCDabcd")
				),
	new Theorem( "lemma", "ERreflexive", 
				array("REABCD"),
				array("ERABCDABCD")
			   ),
	new Theorem( "lemma", "ERsymmetric",
				array("ERABCDabcd"),
				array("ERabcdABCD")
				),
	new Theorem( "lemma", "ERtransitive",   // Lemma 6
				array("ERABCDPQRS",
					  "ERPQRSabcd"
					 ),
				array("ERABCDabcd"
					 )
				),
	new Theorem( "lemma", "ERpermutation",  // Lemma 7 
				array("REABCD"),
				array("ERABCDBCDA")
			   ),
	new Theorem( "lemma", "ERpermutation2", 
				array("REABCD"),
				array("ERABCDBCDA",
					  "ERABCDCDAB",
					  "ERABCDDABC"
					 )
				),
	new Theorem( "lemma", "congruentER",  // half of Lemma 8
				array("REABCD", 
				      "REabcd",
					  "EEABab",
					  "EECDcd"
					 ),
				array("ERABCDabcd")
				),
	new Theorem( "lemma", "ERheight", 
				array("ERABCDabcd",
					  "EEABab"
					 ),
				array("EECDcd")
			   ),
	new Theorem( "lemma", "ERwidth",
				array("ERABCDabcd",
					  "EECDcd"
					 ),
				array("EEABab")
				),
	new Theorem( "lemma", "ERunequal",  // Lemma 9
				array("REABCD",
					  "REabcd",
					  "LTabAB",
					  "LTcdCD"
					  ),
				array("NOERABCDabcd")
				),
	new Theorem( "lemma", "rectanglecutoff",  // Lemma 10
				array("ERABCDabcd",
					  "BEAPB",
					  "BEapb",
					  "BECQD",
					  "BEcqd",
					  "ERPBCQpbcq"
					 ),
				array("ERAPQDapqd")
			   ),
	new Theorem( "lemma", "rectanglepaste",  // Lemma 11
				array("ERAPQapqd",
					  "BEAPB",
					  "BEapb",
					  "BECQD",
					  "BEcqd",
					  "ERAPQDpbcq"
					 ),
				array("ERABCDabcd")
				),
	/* Now for the theory of equal triangles */
	
	new Theorem( "lemma", "ETreflexive",  // starting lemma 12 
				array("TRABC"),
				array("ETABCABC")
			   ),
	new Theorem( "lemma", "ETsymmetric",
				array("ETABCabc"),
				array("ETabcABC")
				),
	new Theorem( "lemma", "ETtransitive",
				array("ETABCpqr",
					  "ETpqrabc"
					 ),
				array("ETABCabc")
			   ),
	new Theorem("lemma", "congruentequal",   // one of the equal figures axioms 
			array("TCABCabc"),
			array("ETABCabc")
			),
	new Theorem("proposition", "37",
				array("PRADBC"),
				array("ETABCDBC")
				),
    new Theorem( "lemma", "ETpermutation1",
				array("TRABC"),
				array("ETABCBCA")
			   ),
	new Theorem( "lemma", "ETpermutation",
				array("TRABC"),
				array("ETABCBCA",
					  "ETABCCAB",
					  "ETABCACB",
					  "ETABCBAC",
					  "ETABCCBA"
					)
			   ),		
		// deZolt1 and deZolt2 say that a triangle is not equal to a triangle it contains
	new Theorem("lemma", "deZolt1",  // Lemma 16
			array("BEBED"),
			array("NOETDBCEBC")
			),
	new Theorem("lemma", "deZolt2",   // Lemma 17 
			array("TRABC",
				  "BEBEA",
				  "BEBFC"
				 ),
			array("NOETABCEBF")
			), 
			
      /*  Now we move on to equal quadrilaterals  */
    new Theorem("lemma", "EFreflexive",
			array("QLABCD"),
			array("EFABCDABCD")
		   ), 
    new Theorem("lemma", "EFsymmetric",
			array("EFABCDabcd"),
			array("EFabcdABCD")
			),
	new Theorem("lemma", "EFtransitive",
			array("EFABCDPQRS",
				  "EFPQRSabcd"
				 ),
			array("EFABCDabcd")
		    ),
	new Theorem("lemma", "halvesofrectangles",
			array("ERABCDabcd",
			      "MIAPB",
			      "MIDQC",
			      "MIapd",
			      "MIbqc"
			     ),
		    array("ERPBCQabqp")
		   ),
	new Theorem("lemma", "halvesofequalshelper",
		   array("FRABCDSPTQ",
				 "ETPTSPTQ"
				),
		   array("ERAPQDPBCQ")
		   ),
	new Theorem("lemma", "halvesofequals",
			array("ETABCBCD",
				  "OSABCD",
				  "ETabcbcd",
				  "OSabcd",
				  "EFABDCabdc"
				 ),
			array("ETABCabc")
			),	
	//	HERE77 
	new Theorem("lemma", "paste3",
	//  paste two triangles together to get a quadrilateral
			array("ETABCabc",
				  "ETABDabd",
				  "BECMD",
				  "BEcmd",
				  "ORBEAMB|EQAM|EQMB",
				  "ORBEamb|EQam|EQmb"
				 ),
			array("EFACBDacbd")
			),
	new Theorem("lemma", "paste4", 
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
			), 
	new Theorem("lemma", "doublesofequals",
			array("ETABCabc",
				  "ETABCABD",
				  "ETabcabd",
				  "BECMD",
	              "BEcmd",
				  "ORBEAMB|EQAM|EQMB",  
				  "ORBEamb|EQam|EQmb"
				 ),
			array("EFACBDacbd")
			),
	new Theorem("lemma", "ERimpliesEF",
			array("ERABCDabcd"),
			array("EFABCDabcd")
		    ),
	new Theorem("lemma", "paste5helper",
			array("FRPQRSABCD",
				  "MIPGS",
				  "MIQHR"
				 ),
			array("EFABCDGHRS")
			),
	new Theorem("lemma","45helper",   // Lemma 27 in the paper
			array("CQABCD",
				  "PGFLMK",
				  "PGGLMG",
				  "BEFGL",
				  "BEKHM",
				  "BEBED",
				  "EFABEDFGHK",
				  "EFCBEDGMLH"
				 ),
			array("EFABCDFLMK")
			),
			
				//	HERE77
/*  Now we return to Euclid */
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

	// HERE77
	
/*  These are a few of the equal-figures axioms
    that we didn't need to prove in the new development,
    at least not to formalize Book I.
 
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
*/
	new Theorem( "proposition", "III.01",
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
	new Theorem( "lemma", "radiusdetermined",
			   array("CIJFPQ", "CIJGpq", "NEPQ", "NEpq"),
			   array("EEPQpq")
			   ),
	new Theorem( "lemma", "radiiequal",
				array("CIJCPQ",
					  "ONAJ",
					  "ONBJ"
					  ),
				array("EECACB")
				),
	new Theorem( "proposition", "III.02",
			   array("CIJDPQ",
					 "ONAJ", 
					 "ONBJ",
					 "BEAEB"
					),
			   array("ICEJ")
			  ),
	new Theorem( "proposition", "III.03A",
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
	new Theorem( "proposition", "III.04",
			   array("CIJFFA",
					 "ONAJ",
					 "ONBJ",
					 "ONCJ",
					 "ONDJ",
					 "NCACF",
					 "NCBDF",
					 "CUABCDE"
					),
				array("ORNOMIBED|NOMIAEC")
				),
	new Theorem( "proposition", "III.05",
				array("CIJEEC",
					  "CIKHHC",
					  "CCJKC"
					 ),
				array("NEEH")
			    ),
/* 	new Theorem( "proposition", "III.06",
				array("CIJEEC",
					  "CIKHHC",
					  "TAJKC"
					 ),
				array("NEEH")
			    )
*/ 
);				


	
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
);  */
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

