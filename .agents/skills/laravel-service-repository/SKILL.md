# Skill: Laravel MVC with Repositories & Services Architecture

## Overview & Purpose
Enforce a clean separation of concerns in Laravel projects using the **Controller -> Service -> Repository -> Model** architecture.
Controllers must stay skinny, Services handle business rules/orchestration, and Repositories handle database queries and data persistence.

---

## Directory Structure
Always organize the codebase following this layout:

```text
app/
├── Http/
│   ├── Controllers/
│   ├── Requests/            # Form Requests for input validation
│   └── Resources/           # API Resources / ViewModels
├── Services/                # Business logic and domain orchestration
│   └── [Domain]Service.php
├── Repositories/
│   ├── Contracts/           # Interfaces
│   │   └── [Domain]RepositoryInterface.php
│   └── Eloquent/            # Concrete implementations
│       └── [Domain]Repository.php
└── Models/                  # Eloquent models (data structure only)
```

## API Guidelines
- **JSON Only**: All controllers must return JSON responses (never views).
- **API Resources**: Use `JsonResource` or `ResourceCollection` in the `app/Http/Resources` directory to format outgoing data. Do not return Eloquent Models directly from the controller.
- **Form Requests**: All validation must be handled in Form Requests (`app/Http/Requests`) returning JSON validation errors automatically (Laravel does this by default for APIs when the `Accept: application/json` header is present).
- **Routes**: Use `routes/api.php` for all endpoints. Prefix is usually `/api` automatically.