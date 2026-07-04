<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tech;

// ─── Schedule ─────────────────────────────────────────────────────────────────
Route::get('/schedule',                          [Tech\ScheduleController::class, 'today'])->name('schedule.today');
Route::get('/schedule/{date}',                   [Tech\ScheduleController::class, 'show'])->name('schedule.show');

// ─── Jobs ─────────────────────────────────────────────────────────────────────
Route::get('/jobs',                              [Tech\JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{assignment}',                 [Tech\JobController::class, 'show'])->name('jobs.show');
Route::post('/jobs/{assignment}/status',         [Tech\JobController::class, 'updateStatus'])->name('jobs.update-status');
Route::post('/jobs/{assignment}/materials',      [Tech\JobController::class, 'logMaterials'])->name('jobs.log-materials');

// ─── Attendance ───────────────────────────────────────────────────────────────
Route::get('/attendance/history',                [Tech\AttendanceController::class, 'history'])->name('attendance.history');
Route::post('/attendance/clock-in',              [Tech\AttendanceController::class, 'clockIn'])->name('attendance.clock-in');
Route::post('/attendance/clock-out',             [Tech\AttendanceController::class, 'clockOut'])->name('attendance.clock-out');

// ─── Location ─────────────────────────────────────────────────────────────────
Route::post('/location/update',                  [Tech\LocationController::class, 'update'])->name('location.update');

// ─── Equipment ────────────────────────────────────────────────────────────────
Route::get('/equipment',                         [Tech\EquipmentController::class, 'index'])->name('equipment.index');
Route::post('/equipment/{assignment}/report',    [Tech\EquipmentController::class, 'reportIssue'])->name('equipment.report-issue');

// ─── Earnings ─────────────────────────────────────────────────────────────────
Route::get('/earnings',                          [Tech\EarningsController::class, 'index'])->name('earnings.index');
Route::get('/earnings/{period}',                 [Tech\EarningsController::class, 'period'])->name('earnings.period');
