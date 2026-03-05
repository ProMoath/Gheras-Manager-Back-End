# Gheras Task Manager - Backend API

<div align="center">

[![Laravel](https://img.shields.io/badge/Laravel-10.x-red)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.1+-blue)](https://www.php.net)
[![Database](https://img.shields.io/badge/MySQL-8.0-orange)](https://www.mysql.com)

**The core API service for managing tasks, projects, and users at Ghras Al-Ilm Academy.**

</div>

---

## 📖 Overview
This repository contains the **Backend Microservice/API** for the Gheras Task Manager. It provides RESTful endpoints, handles authentication (Sanctum), and manages the database logic using a Service-Oriented Architecture.

**Frontend Repository:** [Gheras Manager UI](https://github.com/MMansy19/Gheras-Manager)

---

## 🚧 Project Modules Status

| Feature | Status | Description |
| :--- | :---: | :--- |
| **Authentication** | ✅ Done | Login, Register, Logout (Sanctum) |
| **User Management** | ✅ Done | CRUD operations, Roles (Admin, Supervisor, Volunteer) |
| **Team Management** | ✅ Done | Create Teams, Assign Users, Pivot Tables |
| **Tasks & Projects** | ⏳ Pending | Disabled in this branch to focus on Core Auth |

---

## 🔌 API Endpoints (Core Features)

### 1. Authentication
| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `POST` | `/api/auth/register` | Register a new user |
| `POST` | `/api/auth/login` | Login and receive API Token |
| `POST` | `/api/auth/logout` | Logout (Revoke Token) |
| `POST` | `/api/auth/refresh` | Refresh API Token |

### 2. Users Management
| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/api/users` | List all users (Supports params: `search`, `role_id`, `status`, `sort_by`) |
| `GET` | `/api/users/{id}` | Get specific user details |
| `PUT` | `/api/users/{id}` | Update user details |
| `DELETE` | `/api/users/{id}` | Delete a user |
| `PATCH` | `/api/users/{id}/status` | Toggle user status (Active/Inactive) |
| `GET` | `/api/users/{id}/profile` | Get current user profile with relations |

### 3. Team Assignment (User-Team)
| Method | Endpoint | Description | Body Parameters |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/users/{id}/teams` | Assign user to a team | `{"team_id": 1}` |
| `DELETE` | `/api/users/{id}/teams` | Remove user from a team | `{"team_id": 1}` |

### 4. Teams Management
| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/api/teams` | List all teams (Supports param: `search`, `limit`) |
| `POST` | `/api/teams` | Create a new team |
| `GET` | `/api/teams/{id}` | Get specific team details |
| `PUT` | `/api/teams/{id}` | Update team details |
| `DELETE` | `/api/teams/{id}` | Delete a team |
| `GET` | `/api/teams/{id}/members` | Get all members of a specific team |

---

## 🚀 Getting Started

### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/ProMoath/Gheras-Manager-Back-End.git
   cd Gheras-Manager-Back-End

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
