<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactSettingController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\GalleryCategoryController;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\PlantRevitalizationController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\SocialLinkController;
use App\Http\Controllers\Api\TourPackageController;
use App\Http\Controllers\Api\UniqueRockController;

/*
|--------------------------------------------------------------------------
| API Health Check
|--------------------------------------------------------------------------
*/

Route::get('/test', function () {
    return response()->json([
        'message' => 'API Siwatu berjalan dengan baik.',
        'status' => true,
        'app' => config('app.name'),
        'timestamp' => now(),
    ]);
});

Route::get('/test-database', function () {
    try {
        DB::connection()->getPdo();

        return response()->json([
            'message' => 'Database berhasil terkoneksi.',
            'status' => true,
            'database' => DB::connection()->getDatabaseName(),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Database gagal terkoneksi.',
            'status' => false,
            'error' => $e->getMessage(),
        ], 500);
    }
});

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/gallery-categories', [
    GalleryCategoryController::class,
    'index',
]);

Route::get('/galleries', [
    GalleryController::class,
    'index',
]);

Route::get('/galleries/{gallery}', [
    GalleryController::class,
    'show',
]);

Route::get('/reviews', [
    ReviewController::class,
    'index',
]);

Route::get('/reviews/{review}', [
    ReviewController::class,
    'show',
]);

Route::get('/tour-packages', [
    TourPackageController::class,
    'index',
]);

Route::get('/tour-packages/{tourPackage:slug}', [
    TourPackageController::class,
    'show',
]);

Route::get('/contact', [
    ContactSettingController::class,
    'show',
]);

Route::get('/social-links', [
    SocialLinkController::class,
    'index',
]);

Route::get('/unique-rocks', [
    UniqueRockController::class,
    'index',
]);

Route::get('/unique-rocks/{uniqueRock}', [
    UniqueRockController::class,
    'show',
]);

Route::get('/plant-revitalizations', [
    PlantRevitalizationController::class,
    'index',
]);

Route::get('/plant-revitalizations/{plantRevitalization}', [
    PlantRevitalizationController::class,
    'show',
]);

/*
|--------------------------------------------------------------------------
| Admin Authentication
|--------------------------------------------------------------------------
*/

Route::post('/admin/login', [
    AuthController::class,
    'login',
]);

