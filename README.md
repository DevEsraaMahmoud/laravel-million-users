# Nina Assessment - Laravel User Management System

A comprehensive Laravel application built with Inertia.js and Vue.js for managing users with advanced search capabilities and real-time notifications.

## 🚀 Features

### Core Functionality
- **User Management**
  - Create, read, update, and delete users
  - User details with address information
  - Pagination support
  - Optimized database queries

- **Advanced Search**
  - Search across 1 million+ user records
  - Search by name, email, or address fields
  - Debounced search (3 seconds delay)
  - Instant search on Enter key press
  - Search input protection (read-only during requests)

- **Notifications System**
  - Event-driven notifications for user updates
  - Real-time notification bell with dropdown
  - Mark notifications as read
  - Dismiss notifications
  - Toast notifications for new updates

- **User Interface**
  - Modern, responsive design with Tailwind CSS
  - Modal popups for user details
  - Delete confirmation dialogs
  - Toast notifications for success/error messages
  - Loading states and animations

## 🛠️ Tech Stack

### Backend
- **Laravel 12** - PHP Framework
- **MySQL** - Database
- **Inertia.js** - Server-side routing for SPAs
- **Laravel Events & Listeners** - Event-driven architecture

### Frontend
- **Vue.js 3** - Progressive JavaScript Framework
- **Inertia.js** - SPA framework
- **Tailwind CSS** - Utility-first CSS framework
- **Vite** - Build tool

### Architecture
- **Service Layer** - `UserSearchService` for optimized search queries
- **Form Requests** - Request validation classes
- **Resource Controllers** - RESTful API structure
- **Eloquent ORM** - Database abstraction

## 📋 Requirements

- PHP >= 8.2
- Composer
- Node.js >= 18.x
- npm or yarn
- MySQL >= 8.0
- Git

## 🔧 Installation

### 1. Clone the repository

```bash
git clone https://github.com/DevEsraaMahmoud/nina-assessment.git
cd nina-assessment
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install JavaScript dependencies

```bash
npm install
```

### 4. Environment setup

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configure database

Edit `.env` file and set your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nina-assessment
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 6. Run migrations

```bash
php artisan migrate
```

### 7. Seed the database (1 million users)

```bash
php artisan db:seed
```

**Note:** Seeding 1 million users may take several minutes. The seeder uses chunking and memory optimization to handle large datasets efficiently.

### 8. Build frontend assets

For development:
```bash
npm run dev
```

For production:
```bash
npm run build
```

### 9. Start the development server

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## 📁 Project Structure

```
nina-assessment/
├── app/
│   ├── Events/
│   │   └── UserUpdated.php          # Event fired when user is updated
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── NotificationsController.php
│   │   │   ├── SearchController.php
│   │   │   └── UserController.php
│   │   ├── Middleware/
│   │   │   └── HandleInertiaRequests.php
│   │   └── Requests/
│   │       ├── MarkNotificationsAsReadRequest.php
│   │       ├── StoreNotificationRequest.php
│   │       ├── StoreUserRequest.php
│   │       ├── UpdateNotificationRequest.php
│   │       └── UpdateUserRequest.php
│   ├── Listeners/
│   │   └── SendUserUpdateNotification.php
│   ├── Models/
│   │   ├── Address.php
│   │   ├── Notification.php
│   │   └── User.php
│   └── Services/
│       └── UserSearchService.php    # Optimized search service
├── database/
│   ├── migrations/
│   │   ├── create_users_table.php
│   │   ├── create_addresses_table.php
│   │   └── create_notifications_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── UserSeeder.php           # Seeds 1 million users
├── resources/
│   ├── js/
│   │   ├── Components/
│   │   │   ├── DeleteConfirmationModal.vue
│   │   │   ├── NotificationBell.vue
│   │   │   ├── ToastContainer.vue
│   │   │   └── UserDetailsModal.vue
│   │   ├── Layouts/
│   │   │   └── AuthenticatedLayout.vue
│   │   └── Pages/
│   │       ├── Dashboard.vue
│   │       └── Users/
│   │           ├── Create.vue
│   │           ├── Edit.vue
│   │           └── Show.vue
│   └── views/
│       └── app.blade.php
└── routes/
    └── web.php
```

## 🎯 Key Features Explained

### Search Optimization

The search functionality is optimized for large datasets:

- **Service Layer**: `UserSearchService` handles all search logic
- **Query Optimization**: Uses eager loading and selective field queries
- **Indexing**: Database indexes on searchable columns
- **Debouncing**: Prevents excessive API calls

### Notification System

- **Event-Driven**: Uses Laravel Events to trigger notifications
- **Real-time Updates**: Notifications appear when users are updated
- **Dropdown Interface**: Click the bell icon to view all notifications
- **Mark as Read**: Individual or bulk mark as read functionality

### Database Seeding

The seeder handles 1 million records efficiently:

- **Chunking**: Processes records in batches of 1000
- **Memory Management**: Explicit memory cleanup
- **Sequential Emails**: Prevents unique constraint issues
- **Progress Bar**: Visual feedback during seeding

## 🔍 API Routes

### Users
- `GET /dashboard` - Dashboard with user list
- `GET /users` - List all users (paginated)
- `GET /users/create` - Show create form
- `POST /users` - Store new user
- `GET /users/{user}` - Show user details
- `GET /users/{user}/edit` - Show edit form
- `PUT/PATCH /users/{user}` - Update user
- `DELETE /users/{user}` - Delete user

### Search
- `GET /search/users` - Search users (JSON API)

### Notifications
- `GET /notifications` - Get unread notifications
- `POST /notifications` - Create notification
- `POST /notifications/mark-read` - Mark notifications as read
- `GET /notifications/{notification}` - Show notification
- `PUT/PATCH /notifications/{notification}` - Update notification
- `DELETE /notifications/{notification}` - Delete notification

## 🧪 Testing

Run the test suite:

```bash
php artisan test
```

## 📝 Database Schema

### Users Table
- `id` - Primary key
- `first_name` - User's first name
- `last_name` - User's last name
- `email` - Unique email address
- `password` - Hashed password (nullable)
- `timestamps`

### Addresses Table
- `id` - Primary key
- `user_id` - Foreign key to users
- `country` - Country name
- `city` - City name
- `post_code` - Postal code
- `street` - Street address
- `timestamps`

### Notifications Table
- `id` - Primary key
- `user_id` - Foreign key to users (nullable)
- `type` - Notification type
- `message` - Notification message
- `data` - JSON data
- `read` - Boolean read status
- `read_at` - Timestamp when read
- `timestamps`

## 🚀 Performance Optimizations

1. **Database Indexing**: Indexes on searchable columns
2. **Eager Loading**: Prevents N+1 query problems
3. **Query Optimization**: Selective field queries
4. **Chunking**: Large dataset processing in batches
5. **Memory Management**: Explicit cleanup during seeding

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 👤 Author

**DevEsraaMahmoud**

- GitHub: [@DevEsraaMahmoud](https://github.com/DevEsraaMahmoud)

## 🙏 Acknowledgments

- Laravel Framework
- Inertia.js Team
- Vue.js Community
- Tailwind CSS

---

**Note**: This is an assessment project for Nina.care demonstrating advanced Laravel development skills including large dataset handling, event-driven architecture, and modern frontend integration.
