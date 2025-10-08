# Budget Planner

A personal finance management application built with Laravel, designed to help you track your income, expenses, and budgets with a clean, intuitive interface.

![Budget Planner Screenshot](https://via.placeholder.com/800x500.png?text=Budget+Planner+Screenshot)

## Features

### Income Management
- Track multiple income sources
- Support for recurring income
- Monthly and yearly overviews
- Category-based organization

### Expense Tracking
- Record and categorize expenses
- Set up recurring expenses
- Visual spending breakdowns
- Receipt attachment (basic)

### Budget Planning
- Set monthly category budgets
- Track spending against budgets
- Visual budget progress indicators
- Budget vs. actual reporting

### Reporting & Analytics
- Income vs. expense visualization
- Category spending analysis
- Monthly/Yearly summaries
- Export functionality

## Tech Stack

### Backend
- **Framework**: Laravel 10.x
- **PHP**: 8.2+
- **Database**: MySQL 8.0
- **Caching**: Redis (optional)

### Frontend
- **UI Framework**: Tailwind CSS
- **Interactivity**: Alpine.js
- **Data Visualization**: Chart.js
- **Templating**: Blade templates

### Development Tools
- **Local Environment**: Docker
- **Testing**: PHPUnit
- **Code Quality**: PHP_CodeSniffer (optional)
- **Version Control**: Git

## Getting Started

### Prerequisites

#### Option 1: Docker (Recommended)
- Docker 20.10+
- Docker Compose 2.0+
- Node.js 16+ and npm (for frontend assets)

#### Option 2: Local Development
- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js 16+ and npm

### Installation

#### Docker Setup (Recommended)

1. Clone the repository:
   ```bash
   git clone git@github.com:blank0810/budget-planner.git
   cd budget-planner
   ```

2. Copy the environment file:
   ```bash
   cp .env.example .env
   ```

3. Start the Docker containers:
   ```bash
   docker-compose up -d
   ```

4. Install PHP dependencies (inside container):
   ```bash
   docker-compose exec app composer install
   ```

5. Generate application key:
   ```bash
   docker-compose exec app php artisan key:generate
   ```

6. Run database migrations:
   ```bash
   docker-compose exec app php artisan migrate
   ```

7. Install and build frontend assets (on host machine):
   ```bash
   npm install
   npm run dev
   ```

8. Access the application at: `http://localhost:8000`

#### Local Development Setup

If you prefer to run PHP and MySQL locally instead of using Docker:

1. Ensure you have PHP 8.2+, Composer, and MySQL 8.0+ installed
2. Clone the repository and set up the environment as shown above
3. Install dependencies:
   ```bash
   composer install
   npm install
   ```
4. Configure your `.env` file with your local database credentials
5. Run migrations:
   ```bash
   php artisan migrate
   ```
6. Start the development server:
   ```bash
   php artisan serve
   npm run dev
   ```
7. Access the application at: `http://localhost:8000`

## Development Philosophy

- **KISS (Keep It Simple, Stupid)**: Prefer the simplest solution that works
- **Single Responsibility**: Each component does one thing well
- **Functional over Perfect**: Working code is better than perfect code
- **Practical Development**: Focus on delivering value over architectural purity
- **Progressive Enhancement**: Start simple and enhance as needed

## License

This project is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
