try loadt "proofs.ml" with _ -> ();;

type_invention_error := true;;

if not (mem "point" (map fst (types()))) then (

new_type ("point",0);
new_type ("circle",0);

new_constant ("BE",`:point->point->point->bool`);
new_constant ("EE",`:point->point->point->point->bool`);
new_constant ("CI",`:circle->point->point->point->bool`);

new_constant ("ET",`:point->point->point->point->point->point->bool`);
new_constant ("EF",`:point->point->point->point->point->point->point->point->bool`);

);;

let defn_circle = new_axiom
  `~(A = B) <=> ?X. CI X C A B`;;

