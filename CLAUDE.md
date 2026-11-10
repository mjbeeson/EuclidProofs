# Notes for Claude

This is Michael Beeson's repository of formal proofs of Euclid's *Elements*. He works directly in this folder
(`Dropbox/GitHub/EuclidProofs`) and is new to git and GitHub.

- **"Push my Euclid changes"** means: run `git status` here, tell him which files changed, then `git add -A`,
  `git commit` with a short message saying which proofs changed, and `git push`.
- Never edit, rename, move or "fix" the `.prf` proofs, `Axioms.php` or the checkers unless he asks.
- Do not run the checker (`./CheckProofs.php`) unless he asks.
- Never create two different files with the same name. Equal Figures (`EqualFigures/`) is a finished project with
  its own `AxiomsEqualFigures.php` and `CheckProofsEqualFigures.php`.
- `coq/` (Julien Narboux) and `hol-light/` (Freek Wiedijk) are archives of other people's work; do not change them.
