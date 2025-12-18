# Coding Standards

This document defines the coding standards and conventions for the Library Booking App project. All development should follow these guidelines to maintain consistency and code quality.

---

## AI Assistant Rules

> [!IMPORTANT]
> **Always assumes the user wants to type the code themselves, implement the task one by one, file by file. Never all at once.**

> [!CAUTION]
> **If user asks a question, stop the code generation first and answer the user question truthfully without bias. Try to challenge the user's idea/question to promote critical thinking.**

> [!NOTE]
> **Check for user code first. Spot any bugs, typos, or logic errors - point them out and then continue providing code.**

---

## Application Architecture

### End-to-End Flow

```
Router → Controller → Service → Repository → Database
```

#### Router
- Resolves URLs with params (e.g., `/bookings/{id}`)
- Applies group prefixes and route names
- Runs route-level middleware (CSRF, auth, admin)
- Injects params into controller actions and `$request->route()`

#### Controller
- Keeps **thin** - no business logic here
- Validates input via `$request->validate()`
- Calls Services with clean data
- Returns via Response helpers (`view`, `redirect`, `json`, `back`)

#### Service
- Enforces **domain rules** (availability, quotas, code generation)
- Orchestrates multiple Repositories
- Raises domain/validation errors

#### Repository
- Encapsulates **persistence logic**
- Uses QueryBuilder/DbModel for database access
- Hydrates models from query results
- Model events fire on create/update/delete/restore

#### Models
- Auto-manage `created_at` / `updated_at`
- Support soft deletes (`deleted_at`)
- Participate in lifecycle events

---

## Core Principles

### 1. Keep Controllers Thin
Push business logic into **Services** and persistence logic into **Repositories**.

```php
// ❌ Bad - Logic in Controller
public function store(Request $request): string
{
    $data = $request->validate([...]);
    
    // Business logic should NOT be here
    if ($this->checkAvailability($data)) {
        $booking = new Booking();
        $booking->fill($data);
        $booking->save();
    }
}

// ✅ Good - Delegate to Service
public function store(Request $request): string
{
    $data = $request->validate([...]);
    $this->bookingService->createBooking($data);
    
    flash('success', 'Booking created successfully');
    return redirect('/bookings');
}
```

### 2. Dependency Injection (Container)

Your project uses a **Container** class for dependency injection. Services are auto-wired via constructor injection.

#### Container Methods

| Method | Purpose | Example |
|--------|---------|---------|
| `singleton($abstract, $factory)` | Register shared instance (created once) | Services, Repositories |
| `instance($abstract, $object)` | Register existing object | Request, Session |
| `make($abstract)` | Resolve/create instance | `container()->make(BookingService::class)` |

#### How It Works

```php
// In App.php - Services are registered as singletons
$this->container->singleton(
    CacheService::class,
    fn($c) => new CacheService(cacheDir: App::$ROOT_DIR . '/Storage/Cache')
);

$this->container->singleton(
    Logger::class,
    fn($c) => new Logger(logDir: App::$ROOT_DIR . '/Storage/Logs')
);

// Later, resolve anywhere:
$logger = container()->make(Logger::class); // Same instance every time
```

#### Controller Injection (Auto-Wired)

When the router instantiates your controller, dependencies are automatically resolved:

```php
// ✅ Good - Dependencies injected via constructor
class BookingController extends Controller
{
    public function __construct(
        private BookingService $bookingService,
        private BookingRepository $bookingRepository
    ) {}
    
    public function store(Request $request): string
    {
        // Use injected service directly
        $this->bookingService->createBooking($data);
    }
}

// ❌ Bad - Manual instantiation (avoid this)
class BookingController extends Controller
{
    public function store(Request $request): string
    {
        $service = new BookingService(); // Tightly coupled, hard to test
        $service->createBooking($data);
    }
}
```

### 3. Validate at the Edge
Validate incoming data at the controller level using `$request->validate()` or the Validator helper.

