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
