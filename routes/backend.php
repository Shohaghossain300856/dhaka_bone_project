<?php

use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\ProfileController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\SettingController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\BoneCaseController;
use App\Http\Controllers\Backend\BoneDetailsController;
use App\Http\Controllers\Backend\BoneImageController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth','checkUserStatus'])->group(function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'home')->name('dashboard');
        Route::get('/dashboard/bones', [DashboardController::class, 'create'])->name('dashboard.bones');

      Route::get('/dashboard/bone-details/{id}', 'boneDetails')->name('dashboard.bone.details');
    });

    Route::get('/getuser_id', function () {
        return response()->json([
            'user_id' => Auth::id()
        ]);
    });
    // setting routes;
    Route::middleware(['permission:setting-index|setting-create|setting-edit|setting-delete'])->group(function () {

//        Route::resource('setting',SettingController::class);
        Route::controller(SettingController::class)->group(function () {
            Route::get('setting/{slug}', 'index')->name('setting.index');
            Route::post('setting-store', 'store')->name('setting.store');
        });

    });

    // profile routes;
    Route::controller(ProfileController::class)->group(function () {
        Route::get('profile', 'profile')->name('profile');
        Route::post('profile', 'profileUpdate')->name('profile.update');
        Route::post('user-status-update', 'profileChangeStatus')->name('user-status-update')->middleware('permission:profile-status-update');
        //change password
        Route::get('change-password', 'passwordChange')->name('change-password');
        Route::post('check-old-password', 'oldPasswordCheck')->name('check-old-password');

        Route::get('new-password', 'newPassword')->name('new-password');
        Route::post('password-update', 'passwordUpdate')->name('password-update');
    });
    Route::resource('project',ProjectController::class);//no need this

    Route::middleware(['permission:user-index|user-create|user-edit|user-delete'])->group(function () {
        Route::resource('user',UserController::class);
    });
    //confidential routes
    Route::middleware(['permission:role-index|role-create|role-edit|role-delete'])->group(function () {
        Route::resource('role',RoleController::class);
    });
    Route::middleware(['permission:permission-index|permission-create|permission-edit|permission-delete'])->group(function () {
        Route::resource('permission',PermissionController::class);
    });

    

    Route::resource('bone-cases-create',BoneCaseController::class);
    Route::resource('bone-details-create',BoneDetailsController::class);
   Route::post('bids-create', [BoneCaseController::class, 'bidsCreate']);

});

?>