```php
public function store(Request $request): string
{
    $data = $request->validate([
        'ruangan_id' => 'required|integer',
        'tanggal'    => 'required|date',
        'waktu_mulai'=> 'required',
        'waktu_selesai' => 'required',
    ]);
    
    // $data is now clean and validated
    $this->service->process($data);
}
```

### 4. Use QueryBuilder/DbModel for Database Access
Avoid raw SQL except when necessary. Use the fluent QueryBuilder methods.

```php
// ✅ Good
$bookings = Booking::Query()
    ->where('status', 'verified')
    ->whereDate('tanggal', '>=', date('Y-m-d'))
    ->orderBy('tanggal', 'ASC')
    ->get();
```

### 5. Use Response/Redirect Helpers
Handle responses through helpers. Avoid echoing directly.

```php
// ✅ Good
return view('User/Bookings/Index', ['bookings' => $bookings]);
redirect('/bookings');
back();
response()->json(['success' => true]);
```

---

## Available Global Helpers

Use these helpers from `App/Helpers/Helpers.php`:

| Helper | Description |
|--------|-------------|
| `app()` | Get the App instance |
| `request()` | Get the Request instance |
| `response()` | Get the Response instance |
| `session($key, $default)` | Get session value or Session instance |
| `redirect($url)` | Redirect to URL |
| `back()` | Redirect back |
| `view($view, $params)` | Render view |
| `auth()` | Get AuthService instance |
| `user()` | Get current authenticated User |
| `guest()` | Check if user is guest |
| `old($key, $default)` | Get old input value |
| `flash($key, $value)` | Set/get flash message |
| `config($key, $default)` | Get config from environment |
| `env($key, $default)` | Get environment variable |
| `dd(...$vars)` | Dump and die |
| `dump(...$vars)` | Dump variable |
| `abort($code, $message)` | Abort with HTTP status |
| `url($path)` | Generate URL |
| `asset($path)` | Generate asset URL |
| `csrf_token()` | Get CSRF token |
| `csrf_field()` | Generate CSRF hidden input |
| `dispatch($job)` | Dispatch job to queue |
| `container($abstract)` | Get/resolve from container |
| `resolve($abstract)` | Alias for `container()` - same behavior |
| `formatWaktu($waktu)` | Format time to `H:i WIB` |
| `formatTanggal($tanggal)` | Format date to Indonesian |
| `str_slug($string)` | Convert string to slug |
| `room_photos($room)` | Get room photos as base64 |
| `room_thumbnail($room)` | Get room thumbnail |
| `room_facilities($room)` | Get room facilities array |

---

## Naming Conventions

| Context | Convention | Example |
|---------|------------|---------|
| Database columns | `snake_case` | `created_at`, `user_id`, `nama_ruangan` |
| PHP Properties/Methods | `camelCase` | `$userName`, `findByEmail()` |
| Classes | `PascalCase` | `BookingService`, `UserRepository` |
| Constants | `UPPER_SNAKE_CASE` | `MAX_BOOKING_HOURS` |
| View files | `PascalCase` | `User/Bookings/Index.php` |
| Route names | `dot.notation` | `user.bookings.show` |

---

## Validation Rules Reference

| Rule | Description | Example |
|------|-------------|---------|
| `required` | Field must be present and not empty | `'name' => 'required'` |
| `string` | Must be a string | `'name' => 'string'` |
| `integer` / `numeric` | Must be integer/numeric | `'id' => 'integer'` |
| `email` | Must be valid email | `'email' => 'email'` |
| `min:n` / `max:n` | Minimum/maximum length or value | `'password' => 'min:8'` |
| `between:min,max` | Value must be between min and max | `'age' => 'between:18,65'` |
| `in:a,b,c` | Must be one of the listed values | `'status' => 'in:draft,pending,verified'` |
| `date` | Must be valid date | `'tanggal' => 'date'` |
| `after:date` / `before:date` | Date comparison | `'end' => 'after:start'` |
| `confirmed` | Must have matching `_confirmation` field | `'password' => 'confirmed'` |
| `regex:pattern` | Match regex pattern | `'code' => 'regex:/^[A-Z]{3}$/'` |
| `unique:table,column` | Must be unique in database | `'email' => 'unique:users,email'` |
| `exists:table,column` | Must exist in database | `'room_id' => 'exists:ruangan,id_ruangan'` |

