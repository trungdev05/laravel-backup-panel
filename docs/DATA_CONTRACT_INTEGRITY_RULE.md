# Data Contract & Integrity Rule

## Spirit (the 5 non-negotiables)

- **The contract is the law:** PHP native types, backed enums, PHPDocs, Form Request
  validation, published configuration, and documented public interfaces define truth.
  Code must match the contract—no “just in case” defensive checks.
- **Single Source of Truth per field:** every field has exactly one authoritative
  source. If it’s missing or invalid, **fail fast** with a clear error.
- **No fallbacks or compat hacks:** no multi-source probing, legacy aliases, guessing
  helpers, shadow copies, or hidden reconciliation.
- **Boundary-only validation/normalization:** parsing, coercion, trimming, and
  shape-fixing happen only at explicit boundaries such as Form Requests,
  `prepareForValidation()`, DTO/value-object construction, published configuration,
  or migrations—not in actions, jobs, repositories, or Blade views.
- **Integrity violations must be loud and stopping:** no catch-and-ignore, no
  defaults that mask bad data, and no partial updates that can create inconsistent
  state. If atomicity is unavailable, the operation must fail.

---

## Rules

### 1) Single Source of Truth (SST)

Every field has exactly one authoritative source. A field must not probe multiple
inputs such as `$primary ?? $secondary` for the same meaning.

If the source is missing or invalid, reject it at its boundary or throw a clear error.

### 2) No legacy or compatibility fields

Do not add duplicate names for the same meaning, aliases, deprecated request keys, or
compatibility fallbacks. This package is pre-release: when a contract changes, update
all in-repository producers and consumers together and remove the superseded contract.

### 3) No multi-source normalization helpers

Do not create helpers that search several objects, config keys, request fields, or
payload fields to discover a value. If ownership is unclear, make the contract
explicit; do not guess.

### 4) Maximum explicitness; no shadow flows

Do not retain shadow copies of contract data or reconcile values later. Configuration,
request validation, queue payload construction, and view-data construction must each
state their source and shape directly.

### 5) Nullability discipline

PHP types, enums, PHPDocs, validation rules, and configuration contracts define
nullability. Do not add `?->`, `isset`, `empty`, `== null`, or equivalent checks for
values declared required. Optionality must be declared at the boundary; checks for an
explicitly nullable or optional value are permitted only where that contract is used.

### 6) No fallback defaults for runtime data

Do not use `??`, `?:`, or `||` to invent a value for required runtime data. A static,
documented default in the published config file is allowed because it defines the
installation contract. It must not become a fallback for a missing or invalid request,
queue payload, Spatie value, or host configuration value.

### 7) Normalize only at boundaries

Do not use `trim()`, empty-value probing, or ad-hoc shape repair in actions, jobs,
repositories, or views. If whitespace or empty-string handling is required, define it
once in the Form Request or another explicit boundary and test that behavior there.

### 8) UI strings and translation keys

Do not hide a missing translation with a fallback UI string. A translated string uses
one explicit key; missing keys must remain visible as an error during development.

### 9) No untyped package-owned data

Do not introduce `mixed`, unshaped arrays, or unvalidated raw input for a
package-owned public API, queue payload, or view-data field. Framework callback
signatures that require `mixed` may accept it only at their boundary and must validate
or narrow it immediately.

### 10) DTOs and projections establish contracts, not glue

Create a DTO, resource, or projection only when it defines a named boundary or public
contract. Do not create per-call “minimal” types or arrays solely to rename or copy
fields that already have an authoritative object or contract.

### 11) No backend-to-view key remapping glue

Do not map equivalent fields between actions and Blade just because their keys differ.
Align the package contract and update every producer and consumer. A deliberately
named view-data contract is allowed, but it must be its only representation.

### 12) No accidental shadow state

Do not copy contract values into session data, untyped arrays, mutable static state, or
pass-through variables unless that storage is the explicit contract boundary. A local
variable is allowed when it gives a value a domain meaning, is reused, or records a
validated boundary result.

### 13) No unnecessary parsing or coercion

Do not cast or parse a value that already has the required type. Parsing and coercion
are allowed only at explicit boundaries and must produce the declared PHP type, enum,
or value object.

### 14) Keep validation and normalization out of business and UI logic

Actions, jobs, repositories, and Blade views consume validated, typed data. They must
not patch missing fields, normalize shapes, trim strings, or coerce scalar values.

### 15) No catch-and-ignore on integrity failures

Do not swallow errors around required data access or an integrity-sensitive operation.
Report the failure or let it propagate; never return `null`, an empty value, or a
success response that conceals a failed operation.

### 16) Prefer framework primitives

Use Laravel and Composer ecosystem primitives before custom helpers. Use Form Requests
and validation rules for HTTP input, config accessors for typed configuration, enums
for package-owned states, and Laravel queue facilities for jobs. Add a custom helper
only for a genuine gap with meaningful reuse.
