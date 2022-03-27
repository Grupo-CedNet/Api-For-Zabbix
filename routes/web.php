<?php

use App\Models\GroupsRelated;
use App\Models\User;
use App\Models\UsersGroups;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get("tmp", function () {

    // $gp = new UsersGroups();
    // $gp->gname = "administrator";
    // $gp->permissions = '["read","write"]';
    // $gp->save();

    // $related = new GroupsRelated();
    // $related->id_user = 1;
    // $related->id_group_users = 1;
    // $related->save();

    // $user = new User();
    // $user->name = "Administrador";
    // $user->email = "admin@teste.com";
    // $user->password = Hash::make("1234");
    // $user->last_access_login = date('Y-m-d H:i:s');
    // $user->save();
});

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route("app.home");
    }
    return view("index.login")->with(["title" => "Login | " . env("APP_NAME")]);
})->name("login");

Route::post('/login-auth', [App\Http\Controllers\Index\AuthController::class, "logindo"])->name("login.auth");

// Grupo principal do App.
Route::middleware(['auth'])->prefix("app")->group(function () {
    // Home page - Dashbord
    Route::get('/home', [App\Http\Controllers\Admin\IndexController::class, "home"])->name("app.home");

    // Logout app
    Route::post('/logout', [App\Http\Controllers\Index\AuthController::class, "exit"])->name("app.logout");

    // Rotas não privadas a usuários normais.
    Route::get("profile", [App\Http\Controllers\Admin\UserController::class, "profile"])->name('app.profile');
    Route::put("profile-edit", [App\Http\Controllers\Admin\UserController::class, "profileedit"])->name('app.profile.edit');

    // ApisController
    // Chave de usuário apenas.
    Route::put("user-relogin-api", [App\Http\Controllers\Admin\ApisController::class, "update"])->name('app.relogin.api');

    // Grupo de admin/rotas privadas.
    Route::middleware('isadmin')->prefix('admin')->group(function () {

        // ApisController updatekeyfromusers
        Route::get("users-apis-keys", [App\Http\Controllers\Admin\ApisController::class, "index"])->name('admin.apis.list');
        Route::put("user-relogin-api-user", [App\Http\Controllers\Admin\ApisController::class, "updatekeyfromusers"])->name('app.relogin.api.user');
        Route::delete("user-api-key", [App\Http\Controllers\Admin\ApisController::class, "destroy"])->name('admin.apis.delete');

        // Super Admin Group
        Route::middleware("issuperadmin")->group(function () {
            // UserController
            Route::get("users", [App\Http\Controllers\Admin\UserController::class, "listusers"])->name('admin.users');
            Route::get("users-list-one", [\App\Http\Controllers\Admin\UserController::class, "listondeuser"])->name("admin.list.onde.user");
            Route::put("add-new-user", [\App\Http\Controllers\Admin\UserController::class, "adduser"])->name("admin.new.user");
            Route::put("user-edit", [App\Http\Controllers\Admin\UserController::class, "edituser"])->name('admin.user.edit');
            Route::put("user-update-permissions", [App\Http\Controllers\Admin\UserController::class, "updatepermissions"])->name('admin.user.update.permissions');
            Route::delete("user-delete", [App\Http\Controllers\Admin\UserController::class, "deleteuser"])->name('admin.user.delete');

            // UsersGroupController
            Route::get("group-users", [App\Http\Controllers\Admin\UsersGroupController::class, "index"])->name('admin.users.group');
            Route::get("group-users-edit", [App\Http\Controllers\Admin\UsersGroupController::class, "show"])->name('admin.users.group.store');
            Route::put("group-users-update", [App\Http\Controllers\Admin\UsersGroupController::class, "create"])->name('admin.users.group.create');
            Route::patch("group-users-update", [App\Http\Controllers\Admin\UsersGroupController::class, "update"])->name('admin.users.group.update');
            Route::delete("group-users-delete", [App\Http\Controllers\Admin\UsersGroupController::class, "destroy"])->name('admin.users.group.delete');
        });

    });

});