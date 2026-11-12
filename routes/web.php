<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\PhotoGalleryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SlideController;
use App\Http\Controllers\Admin\VideoGalleryController;
use App\Http\Controllers\Site\HomeController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Ana səhifə — məlumatlar admin paneldən
Route::get('/', [HomeController::class, 'index'])->name('home');

// Axtarış sistemləri üçün: robots.txt və sitemap.xml (ünvanlar cari domendən qurulur)
Route::get('robots.txt', fn () => response(
    "User-agent: *\nDisallow: /admin\nDisallow: /migrate\n\nSitemap: " . url('sitemap.xml') . "\n"
)->header('Content-Type', 'text/plain; charset=UTF-8'));

Route::get('sitemap.xml', function () {
    $lastmod = collect([
        \App\Models\Product::max('updated_at'),
        \App\Models\News::max('updated_at'),
        \App\Models\Setting::max('updated_at'),
    ])->filter()->max();

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
        . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n"
        . '  <url><loc>' . e(url('/')) . '</loc>'
        . ($lastmod ? '<lastmod>' . \Illuminate\Support\Carbon::parse($lastmod)->toAtomString() . '</lastmod>' : '')
        . '<changefreq>weekly</changefreq><priority>1.0</priority></url>' . "\n"
        . '</urlset>';

    return response($xml)->header('Content-Type', 'application/xml; charset=UTF-8');
});

/*
|--------------------------------------------------------------------------
| Migrasiya linki — gizli token ilə (lokal VƏ hosting üçün işləyir)
| Açıq:  /migrate/ba516cb8eb47a84a7d866924f10bade5
| DİQQƏT: Hostingdə migrasiyadan sonra bu marşrutu silin və ya tokeni dəyişin —
| daimi açıq migrasiya ünvanı saxlamaq təhlükəlidir.
|--------------------------------------------------------------------------
*/
Route::get('migrate/{token}', function (string $token) {
    abort_unless(hash_equals('ba516cb8eb47a84a7d866924f10bade5', $token), 403, 'Yanlış token.');

    Artisan::call('migrate', ['--force' => true]);
    $output = Artisan::output();

    Artisan::call('optimize:clear');
    $output .= "\n" . Artisan::output();

    return response(
        '<pre style="font:14px/1.5 monospace;padding:20px;background:#0E2A47;color:#9fe7c9;min-height:100vh;margin:0">'
        . e($output) .
        '</pre>'
    );
})->name('dev.migrate');

// "Bizə yazın" forması — ictimai müraciət göndərir
Route::post('contact', [HomeController::class, 'contact'])->middleware('throttle:5,1')->name('contact.send');

// Dil seçimi — seçilən dili sessiyada saxlayır və geri qayıdır
Route::get('lang/{locale}', function (string $locale) {
    if (in_array($locale, \App\Support\Locales::codes(), true)) {
        session(['locale' => $locale]);
    }

    return redirect()->back();
})->name('lang.switch');

/*
|--------------------------------------------------------------------------
| Admin panel
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    // Giriş etməmiş istifadəçilər üçün
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.submit');
    });

    // Yalnız admin
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        // Kataloq
        Route::resource('categories', CategoryController::class)->except('show');
        Route::resource('products', ProductController::class)->except('show');
        Route::delete('product-images/{productImage}', [ProductController::class, 'destroyImage'])
            ->name('product-images.destroy');

        // Məzmun
        Route::resource('news', NewsController::class)->except('show')
            ->parameters(['news' => 'news']);
        Route::resource('slides', SlideController::class)->except('show');
        Route::resource('photos', PhotoGalleryController::class)->except('show');
        Route::resource('videos', VideoGalleryController::class)->except('show');
        Route::resource('certificates', CertificateController::class)->except('show');
        Route::resource('partners', PartnerController::class)->except('show');

        // Müraciətlər (yalnız baxmaq və silmək)
        Route::resource('messages', ContactMessageController::class)->only(['index', 'show', 'destroy']);

        // Ayarlar
        Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    });
});
