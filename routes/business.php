<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Business;

// ─── Team Members ─────────────────────────────────────────────────────────────
Route::get('/team',                              [Business\TeamMemberController::class, 'index'])->name('team.index');
Route::get('/team/invite',                       [Business\TeamMemberController::class, 'invite'])->name('team.invite');
Route::post('/team/invite',                      [Business\TeamMemberController::class, 'inviteStore'])->name('team.invite.store');

// ─── Roles & Permissions (must come BEFORE /team/{member} wildcard) ──────────
Route::get('/team/terminated',                   [Business\TeamMemberController::class, 'terminated'])->name('team.terminated');
Route::get('/team/roles',                        [Business\TeamRoleController::class, 'index'])->name('team.roles.index');
Route::get('/team/roles/create',                 [Business\TeamRoleController::class, 'create'])->name('team.roles.create');
Route::post('/team/roles',                       [Business\TeamRoleController::class, 'store'])->name('team.roles.store');
Route::get('/team/roles/{role}/edit',            [Business\TeamRoleController::class, 'edit'])->name('team.roles.edit');
Route::put('/team/roles/{role}',                 [Business\TeamRoleController::class, 'update'])->name('team.roles.update');
Route::delete('/team/roles/{role}',              [Business\TeamRoleController::class, 'destroy'])->name('team.roles.destroy');

// ─── Team Member detail/edit/update/destroy ───────────────────────────────────
Route::get('/team/{member}',                     [Business\TeamMemberController::class, 'show'])->name('team.show');
Route::get('/team/{member}/edit',                [Business\TeamMemberController::class, 'edit'])->name('team.edit');
Route::put('/team/{member}',                     [Business\TeamMemberController::class, 'update'])->name('team.update');
Route::post('/team/{member}/terminate',           [Business\TeamMemberController::class, 'terminate'])->name('team.terminate');
Route::post('/team/{member}/role',               [Business\TeamMemberController::class, 'assignRole'])->name('team.assign-role');
Route::post('/team/{member}/services',           [Business\TeamServiceController::class, 'sync'])->name('team.services.sync');

// ─── Job Dispatch ─────────────────────────────────────────────────────────────
Route::get('/dispatch',                          [Business\DispatchController::class, 'index'])->name('dispatch.index');
Route::post('/dispatch/{job}/assign',            [Business\DispatchController::class, 'assign'])->name('dispatch.assign');
Route::put('/dispatch/{assignment}/reassign',    [Business\DispatchController::class, 'reassign'])->name('dispatch.reassign');
Route::post('/dispatch/{assignment}/unassign',   [Business\DispatchController::class, 'unassign'])->name('dispatch.unassign');
Route::get('/dispatch/{job}/suggestions',        [Business\DispatchController::class, 'suggestions'])->name('dispatch.suggestions');

// ─── Schedule & Route ─────────────────────────────────────────────────────────
Route::get('/schedule',                          [Business\ScheduleController::class, 'index'])->name('schedule.index');
Route::get('/schedule/{date}',                   [Business\ScheduleController::class, 'show'])->name('schedule.show');
Route::post('/schedule/optimize',                [Business\ScheduleController::class, 'optimize'])->name('schedule.optimize');
Route::post('/schedule/{schedule}/publish',      [Business\ScheduleController::class, 'publish'])->name('schedule.publish');
Route::post('/schedule/reorder',                  [Business\ScheduleController::class, 'reorder'])->name('schedule.reorder');

// ─── Attendance ───────────────────────────────────────────────────────────────
Route::get('/attendance',                        [Business\AttendanceController::class, 'index'])->name('attendance.index');
Route::get('/attendance/{date}',                 [Business\AttendanceController::class, 'show'])->name('attendance.show');
Route::get('/attendance/{member}/history',       [Business\AttendanceController::class, 'memberHistory'])->name('attendance.member-history');
Route::post('/attendance/{attendance}/verify',   [Business\AttendanceController::class, 'verify'])->name('attendance.verify');

// ─── Live Location ────────────────────────────────────────────────────────────
Route::get('/location',                          [Business\LocationController::class, 'live'])->name('location.live');
Route::get('/location/{member}/history',         [Business\LocationController::class, 'memberHistory'])->name('location.member-history');

