# Project Setup and Overview

### Prerequisites
- Docker
- Docker Compose

### Setup

1. Build and run the Docker containers:
   ```bash
   docker-compose up -d --build
2. Run the database migrations:
   ```bash
   docker-compose exec app php artisan migrate
 Project Details
- API Documentation: Swagger is set up for this project, accessible at http://localhost:8080/api/documentation
- Data Seeding: During the migration, the database is seeded with countries and currencies data, filling the countries and currencies tables automatically.
- Web Service: An API endpoint is provided to retrieve a list of countries along with their currencies.
- Database Optimization: Necessary indexes are applied to improve query performance.
- Design Pattern: The project uses the Repository Pattern for better code organization and maintainability.
- Filtering and Sorting: Comprehensive filters and sorting capabilities are implemented for users by country and currency.
- SQL Optimization: Query design is optimized, focusing on minimizing the number of joins and enhancing database efficiency.
- Testing: The project includes 5 unit tests and 5 feature tests for robust validation.
- Dockerization: The entire application is Dockerized for simplified deployment.
