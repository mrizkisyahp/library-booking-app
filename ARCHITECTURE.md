Architecture.md (Version 2 — Updated After Authentication Refactor)

Official Project Architecture Specification

1. Overview

This project uses a Custom MVC Monolith with a clearly defined separation of responsibilities:

Controllers: Thin, handle HTTP flow only

Models: ActiveRecord + validation only

Services: Multi-step workflows (business logic)

Core Layer: Routing, requests, responses, DB, sessions

Views: Presentation only (Tailwind HTML/PHP)

Architecture v2 reflects the updated authentication subsystem and enforces consistent patterns for all future features.

2. Folder Structure (Authoritative Layout)
App/
  Core/           # Framework (Router, Request, Response, DB, Session, Middleware)
  Controllers/    # HTTP controllers
  Models/         # ActiveRecord models (no workflow logic)
  Services/       # Multi-step domain workflows (AuthService, BookingService, etc.)
  Views/          # PHP templates using Tailwind
public/            # Web root
migrations/        # Database schema changes
vendor/            # Composer dependencies
.env               # Config

Rules

❌ Do NOT add new top-level folders

❌ Do NOT rename folders

✔ All code must live under App/ except public/index, migrations, vendor

✔ Every module MUST follow the same structure

3. Request Lifecycle (Exact Behavior)
HTTP Request
  → Request (parse URL/method/body)
  → Router (map to controller)
  → Controller@action
  → Service (workflow logic)
  → Model (ActiveRecord or validation)
  → View
  → Response (HTML + status codes)

Component Responsibilities
Component	Responsibility
Request	URL, GET/POST parsing
Router	Route → Controller method
Controller	Thin orchestrator (CSRF check → validate model → call service → render/redirect)
Model	Validation rules + ActiveRecord methods
Service	Encapsulate multi-step workflows (login, registration, OTP, etc.)
View	Rendering only
Session	Flash messages + session state
DB	PDO wrapper with prepared statements
4. Controllers (Thin Layer)

Controllers must:

✔ Check CSRF
✔ Load model data
✔ Validate model
✔ Call the appropriate Service method
✔ Set flash messages
✔ Redirect or render views

Controllers must NOT:

❌ Query database
❌ Hash passwords
❌ Make API calls
❌ Talk to CacheService or EmailService
❌ Implement workflow/business logic
❌ Write logs directly (except simple events)

Required CRUD Actions (for all modules)
index()  
show($id)
create()
store()
edit($id)
update($id)
delete($id)

5. Models (ActiveRecord Only)

Models define:

Attributes → DB columns

Validation rules

Scenarios

ActiveRecord methods (save, update, delete, findOne, findAll)

Query helpers (search, pagination)

Models must NOT:

❌ Access session
❌ Perform workflow logic
❌ Implement authentication
❌ Call EmailService / CacheService
❌ Manage business processes

6. Services (Workflow Layer)

This is the major update introduced in v2.

Services encapsulate multi-step business workflows, including:

Authentication

Turnstile verification

Registration

Login

OTP verification & resend

Future: booking logic, suspension rules, admin workflows, etc.

Example: AuthService.php now handles

✔ Turnstile verification
✔ Login process
✔ Registration workflow
✔ OTP generation, validation, resend
✔ CacheService and EmailService integration
✔ Logging
✔ Status transitions
✔ Session state
✔ Role-based behavior

Controllers only call these methods — they never reimplement this logic.

7. Views

Views must only render HTML and must not:

❌ Query database
❌ Validate data
❌ Implement workflow logic
❌ Touch sessions except for flash display

Views reside under:

Views/
  ModuleName/
    index.php
    create.php
    edit.php
    show.php
  layouts/
    main.php
    auth.php

8. Database Layer

Uses PDO with exception mode

All queries must use prepared statements

Models access DB through App::$app->db

No controller may execute SQL directly

9. Routing

Routes must follow REST-like conventions:

GET    /rooms            → RoomController@index
GET    /rooms/create     → RoomController@create
POST   /rooms            → RoomController@store
GET    /rooms/{id}       → RoomController@show
GET    /rooms/{id}/edit  → RoomController@edit
POST   /rooms/{id}       → RoomController@update
POST   /rooms/{id}/delete→ RoomController@delete


Authentication routes follow:

/login
/register
/register/mahasiswa
/register/dosen
/verify
/verify/resend
/logout

10. Validation Rules

Validation is done exclusively in Model classes via rules().

Supported rules:

RULE_REQUIRED

RULE_EMAIL

RULE_MIN

RULE_MAX

RULE_MATCH

RULE_NUMBER

RULE_UNIQUE

Controllers and Services must NOT perform validation logic — they only call:

if ($model->validate()) …

11. Flash Messages & Session Handling

Flash messages live in Session

Controllers set them

Services may set flash messages for workflow purposes

Views display them

$session->setFlash('error', '…')
$session->setFlash('success', '…')

12. Logging

Logging must happen in Services, not Controllers.

Typical usage:

Logger::auth('logged in', $userId)
Logger::auth('registered', $userId)
Logger::auth('email verified', $userId)

13. Architectural Style Summary (v2)

The application now uses:

✔ Custom MVC
✔ ActiveRecord models
✔ Service-based workflow orchestration
✔ RESTful routing
✔ Model-scoped validation
✔ Session-based authentication state
✔ Turnstile integration via service
✔ OTP + email workflow via service
✔ Thin controllers

This architecture is now:

Clean

Extendable

Testable

AI-friendly

Consistent

Predictable

Maintainable

All future features must follow the v2 patterns.

14. Rules for AI Assistants (Codex, ChatGPT, Copilot)

AI MUST:

✔ Follow this Architecture.md exactly
✔ Write thin controllers
✔ Put business logic in services
✔ Keep models pure
✔ Avoid duplicating workflows in controllers
✔ Never introduce new folder structures
✔ Always use prepared statements in models
✔ Respect naming and routing conventions

AI must NOT:

❌ Invent new abstractions
❌ Introduce new folders
❌ Put workflow logic in controllers or models
❌ Modify architecture unless explicitly allowed