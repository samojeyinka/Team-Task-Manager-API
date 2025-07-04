# Setup Instructions
1. Clone and Install
`git clone [https://github.com/yourusername/team-task-manager-api.git](https://github.com/samojeyinka/Team-Task-Manager-API.git)`

cd team-task-manager-api
composer install
cp .env.example .env
php artisan key:generate
3. Database Configuration
# Create database
mysql -u root -p
CREATE DATABASE team_task_manager;

# Run migrations and seeders
php artisan migrate
php artisan db:seed
3. Generate Sanctum Secret
php artisan sanctum:install
4. Start Development Server
php artisan serve
5. Test Credentials

create new account in this format - {
    "name": "John Doe",
    "email": "john3@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "member"
}

and login in this format -
{
    "email": "john3@example.com",
    "password": "password123"
}

Deployment Considerations
Production Environment
 Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set up queue workers for background processing
php artisan queue:work --daemon
Environment Variables
Some details are visible in the env,I know its not the best practice but to make it eaiser to access in this case.

SANCTUM_STATEFUL_DOMAINS=your-frontend-domain.com
Key Features Implemented
✅ Authentication & Authorization

Token-based authentication with Laravel Sanctum
Role-based access control (Admin/Member)
Comprehensive authorization policies

✅ Task Management

Full CRUD operations with proper authorization
Status updates for members
Due date validation

✅ Soft Deletes

Soft delete implementation
Restore functionality
Force delete for permanent removal

✅ Excel Import/Export

Robust import with validation and error handling
Export functionality based on user role
Proper error reporting

✅ Clean Architecture

Service layer implementation
Request validation
Resource transformers
Proper separation of concerns

✅ Testing

Feature tests for core functionality
Factory classes for test data
Authorization testing
