# Agent Guidelines

## Project overview

Laravel Backup Panel is a Composer package that adds a Blade dashboard for managing
`spatie/laravel-backup`: create full/database/files backups, inspect backup health
and files, download or delete backups, and monitor storage disks.

The package supports PHP 8.3+, Laravel 12.40–13, and Spatie Laravel Backup 10.
Its primary integration points are the service providers, `routes/web.php`, invokable
HTTP actions, queued backup job, published Blade/assets/configuration, and Testbench
feature tests.

## Non-negotiable data integrity rule

`docs/DATA_CONTRACT_INTEGRITY_RULE.md` is mandatory for every change. Read it before
changing any data contract, including HTTP request input, route parameters, config,
queue payloads, view data, public APIs, tests, and documentation.

Its 16 rules are absolute requirements:

- Contracts in PHP types, PHPDocs, validation rules, configuration, and documented
  interfaces are the source of truth.
- Give every field exactly one authoritative source. Missing or invalid required data
  must fail loudly and immediately.
- Validate, parse, coerce, and normalize only at explicit boundaries such as Laravel
  request validation, DTO construction, package configuration, or migrations.
- Do not add fallback chains, aliases, compatibility fields, remapping glue, hidden
  reconciliation, shadow copies, catch-and-ignore behavior, or defaults that conceal
  invalid data.
- Do not add defensive null/empty checks for values the contract declares required.
  Correct the contract or the data at its boundary instead.
- Prefer Laravel and Composer framework primitives to custom helpers when a suitable
  primitive already exists.

When this file and another instruction disagree, preserve data-contract integrity and
ask for clarification before introducing an exception.

## Project constraints

This package is pre-release. Make the current contract explicit and safe; do not add
backward-compatibility layers, legacy APIs, aliases, fallback behavior, or migration
shims unless the task explicitly requires them. A necessary contract change must
update all in-repository producers and consumers together and remove the superseded
contract in the same change.

Keep work narrowly scoped. Preserve unrelated working-tree changes. Use the package's
existing Laravel conventions and only add tests that exercise the changed behavior.
Before completing code changes, run the smallest relevant check; use `composer test`
when a package-level verification is needed.
