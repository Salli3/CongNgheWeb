<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;   // TODO 11: Import PageController

// TODO 12: Xóa route mặc định và thêm 2 route mới
Route::get('/', [PageController::class, 'showHomepage']);
Route::get('/about', [PageController::class, 'showHomepage']);