```php
// Combine multiple rules with pipe
$data = $request->validate([
    'email'    => 'required|email|unique:users,email',
    'password' => 'required|string|min:8|confirmed',
    'role'     => 'required|in:mahasiswa,dosen,tendik',
]);
```

---

## Middleware

### Using Middleware

Apply middleware at the router level for cross-cutting concerns:

```php
// In routes/web.php
$router->group(['middleware' => ['auth', 'admin']], function($router) {
    $router->get('/admin/dashboard', [AdminController::class, 'dashboard']);
});
```

### Available Middleware

| Middleware | Purpose |
|------------|---------|
| `csrf` | Validates CSRF token on POST/PUT/DELETE |
| `auth` | Requires authenticated user |
| `admin` | Requires admin role |
| `guest` | Only allows non-authenticated users |

### Creating New Middleware

1. Create file in `App/Core/Middleware/`:

```php
// App/Core/Middleware/RoleMiddleware.php
namespace App\Core\Middleware;

use App\Core\Request;
use App\Core\Exceptions\ForbiddenException;

class RoleMiddleware
{
    public function handle(Request $request, string $role): void
    {
        if (!user() || user()->role !== $role) {
            throw new ForbiddenException('Access denied');
        }
    }
}
```

2. Register in router (if needed) or use in route groups

---

## Flash Messages

Use Session flash helpers for user-facing messages:

```php
// Set flash message
flash('success', 'Booking berhasil dibuat!');
flash('error', 'Terjadi kesalahan.');

// In view
<?php if ($success = flash('success')): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>
```

---

## Error Handling (App::run())

The application's `App::run()` method handles all exceptions centrally. Understanding this flow helps you throw the right exception types.

### Exception Types

| Exception | HTTP Code | Use Case |
|-----------|-----------|----------|
| `NotFoundException` | 404 | Resource not found (e.g., `findOrFail()`) |
| `ForbiddenException` | 403 | Access denied, unauthorized actions |
| `ValidationException` | 422 | Input validation failures |
| `PDOException` | 500 | Database errors |
| `Throwable` (general) | 500 | All other unhandled errors |

### How to Throw Exceptions

```php
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\ForbiddenException;
use App\Core\Exceptions\ValidationException;

// 404 - Resource not found
if (!$booking) {
    throw new NotFoundException('Booking not found');
}

// 403 - Access forbidden
if ($booking->id_user !== user()->id_user) {
    throw new ForbiddenException('You cannot access this booking');
}

// 422 - Validation (automatic via $request->validate())
$data = $request->validate([
    'ruangan_id' => 'required|integer',
]);
// ValidationException is thrown automatically if validation fails
```

### Automatic Behaviors

**ValidationException (422):**
- **Regular request**: Flashes errors + old input, then redirects back
- **AJAX request**: Returns JSON `{ success: false, errors: {...} }`

**NotFoundException (404) / ForbiddenException (403):**
- **Regular request**: Renders `errors/404` or `errors/403` view
- **AJAX request**: Returns JSON with error details

**PDOException / General Errors (500):**
- **Development mode** (`APP_ENV=development`): Shows full error details
- **Production mode**: Shows generic message, logs full details

### When to Catch vs Let Bubble Up

