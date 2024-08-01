<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    ArticleController,
    ArticleTypeController,
    AttendanceSheetController,
    BankController,
    DocumentTypeController,
    EmployeeController,
    ImageController,
    MachineController,
    MaintenanceSheetController,
    MaintenanceTypeController,
    NotificationController,
    PositionController,
    SupplierController,
    SupplierTypeController,
    UserController,
    WorkingSheetController,
    RoleController,
    PermissionController,
    FileController,
    DashboardController
};
use Tests\Unit\NotificationControllerTest;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1/')->group(function () {
    // Route::get('/send', function () {
    //     $data = [
    //         "machine" => [
    //             "serie_number" => 'AAA123',
    //             "name" => 'Maquina XSY6',
    //         ],
    //         "message" => 'mensaje de prueba - maquina necesita mantenimiento',
    //         "date_send_notification" => date('Y-m-d H:i:s'),
    //         "is_view" => 0
    //     ];
    //     event(new \App\Events\NewNotification($data));
    //     return 'send';
    // });
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);

        Route::group(['middleware' => ['permission:dashboard']], function () {
            Route::get('dashboard', [DashboardController::class, 'index']);
        });
        Route::group(['middleware' => ['permission:notifications']], function () {
            Route::get('notifications', [NotificationController::class, 'index']);
            Route::get('notifications/check', [NotificationController::class, 'check']);
        });

        Route::group(['middleware' => ['permission:users']], function () {
            Route::get('users', [UserController::class, 'index']);
            Route::get('users/{user}', [UserController::class, 'show']);
            //			Route::get('users/{user}/notifications', [UserController::class, 'show_notifications']);
            Route::post('users', [UserController::class, 'store']);
            Route::put('users/{user}', [UserController::class, 'update']);
            Route::delete('users/{user}', [UserController::class, 'destroy']);
        });
        Route::group(['middleware' => ['permission:employees']], function () {
            //
            Route::get('employees', [EmployeeController::class, 'index']);
            Route::get('employees/withoutUser', [EmployeeController::class, 'index_withoutuser']);
            Route::get('employees/{employee}', [EmployeeController::class, 'show']);
            Route::post('employees', [EmployeeController::class, 'store']);
            Route::put('employees/{employee}', [EmployeeController::class, 'update']);
            Route::post('employees/{employee}/generate-safe-credentials', [EmployeeController::class, 'generate_safe_credentials']);
            Route::delete('employees/{employee}', [EmployeeController::class, 'destroy']);
        });
        Route::group(['middleware' => ['permission:attendance-sheets']], function () {
            Route::get('attendance-sheets', [AttendanceSheetController::class, 'index']);
            Route::get('attendance-sheets/{attendanceSheet}', [AttendanceSheetController::class, 'show']);
            Route::post('attendance-sheets', [AttendanceSheetController::class, 'store']);
            Route::put('attendance-sheets/{attendanceSheet}', [AttendanceSheetController::class, 'update']);
            Route::put('attendance-sheets/{attendanceSheet}/check-in', [AttendanceSheetController::class, 'check_in']);
            Route::put('attendance-sheets/{attendanceSheet}/check-out', [AttendanceSheetController::class, 'check_out']);
            Route::put('attendance-sheets/{attendanceSheet}/justified-absence', [AttendanceSheetController::class, 'justified_absence']);
            Route::put('attendance-sheets/{attendanceSheet}/closed', [AttendanceSheetController::class, 'closed']);
        });

        Route::group(['middleware' => ['permission:suppliers']], function () {
            //
            Route::get('suppliers', [SupplierController::class, 'index']);
            Route::get('suppliers/{supplier}', [SupplierController::class, 'show']);
            Route::post('suppliers', [SupplierController::class, 'store']);
            Route::put('suppliers/{supplier}', [SupplierController::class, 'update']);
            Route::delete('suppliers/{supplier}', [SupplierController::class, 'destroy']);
        });
        Route::group(['middleware' => ['permission:articles']], function () {

            Route::get('articles', [ArticleController::class, 'index']);
            Route::get('articles/{article}', [ArticleController::class, 'show']);
            Route::post('articles', [ArticleController::class, 'store']);
            Route::put('articles/{article}', [ArticleController::class, 'update']);
            Route::delete('articles/{article}', [ArticleController::class, 'destroy']);
            //
        });
        Route::group(['middleware' => ['permission:machines']], function () {
            //
            Route::get('machines', [MachineController::class, 'index']);
            Route::get('machines/{machine}', [MachineController::class, 'show']);
            Route::post('machines', [MachineController::class, 'store']);
            Route::put('machines/{machine}', [MachineController::class, 'update']);
            Route::delete('machines/{machine}', [MachineController::class, 'destroy']);
        });

        Route::group(['middleware' => ['permission:maintenance-sheets']], function () {
            Route::get('maintenance-sheets', [MaintenanceSheetController::class, 'index']);
            Route::get('maintenance-sheets/{maintenanceSheet}', [MaintenanceSheetController::class, 'show']);
            Route::post('maintenance-sheets', [MaintenanceSheetController::class, 'store']);
            Route::delete('maintenance-sheets/{maintenanceSheet}', [MaintenanceSheetController::class, 'destroy']);
            Route::get('maintenance-sheets/{maintenanceSheet}/pdf', [MaintenanceSheetController::class, 'show_pdf']);

            //
        });

        Route::group(['middleware' => ['permission:working-sheets']], function () {
            //
            Route::get('working-sheets', [WorkingSheetController::class, 'index']);
            Route::get('working-sheets/{workingSheet}', [WorkingSheetController::class, 'show']);
            Route::post('working-sheets/start', [WorkingSheetController::class, 'start']);
            Route::put('working-sheets/{workingSheet}/pause', [WorkingSheetController::class, 'pause']);
            Route::put('working-sheets/{workingSheet}/restart', [WorkingSheetController::class, 'restart']);
            Route::put('working-sheets/{workingSheet}/stop', [WorkingSheetController::class, 'stop']);
            Route::delete('working-sheets/{workingSheet}', [WorkingSheetController::class, 'destroy']);
            Route::get('working-sheets/{workingSheet}/pdf', [WorkingSheetController::class, 'show_pdf']);
        });


        //        Route::get('images', [ImageController::class, 'index']);
        //        Route::get('images/{image}', [ImageController::class, 'show']);
        Route::post('images', [ImageController::class, 'upload']);
        Route::post('files', [FileController::class, 'upload']);

        Route::group(['middleware' => ['permission:article-types']], function () {
            Route::get('article-types', [ArticleTypeController::class, 'index']);
            Route::get('article-types/{articleType}', [ArticleTypeController::class, 'show']);
            Route::post('article-types', [ArticleTypeController::class, 'store']);
            Route::put('article-types/{articleType}', [ArticleTypeController::class, 'update']);
            Route::delete('article-types/{articleType}', [ArticleTypeController::class, 'destroy']);
            //
        });

        Route::get('maintenance-types', [MaintenanceTypeController::class, 'index']);

        Route::get('positions', [PositionController::class, 'index']);

        Route::get('document-types', [DocumentTypeController::class, 'index']);

        Route::get('banks', [BankController::class, 'index']);

        Route::get('supplier-types', [SupplierTypeController::class, 'index']);


        Route::group(['middleware' => ['permission:reports']], function () {
            Route::post('maintenance-sheets/pdf', [MaintenanceSheetController::class, 'index_pdf']);
            Route::post('attendance-sheets/pdf', [AttendanceSheetController::class, 'index_pdf']);
        });

        Route::group(['middleware' => ['permission:roles']], function () {
            Route::get('roles', [RoleController::class, 'index']);
            Route::get('roles/{role}', [RoleController::class, 'show']);
            Route::post('roles', [RoleController::class, 'store']);
            Route::put('roles/{role}', [RoleController::class, 'update']);
            Route::delete('roles/{role}', [RoleController::class, 'destroy']);
            //
        });
        Route::get('permissions', [PermissionController::class, 'index']);
    });
});
