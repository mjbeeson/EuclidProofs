
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
Definition neq A B := ~ eq A B.
Definition Col A B C := (eq A B \/ eq A C \/ eq B C \/ BetS B A C \/ BetS A B C \/ BetS A C B).
Definition nCol A B C := neq A B /\ neq A C /\ neq B C /\ ~ BetS A B C /\ ~ BetS A C B /\ ~ BetS B A C.
Definition InCirc P J := exists X Y U V W, CI J U V W /\ (eq P U \/ (BetS U Y X /\ Cong U X V W /\ Cong U P U Y)).
Definition OutCirc P J := exists X U V W, CI J U V W /\ BetS U X P /\ Cong U X V W.
Parameter OnCirc : Point -> Circle -> Prop.
Axiom ON : OnCirc B J -> CI J A C D -> Cong A B C D.
Definition equilateral A B C := Cong A B B C /\ Cong B C C A.
Definition Triangle A B C := nCol A B C.
Definition Out A B C := exists X, BetS X A C /\ BetS X A B.
Definition Lt A B C D := exists X, BetS C X D /\ Cong C X A B.
Definition Midpoint A B C := BetS A B C /\ Cong A B B C.
Definition CongA A B C a b c := exists U V u v, Out B A U /\ Out B C V /\ Out b a u /\ Out b c v /\ Cong B U b u /\ Cong B V b v /\ Cong U V u v /\ nCol A B C.
Definition Supp A B C D F := Out B C D /\ BetS A B F.
Definition Per A B C := exists X, BetS A B X /\ Cong A B X B /\ Cong A C X C /\ neq B C.
Definition Perp_at P Q A B C := exists X, Col P Q C /\ Col A B C /\ Col A B X /\ Per X C P.
Definition Perp P Q A B := exists X, Perp_at P Q A B X.
Definition InAngle A B C P := exists X Y, Out B A X /\ Out B C Y /\ BetS X P Y.
Definition TS P A B Q := exists X, BetS P X Q /\ Col A B X /\ nCol A B P.
Definition OS P Q A B := exists X U V, Col A B U /\ Col A B V /\ BetS P U X /\ BetS Q V X /\ nCol A B P /\ nCol A B Q.
Definition isosceles A B C := Triangle A B C /\ Cong A B A C.
Definition Cut A B C D E := BetS A E B /\ BetS C E D /\ nCol A B C /\ nCol A B D.
Definition Cong_3 A B C a b c := Cong A B a b /\ Cong B C b c /\ Cong A C a c /\ Triangle A B C.
Definition LtA A B C D E F := exists U X V, BetS U X V /\ Out E D U /\ Out E F V /\ CongA A B C D E X.
Definition TG A B C D E F := exists X, BetS A B X /\ Cong B X C D /\ Lt E F A X.
Definition TT A B C D E F G H := exists X, BetS E F X /\ Cong F X G H /\ TG A B C D E X.
Definition RT A B C D E F := exists X Y Z U V, Supp X Y U V Z /\ CongA A B C X Y U /\ CongA D E F V Y Z.
Definition Meet A B C D := exists X, neq A B /\ neq C D /\ Col A B X /\ Col C D X.
Definition CR A B C D := exists X, BetS A X B /\ BetS C X D.
Definition TP A B C D := neq A B /\ neq C D /\ ~ Meet A B C D /\ OS C D A B.
Definition Par A B C D := exists U V u v X, neq A B /\ neq C D /\ Col A B U /\ Col A B V /\ neq U V /\ Col C D u /\ Col C D v /\ neq u v /\ ~ Meet A B C D /\ BetS U X v /\ BetS u X V.
Definition SumA A B C D E F P Q R := exists X, CongA A B C P Q X /\ CongA D E F X Q R /\ BetS P X R.
Definition PG A B C D := Par A B C D /\ Par A D B C.
Definition SQ A B C D := Cong A B C D /\ Cong A B B C /\ Cong A B D A /\ Per D A B /\ Per A B C /\ Per B C D /\ Per C D A.
Definition RE A B C D := Per D A B /\ Per A B C /\ Per B C D /\ Per C D A /\ CR A C B D.
Definition  A B J E := CI J C P Q /\ OnCirc E J /\ BetS A E B /\ InCirc A J /\ OutCirc B J.
Definition  A B J := CI C J C P Q /\ Col A B X /\ Col A B Y /\ Col A B Z /\  X Y J Z.
Definition  A B J E := CI J C P Q /\ OnCirc E J /\ BetS A E B /\ ~  A B J.
Definition RC A B C D a b c d := RE A B C D /\ RE a b c d /\ Cong A B a b /\ Cong B C b c.
Definition ER A B C D a b c d := exists X Y Z U x z u w W, RC A B C D X Y Z U /\ RC a b c d x Y z u /\ BetS x Y Z /\ BetS X Y z /\ BetS W U w.
Definition BR A B C D E := RE B C D E /\ Col D E A.
Definition FR A B C D E F G H := RE E F G H /\ BetS E B F /\ BetS H D G /\ BR C B D G F /\ BF A B D H E.
Definition TE A B C a b c := exists X Y x y, RE A B X Y /\ RE a b x y /\ Col X Y C /\ Col x y c /\ ER A B X Y a b x y.
Definition FE A B C D a b c d := exists X Y Z U x y z u, TS A B C D /\ TS a b c d /\ FR A B C D X Y Z U /\ FR a b c d x y z u /\ ER X Y Z U x y z u.
Definition TF A B C a b c d := exists X Y x y z u, BR A B C X Y /\ FR a b c d x y z u /\ ER B C X Y x y z u.
Definition  J K B := OnCirc J B /\ OnCirc K B /\ OnCirc K X /\ OnCirc K Y /\ InCirc J X /\ OutCirc J Y.

