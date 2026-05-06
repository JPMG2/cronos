Generate and apply the fillable property directly into the model file for the given model name.

Steps:
1. Use `mcp__laravel-boost__database-schema` to get the table columns for the model (snake_case table name).
2. Exclude: `id`, `created_at`, `updated_at`, `deleted_at`, and any auto-increment or timestamp columns.
3. Find the model file under `app/Models/`.
4. **CRITICAL — Validate before writing:**
   - List the exact column names returned by `database-schema` for the table.
   - Every field you include in `$fillable` or `#[Fillable([...])]` MUST appear verbatim in that list.
   - Never infer, guess, or rename column names (e.g. do NOT substitute `status` for `name` or vice versa).
   - If the table does not exist or returns no columns, stop and report the error to the user — do not write anything.
5. Apply the changes directly to the model file:
   - Add `declare(strict_types=1);` after `<?php` if not present.
   - If there are 5 or fewer fillable fields: use the PHP attribute syntax `#[Fillable(['field1', 'field2'])]` with the import `use Illuminate\Database\Eloquent\Attributes\Fillable;`
   - If there are more than 5 fillable fields: use the standard `protected $fillable = [...]` property.
6. Generate a `casts()` method for columns that need type casting, based on their DB type:
   - `json` / `jsonb` → `'array'`
   - `boolean` / `tinyint(1)` → `'boolean'`
   - `date` → `'date'`
   - `datetime` / `timestamp` (non-auto) → `'datetime'`
   - `decimal` / `numeric` → `'decimal:2'`
   - `integer` / `bigint` / `smallint` (non-auto, non-id) → `'integer'`
   - Only include columns that actually benefit from casting (skip `string`, `text`, `varchar`, `char`).
   - Use the `protected function casts(): array` method form (Laravel 12 convention).
   - Only add the `casts()` method if there is at least one column to cast.
7. Do not ask for confirmation — apply the changes immediately.
