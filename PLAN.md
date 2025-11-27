# Laravel-like Enhancements Plan

## Goals
- Add Laravel-style routing with parameters and route names.
- Enhance Request/Response APIs.
- Add validation flow with Validator/ValidationException.
- Introduce QueryBuilder integrated with DbModel.
- Provide global helper functions and better error handling.

## Progress Checklist
- [x] Router: groups with prefix/name stacking, named routes, path params, route-level middleware, params injected.
- [x] Request: input/query helpers, AJAX detection, `$request->validate()` hook.
- [x] Response: helpers (`json/view/redirect/back/abort/status/headers/download`) with AJAX-friendly payloads.
- [x] Validation: Validator + ValidationException + rule set.
- [x] QueryBuilder: fluent queries + pagination + DbModel integration.
- [ ] Models: timestamps + soft deletes + default scope + restore helpers.
- [ ] Events: dispatcher + model lifecycle events.
- [ ] Queue worker: dispatch helper + CLI worker.
- [ ] Helpers: global functions + DI container wiring.
- [ ] Error handling/middleware: validation redirect handling + CSRF/auth/admin middleware; flash persistence improvements.
- [ ] DB migration note: plan for MySQL → PostgreSQL later.

## Work Breakdown
1) Router
   - Support path params (`/users/{id}`, `{slug?}`, `{id:\d+}`).
   - Store route names for `route('users.show', ['id' => 1])`.
   - Inject route params into controller methods and `$request->route()`.
   - Add router-level middleware and route groups with prefix/name stacking.

2) Request
   - Helpers: `all()`, `input()`, `only/except`, `has()`, `boolean()`, `query()`, `header()`, `ip()`, `userAgent()`, `file()/hasFile()`, `route()`.
   - `$request->validate($rules)` delegating to Validator; on failure, throw ValidationException.

3) Response
   - Helpers: `json()`, `view()`, `redirect()`, `back()`, `abort()`, `status()`, `withHeaders()`, `download()`.
   - AJAX-friendly responses: detect `X-Requested-With`/`Accept: application/json` and provide standardized JSON success/error payloads.

4) Validation
   - Add `Validator` class with rules: required, string, int/numeric, email, min/max/between, in, date, after/before, confirmed, regex, unique, exists.
   - Add `ValidationException`; include custom messages and `sometimes` behavior.

5) QueryBuilder
   **Core QueryBuilder (Mandatory):**
   - [x] `table()` - set table name
   - [x] `select()` - choose columns
   - [x] `where()` - basic WHERE condition
   - [x] `orWhere()` - OR WHERE condition
   - [x] `whereIn()` - WHERE IN clause
   - [x] `whereNotIn()` - WHERE NOT IN clause
   - [x] `whereNull()` - WHERE column IS NULL
   - [x] `whereNotNull()` - WHERE column IS NOT NULL
   - [x] `whereDate()` - WHERE date comparison
   - [x] `whereBetween()` - WHERE BETWEEN clause
   - [x] `whereRaw()` - raw WHERE clause
   - [x] `join()` - INNER JOIN
   - [x] `leftJoin()` - LEFT JOIN
   - [x] `orderBy()` - ORDER BY clause
   - [x] `groupBy()` - GROUP BY clause
   - [x] `having()` - HAVING clause
   - [x] `limit()` - LIMIT clause
   - [x] `offset()` - OFFSET clause
   - [x] `insert()` - INSERT query
   - [x] `update()` - UPDATE query
   - [x] `delete()` - DELETE query
   - [x] `get()` - execute and fetch all results
   - [x] `first()` - execute and fetch first result
   - [x] `find()` - find by primary key
   - [x] `findOrFail()` - find or throw exception
   - [x] `exists()` - check if records exist
   - [x] `count()` - count records
   - [x] `raw()` - raw SQL execution
   - [x] `paginate()` - pagination with metadata (items, total, perPage, currentPage, lastPage, next/prev URLs)
   
   **Model Layer (Mandatory):**
   - [ ] BaseModel with `tableName()`, `primaryKey()`
   - [ ] `save()` - insert or update
   - [ ] `update()` - update existing record
   - [ ] `delete()` - delete record
   
   **Relationship Helpers (Mandatory):**
   - [ ] `belongsTo()` - inverse one-to-many
   - [ ] `hasMany()` - one-to-many
   - [ ] `hasOne()` - one-to-one
   - [ ] `belongsToMany()` - many-to-many
   - [ ] `with()` - eager loading
   - [ ] `load()` - lazy loading
   
   **Optional (Recommended):**
   - [x] `chunk()` - process large datasets in chunks
   
   **Integration:**
   - [x] Integrate with `DbModel` via `$this->newQuery()` and hydration helpers.
   - [x] Support transactions: `beginTransaction()`, `commit()`, `rollback()`.

6) Helpers
   - Create `App/helpers.php` (autoload via Composer files).
   - Functions: `request()`, `response()`, `view()`, `route()`, `redirect()`, `back()`, `abort()`, `url()/asset()`, `session()/flash()/old()`, `csrf_field()/method_field()`, `auth()/user()/guest()`, `validator()`, `config()/env()`, `logger()`, `dd()/dump()`.

7) Models: timestamps & soft deletes
   - Auto-fill `created_at`/`updated_at` on insert/update.
   - Optional `deleted_at` with query helpers: `onlyTrashed()`, `withTrashed()`, `restore()` and soft-delete default scope.

8) Events
   - Lightweight dispatcher for model lifecycle hooks: `created`, `updated`, `deleted`, `restored`. Allow listeners registration and dispatch inside DbModel operations.

9) Queue Worker
   - Simple in-memory/file-backed queue with a CLI worker script (e.g., `php script/queue.php work`) and helper `dispatch($job)` for async tasks; graceful shutdown handling.

10) Error Handling / Middleware
   - Catch `ValidationException`, flash errors + old input, redirect back.
   - Add CSRF middleware; keep auth/admin middleware pattern.
   - Update Session flash (setFlash/getFlash) to survive redirects reliably.

11) Dependency Injection
    - Add a lightweight container for bindings/singletons and resolving controllers/services/repositories.
    - Prefer constructor injection via the container; avoid manual `new` in controllers where possible.

## Application Flow (Controller → Service → Repository)
- Controller: receives user request, validates input, forwards clean data to Service; handles Response (view/json/redirect).
- Service: enforces domain rules (availability, quotas, code generation), coordinates repositories, and raises domain errors.
- Repository: encapsulates persistence; uses QueryBuilder/DbModel to run SQL (`insert/update/find`).
- Database: executes queries (currently MySQL; plan below for PostgreSQL switch).

## Database Migration Note
- Plan to migrate database from MySQL to PostgreSQL (schema, connection DSN, SQL differences). Do this after core features if time permits.

## Implementation Order
Router ➜ Request ➜ Response ➜ Validation ➜ QueryBuilder (+pagination) ➜ Models (timestamps/soft deletes) ➜ Events ➜ Queue ➜ Helpers ➜ Error handler/middleware wiring.

## Critical Note
- User wants to type the code themselves. Implement features one by one, file by file—not all at once. Keep instructions and changes easy to follow for a beginner in PHP MVC.
