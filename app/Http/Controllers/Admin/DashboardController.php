<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\News;
use App\Models\PhotoGallery;
use App\Models\Product;
use App\Models\VideoGallery;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                ['label' => 'Məhsullar',      'value' => Product::count(),        'route' => 'admin.products.index'],
                ['label' => 'Kateqoriyalar',  'value' => Category::count(),       'route' => 'admin.categories.index'],
                ['label' => 'Xəbərlər',       'value' => News::count(),           'route' => 'admin.news.index'],
                ['label' => 'Şəkillər',       'value' => PhotoGallery::count(),   'route' => 'admin.photos.index'],
                ['label' => 'Videolar',       'value' => VideoGallery::count(),   'route' => 'admin.videos.index'],
                ['label' => 'Yeni müraciət',  'value' => ContactMessage::unread()->count(), 'route' => 'admin.messages.index'],
            ],
            'latestMessages' => ContactMessage::latest()->limit(5)->get(),
        ]);
    }
}