/*
|--------------------------------------------------------------------------
| Protected Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')
    ->prefix('admin')
    ->group(function () {
        /*
        |--------------------------------------------------------------------------
        | Authentication
        |--------------------------------------------------------------------------
        */

        Route::get('/profile', [
            AuthController::class,
            'profile',
        ]);

        Route::post('/logout', [
            AuthController::class,
            'logout',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', DashboardController::class);

        /*
        |--------------------------------------------------------------------------
        | Gallery Categories
        |--------------------------------------------------------------------------
        */

        Route::get('/gallery-categories', [
            GalleryCategoryController::class,
            'adminIndex',
        ]);

        Route::get('/gallery-categories/{galleryCategory}', [
            GalleryCategoryController::class,
            'adminShow',
        ]);

        Route::post('/gallery-categories', [
            GalleryCategoryController::class,
            'store',
        ]);

        Route::patch('/gallery-categories/{galleryCategory}', [
            GalleryCategoryController::class,
            'update',
        ]);

        Route::delete('/gallery-categories/{galleryCategory}', [
            GalleryCategoryController::class,
            'destroy',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Galleries
        |--------------------------------------------------------------------------
        */

        Route::get('/galleries', [
            GalleryController::class,
            'adminIndex',
        ]);

        Route::get('/galleries/{gallery}', [
            GalleryController::class,
            'adminShow',
        ]);

        Route::post('/galleries', [
            GalleryController::class,
            'store',
        ]);

        /*
         * POST tambahan digunakan untuk update multipart/form-data
         * apabila terdapat upload gambar.
         */
        Route::post('/galleries/{gallery}', [
            GalleryController::class,
            'update',
        ]);

        Route::patch('/galleries/{gallery}', [
            GalleryController::class,
            'update',
        ]);

        Route::delete('/galleries/{gallery}', [
            GalleryController::class,
            'destroy',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Reviews
        |--------------------------------------------------------------------------
        */

        Route::get('/reviews', [
            ReviewController::class,
            'adminIndex',
        ]);

        Route::get('/reviews/{review}', [
            ReviewController::class,
            'adminShow',
        ]);

        Route::post('/reviews', [
            ReviewController::class,
            'store',
        ]);

        Route::patch('/reviews/{review}', [
            ReviewController::class,
            'update',
        ]);

        Route::delete('/reviews/{review}', [
            ReviewController::class,
            'destroy',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Tour Packages
        |--------------------------------------------------------------------------
        */

        Route::get('/tour-packages', [
            TourPackageController::class,
            'adminIndex',
        ]);

        Route::get('/tour-packages/{tourPackage}', [
            TourPackageController::class,
            'adminShow',
        ]);

        Route::post('/tour-packages', [
            TourPackageController::class,
            'store',
        ]);

        Route::patch('/tour-packages/{tourPackage}', [
            TourPackageController::class,
            'update',
        ]);

        Route::delete('/tour-packages/{tourPackage}', [
            TourPackageController::class,
            'destroy',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Contact Settings
        |--------------------------------------------------------------------------
        */

        Route::get('/contact', [
            ContactSettingController::class,
            'adminShow',
        ]);

        Route::patch('/contact', [
            ContactSettingController::class,
            'update',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Social Links
        |--------------------------------------------------------------------------
        */

        Route::get('/social-links', [
            SocialLinkController::class,
            'adminIndex',
        ]);

        Route::get('/social-links/{socialLink}', [
            SocialLinkController::class,
            'adminShow',
        ]);

        Route::post('/social-links', [
            SocialLinkController::class,
            'store',
        ]);

        Route::patch('/social-links/{socialLink}', [
            SocialLinkController::class,
            'update',
        ]);

        Route::delete('/social-links/{socialLink}', [
            SocialLinkController::class,
            'destroy',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Unique Rocks
        |--------------------------------------------------------------------------
        */

        Route::get('/unique-rocks', [
            UniqueRockController::class,
            'adminIndex',
        ]);

        Route::get('/unique-rocks/{uniqueRock}', [
            UniqueRockController::class,
            'adminShow',
        ]);

        Route::post('/unique-rocks', [
            UniqueRockController::class,
            'store',
        ]);

        Route::post('/unique-rocks/{uniqueRock}', [
            UniqueRockController::class,
            'update',
        ]);

        Route::patch('/unique-rocks/{uniqueRock}', [
            UniqueRockController::class,
            'update',
        ]);

        Route::delete('/unique-rocks/{uniqueRock}', [
            UniqueRockController::class,
            'destroy',
        ]);

        Route::post('/unique-rocks/{uniqueRock}/regenerate-qr', [
            UniqueRockController::class,
            'regenerateQr',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Plant Revitalizations
        |--------------------------------------------------------------------------
        */

        Route::get('/plant-revitalizations', [
            PlantRevitalizationController::class,
            'adminIndex',
        ]);

        Route::get('/plant-revitalizations/{plantRevitalization}', [
            PlantRevitalizationController::class,
            'adminShow',
        ]);

        Route::post('/plant-revitalizations', [
            PlantRevitalizationController::class,
            'store',
        ]);

        Route::post('/plant-revitalizations/{plantRevitalization}', [
            PlantRevitalizationController::class,
            'update',
        ]);

        Route::patch('/plant-revitalizations/{plantRevitalization}', [
            PlantRevitalizationController::class,
            'update',
        ]);

        Route::delete('/plant-revitalizations/{plantRevitalization}', [
            PlantRevitalizationController::class,
            'destroy',
        ]);

        Route::post(
            '/plant-revitalizations/{plantRevitalization}/regenerate-qr',
            [
                PlantRevitalizationController::class,
                'regenerateQr',
            ]
        );
    });