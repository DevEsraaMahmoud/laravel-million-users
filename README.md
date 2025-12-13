# Laravel Million Users — High-Load Search Engine

A backend-focused Laravel application designed to handle **large datasets (1M+ users)** with an emphasis on **search performance, clean architecture, and event-driven design**.

This project was built to simulate real production challenges rather than a UI-heavy demo.

---

## 🎯 Project Goals

- Design a scalable user management system
- Optimize search performance over large datasets
- Apply clean architecture principles (Services, Repositories)
- Implement event-driven notifications
- Balance performance, simplicity, and maintainability

---

## ✨ Key Features

### 🔍 Advanced Search
- MySQL **FULLTEXT search** over 1M+ users
- Automatic detection of email queries (exact match vs fulltext)
- Cached search results with controlled TTL
- Boolean mode search for complex queries
- Debounced search to reduce unnecessary requests

### 👤 User Management
- Full CRUD operations with addresses
- Database transactions for data consistency
- Strong request validation layer
- Paginated results to control memory usage

### 🔔 Notifications
- Event-driven notification system
- Queued listeners for non-blocking execution
- Retry mechanism with backoff strategy
- Real-time unread notifications counter

---

## 🏗️ Architecture Overview

- **Repository pattern** for data access
- **Service layer** for business logic (search & caching)
- **Event-driven design** for notifications
- Clear separation between:
  - Domain logic
  - Infrastructure (cache, DB)
  - Presentation layer (Inertia)

The architecture is intentionally kept **monolithic but scalable**, mirroring many real-world Laravel systems.

---

## 🧰 Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Inertia.js + Vue 3
- **Database**: MySQL (SQLite supported locally)
- **Async**: Laravel Queues
- **Caching**: Laravel Cache (Redis-ready)
- **Styling**: TailwindCSS

---

## 🚀 Getting Started

### Installation

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate
php artisan migrate
```

Database Seeding (1M Users)
```bash
php artisan db:seed
```

### Seeding is optimized using:

- Chunked bulk inserts
- Disabled query logging
- Controlled memory usage

### 🔍 Search & Performance Notes

- FULLTEXT indexes are used instead of external search engines

- Queries were analyzed using EXPLAIN

- Indexes added based on real execution plans

- Caching is applied only where measurable benefits exist

### 📌 Design Decisions & Trade-offs

- FULLTEXT over Elasticsearch to focus on core database optimization

- Inertia.js to keep a backend-driven architecture

- Queued notifications to avoid blocking user actions

- Minimal UI to prioritize backend correctness and clarity

### 🔮 Possible Improvements

- Introduce dedicated search engines (Meilisearch / Elasticsearch)

Add read replicas for high-load scenarios

- Improve observability (metrics, tracing)

- Load testing with realistic traffic patterns

### 📄 License

MIT License — for learning and portfolio purposes.

### 👤 Author

### Esraa Mahmoud
Senior Full-Stack Engineer (Laravel & Vue)

### GitHub: https://github.com/DevEsraaMahmoud
