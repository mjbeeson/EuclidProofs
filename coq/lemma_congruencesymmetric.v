Require Export GeoCoq.Elements.euclidean_axioms.

Lemma lemma_congruencesymmetric : 
   forall A B C D, 
   Cong B C A D ->
   Cong A D B C.
Proof.
intros.
assert (Cong B C B C) by (conclude cn_congruencereflexive).
assert (Cong A D B C) by (conclude cn_congruencetransitive).
close.
Qed.

End Euclid.
