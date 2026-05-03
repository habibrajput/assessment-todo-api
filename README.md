# Todo List API

A clean, service-based REST API built with **Laravel 13** and **JWT authentication**.
Every response follows a consistent `GlobalApiResponse` envelope so the frontend always knows what to expect.

---

## Tech Stack

| Layer        | Version       |
|--------------|---------------|
| PHP          | 8.3.30        |
| Laravel      | 13.7.0        |
| JWT Auth     | tymon/jwt-auth v2 |
| Database     | MySQL / MariaDB / PostgreSQL |
| Mail         | Mailtrap (local) / SMTP (prod) |

---

## Project Structure

```
app/
├── Http/
│   ├── Controllers/Api/
│   │   ├── AuthController.php         ← 2 lines per method: call service, return response
│   │   └── TodoController.php
│   ├── Middleware/
│   │   └── JwtMiddleware.php          ← validates JWT, returns standard error shape
│   └── Requests/
│       ├── BaseApiRequest.php         ← overrides failedValidation → GlobalApiResponse
│       ├── Auth/
│       │   ├── RegisterRequest.php
│       │   └── LoginRequest.php
│       └── Todo/
│           ├── StoreTodoRequest.php
│           └── UpdateTodoRequest.php
│
├── Interfaces/
│   ├── AuthServiceInterface.php       ← contract for auth operations
│   └── TodoServiceInterface.php       ← contract for todo CRUD operations
│
├── libs/
│   ├── ErrorBook.php                  ← numeric success/error constants
│   └── Response/
│       ├── GlobalApiResponse.php      ← universal response builder
│       └── GlobalApiResponseCodeBook.php ← all outcome codes in one place
│
├── Models/
│   ├── User.php                       ← JWTSubject + helper methods
│   └── Todo.php                       ← query scopes + helper methods
│
├── Notifications/
│   └── VerifyEmailNotification.php
│
├── Providers/
│   └── AppServiceProvider.php         ← binds interfaces to implementations
│
├── Services/
│   ├── AuthService.php                ← implements AuthServiceInterface
│   └── TodoService.php                ← implements TodoServiceInterface
│
└── Traits/
    ├── ApiResponseTrait.php           ← response helpers used in services
    └── HasApiResponse.php             ← HTTP status resolver used in controllers

database/
├── migrations/
│   ├── ..._create_users_table.php
│   └── ..._create_todos_table.php
└── seeders/
    └── DatabaseSeeder.php             ← demo users for all test scenarios

postman/
├── TodoAPI.postman_collection.json
└── TodoAPI.postman_environment.json

routes/
└── api.php
```

---

## Architecture Overview

### Service-Based Pattern
Controllers contain zero business logic. Each method calls one service method and returns the response. All decisions, database access, and error handling live in the service layer.

### Interface-Driven Design
Controllers depend on interfaces, not concrete classes. The `AppServiceProvider` is the only place that knows which class implements which interface — making it trivial to swap implementations or mock in tests.

```
Controller → Interface → Service
```

### Traits
**`ApiResponseTrait`** (used in Services) — replaces repetitive `(new GlobalApiResponse())->error(...)` calls with clean one-liners like `$this->notFound()`, `$this->serverError()`, `$this->success()`.

**`HasApiResponse`** (used in Controllers) — the `respond()` method automatically resolves the correct HTTP status code from the outcome code. No hardcoded `404`, `401`, `422` in controllers.

### Model Helpers & Scopes
**User model helpers:** `generateVerificationCode()`, `markAsVerified()`, `isNotVerified()`, `toPublicArray()`

**Todo query scopes:** `forUser($userId)`, `search($keyword)`, `latestFirst()` — so queries read like plain English:
```php
Todo::forUser($userId)->search($keyword)->latestFirst()->paginate(10);
```

### Sub-function Pattern in Services
Each public service method is an orchestrator that calls small, focused private helpers — each doing exactly one thing:

```
register()
  ├── emailAlreadyTaken()
  ├── createUser()
  └── sendVerificationEmail()

login()
  ├── attemptLogin()
  ├── invalidateToken()
  └── buildTokenPayload()
        └── getTokenTTL()
```

### GlobalApiResponse Envelope
Every response — success or error — is wrapped in the same shape:
```json
{
    "_metadata": {
        "outcome":      "SUCCESS",
        "outcomeCode":  0,
        "numOfRecords": 1,
        "message":      "Logged in successfully."
    },
    "records": { ... },
    "errors":  []
}
```

---

## Prerequisites

