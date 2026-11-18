# Copilot instructions for InventarioWM

## Project context
- This repository is a Laravel 12 inventory-management application.
- Runtime requirements are PHP 8.2+ and SQLite.
- The active application is Laravel. The code under `legacy/flask/` is historical reference code and must not be treated as the active application.

## Before changing code
- Inspect the existing implementation, routes, migrations, views, and related feature tests before editing.
- Explain the intended change and identify affected files.
- Preserve existing behavior unless the task explicitly requests a behavior change.
- Prefer small, focused changes over broad rewrites.
- Do not delete, rename, or rewrite existing migrations that may already have run in user databases. Add a new migration for schema changes.
- Never modify or expose real `.env` values, credentials, database files, or secrets.

## Inventory business rules
- Every new product must have a unique SKU and a valid category catalog reference.
- Supplier assignment is optional; categories and suppliers in use must not be hard-deleted.
- Preserve the legacy product category label while synchronizing it with the selected category record for compatibility with existing reports.
- Every stock entry or exit must create an inventory movement.
- Every new movement must persist `stock_before` and `stock_after` in the same transaction as the product stock update; historical rows may keep these nullable fields empty when their original balances are unknown.
- Movement creation and product stock updates must remain atomic in a database transaction.
- An exit must never make stock negative.
- Do not silently change stock through ordinary product editing. Stock changes must be traceable through movements or a specifically designed adjustment workflow.
- A product with movement history must not be hard-deleted.
- Preserve support for initial stock being recorded as an initial entry.
- Future multi-product deliveries must support independent review of each product line; do not make approval of one line depend on every other line.

## Security and data integrity
- Keep server-side validation for all user-controlled input.
- Keep protected routes behind the existing authentication middleware.
- Preserve CSRF protection and safe session handling.
- Use parameterized Eloquent/query-builder operations; do not concatenate untrusted input into raw SQL.
- Escape output in Blade and protect CSV exports against spreadsheet formula injection.
- Use foreign keys and appropriate indexes for new relational data.
- Consider concurrent stock updates and use database transactions/locking where supported.

## Code conventions
- Follow Laravel conventions and the existing PHP style.
- Keep controllers focused; extract services/actions only when complexity justifies it.
- Use explicit, descriptive names and avoid unnecessary dependencies.
- Keep user-facing text in Spanish, consistent with the current interface.
- Do not introduce a new architecture or package without explaining why it is needed.

## Tests and verification
- Add or update feature tests for every behavior change, including validation failures and authorization boundaries.
- Cover both successful and rejected stock movements.
- Run `composer validate`, `php artisan test`, and relevant formatting checks when the environment permits.
- Report exactly which checks were run and their results. Never claim tests passed if they were not executed.
