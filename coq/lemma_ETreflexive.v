Require Export GeoCoq.Elements.OriginalProofs.lemma_TCreflexive.

Section Euclid.
Context `{Ax:euclidean_neutral_ruler_compass}.
Lemma lemma_ETreflexive : 
   forall A B C, 
   Triangle A B C ->
   ET A B C A B C.
Proof.
intros.
assert (Cong_3 A B C A B C) by (conclude lemma_TCreflexive).
assert (ET A B C A B C) by (conclude axiom_congruentequal).
close.
Qed.

End Euclid.