// ─── Equipment & Tools ────────────────────────────────────────────────────────
Route::get('/equipment',                         [Business\EquipmentController::class, 'index'])->name('equipment.index');
Route::get('/equipment/create',                  [Business\EquipmentController::class, 'create'])->name('equipment.create');
Route::post('/equipment',                        [Business\EquipmentController::class, 'store'])->name('equipment.store');
Route::get('/equipment/{equipment}/edit',        [Business\EquipmentController::class, 'edit'])->name('equipment.edit');
Route::put('/equipment/{equipment}',             [Business\EquipmentController::class, 'update'])->name('equipment.update');
Route::post('/equipment/{equipment}/assign',     [Business\EquipmentController::class, 'assign'])->name('equipment.assign');
Route::post('/equipment/{equipment}/return',     [Business\EquipmentController::class, 'returnEquipment'])->name('equipment.return');
Route::post('/equipment/{equipment}/lost',       [Business\EquipmentController::class, 'reportLost'])->name('equipment.lost');
Route::get('/equipment/{equipment}/maintenance', [Business\EquipmentMaintenanceController::class, 'index'])->name('equipment.maintenance.index');
Route::post('/equipment/{equipment}/maintenance',[Business\EquipmentMaintenanceController::class, 'store'])->name('equipment.maintenance.store');

// ─── Inventory ────────────────────────────────────────────────────────────────
Route::get('/inventory',                         [Business\InventoryController::class, 'index'])->name('inventory.index');
Route::get('/inventory/low-stock',               [Business\InventoryController::class, 'lowStock'])->name('inventory.low-stock');
Route::get('/inventory/create',                  [Business\InventoryController::class, 'create'])->name('inventory.create');
Route::post('/inventory',                        [Business\InventoryController::class, 'store'])->name('inventory.store');
Route::get('/inventory/{item}/edit',             [Business\InventoryController::class, 'edit'])->name('inventory.edit');
Route::put('/inventory/{item}',                  [Business\InventoryController::class, 'update'])->name('inventory.update');
Route::post('/inventory/{item}/restock',         [Business\InventoryController::class, 'restock'])->name('inventory.restock');
Route::get('/inventory/{item}/transactions',     [Business\InventoryController::class, 'transactions'])->name('inventory.transactions');

// ─── Vehicles ─────────────────────────────────────────────────────────────────
Route::get('/vehicles',                          [Business\VehicleController::class, 'index'])->name('vehicles.index');
Route::get('/vehicles/create',                   [Business\VehicleController::class, 'create'])->name('vehicles.create');
Route::post('/vehicles',                         [Business\VehicleController::class, 'store'])->name('vehicles.store');
Route::get('/vehicles/{vehicle}/edit',           [Business\VehicleController::class, 'edit'])->name('vehicles.edit');
Route::put('/vehicles/{vehicle}',                [Business\VehicleController::class, 'update'])->name('vehicles.update');
Route::post('/vehicles/{vehicle}/assign',        [Business\VehicleController::class, 'assign'])->name('vehicles.assign');
Route::post('/vehicles/{vehicle}/return',        [Business\VehicleController::class, 'returnVehicle'])->name('vehicles.return');
Route::get('/vehicles/{vehicle}/fuel',           [Business\VehicleFuelController::class, 'index'])->name('vehicles.fuel.index');
Route::post('/vehicles/{vehicle}/fuel',          [Business\VehicleFuelController::class, 'store'])->name('vehicles.fuel.store');
Route::get('/vehicles/{vehicle}/maintenance',    [Business\VehicleMaintenanceController::class, 'index'])->name('vehicles.maintenance.index');
Route::post('/vehicles/{vehicle}/maintenance',   [Business\VehicleMaintenanceController::class, 'store'])->name('vehicles.maintenance.store');

// ─── Payroll ──────────────────────────────────────────────────────────────────
Route::get('/payroll',                           [Business\PayrollController::class, 'index'])->name('payroll.index');
Route::post('/payroll/calculate',                [Business\PayrollController::class, 'calculate'])->name('payroll.calculate');
Route::post('/payroll/process',                  [Business\PayrollController::class, 'process'])->name('payroll.process');
Route::get('/payroll/reports',                   [Business\PayrollController::class, 'reports'])->name('payroll.reports');
Route::get('/payroll/reports/export',            [Business\PayrollController::class, 'export'])->name('payroll.export');

// ─── Analytics ────────────────────────────────────────────────────────────────
Route::get('/analytics/team',                    [Business\AnalyticsController::class, 'team'])->name('analytics.team');
Route::get('/analytics/team/{member}',           [Business\AnalyticsController::class, 'member'])->name('analytics.member');
