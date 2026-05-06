<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ScanController;

Route::middleware('auth:sanctum')->post('/scan', [ScanController::class, 'scan']);