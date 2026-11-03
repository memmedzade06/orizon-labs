<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Certificate;
use App\Models\ContactMessage;
use App\Models\News;
use App\Models\Partner;
use App\Models\PhotoGallery;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Slide;
use App\Models\VideoGallery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Ana səhifə — bütün məlumat admin paneldən gəlir.
 */
class HomeController extends Controller
{
    /** Ayarlar admin paneldə doldurulmayıbsa istifadə olunan dəyərlər */
    private const DEFAULTS = [
        'contact.phone'          => '+994 55 265 15 22',
        'contact.whatsapp'       => '994552651522',
        'contact.email'          => 'info@orizon-labs.com',
        'contact.address'        => 'Abşeron rayonu, Bakı, AZ 0121',
        'about.founded_year'     => '2019',
        'about.countries_count'  => '5',
        'about.partners_count'   => '70+',
        'about.export_countries' => 'Türkiyə, Gürcüstan, Polşa, Latviya, Çexiya',
    ];

    public function index(): View
    {
        $site = $this->siteSettings();

        // Bütün aktiv kateqoriyalar (əsas + alt) — ad axtarışı və filtr üçün
        $categories = Category::active()->ordered()->get();

        // Saytda kart kimi yalnız əsas kateqoriyalar göstərilir; hər birinin alt-kateqoriyaları ilə
        $roots = $categories->whereNull('parent_id')->values();

        // Hər kateqoriya üçün onun kök (əsas) slug-ı — məhsul filtri bunu istifadə edir
        $rootSlugOf = [];
        foreach ($categories as $c) {
            $root = $c->parent_id ? $categories->firstWhere('id', $c->parent_id) : $c;
            $rootSlugOf[$c->id] = $root?->slug ?? $c->slug;
        }

        $products = Product::active()
            ->whereIn('category_id', $categories->pluck('id'))
            ->with('images')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        $data = [
            'slides'   => Slide::active()->ordered()->get()->map(fn (Slide $s) => [
                'img'     => $this->url($s->image),
                'eyebrow' => $s->tr('eyebrow'),
                'title'   => $s->tr('title'),
                'text'    => $s->tr('text'),
                'btnText' => $s->tr('button_text'),
                'btnUrl'  => $s->button_url,
            ])->values(),

            // Saytdakı kartlar — əsas kateqoriyalar, hər biri öz alt-kateqoriyaları ilə
            'cats'     => $roots->map(fn (Category $c) => [
                'id'   => $c->slug,
                'name' => $c->tr('name'),
                'desc' => $c->tr('description'),
                'img'  => $this->url($c->image),
                'subs' => $categories->where('parent_id', $c->id)->map(fn (Category $s) => [
                    'id'   => $s->slug,
                    'name' => $s->tr('name'),
                ])->values(),
            ])->values(),

            // Ad axtarışı / etiket üçün bütün kateqoriyalar (əsas + alt)
            'allcats'  => $categories->map(fn (Category $c) => [
                'id'   => $c->slug,
                'name' => $c->tr('name'),
            ])->values(),

            'products' => $products->map(function (Product $p) use ($categories, $rootSlugOf) {
                $images = collect([$p->image])
                    ->merge($p->images->pluck('path'))
                    ->filter()
                    ->map(fn ($path) => $this->url($path))
                    ->values();

                return [
                    'id'          => $p->id,
                    'cat'         => $categories->firstWhere('id', $p->category_id)?->slug,
                    'root'        => $rootSlugOf[$p->category_id] ?? null,
                    'name'        => $p->tr('name'),
                    'type'        => $p->tr('dosage_form'),
                    'size'        => $p->tr('package_size'),
                    'short'       => $p->tr('short_description'),
                    'description' => $p->tr('description'),
                    'composition' => $p->tr('composition'),
                    'usage'       => $p->tr('usage'),
                    'warnings'    => $p->tr('warnings'),
                    'img'         => $images->first(),
                    'images'      => $images,
                    'pop'         => $p->popularity,
                    'isNew'       => $p->is_new,
                    'top'         => $p->is_featured,
                ];
            })->values(),

            'news'     => News::published()
                ->orderByDesc('is_featured')
                ->orderByDesc('published_at')
                ->limit(30)
                ->get()
                ->map(fn (News $n) => [
                    'title' => $n->tr('title'),
                    'text'  => $n->tr('excerpt'),
                    'body'  => $n->tr('body'),
                    'tag'   => $n->tr('tag'),
                    'date'  => $n->published_at?->locale(app()->getLocale())->translatedFormat('j F Y'),
                    'iso'   => $n->published_at?->toDateString(),
                    'img'   => $this->url($n->image),
                ])->values(),

            'photos'   => PhotoGallery::active()->ordered()->get()->map(fn (PhotoGallery $ph) => [
                'img'   => $this->url($ph->image),
                'title' => $ph->tr('title'),
            ])->values(),

            'videos'   => VideoGallery::active()->ordered()->get()
                ->filter(fn (VideoGallery $v) => $v->embedUrl() !== null)
                ->map(fn (VideoGallery $v) => [
                    'title' => $v->tr('title'),
                    'embed' => $v->embedUrl(),
                    'thumb' => $v->thumbnailUrl(),
                ])->values(),

            // Sertifikatlar / lisenziyalar — sol üzən zolaq
            'certs'    => Certificate::active()->ordered()->get()->map(fn (Certificate $c) => [
                'img'   => $this->url($c->image),
                'title' => $c->tr('title'),
            ])->values(),

            // Tərəfdaş brendlər — "Aptek, klinika və distribyutorlar üçün" bölməsinin yanında
            'partners' => Partner::active()->ordered()->get()->map(fn (Partner $p) => [
                'name' => $p->name,
                'logo' => $this->url($p->logo),
                'url'  => $p->url,
            ])->values(),

            'whatsapp' => $site['whatsapp'],

            // Cari dil və JS tərəfdə göstərilən mətnlərin tərcümələri
            'locale'   => app()->getLocale(),
            'i18n'     => [
                'product'      => __('məhsul'),
                'all'          => __('Hamısı'),
                'allProducts'  => __('Bütün məhsullar'),
                'found'        => __('məhsul tapıldı'),
                'searchFor'    => __('üzrə axtarış'),
                'noResults'    => __('Bu sorğu üzrə məhsul tapılmadı'),
                'noProducts'   => __('Məhsul yoxdur'),
                'tryOther'     => __('Başqa söz yazın və ya "Hamısı" kateqoriyasını seçin.'),
                'comingSoon'   => __('Tezliklə əlavə olunacaq.'),
                'noCategory'   => __('Kateqoriya yoxdur'),
                'allOf'        => __('Bütün'),
                'noSubcat'     => __('Alt-kateqoriya yoxdur'),
                'details'      => __('Ətraflı bax'),
                'readMore'     => __('Ətraflı oxu'),
                'certificates' => __('Sertifikatlar'),
                'badgeNew'     => __('Yeni'),
                'badgeFeatured' => __('Ən çox seçilən'),
                'close'        => __('Bağla'),
                'aboutCompany' => __('Şirkət haqqında'),
                'form'         => __('Forma'),
                'package'      => __('Qablaşdırma'),
                'maker'        => __('İstehsalçı'),
                'makerName'    => __('Orizon Labs, Azərbaycan'),
                'descTitle'    => __('Təsviri'),
                'composition'  => __('Tərkibi'),
                'usage'        => __('İstifadə qaydası'),
                'warnings'     => __('Əks göstərişlər'),
                'noInfo'       => __('Məhsul haqqında ətraflı məlumat üçün bizimlə əlaqə saxlayın.'),
                'whereBuy'     => __('Harada almaq olar'),
                'whereBuyText' => __('Azərbaycandakı tərəfdaş apteklərdə. Ən yaxın satış nöqtəsini öyrənmək üçün bizə yazın.'),
                'askWhatsapp'  => __('WhatsApp ilə soruş'),
                'waHello'      => __('Salam,'),
                'waProduct'    => __('məhsulu haqqında məlumat almaq istəyirəm.'),
                'zoomPhoto'    => __('Şəkli böyüt'),
                'playVideo'    => __('Videonu oynat'),
            ],
        ];

        return view('site.home', compact('data', 'site'));
    }

