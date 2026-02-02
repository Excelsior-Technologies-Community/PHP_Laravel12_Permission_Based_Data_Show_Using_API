# PHP_Laravel12_Permission_Based_Data_Show_Using_API

## About Project

This project demonstrates **Role & Permission Based Data Access using Laravel 12 APIs**.
It uses **Laravel Sanctum** for API authentication and **Spatie Laravel Permission** package for managing roles and permissions.

The system contains **two roles (Admin & User)** and APIs return data based on the logged‑in user's role and permissions.

---

## Features

* Laravel 12 REST APIs
* Sanctum Token Based Authentication
* Role & Permission Management (Spatie)
* Admin & User Role Separation
* Secure Role Wise API Responses

---

## Project Folder Name

```
PHP_Laravel12_Permission_Based_Data_Show_Using_API
```

---

## Step 1: Install Fresh Laravel 12 Application

Open Terminal / Command Prompt and run:

```
composer create-project laravel/laravel:^12.0 PHP_Laravel12_Permission_Based_Data_Show_Using_API
```

Move into the project directory:

```
cd PHP_Laravel12_Permission_Based_Data_Show_Using_API
```

Generate application key:

```
php artisan key:generate
```

---

## Step 2: Configure Database (.env)

Open the `.env` file and update database credentials:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=permission_api
DB_USERNAME=root
DB_PASSWORD=
```

Save the file and run migrations:

```
php artisan migrate
```

---

## Step 3: Install Required Packages

### Install Laravel Sanctum

```
composer require laravel/sanctum
```

Publish Sanctum configuration:

```
php artisan vendor:publish --provider="Laravel\\Sanctum\\SanctumServiceProvider"
```

Run migrations:

```
php artisan migrate
```

### Install Spatie Role & Permission Package

```
composer require spatie/laravel-permission
```

Publish config and migrations:

```
php artisan vendor:publish --provider="Spatie\\Permission\\PermissionServiceProvider"
```

Run migrations:

```
php artisan migrate
```

---

## Step 4: Update User Model

Open file:

```
app/Models/User.php
```
```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];
}
```



## Step 5: Create Roles and Permissions
```php
Open Tinker:

```
php artisan tinker
```

Create roles:

```php
use Spatie\Permission\Models\Role;

Role::create(['name' => 'Admin']);
Role::create(['name' => 'User']);
```

Create permissions:

```php
use Spatie\Permission\Models\Permission;

Permission::create(['name' => 'view_admin_data']);
Permission::create(['name' => 'view_user_data']);
```

Assign permissions to roles:

```php
$admin = Role::findByName('Admin');
$admin->givePermissionTo(['view_admin_data','view_user_data']);

$user = Role::findByName('User');
$user->givePermissionTo('view_user_data');
```

---

## Step 6: Create Users and Assign Roles

Create users using tinker or database seeder:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $user  = Role::firstOrCreate(['name' => 'User']);

        $p1 = Permission::firstOrCreate(['name' => 'view_admin_data']);
        $p2 = Permission::firstOrCreate(['name' => 'view_user_data']);

        $admin->syncPermissions([$p1, $p2]);
        $user->syncPermissions([$p2]);
    }
}

```

---

## Step 7: Create Authentication Controller

Create controller:

```
php artisan make:controller Api/AuthController
```
```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = Auth::user();

        return response()->json([
            'token' => $user->createToken('api-token')->plainTextToken,
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ]);
    }
}

```

Handles login and token generation using Sanctum.

---

## Step 8: Create Role Wise Data Controller

```
php artisan make:controller Api/RoleWiseDataController
```
```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoleWiseDataController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->can('view_admin_data')) {
            return response()->json([
                'data' => 'This is ADMIN data'
            ]);
        }

        if ($user->can('view_user_data')) {
            return response()->json([
                'data' => 'This is USER data'
            ]);
        }

        return response()->json([
            'message' => 'Unauthorized'
        ], 403);
    }
}

```
This controller checks role & permission and returns data accordingly.

---

## Step 9: Define API Routes

Open file:

```
routes/api.php
```
```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleWiseDataController;
use App\Http\Controllers\Api\Admin\RolePermissionController;


Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/role-wise-data', [RoleWiseDataController::class, 'index']);

    // Admin side
    Route::get('/admin/roles', [RolePermissionController::class, 'roles']);
    Route::post('/admin/assign-permission', [RolePermissionController::class, 'assignPermission']);
});


Route::get('/check', function () {
    return response()->json(['status' => 'API WORKING']);
});

```

## Step 10: Run Laravel Application

Start server:

```
php artisan serve
```

Server will start at:

```
http://127.0.0.1:8000
```
<img width="1193" height="570" alt="image" src="https://github.com/user-attachments/assets/3cd5bb7d-bfae-44f5-855b-c3009ce02c7a" />

---

## Step 11: Test APIs Using Postman

### Login API

**POST** `/api/login`
<img width="1240" height="549" alt="image" src="https://github.com/user-attachments/assets/83751935-504b-4f0d-b20b-3e67e46ef91f" />


### Role Wise Data API

**GET** `/api/role-wise-data`

Headers:

```
Authorization: Bearer 1|s0xXjmnxz0fi28BbLogdfRdAiPbWcPdyUjfIC2087c48c939
Accept: application/json
```
<img width="1213" height="464" alt="image" src="https://github.com/user-attachments/assets/6b178481-e6d6-4530-ac1a-7b3cdffcacb6" />

## Project Structure

```
PHP_Laravel12_Permission_Based_Data_Show_Using_API
├── app/
│   ├── Http/Controllers/Api/
│   │   ├── AuthController.php
│   │   └── RoleWiseDataController.php
│   ├── Models/User.php
│
├── routes/
│   └── api.php
│
├── database/
│   └── migrations/
│
├── storage/
│   └── logs/
│       └── laravel.log
│
├── .env
└── artisan
```

---

## Conclusion
```php
This project is a complete **Laravel 12 Role & Permission Based API example** suitable for real‑world applications such as admin panels, dashboards, and secure APIs.
```
