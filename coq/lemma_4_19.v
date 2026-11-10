Require Export GeoCoq.Elements.OriginalProofs.lemma_interior5.

Section Euclid.
Context `{Ax:euclidean_neutral_ruler_compass}.
Lemma lemma_4_19 : 
   forall A B C D, 
   BetS A D B -> Cong A C A D -> Cong B D B C ->
   eq C D.
Proof.
intros.
assert (Cong A D A D) by (conclude cn_congruencereflexive).
assert (Cong D B D B) by (conclude cn_congruencereflexive).
assert (Cong B C B D) by (conclude lemma_congruencesymmetric).
assert (Cong D C D D) by (conclude lemma_interior5).
assert (~ neq D C).
 {
 intro.
 assert (neq D D) by (conclude axiom_nocollapse).
 assert (eq D D) by (conclude cn_equalityreflexive).
 contradict.
 }
assert (eq C D) by (conclude lemma_equalitysymmetric).
close.
Qed.

End Euclid.
