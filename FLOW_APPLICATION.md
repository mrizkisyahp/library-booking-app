# Application Flow & Standards

## End-to-End Flow
- Router resolves URLs (with params), applies group prefixes/names, and runs route-level middleware (CSRF/auth/admin). Route params are injected into controller actions and available via `$request->route()`.
- Controller keeps thin: validates input (`$request->validate()`/Validator), calls Services with clean data, and returns via Response helpers (`view/redirect/json/back`, AJAX-aware).
- Service enforces domain rules (availability/quota/code generation, etc.), orchestrates Repositories, and raises domain/validation errors.
- Repository encapsulates persistence with QueryBuilder/DbModel (select/where/paginate/soft deletes) and hydrates models; model events fire on create/update/delete/restore.
- Models auto-manage `created_at/updated_at`, support soft deletes, and participate in lifecycle events.
- Helpers (`request/response/route/session/flash/auth/asset/...`) are available in controllers/views for cleaner templates.
- Queue: jobs can be dispatched for async work and processed by the worker script.
- Errors: Validation exceptions redirect back with flashed errors/old input; other errors render 403/404/500 or JSON error payloads.
- DB migration note: current MySQL; plan exists to switch to PostgreSQL later via updated DSN/schema adjustments.

## Coding Standards
- Keep controllers thin; push business logic into services and persistence into repositories.
- Prefer dependency injection over newing classes directly; keep constructors light.
- Validate incoming data at the edge (controllers) using `$request->validate()` or the Validator helper.
- Use QueryBuilder/DbModel methods for DB access; avoid raw SQL except for necessary cases.
- Handle responses through the Response/redirect/json helpers; avoid echoing directly.
- Use middleware for cross-cutting concerns (CSRF/auth/role checks) at the router level.
- Flash user-facing messages via Session flash helpers; ensure redirects preserve flash/old input.
- Keep naming consistent: snake_case columns, camelCase properties/methods; prefer descriptive method names (`findByEmail`, `paginateActive`).
- Add comments sparingly to clarify non-obvious logic; avoid redundant comments.
- For async tasks, wrap work in jobs and dispatch to the queue worker; keep jobs idempotent where possible.
