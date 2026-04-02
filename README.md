# Business Listing & Rating System

A complete Business Listing and Rating System built with Core PHP, MySQL, jQuery, AJAX, Bootstrap 5, and the Raty jQuery plugin.

## Features

- **Business CRUD Operations**: Add, Edit, Delete businesses with AJAX (no page refresh)
- **Rating System**: Submit and update ratings using the Raty jQuery plugin
- **Half-Star Support**: Rating scale 0-5 with half-star precision
- **Real-Time Updates**: All changes update the UI dynamically without page refresh
- **Bootstrap Modals**: Clean modal interfaces for all forms
- **Responsive Design**: Works on all screen sizes

## Tech Stack

- **Backend**: Core PHP (no framework)
- **Database**: MySQL
- **Frontend**: jQuery, Bootstrap 5, AJAX
- **Rating Plugin**: Raty jQuery Plugin

## Requirements

### Option 1: Docker (Recommended)
- Docker & Docker Compose installed

### Option 2: Manual Setup
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Web browser with JavaScript enabled

---

## Installation

### Option 1: Docker Setup (Recommended)

#### 1. Clone or Download

```bash
git clone <repository-url>
cd business-listing
```

#### 2. Start Docker Containers

```bash
docker-compose up -d --build
```

#### 3. Access the Application

Open your browser and navigate to:
```
http://localhost:8080
```

The database will be automatically initialized with sample data.

#### Docker Commands

```bash
# Start containers
docker-compose up -d

# Stop containers
docker-compose down

# View logs
docker-compose logs -f

# Rebuild containers
docker-compose up -d --build

# Stop and remove volumes (reset database)
docker-compose down -v
```

#### Docker Services

| Service | Container | Port |
|---------|-----------|------|
| PHP Apache | business-listing-app | 8080 |
| MySQL 8.0 | business-listing-db | 3307 |

#### Database Credentials (Docker)

| Key | Value |
|-----|-------|
| Host | mysql (container name) |
| Database | business_listing |
| User | root |
| Password | root_password |

---

### Option 2: Manual Setup

#### 1. Clone or Download

```bash
git clone <repository-url>
# or download and extract the ZIP file
```

#### 2. Database Setup

1. Open phpMyAdmin or MySQL command line
2. Import the `database.sql` file:
   - **phpMyAdmin**: Go to Import tab and select `database.sql`
   - **Command line**: `mysql -u root -p < database.sql`

#### 3. Configuration

Edit `config.php` and update database credentials:

```php
define('DB_HOST', 'localhost');  // Your database host
define('DB_USER', 'root');       // Your database username
define('DB_PASS', '');           // Your database password
define('DB_NAME', 'business_listing'); // Database name
```

#### 4. Web Server Setup

Place the project folder in your web server's document root:

- **XAMPP**: `C:\xampp\htdocs\business-listing\`
- **WAMP**: `C:\wamp64\www\business-listing\`
- **MAMP**: `/Applications/MAMP/htdocs/business-listing/`
- **Linux Apache**: `/var/www/html/business-listing/`

#### 5. Access the Application

Open your browser and navigate to:
```
http://localhost/business-listing/
```

## Project Structure

```
business-listing/
├── api/
│   ├── business.php    # Business CRUD API endpoints
│   └── rating.php      # Rating API endpoints
├── assets/
│   ├── css/
│   │   └── style.css   # Custom styles
│   └── js/
│       └── app.js      # Main JavaScript application
├── config.php          # Database configuration
├── database.sql        # Database schema and sample data
├── docker-compose.yml  # Docker orchestration
├── Dockerfile          # PHP container definition
├── index.php           # Main application page
└── README.md           # This file
```

## API Endpoints

### Business API (`api/business.php`)

| Action | Method | Description |
|--------|--------|-------------|
| `?action=list` | GET | Get all businesses with average ratings |
| `?action=get&id={id}` | GET | Get single business by ID |
| `?action=create` | POST | Create new business |
| `?action=update&id={id}` | POST | Update existing business |
| `?action=delete&id={id}` | POST | Delete business |

### Rating API (`api/rating.php`)

| Action | Method | Description |
|--------|--------|-------------|
| `?action=submit&business_id={id}` | POST | Submit or update rating |
| `?action=get&business_id={id}` | GET | Get all ratings for a business |
| `?action=average&business_id={id}` | GET | Get average rating for a business |

## Rating Logic

- If an email or phone already exists for a business, the existing rating is updated/overwritten
- If it's a new user, a new rating is inserted
- Average rating is recalculated in real-time after each submission
- Supports half-star ratings (e.g., 3.5, 4.5)

## Database Schema

### businesses
| Column | Type | Description |
|--------|------|-------------|
| id | INT(11) | Primary key, auto-increment |
| name | VARCHAR(255) | Business name |
| address | TEXT | Business address |
| phone | VARCHAR(50) | Phone number |
| email | VARCHAR(255) | Email address |
| created_at | TIMESTAMP | Creation timestamp |

### ratings
| Column | Type | Description |
|--------|------|-------------|
| id | INT(11) | Primary key, auto-increment |
| business_id | INT(11) | Foreign key to businesses |
| name | VARCHAR(255) | Reviewer name |
| email | VARCHAR(255) | Reviewer email |
| phone | VARCHAR(50) | Reviewer phone |
| rating | DECIMAL(2,1) | Rating value (0-5) |
| created_at | TIMESTAMP | Creation timestamp |

## Dependencies (CDN)

- Bootstrap 5.3.0
- jQuery 3.7.0
- jQuery Raty 3.1.1
- Bootstrap Icons 1.10.0

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## License

This project is created for machine test evaluation purposes.