| Scenario | Action | Result |
|----------|--------|--------|
| Resource not found (404) | Let bubble up | Shows error page |
| Access denied (403) | Let bubble up | Shows error page |
| Domain error (e.g., "room unavailable") | **Catch and flash** | Shows flash on same page |
| Validation error | Let bubble up (automatic) | Redirects back with errors |

### Best Practices

```php
// ✅ Domain errors: Catch and flash for better UX
public function store(Request $request): string
{
    $data = $request->validate([...]);
    
    try {
        $this->bookingService->createBooking($data);
        flash('success', 'Booking berhasil dibuat!');
        return redirect('/bookings');
        
    } catch (\Exception $e) {
        flash('error', $e->getMessage());
        return back();
    }
}

// ✅ Not found: Let bubble up for 404 page
public function show(Request $request, int $id): string
{
    $booking = Booking::findOrFail($id); // Throws NotFoundException automatically
    return view('Bookings/Show', ['booking' => $booking]);
}

// ✅ Access check: Let bubble up for 403 page
public function edit(Request $request, int $id): string
{
    $booking = Booking::findOrFail($id);
    
    if ($booking->id_user !== user()->id_user) {
        throw new ForbiddenException('Anda tidak memiliki akses');
    }
    
    return view('Bookings/Edit', ['booking' => $booking]);
}
```

---

## QueryBuilder Reference

### Core Methods
- `table()`, `select()`, `where()`, `orWhere()`
- `whereIn()`, `whereNotIn()`, `whereNull()`, `whereNotNull()`
- `whereDate()`, `whereBetween()`, `whereRaw()`
- `join()`, `leftJoin()`
- `orderBy()`, `groupBy()`, `having()`
- `limit()`, `offset()`
- `insert()`, `update()`, `delete()`
- `get()`, `first()`, `find()`, `findOrFail()`
- `exists()`, `count()`, `paginate()`

### Model Relationships
- `belongsTo()`, `hasMany()`, `hasOne()`, `belongsToMany()`
- `with()` (eager loading), `load()` (lazy loading)

### Transactions
```php
DB::beginTransaction();
try {
    // operations
    DB::commit();
} catch (\Exception $e) {
    DB::rollback();
    throw $e;
}
```

---

## Comments

Add comments sparingly to clarify **non-obvious logic only**. Avoid redundant comments.

```php
// ❌ Bad - Redundant
// Get user by ID
$user = User::find($id);

// ✅ Good - Explains non-obvious logic
// Check if booking overlaps with blocked dates (admin-set maintenance windows)
$hasConflict = $this->checkBlockedDateOverlap($date, $roomId);
```

---

## Async Tasks (Queue)

For async tasks, wrap work in jobs and dispatch to the queue worker. Keep jobs idempotent where possible.

```php
// Dispatch a job
dispatch(new SendEmailJob($user, $template));

// Run worker
// php script/queue.php work
```

---

## File Organization

```
App/
├── Controllers/          # HTTP request handlers
├── Core/                 # Framework core classes
│   ├── Exceptions/       # Custom exception classes
│   ├── Middleware/       # HTTP middleware
│   ├── Queue/            # Queue system
│   ├── Repository/       # Core repositories (UserRepository)
│   └── Services/         # Core services (Auth, Cache, Email, Logger, etc.)
├── Helpers/              # Global helper functions
├── Models/               # Database models
├── Repositories/         # Domain repositories (BookingRepository, etc.)
├── Services/             # Domain services (BookingService, etc.)
└── Views/                # View templates
    ├── Admin/            # Admin views
    ├── Auth/             # Authentication views
    ├── Profile/          # Profile views
    └── User/             # User views
```

---

## Implementation Guidelines

> [!NOTE]
> When implementing new features, follow this order:
> 1. **Route** - Define in router
> 2. **Controller** - Create method, add validation
> 3. **Service** - Add business logic
> 4. **Repository** - Add data access methods
> 5. **View** - Create/update view files

**Remember: One file at a time, one feature at a time.**
