<div align="center">

# 🧩 Magiya TaskHub

**A role-aware team task management system built with Laravel 12 & Livewire 4**

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-4-FB70A9?style=for-the-badge&logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-3-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-3-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white)](https://alpinejs.dev)
[![License](https://img.shields.io/badge/License-MIT-22c55e?style=for-the-badge)](LICENSE)

<br/>

> Manage tasks, teams, and deadlines — with intelligent role-scoped dashboards, a Kanban board, real-time notifications, and a full activity audit trail. No separate API. No SPA overhead. Just Laravel.

<br/>

[✨ Features](#-features) · [🚀 Quick Start](#-quick-start) · [🗂 Project Structure](#-project-structure) · [🧪 Testing](#-testing) · [⚙️ Design Decisions](#️-design-decisions)

</div>

---

## 📸 Screenshots

<div align="center">
<table>
  <tr>
    <td align="center"><b>Dashboard</b></td>
    <td align="center"><b>Kanban Board</b></td>
  </tr>
  <tr>
    <td><img src="[https://placehold.co/480x280?text=Dashboard+View](https://github.com/it21387494samitha/Task_Hub/blob/main/magiya-taskhub/public/screenshots/analytics.png?raw=true)" alt="Dashboard" width="480"/></td>
    <td><img src="https://placehold.co/480x280?text=Kanban+Board+View" alt="Kanban Board" width="480"/></td>
  </tr>
  <tr>
    <td align="center"><b>Task Detail</b></td>
    <td align="center"><b>Admin Panel</b></td>
  </tr>
  <tr>
    <td><img src="https://placehold.co/480x280?text=Task+Detail+View" alt="Task Detail" width="480"/></td>
    <td><img src="https://placehold.co/480x280?text=Admin+Panel+View" alt="Admin Panel" width="480"/></td>
  </tr>
</table>

</div>

---

## ✨ Features

<table>
  <tr>
    <td width="50%" valign="top">

### 📋 Task Management
- Full CRUD with soft deletes
- Kanban board view with status lanes
- Comments & file attachments per task
- Task templates for recurring work
- Due dates with overdue detection
- Block reason tracking
- Time tracking: `started_at`, `completed_at`, `blocked_at`

**Statuses**

![Todo](https://img.shields.io/badge/To_Do-6b7280?style=flat-square)
![In Progress](https://img.shields.io/badge/In_Progress-3b82f6?style=flat-square)
![Done](https://img.shields.io/badge/Done-22c55e?style=flat-square)
![Blocked](https://img.shields.io/badge/Blocked-ef4444?style=flat-square)

**Priorities**

![Low](https://img.shields.io/badge/Low-64748b?style=flat-square)
![Medium](https://img.shields.io/badge/Medium-f59e0b?style=flat-square)
![High](https://img.shields.io/badge/High-f97316?style=flat-square)
![Critical](https://img.shields.io/badge/Critical-dc2626?style=flat-square)

**Tags**

![Prod Issue](https://img.shields.io/badge/Prod_Issue-b91c1c?style=flat-square)
![Hotfix](https://img.shields.io/badge/Hotfix-ea580c?style=flat-square)
![Release Blocker](https://img.shields.io/badge/Release_Blocker-7c3aed?style=flat-square)
![Tech Debt](https://img.shields.io/badge/Tech_Debt-0369a1?style=flat-square)

</td>
    <td width="50%" valign="top">

### 🔔 Notifications & Activity
- In-app notification bell with unread badge
- Notifications on task assignment & deletion
- Per-user notification preference controls
- Full activity audit log
- Every lifecycle event recorded: created, updated, assigned, deleted

### 📊 Dashboard & Analytics
- Role-scoped statistics
- Task counts by status
- Overdue task detection
- Tasks-per-user breakdown
  - Leaders & Admins: see all developers
  - Developers: see only their own stats

### 🛡️ Admin Panel
- User management (create, edit, deactivate)
- Team management
- Admin-specific analytics dashboard

</td>
  </tr>
</table>

---

## 👥 Role-Based Access Control

<div align="center">

| Role | Badge | Capabilities |
|------|-------|-------------|
| **Admin** | ![Admin](https://img.shields.io/badge/Admin-FF2D20?style=flat-square&logo=laravel&logoColor=white) | Full access — users, teams, all tasks, system settings |
| **Team Leader** | ![Team Leader](https://img.shields.io/badge/Team_Leader-3b82f6?style=flat-square) | Manage team tasks, org-wide stats, assign developers |
| **Developer** | ![Developer](https://img.shields.io/badge/Developer-22c55e?style=flat-square) | View & update only their own assigned tasks |

</div>

---

## 🛠 Tech Stack

<div align="center">

| Layer | Technology | Purpose |
|-------|-----------|---------|
| 🏗️ Framework | [Laravel 12](https://laravel.com) | Core application framework |
| ⚡ Reactive UI | [Livewire 4](https://livewire.laravel.com) | Server-driven dynamic components |
| 🎨 Styling | [Tailwind CSS 3](https://tailwindcss.com) | Utility-first CSS framework |
| 🏔️ JS Interactivity | [Alpine.js 3](https://alpinejs.dev) | Lightweight JS for UI behavior |
| 🔐 Auth | [Laravel Breeze](https://laravel.com/docs/starter-kits) | Authentication scaffolding |
| 📦 Build Tool | [Vite](https://vitejs.dev) | Fast asset bundling & HMR |
| 🧪 Testing | [PestPHP 4](https://pestphp.com) | Expressive test framework |

</div>

---

## 🚀 Quick Start

### Requirements

| Requirement | Version |
|-------------|---------|
| PHP | `>= 8.2` |
| Composer | latest |
| Node.js | `>= 18` |
| Database | MySQL / PostgreSQL / SQLite |

### ⚡ One-Command Setup

```bash
composer run setup
```

This single command will:
1. `composer install` — PHP dependencies
2. Copy `.env.example` → `.env`
3. Generate application key
4. Run all database migrations
5. `npm install` — JS dependencies
6. `npm run build` — compile assets

### 🔧 Manual Setup

```bash
# 1. Clone the repository
git clone https://github.com/your-username/magiya-taskhub.git
cd magiya-taskhub

# 2. Install dependencies
composer install
npm install

# 3. Environment configuration
cp .env.example .env
php artisan key:generate

# 4. Configure DB_* variables in .env, then:
php artisan migrate

# 5. (Optional) Seed demo data
php artisan db:seed

# 6. Build assets
npm run build
```

---

## 💻 Running Locally

Start everything with one command:

```bash
composer run dev
```

This concurrently launches:

| Service | Command | URL |
|---------|---------|-----|
| 🌐 Web Server | `php artisan serve` | http://localhost:8000 |
| 🔄 Queue Worker | `php artisan queue:listen` | — |
| 📋 Log Watcher | `php artisan pail` | — |
| ⚡ Vite Dev Server | `npm run dev` | http://localhost:5173 |

---

## 🧪 Testing

```bash
# Run all tests
composer run test

# Artisan
php artisan test

# Pest directly
./vendor/bin/pest

# Specific test file
./vendor/bin/pest tests/Feature/TaskTest.php

# With coverage
./vendor/bin/pest --coverage
```

---

## 🗂 Project Structure

<details>
<summary><b>📂 Click to expand the full structure</b></summary>

```
app/
├── 📁 Enums/
│   ├── Role.php              # Admin | Team Leader | Developer
│   ├── TaskStatus.php        # Todo | In Progress | Done | Blocked
│   ├── TaskPriority.php      # Low | Medium | High | Critical
│   ├── TaskTag.php           # Prod Issue | Hotfix | Release Blocker | Tech Debt
│   └── TemplateType.php
│
├── 📁 Events/
│   ├── TaskCreated.php
│   ├── TaskUpdated.php
│   ├── TaskAssigned.php
│   └── TaskDeleted.php
│
├── 📁 Http/
│   ├── Controllers/           # Thin controllers (auth + profile only)
│   ├── Middleware/
│   └── Requests/              # Form request validation
│
├── 📁 Listeners/              # Event → Activity log writers
│
├── 📁 Livewire/
│   ├── Admin/
│   │   ├── Dashboard.php
│   │   ├── UserManagement.php
│   │   └── TeamManagement.php
│   ├── Tasks/
│   │   ├── Board.php          # Kanban board
│   │   ├── Index.php          # Task list
│   │   ├── Create.php
│   │   ├── Edit.php
│   │   └── Show.php
│   ├── Dashboard.php          # Role-aware main dashboard
│   ├── NotificationBell.php
│   └── NotificationSettings.php
│
├── 📁 Models/
│   ├── Task.php
│   ├── User.php
│   ├── Team.php
│   ├── Comment.php
│   ├── Attachment.php
│   ├── ActivityLog.php
│   ├── TaskTemplate.php
│   └── NotificationSetting.php
│
├── 📁 Notifications/          # Laravel notification classes
├── 📁 Policies/               # Authorization policies
├── 📁 Repositories/           # Data access layer
└── 📁 Services/
    └── StatsService.php       # Role-scoped dashboard statistics
```

</details>

---

## ⚙️ Design Decisions

<details>
<summary><b>🏛️ Service Layer</b></summary>

Business logic (e.g., role-scoped stats) lives in dedicated `Services/` classes, keeping Livewire components and controllers lean. This mirrors the controller-service pattern common in MERN/NestJS apps.

</details>

<details>
<summary><b>🗄️ Repository Pattern</b></summary>

Database queries are abstracted behind repositories for testability and separation of concerns. Swapping query implementations doesn't touch business logic.

</details>

<details>
<summary><b>⚡ Livewire over SPA</b></summary>

All reactive UI is handled by Livewire — no Vue, React, or separate frontend build to maintain. The result is full-stack interactivity with server-rendered HTML and zero JSON endpoints.

</details>

<details>
<summary><b>📡 Event-Driven Activity Logging</b></summary>

Task lifecycle events (`TaskCreated`, `TaskUpdated`, `TaskAssigned`, `TaskDeleted`) fire Laravel Events consumed by Listeners. Activity log entries are written without polluting model methods or service logic.

</details>

<details>
<summary><b>🔢 PHP 8.2 Backed Enums</b></summary>

All status, priority, role, and tag values use native PHP backed enums for type safety, IDE autocompletion, and Tailwind color mapping across the entire application — no magic strings anywhere.

</details>

---

## 📄 License

This project is open-sourced software licensed under the [MIT License](LICENSE).

---

<div align="center">

Made with ❤️ using [Laravel](https://laravel.com) · [Livewire](https://livewire.laravel.com) · [Tailwind CSS](https://tailwindcss.com)

</div>