- PHP >= 8.3
- Composer >= 2
- MySQL 8 / MariaDB 10.6+ / PostgreSQL 14+
- A [Mailtrap](https://mailtrap.io) account (free) for e-mail testing

---

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/habibrajput/assessment-todo-api.git
cd assessment-todo-api
```

### 2. Install dependencies

```bash
composer install
```

### 3. Create the environment file

```bash
cp .env.example .env
```

### 4. Generate the application key

```bash
php artisan key:generate
```

### 5. Set the application URL

Open `.env` and update:

```dotenv
APP_URL=http://localhost:8000
```

> This is required for email verification links to work correctly.

### 6. Configure your database

Open `.env` and update:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=assessment_todo_api
DB_USERNAME=root
DB_PASSWORD=your_password
```

Create the database:

```sql
CREATE DATABASE assessment_todo_api CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 7. Configure Mailtrap

1. Sign up at [mailtrap.io](https://mailtrap.io)
2. Go to **Email Testing → Inboxes → SMTP Settings → Laravel**
3. Copy credentials into `.env`:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@todo-api.test"
MAIL_FROM_NAME="Todo API"
```

### 8. Generate the JWT secret

```bash
php artisan jwt:secret
```

### 9. Run migrations

```bash
php artisan migrate
```

### 10. Seed the database (optional but recommended)

```bash
php artisan db:seed
```

Seeds three test users covering every login scenario:

| Email | Password | Status |
|-------|----------|--------|
| `demo@example.com` | `password` | ✅ Verified — can login |
| `unverified@example.com` | `password` | ❌ Not verified — login blocked |
| `wrongpass@example.com` | `wrongpassword` | ❌ Wrong password test |

---

## Running the Server

```bash
php artisan serve
# API available at http://localhost:8000/api
```

---

## Postman Setup

1. Open Postman → **Import**
2. Import both files from the `postman/` directory:
   - `TodoAPI.postman_collection.json`
   - `TodoAPI.postman_environment.json`
3. Select **Todo API – Local** environment (top-right dropdown)
4. Follow this flow:

```
Register → (copy code from Mailtrap) → Verify Email → Login → (token auto-saved) → Todo endpoints
```

> The Login request test script automatically saves the JWT to `{{token}}`. All protected requests use it automatically.

---

## API Reference

### Base URL
```
http://localhost:8000/api
```

### Authentication Endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|:----:|-------------|
| POST | `/auth/register` | ✗ | Create account, send verification mail |
| GET | `/auth/verify/{code}` | ✗ | Verify email address |
| POST | `/auth/login` | ✗ | Authenticate, receive JWT |
| POST | `/auth/logout` | ✓ | Invalidate JWT |

### Todo Endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|:----:|-------------|
| GET | `/todos` | ✓ | Paginated list — optional `?search=keyword&page=1` |
| POST | `/todos` | ✓ | Create a new todo |
| GET | `/todos/{id}` | ✓ | View a single todo |
| PUT | `/todos/{id}` | ✓ | Update title and description |
| DELETE | `/todos/{id}` | ✓ | Delete a todo |

### Authorization Header
```
Authorization: Bearer <your_jwt_token>
```

---

## Response Envelope

### Success
```json
{
    "_metadata": {
        "outcome":      "SUCCESS",
        "outcomeCode":  0,
        "numOfRecords": 1,
        "message":      "To-do item created successfully."
    },
    "records": {
        "todo": {
            "id":          1,
            "title":       "Buy groceries",
            "description": "Milk, eggs, bread.",
            "created_at":  "2026-05-01T10:30:00.000000Z",
            "updated_at":  "2026-05-01T10:30:00.000000Z"
        }
    },
    "errors": []
}
```

### Paginated List
```json
{
    "_metadata": {
        "outcome":      "SUCCESS",
        "outcomeCode":  0,
        "numOfRecords": 15,
        "message":      "To-do list retrieved successfully."
    },
    "records": {
        "todos": [ ... ],
        "pagination": {
            "current_page":  1,
            "last_page":     2,
            "per_page":      10,
            "total":         15,
            "next_page_url": "http://localhost:8000/api/todos?page=2",
            "prev_page_url": null
        }
    },
    "errors": []
}
```

### Error
```json
{
    "_metadata": {
        "outcome":      "INVALID_CREDENTIALS",
        "outcomeCode":  3,
        "numOfRecords": 0,
        "message":      "The email or password you entered is incorrect."
    },
    "records": {},
    "errors":  []
}
```

---

## Outcome Codes

| Code | Constant | HTTP | Meaning |
|------|----------|:----:|---------|
| 0 | `SUCCESS` | 200/201 | Request completed successfully |
| 2 | `INVALID_FORM_INPUTS` | 422 | Validation failed |
| 3 | `INVALID_CREDENTIALS` | 401 | Wrong email or password |
| 4 | `NOT_LOGGED_IN` | 401 | Missing or invalid JWT |
| 5 | `RECORD_ALREADY_EXIST` | 422 | Duplicate email on register |
| 6 | `RECORD_NOT_EXIST` | 404 | Todo or user not found |
| 8 | `INTERNAL_SERVER_ERROR` | 500 | Unexpected server error |
| 10 | `EMAIL_NOT_VERIFIED` | 401 | Account exists but unverified |

---

## Git Commit Convention

Commits follow [Conventional Commits](https://www.conventionalcommits.org/):

```
chore: init Laravel 13.7.0 project
chore: install and configure tymon/jwt-auth
feat: add GlobalApiResponse envelope and error codebook
feat: add User and Todo models with helper methods and scopes
chore: add database migrations for users and todos
feat: add AuthServiceInterface and TodoServiceInterface
feat: add ApiResponseTrait and HasApiResponse trait
feat: add AuthService with sub-function pattern
feat: add TodoService with sub-function pattern
feat: add JWT middleware and base form request
feat: add auth and todo form requests with validation
feat: add AuthController and TodoController
feat: add email verification notification
feat: bind interfaces in AppServiceProvider
feat: configure JWT guard and register API routes
chore: add database seeder with test users and todos
docs: add Postman collection and environment
docs: add README with full setup and architecture guide
```

---

## Running Tests

```bash
php artisan test
```