Lemma Col_or_nCol : forall A B C, 
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


Tactic Notation "by" "cases" "on" constr(t) :=
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

Axiom cn_equalitytransitive : forall A B C, eq A C -> eq B C -> eq A B.
Axiom cn_congruencetransitive : forall B C D E P Q, Cong P Q B C -> Cong P Q D E -> Cong B C D E.
Axiom cn_equalityreflexive : forall A, eq A A.
Axiom cn_congruencereflexive : forall A B, Cong A B A B.
Axiom cn_equalityreverse : forall A B, Cong A B B A.
Axiom cn_sumofparts : forall A B C a b c, Cong A B a b -> Cong B C b c -> BetS A B C -> BetS a b c -> Cong A C a c.
Axiom cn_stability : forall A B, ~ neq A B -> eq A B.
Axiom cn_equalitysub : forall A B C D, eq D A -> BetS A B C -> BetS D B C.
Axiom axiom_betweennessidentity : forall A B, ~ BetS A B A.
Axiom axiom_betweennesssymmetry : forall A B C, BetS A B C -> BetS C B A.
Axiom axiom_innertransitivity : forall A B C D, BetS A B D -> BetS B C D -> BetS A B C.
Axiom axiom_connectivity : forall A B C D, BetS A B D -> BetS A C D -> ~ BetS A B C -> ~ BetS A C B -> eq B C.
Axiom axiom_nocollapse : forall A B C D, neq A B -> Cong A B C D -> neq C D.
Axiom axiom_5_line : forall A B C D a b c d, Cong B C b c -> Cong A D a d -> Cong B D b d -> BetS A B C -> BetS a b c -> Cong A B a b -> Cong D C d c.
Axiom postulate_Pasch_inner : forall A B C P Q, BetS A P C -> BetS B Q C -> nCol A C B -> exists X, BetS A X Q /\ BetS B X P.
Axiom postulate_Pasch_outer : forall A B C P Q, BetS A P C -> BetS B C Q -> nCol B Q A -> exists X, BetS A X Q /\ BetS B P X.
Axiom postulate_Euclid2 : forall A B, neq A B -> exists X, BetS A B X.
Axiom postulate_Euclid3 : forall A B, neq A B -> exists X, CI X A A B.
Axiom postulate_line_circle : forall A B C K P Q, CI K C P Q -> InCirc B K -> neq A B -> exists X Y, Col A B X /\ BetS A B Y /\ OnCirc X K /\ OnCirc Y K /\ BetS X B Y.
Axiom postulate_circle_circle : forall C D F G J K P Q R S, CI J C R S -> InCirc P J -> OutCirc Q J -> CI K D F G -> OnCirc P K -> OnCirc Q K -> exists X, OnCirc X J /\ OnCirc X K.
Axiom postulate_Euclid5 : forall a p q r s t, BetS r t s -> BetS p t q -> BetS r a q -> Cong p t q t -> Cong t r t s -> nCol p q s -> exists X, BetS p a X /\ BetS s q X.
Axiom axiom_circle_center_radius : forall A B C J P, CI J A B C -> OnCirc P J -> Cong A P B C.
Axiom axiom_congruentequal : forall A B C a b c, Cong_3 A B C a b c -> ET A B C a b c.
Axiom axiom_ETpermutation : forall A B C a b c, ET A B C a b c -> ET A B C b c a /\ ET A B C a c b /\ ET A B C b a c /\ ET A B C c b a /\ ET A B C c a b.
Axiom axiom_ETsymmetric : forall A B C a b c, ET A B C a b c -> ET a b c A B C.
Axiom axiom_EFpermutation : forall A B C D a b c d, EF A B C D a b c d -> EF A B C D b c d a /\ EF A B C D d c b a /\ EF A B C D c d a b /\ EF A B C D b a d c /\ EF A B C D d a b c /\ EF A B C D c b a d /\ EF A B C D a d c b.
Axiom axiom_halvesofequals : forall A B C D a b c d, ET A B C B C D -> TS A B C D -> ET a b c b c d -> TS a b c d -> EF A B D C a b d c -> ET A B C a b c.
Axiom axiom_EFsymmetric : forall A B C D a b c d, EF A B C D a b c d -> EF a b c d A B C D.
Axiom axiom_EFtransitive : forall A B C D P Q R S a b c d, EF A B C D a b c d -> EF a b c d P Q R S -> EF A B C D P Q R S.
Axiom axiom_ETtransitive : forall A B C P Q R a b c, ET A B C a b c -> ET a b c P Q R -> ET A B C P Q R.
Axiom axiom_cutoff1 : forall A B C D E a b c d e, BetS A B C -> BetS a b c -> BetS E D C -> BetS e d c -> ET B C D b c d -> ET A C E a c e -> EF A B D E a b d e.
Axiom axiom_cutoff2 : forall A B C D E a b c d e, BetS B C D -> BetS b c d -> ET C D E c d e -> EF A B D E a b d e -> EF A B C E a b c e.
Axiom axiom_paste1 : forall A B C D E a b c d e, BetS A B C -> BetS a b c -> BetS E D C -> BetS e d c -> ET B C D b c d -> EF A B D E a b d e -> ET A C E a c e.
Axiom axiom_deZolt1 : forall B C D E, BetS B E D -> ~ ET D B C E B C.
Axiom axiom_deZolt2 : forall A B C E F, Triangle A B C -> BetS B E A -> BetS B F C -> ~ ET A B C E B F.
Axiom axiom_paste2 : forall A B C D E M a b c d e m, BetS B C D -> BetS b c d -> ET C D E c d e -> EF A B C E a b c e -> BetS A M D -> BetS B M E -> BetS a m d -> BetS b m e -> EF A B D E a b d e.
Axiom axiom_paste3 : forall A B C D M a b c d m, ET A B C a b c -> ET A B D a b d -> BetS C M D -> (BetS A M B \/ eq A M \/ eq M B) -> BetS c m d -> (BetS a m b \/ eq a m \/ eq m b) -> EF A C B D a c b d.
Axiom axiom_paste4 : forall A B C D F G H J K L M P e m, EF A B m D F K H G -> EF D B e C G H M L -> BetS A P C -> BetS B P D -> BetS K H M -> BetS F G L -> BetS B m D -> BetS B e C -> BetS F J M -> BetS K J L -> EF A B C D F K M L.
