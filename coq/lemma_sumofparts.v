Require Export GeoCoq.Elements.OriginalProofs.lemma_congruencetransitive.

Section Euclid.
Context `{Ax:euclidean_neutral_ruler_compass}.
Lemma lemma_sumofparts : 
   forall A B C a b c, 
   Cong A B a b -> Cong B C b c -> BetS A B C -> BetS a b c ->
   Cong A C a c.
Proof.
intros.
assert (Cong A A a a) by (conclude axiom_nullsegment2).
assert (Cong B A A B) by (conclude cn_equalityreverse).
assert (Cong a b b a) by (conclude cn_equalityreverse).
assert (Cong B A a b) by (conclude lemma_congruencetransitive).
assert (Cong B A b a) by (conclude lemma_congruencetransitive).
assert (Cong A C a c) by (conclude axiom_5_line).
close.
Qed.

End Euclid.
