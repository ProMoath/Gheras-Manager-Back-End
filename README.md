# Gheras Task Manager - Backend API

<div align="center">

[![Laravel](https://img.shields.io/badge/Laravel-10.x-red)](https://laravel.com)
[![Module Status](https://img.shields.io/badge/Module-Users_%26_Teams-green)]()

**Focus Branch: Users, Teams, and Authentication Implementation**

</div>

---

## 🚧 Module Status: Users & Teams
This branch implements the core structure for User Management, Authentication, and Team assignments.

| Feature | Status | Description |
| :--- | :---: | :--- |
| **Authentication** | ✅ Done | Login, Register, Logout (Sanctum) |
| **User Management** | ✅ Done | CRUD operations, Roles (Admin, Supervisor, Volunteer) |
| **Team Management** | ✅ Done | Create Teams, Assign Users, Pivot Tables |
| **Tasks & Projects** | ⏳ Pending | Disabled in this branch to focus on Core Auth |

---

## 🔌 API Endpoints (Implemented in this Branch)

### 1. Authentication
| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `POST` | `/api/auth/register` | Register a new user |
| `POST` | `/api/auth/login` | Login and receive API Token |
| `POST` | `/api/auth/logout` | Logout (Revoke Token) |

### 2. Users
| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/api/users` | List all users |
| `GET` | `/api/users/{id}` | Get specific user details |
| `POST` | `/api/users/{user}/teams/{team}` | Assign a user to a specific team |

### 3. Teams
| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/api/teams` | List all teams |
| `POST` | `/api/teams` | Create a new team |
| `GET` | `/api/teams/{team}/members` | Get all members of a specific team |

---

## 🚀 Getting Started

### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL

### Installation

1. **Clone the repository**
   ```bash
   git clone [https://github.com/YOUR_USERNAME/Gheras-Manager-Back-End
   .git](https://github.com/YOUR_USERNAME/gheras-backend.git)
   cd gheras-backend

2. **Install Dependencies**
   ```bash
   composer install   
3. **Environment Setup**
   ```bash
      cp .env.example .env
      php artisan key:generate   

Configure your database credentials in the .env file.

4. **Database Migration & Seeding**
   ```bash
      php artisan migrate --seed      

5. **Run the Server**
   ```bash
      php artisan serve

## 📂 Architecture
### This project follows a Service-Oriented Architecture to ensure scalability and clean code:
- Controllers: Handle HTTP requests and responses only.
-  Services: Contain the business logic (e.g., TaskService, ProjectService).
-  Resources: Transform data into standardized JSON responses.
-   Requests: Handle form validation.