    /** "Bizə yazın" formasından gələn müraciəti saxlayır */
    public function contact(Request $request): RedirectResponse
    {
        // Honeypot: "website" yalnız botlar tərəfindən doldurulur. Dolubsa, saxlamadan
        // "uğurlu" cavab qaytarırıq ki, bot spam-ın tutulduğunu bilməsin.
        if ($request->filled('website')) {
            return redirect()->back()
                ->with('contact_success', __('Mesajınız göndərildi. Tezliklə sizinlə əlaqə saxlayacağıq.'))
                ->withFragment('write');
        }

        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:50',
            'message' => 'required|string|max:5000',
        ], [], [
            'name'    => __('Ad'),
            'surname' => __('Soyad'),
            'email'   => __('E-poçt'),
            'phone'   => __('Telefon'),
            'message' => __('Mesaj'),
        ]);

        ContactMessage::create([
            'type'       => 'contact',
            'name'       => trim($data['name'] . ' ' . $data['surname']),
            'email'      => $data['email'] ?? null,
            'phone'      => $data['phone'] ?? null,
            'message'    => $data['message'],
            'locale'     => app()->getLocale(),
            'ip_address' => $request->ip(),
        ]);

        return redirect()
            ->back()
            ->with('contact_success', __('Mesajınız göndərildi. Tezliklə sizinlə əlaqə saxlayacağıq.'))
            ->withFragment('write');
    }

    /** Header, footer və "Haqqımızda" üçün ayarlar */
    private function siteSettings(): array
    {
        $get = fn (string $key) => Setting::get($key) ?: (self::DEFAULTS[$key] ?? null);

        $phone    = $get('contact.phone');
        $whatsapp = preg_replace('/\D+/', '', (string) $get('contact.whatsapp'));
        $catalog  = Setting::raw('catalog.pdf');

        return [
            'phone'      => $phone,
            'phoneHref'  => 'tel:' . preg_replace('/[^\d+]/', '', (string) $phone),
            'email'      => $get('contact.email'),
            'address'    => $get('contact.address'),
            'whatsapp'   => $whatsapp,
            'whatsappUrl'=> $whatsapp ? "https://wa.me/{$whatsapp}" : null,
            'catalogUrl' => $this->url($catalog),
            'founded'    => $get('about.founded_year'),
            'countries'  => $get('about.countries_count'),
            'partners'   => $get('about.partners_count'),
            'exportList' => array_values(array_filter(array_map('trim', explode(',', (string) $get('about.export_countries'))))),
            'social'     => array_filter([
                'Instagram' => Setting::get('social.instagram'),
                'Facebook'  => Setting::get('social.facebook'),
                'LinkedIn'  => Setting::get('social.linkedin'),
            ]),
        ];
    }

    private function url(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }
}
