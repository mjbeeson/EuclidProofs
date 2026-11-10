type proofstep =
  | Known of term
  | Step of term * thm
  | Equalitysub of term
  | Assumption of term
  | Reductio of term
  | Cases of term * term
  | Case of int * term
  | Cases_ of term
  | Qedcase;;

let LARGE_ASM_MESON_TAC = GEN_MESON_TAC 0 1000 1;;

let ASSUMES_TAC = MAP_EVERY (REPEAT_TCL CHOOSE_THEN ASSUME_TAC) o CONJUNCTS;;

let rec X_CHOOSES_TAC xl =
  match xl with
  | [] -> ASSUMES_TAC
  | x::xl' -> X_CHOOSE_THEN x (X_CHOOSES_TAC xl');;

let UNFOLD_CHOOSES_TAC th tm ass =
  let ass' = REWRITE_RULE[th] ass in
  let (xl,tm') = strip_exists (concl ass') in
  let (_,il,_) = term_match (subtract (frees tm') xl) tm' tm in
  X_CHOOSES_TAC (map (fun x -> rev_assoc x il) xl) ass';;

let SUBGOAL_UNFOLD_TAC th tm =
  if not (is_eq (concl th)) then NO_TAC else
  FIRST_ASSUM (UNFOLD_CHOOSES_TAC th tm);;

let SUBGOAL_MATCH_TAC th tm =
  try let (l,r) = dest_imp (concl th) in
    if not (set_eq (frees l) (frees r)) then NO_TAC else
    let (xl,tm') = strip_exists r in
    let (_,il,_) = term_match [] tm' tm in
    let th' = INST il th in
    let (l,r) = dest_imp (concl th') in
    SUBGOAL_THEN l (fun th'' ->
      (X_CHOOSES_TAC (map (fun x -> rev_assoc x il) xl) (MP th' th''))) THENL
    [REPEAT CONJ_TAC THEN FIRST_ASSUM ACCEPT_TAC; ALL_TAC]
  with Failure _ -> NO_TAC;;

let SUBGOAL_MESON_TAC thl tm =
  (SUBGOAL_THEN tm (K ALL_TAC) THENL [FIRST_ASSUM ACCEPT_TAC; ALL_TAC]) ORELSE
  (SUBGOAL_THEN tm ASSUME_TAC THENL
   [LARGE_ASM_MESON_TAC thl; POP_ASSUM ASSUMES_TAC]);;

let CLOSE_TAC = (FIRST_ASSUM ACCEPT_TAC) ORELSE LARGE_ASM_MESON_TAC[];;

let CHOOSE_TAC' = (fun th -> X_CHOOSE_TAC (fst (dest_exists (concl th))) th);;

let (pull:int->refinement) =
  fun n (meta,sgs,just) ->
    let sgs1,sgs2 = chop_list n sgs in
    let sgs' = (hd sgs2)::sgs1@(tl sgs2) in
    let just' i ths =
      let ths1,ths2 = chop_list (n + 1) ths in
      let ths' = (tl ths1)@(hd ths1)::ths2 in
      just i ths' in
    (meta,sgs',just');;

let rec find_case tm sgs n =
  if concl (snd (hd (fst (hd sgs)))) = tm then n else
  find_case tm (tl sgs) (n + 1);;

let bind_exists g tm =
  let (asl,w) = (g:goal) in
  let xl = subtract (frees tm)
    (union (unions (map (frees o concl o snd) asl)) (frees w)) in
  list_mk_exists (xl,tm);;

let rec (STEP_TAC : proofstep -> tactic) = fun step g ->
  match step with
  | Known tm | Equalitysub tm | Cases_ tm ->
     (SUBGOAL_MESON_TAC[] (bind_exists g tm)) g
  | Step (tm,th) ->
     (SUBGOAL_UNFOLD_TAC th tm ORELSE SUBGOAL_MATCH_TAC th tm ORELSE
      (SUBGOAL_MESON_TAC[th] (bind_exists g tm) THEN
       REPEAT (POP_ASSUM CHOOSE_TAC') THEN
       POP_ASSUM ASSUMES_TAC)) g
  | Assumption tm ->
      (SUBGOAL_THEN (mk_neg tm) ASSUME_TAC THENL
       [DISCH_THEN ASSUMES_TAC; REWRITE_TAC[]]) g
  | Reductio tm -> CLOSE_TAC g
  | Cases (tm,disj) ->
      (SUBGOAL_THEN (bind_exists g tm) ASSUMES_TAC THENL
       [SUBGOAL_THEN disj ASSUME_TAC THENL [CLOSE_TAC; ALL_TAC] THEN
        REPEAT (POP_ASSUM DISJ_CASES_TAC);
        ALL_TAC]) g
  | Case _ -> ALL_TAC g
  | Qedcase -> CLOSE_TAC g;;

let last_step = ref (Known `T`);;

let run_step step =
  last_step := step;
  ignore (match step with
  | Case (_,tm) ->
      let (_,sgs,_) = hd !current_goalstack in
      ignore (refine (pull (find_case tm sgs 0)));
      e (POP_ASSUM ASSUMES_TAC)
  | _ -> e (STEP_TAC step));
  match !last_step with
  | Cases _ | Qedcase -> ()
  | _ -> if exists (fun tm -> length (conjuncts tm) > 1) (fst (top_goal()))
      then failwith "conjuncts";;

let run_proof tm stepl =
  ignore (g tm); ignore (e (DISCH_THEN ASSUMES_TAC));
  do_list run_step stepl;
  ignore (e (LARGE_ASM_MESON_TAC[]));
  top_thm();;
