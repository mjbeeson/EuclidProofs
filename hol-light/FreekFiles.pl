#!/usr/bin/perl -w
use strict "subs";

sub fixit {
  s/\~EQ (.) (.)/~($1 = $2)/g;
  s/\~NE (.) (.)/~~($1 = $2)/g;
  s/EQ (.) (.)/$1 = $2/g;
  s/NE (.) (.)/~($1 = $2)/g;
  s/(\w) = /$1:point = /g;
  s/\bEL\b/EL_/g;
  s/\bF\b/F_/g;
  s/\bT\b/T_/g;
  s/\bNC\b/~CO/g;
  s/\~\~CO/CO/g;
  s/\~\~/~ ~/g;
}

system "cp head.ml michael.ml";
open(F, 'proofs/Definitions.txt');
open(G, ">>", "michael.ml");
while (<F>) {
  die if !/^(.*)\t(.*)$/;
  $x = $1;
  $_ = $2;
  &fixit;
  $x =~ s/[-.]/_/g;
  next if $x eq "circle" || $x eq "unequal";
  $y = $_;
  $h = s/`(CI J (.*)) \/\\ (.*) <=> (.*)`/`$3 <=> ?$2. $1 \/\\ $4`/;
  if ($h) {
    print G "let def_$x = (SPEC_ALL o new_definition)\n  $_;;\n\n";
    print G "let defn_$x = new_axiom\n  $y;;\n\n";
  } elsif ($x eq "noncollinear") {
    $z = "let defn_$x = MESON[defn_collinear]\n  $y;;\n\n";
  } else {
    print G "let defn_$x = (SPEC_ALL o new_definition)\n  $_;;\n\n";
  }
}
print G $z;
close(F);
open(F, "proofs/Theorems.txt");
while (<F>) {
  die if !/^(.*)\t(.*)\t(.*)\t(.*)$/;
  $c = $1;
  $x = "$1_$2";
  $_ = $3;
  $y = $4;
  $x =~ s/[-.]/_/g;
  &fixit;
  if ($y eq "") {
    $axiom = "new_axiom";
    $axiom = "MESON[]" if $x eq "cn_equalitytransitive" ||
      $x eq "cn_equalityreflexive" ||
      $x eq "cn_stability" ||
      $x eq "cn_equalitysub";
    die if $c eq "lemma" || $c eq "proposition";
    print G "let $x = $axiom\n  $_;;\n\n"; # if $y eq "";
  } else {
    print G "let $x = run_proof\n  $_\n  [\n";
    print G `./ConvertProof.pl proofs/$y`;
    print G "  ];;\n\n";
  }
}
close(F);
close(G);
