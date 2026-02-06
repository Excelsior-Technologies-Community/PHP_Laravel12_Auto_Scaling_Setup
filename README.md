# PHP_Laravel12_Auto_Scaling_Setup

## Overview

PHP Laravel 12 Auto Scaling Setup is a demonstration project that simulates automatic worker scaling based on system load metrics.
The project shows how caching, queues, metrics tracking, and scaling logic can be implemented inside a Laravel application without real cloud infrastructure.

This project is ideal for learning concepts such as load balancing, worker scaling logic, queue monitoring, and performance metrics visualization using Laravel services and dashboards.

---

## Key Features

* Automatic worker scaling simulation
* Load threshold based scale up and scale down
* Metrics dashboard with live data
* Scaling history logs stored in database
* Redis based cache and queue usage
* Service layer architecture
* Feature testing support
* Tailwind UI dashboard

---

## Technology Stack

* PHP 8 or Higher
* Laravel 12
* SQLite / MySQL
* Redis
* Tailwind CSS
* Chart.js
* Composer Packages (Predis, Guzzle)

---

## Project Structure

```
php_laravel12_auto_scaling_setup/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── ScalingController.php
│   ├── Jobs/
│   │   └── ProcessRequest.php
│   └── Services/
│       └── ScalingService.php
├── resources/
│   └── views/
│       ├── dashboard.blade.php
│       └── metrics.blade.php
├── routes/
│   └── web.php
├── database/
│   ├── migrations/
│   │   └── 2024_01_01_create_scaling_logs_table.php
│   └── seeders/
│       └── DatabaseSeeder.php
├── config/
│   └── scaling.php
├── tests/
│   └── Feature/
│       └── ScalingTest.php
└── composer.json
```

---

## Installation Steps

### Step 1 – Create Laravel Project

```bash
composer create-project laravel/laravel php_laravel12_auto_scaling_setup
cd php_laravel12_auto_scaling_setup
composer require predis/predis
composer require guzzlehttp/guzzle --dev
```

Create SQLite database:

```bash
touch database/database.sqlite
```

---

### Step 2 – Configure Environment

Edit `.env` file:

```
APP_NAME="Laravel Auto Scaling Demo"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

SESSION_DRIVER=redis
```

---

### Step 3 – Run Migration

```bash
php artisan migrate
```

---

## Core Components

### Scaling Service

Handles:

* Load simulation
* Auto scaling logic
* Worker adjustment
* Metrics calculation
* Scaling history logging

### Scaling Controller

Responsible for:

* Dashboard rendering
* Load simulation endpoints
* Reset actions
* Metrics JSON API

### Dashboard View

* Live worker count
* Current load visualization
* Memory usage
* Scaling history table
* Chart based gauge display

---

## Routes

```
GET   /                    Dashboard
GET   /simulate/random     Random Load
POST  /simulate/custom     Custom Load
GET   /simulate/pattern    Pattern Load
POST  /reset               Reset System
GET   /metrics             JSON Metrics
```

---

## Database Schema

### scaling_logs Table

* id
* current_workers
* new_workers
* load_percentage
* action
* reason
* created_at
* updated_at

---

## Configuration File

`config/scaling.php` contains:

* max workers
* min workers
* scale thresholds
* cooldown period
* metrics retention

---

## Seeder

Preloads sample scaling history records for dashboard testing.

Run:

```bash
php artisan db:seed
```

---

## Testing

Feature tests validate:

* Dashboard response
* Scale up logic
* Scale down logic

Run tests:

```bash
php artisan test
```
<img width="1838" height="964" alt="image" src="https://github.com/user-attachments/assets/951a2212-adda-47e7-aa59-4cfdcd87392f" />

---

## How Auto Scaling Works

1. System simulates incoming load
2. Service checks current worker count
3. Compares load with thresholds
4. Decides scale up, scale down, or maintain
5. Stores history in database
6. Dashboard updates metrics

---

## Use Cases

* Cloud scaling concept learning
* Queue worker simulations
* DevOps education
* Performance monitoring demos
* Interview demonstration project

---

## Future Enhancements

* Real AWS Auto Scaling integration
* Kubernetes support
* WebSocket live updates
* Advanced analytics charts
* Multi‑node cluster simulation

---

## Requirements

* PHP 8+
* Composer
* Redis Server
* SQLite or MySQL
* Internet Connection

---

## License

MIT License

