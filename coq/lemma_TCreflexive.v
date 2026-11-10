Require Export GeoCoq.Elements.euclidean_axioms.

Lemma lemma_TCreflexive : 
   forall A B C, 
   Triangle A B C ->
   Cong_3 A B C A B C.
Proof.
intros.
assert (Cong A B A B) by (conclude cn_congruencereflexive).
assert (Cong B C B C) by (conclude cn_congruencereflexive).
assert (Cong A C A C) by (conclude cn_congruencereflexive).
assert (Cong_3 A B C A B C) by (conclude_def Cong_3 ).
close.
Qed.

End Euclid.
