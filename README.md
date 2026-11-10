# EuclidProofs

Formal proofs of the propositions of Euclid's *Elements*, by Michael Beeson, written in a simple proof
language (`.prf` files) and checked by a proof checker written in PHP.

The proofs and the method are described in M. Beeson, J. Narboux and F. Wiedijk, *Proof-checking Euclid*,
Annals of Mathematics and Artificial Intelligence 85 (2019), 213–257.

## Contents

| Folder | What it is |
|---|---|
| top level | **Books I and III.** The `.prf` proofs, the theory file `Axioms.php` (axioms, definitions and the statements of all theorems), and the checker `CheckProofs.php`. |
| `EqualFigures/` | **Equal Figures** (finished project, 2020). A separate theory, in which axioms of Book I about equal figures (de Zolt's axioms, the paste axioms and others) are proved as theorems. It has its own proofs, its own theory file `AxiomsEqualFigures.php` and its own checker `CheckProofsEqualFigures.php`. |
| `coq/` | Archive: the Coq version of the Book I proofs (2017–2018), generated from the `.prf` files by `CoqExport.php` and written to be checked in Coq as part of [GeoCoq](https://github.com/GeoCoq/GeoCoq) (its `Elements/OriginalProofs` directory), which is where the maintained Coq version lives. |
| `hol-light/` | Archive: the HOL Light version of the Book I proofs (2018), by Freek Wiedijk. His Perl script `FreekFiles.pl` converts the `.prf` files into `michael.ml`, which HOL Light checks using his file `proofs.ml`; see his `README`. |

## Checking the proofs

You need PHP. In the top-level folder:

```bash
./CheckProofs.php
```

This checks, in order, the proof of every theorem listed in `Axioms.php`. For Equal Figures, in the
`EqualFigures/` folder:

```bash
./CheckProofsEqualFigures.php
```

The checkers write a dependency file for each proof into a subfolder `dependencies/`, which is not part of the
repository. The first line of each checker names the PHP interpreter (`/opt/local/bin/php82` and
`/usr/bin/php` respectively); alternatively run `php CheckProofs.php`.

The other PHP programs at the top level are tools for the proofs: `Euclid.php` and `Formalize.php`,
`MakeAppendix.php` (the appendix of the paper), `FreekFiles.php` (the input files `Definitions.txt` and
`Theorems.txt` for the HOL Light conversion), `Analyze.php` (dependencies), and `OriginalCheckProofs.php`, an
earlier version of the checker.

## License

The proofs and programs by Michael Beeson are released under the MIT license (see `LICENSE`). The files in
`hol-light/` are by Freek Wiedijk, and the Coq axiom and tactics file in `coq/` was written for the GeoCoq project;
they are included here as an archive and remain under their authors' terms.
