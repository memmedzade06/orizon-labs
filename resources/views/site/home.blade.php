<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ __('Orizon Labs — dərman vasitələri, qida əlavələri və kosmetika istehsalçısı') }}</title>
<meta name="description" content="{{ __('Orizon Labs Pharmaceuticals — Bakıda dərman vasitələri, qida əlavələri və kosmetika istehsalçısı.') }}">
<link rel="icon" type="image/png" href="{{ asset('assets/site/logo.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&amp;family=Unbounded:wght@500;600;700&amp;display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/site/site.css') }}?v={{ filemtime(public_path('assets/site/site.css')) }}">
</head>
<body>

<div class="util">
  <div class="wrap">
    <div class="util-left">
      @if ($site['phone'])<a href="{{ $site['phoneHref'] }}">{{ $site['phone'] }}</a>@endif
      @if ($site['email'])<span><a href="mailto:{{ $site['email'] }}">{{ $site['email'] }}</a></span>@endif
    </div>
    <div class="lang" role="group" aria-label="{{ __('Dil') }}">
      @foreach (\App\Support\Locales::ALL as $code => $label)
        <a href="{{ route('lang.switch', $code) }}" @if(app()->getLocale() === $code) aria-current="true" @endif>{{ $label }}</a>
      @endforeach
    </div>
  </div>
</div>

<header class="header">
  <div class="wrap">
    <a href="{{ url('/') }}" class="logo" aria-label="{{ __('Orizon Labs ana səhifə') }}"><img class="logo-img" src="{{ asset('assets/site/logo.png') }}" alt="Orizon Labs"></a>
    <form class="search" role="search" id="searchForm">
      <label for="q" class="sr-only">{{ __('Məhsul axtar') }}</label>
      <input id="q" type="search" placeholder="{{ __('Məhsul adı, kateqoriya və ya forma: vitamin, krem, kapsul…') }}" autocomplete="off">
      <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-4-4"></path></svg>
      <button type="submit">{{ __('Axtar') }}</button>
    </form>
    <div class="head-cta">
      @if ($site['catalogUrl'])
        <a href="{{ $site['catalogUrl'] }}" class="btn btn-line btn-sm" target="_blank" rel="noopener">{{ __('Kataloq (PDF)') }}</a>
      @endif
      <a href="#contact" class="btn btn-navy btn-sm">{{ __('Əlaqə') }}</a>
    </div>
  </div>
</header>

<nav class="mainnav" aria-label="{{ __('Əsas menyu') }}">
  <div class="wrap">
    <ul>
      <li><a href="{{ url('/') }}" aria-current="page">{{ __('Ana səhifə') }}</a></li>
      <li class="nav-mega" data-mega><a href="#categories" aria-haspopup="true" aria-expanded="false">{{ __('Kateqoriyalar') }}</a></li>
      <li><a href="#products">{{ __('Məhsullar') }}</a></li>
      <li><a href="{{ count($data['photos']) || count($data['videos']) ? '#gallery' : '#top' }}">{{ __('Qalereya') }}</a></li>
      @if (count($data['news']))<li><a href="#news">{{ __('Xəbərlər') }}</a></li>@endif
      <li><a href="#about">{{ __('Haqqımızda') }}</a></li>
      <li><a href="#partners">{{ __('Tərəfdaşlar üçün') }}</a></li>
      <li><a href="#contact">{{ __('Əlaqə') }}</a></li>
    </ul>
    <div class="mega" id="catMega" role="menu" aria-label="{{ __('Kateqoriyalar') }}" aria-hidden="true"></div>
  </div>
</nav>

<main>
  @if (count($data['slides']))
  <!-- Slayder -->
  <section class="slider" id="top" aria-roledescription="carousel" aria-label="{{ __('İstehsalatdan görüntülər') }}">
    <div class="slides" id="slides"></div>
    <div class="slider-ui">
      <div class="wrap">
        <div class="dots" id="dots" role="group" aria-label="{{ __('Slaydlar') }}"></div>
        <span class="counter" id="counter" aria-live="polite"></span>
        <div class="arrows">
          <button type="button" class="arrow" id="prev" aria-label="{{ __('Əvvəlki slayd') }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m15 5-7 7 7 7"></path></svg></button>
          <button type="button" class="arrow" id="next" aria-label="{{ __('Növbəti slayd') }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m9 5 7 7-7 7"></path></svg></button>
        </div>
      </div>
    </div>
  </section>
  @endif

  <!-- Kateqoriyalar -->
  <section class="block" id="categories" aria-labelledby="catTitle">
    <div class="wrap">
      <div class="sec-head">
        <div>
          <h2 id="catTitle">{{ __('Məhsul kateqoriyaları') }}</h2>
          <p>{{ __('Kateqoriyanı seçin, həmin qrupun bütün məhsulları aşağıda göstəriləcək.') }}</p>
        </div>
      </div>
      <div class="cats" id="cats"></div>
    </div>
  </section>

  <!-- Məhsullar -->
  <section class="block" id="products" aria-labelledby="prodTitle">
    <div class="wrap">
      <div class="sec-head">
        <div><h2 id="prodTitle">{{ __('Bütün məhsullar') }}</h2></div>
        @if ($site['catalogUrl'])
          <a href="{{ $site['catalogUrl'] }}" class="more" target="_blank" rel="noopener">{{ __('Tam kataloqu yüklə') }}</a>
        @endif
      </div>
      <div class="toolbar">
        <div class="tabs" role="tablist" id="tabs" aria-label="{{ __('Kateqoriya filtri') }}"></div>
        <label class="sort">{{ __('Sırala') }}
          <select id="sort">
            <option value="popular">{{ __('Populyarlığa görə') }}</option>
            <option value="new">{{ __('Əvvəlcə yenilər') }}</option>
            <option value="az">{{ __('Ada görə (A–Z)') }}</option>
          </select>
        </label>
      </div>
      <div class="result-note" id="resultNote" aria-live="polite"></div>
      <div class="grid" id="grid"></div>
      <div class="show-more"><button type="button" class="btn btn-line" id="showMore">{{ __('Daha çox məhsul göstər') }}</button></div>
    </div>
  </section>

  @if (count($data['photos']) || count($data['videos']))
  @php $hasPhotos = count($data['photos']); $hasVideos = count($data['videos']); $firstTab = $hasPhotos ? 'photos' : 'videos'; @endphp
  <!-- Qalereya: foto və video -->
  <section class="block" id="gallery" aria-labelledby="galleryTitle">
    <div class="wrap">
      <div class="sec-head">
        <div>
          <h2 id="galleryTitle">{{ __('Qalereya') }}</h2>
          <p>{{ __('Şəkil və video qalereyamıza baxın — laboratoriya, istehsal və məhsullarımızdan görüntülər.') }}</p>
        </div>
      </div>
      <div class="tabs gallery-tabs" role="tablist" aria-label="{{ __('Qalereya') }}">
        <button type="button" role="tab" class="tab" data-gtab="photos" aria-selected="{{ $firstTab === 'photos' ? 'true' : 'false' }}">{{ __('Foto qalereya') }}</button>
        <button type="button" role="tab" class="tab" data-gtab="videos" aria-selected="{{ $firstTab === 'videos' ? 'true' : 'false' }}">{{ __('Video qalereya') }}</button>
      </div>
      <div data-gpane="photos" @if($firstTab !== 'photos') hidden @endif>
        @if ($hasPhotos)
          <div class="photo-grid" id="photoGrid"></div>
          <div class="gallery-more"><button type="button" class="btn btn-line" id="morePhotos">{{ __('Daha çox şəkil') }}</button></div>
        @else
          <div class="empty"><b>{{ __('Şəkil yoxdur') }}</b>{{ __('Tezliklə əlavə olunacaq.') }}</div>
        @endif
      </div>
      <div data-gpane="videos" @if($firstTab !== 'videos') hidden @endif>
        @if ($hasVideos)
          <div class="video-grid" id="videoGrid"></div>
        @else
          <div class="empty"><b>{{ __('Video yoxdur') }}</b>{{ __('Tezliklə əlavə olunacaq.') }}</div>
        @endif
      </div>
    </div>
  </section>
  @endif

  @if (count($data['news']))
  <!-- Xəbərlər -->
  <section class="block" id="news" aria-labelledby="newsTitle">
    <div class="wrap">
      <div class="sec-head">
        <div><h2 id="newsTitle">{{ __('Xəbərlər') }}</h2></div>
      </div>
      <div class="news-grid" id="newsGrid"></div>
      <div class="show-more" id="newsMore" style="display:none"><button type="button" class="btn btn-line" id="showMoreNews">{{ __('Bütün Xəbərlər') }}</button></div>
    </div>
  </section>
  @endif

  <!-- Haqqımızda -->
  <section class="maker" id="about" aria-labelledby="aboutTitle">
    <div class="wrap">
      <div>
        <h2 id="aboutTitle">{{ __('Bakıda qurulmuş beynəlxalq əczaçılıq şirkəti') }}</h2>
        <p>{{ __('Orizon Labs Pharmaceuticals :year-cu ildə yaradılıb. Dərman vasitələri, qida əlavələri və kosmetika müasir istehsal sahələrində, öz laboratoriya nəzarətimizlə hazırlanır.', ['year' => $site['founded']]) }}</p>
        <p>{{ __('Məhsullarımız Azərbaycanda aptek və klinikalarda, eləcə də :count ölkədə tərəfdaşlarımız vasitəsilə satılır.', ['count' => $site['countries']]) }}</p>
        @if ($site['catalogUrl'])
          <a href="{{ $site['catalogUrl'] }}" class="btn btn-sun" style="margin-top:8px" target="_blank" rel="noopener">{{ __('Məhsul kataloqunu yüklə (PDF)') }}</a>
        @endif
      </div>
      <div class="facts">
        <div class="fact"><b>{{ $site['founded'] }}</b><span>{{ __('fəaliyyətə başladıq') }}</span></div>
        <div class="fact"><b>{{ $site['countries'] }}</b><span>{{ __('ölkəyə ixrac') }}</span></div>
        <div class="fact"><b>{{ $site['partners'] }}</b><span>{{ __('aptek, klinika və distribyutor tərəfdaş') }}</span></div>
        @if (count($site['exportList']))
          <div class="export">{{ __('İxrac etdiyimiz ölkələr') }}
            <ul>@foreach ($site['exportList'] as $country)<li>{{ $country }}</li>@endforeach</ul>
          </div>
        @endif
      </div>
    </div>
  </section>

  <!-- Tərəfdaşlar -->
  <section class="block" id="partners" aria-labelledby="b2bTitle">
    <div class="wrap">
      <div class="sec-head"><div><h2 id="b2bTitle">{{ __('Aptek, klinika və distribyutorlar üçün') }}</h2></div></div>
      <div class="b2b">
        <div class="b2b-card">
          <h3>{{ __('Tərəfdaşımız olun') }}</h3>
          <p>{{ __('Məhsullarımızı satmaq və ya distribyusiya etmək istəyirsinizsə, bizimlə əlaqə saxlayın. Menecerimiz şərtləri və məhsul siyahısını göndərəcək.') }}</p>
          <ul>
            <li>{{ __('Azərbaycan və xarici bazarlar üçün distribyusiya') }}</li>
            <li>{{ __('Aptek şəbəkələri və klinikalarla birbaşa iş') }}</li>
            <li>{{ __('Məhsullar üzrə sənədlər və sertifikatlar') }}</li>
          </ul>
          <a href="{{ $site['whatsappUrl'] ? $site['whatsappUrl'] . '?text=' . rawurlencode(__('Salam, tərəfdaşlıq barədə məlumat almaq istəyirəm.')) : '#contact' }}"
             class="btn btn-navy" @if ($site['whatsappUrl']) target="_blank" rel="noopener" @endif>{{ __('Tərəfdaşlıq üçün yazın') }}</a>
        </div>
        @if ($site['catalogUrl'])
        <div class="b2b-card b2b-cat">
          <div>
            <h3>{{ __('Tam məhsul kataloqu') }}</h3>
            <p>{{ __('Bütün məhsulların tərkibi, qablaşdırma ölçüləri və qeydiyyat məlumatları bir faylda.') }}</p>
          </div>
          <a href="{{ $site['catalogUrl'] }}" class="btn btn-teal" target="_blank" rel="noopener">{{ __('Kataloqu yüklə') }}</a>
        </div>
        @endif
      </div>
    </div>
  </section>

  @if (count($data['certs']))
  <!-- Sertifikatlar — mobil bölmə (masaüstündə sol zolaq göstərilir) -->
  <section class="block cert-section" id="certificates" aria-labelledby="certTitle">
    <div class="wrap">
      <div class="sec-head"><div><h2 id="certTitle">{{ __('Sertifikatlar') }}</h2></div></div>
      <div class="cert-grid" id="certGrid"></div>
    </div>
  </section>
  @endif

  <!-- Bizə yazın -->
  <section class="block" id="write" aria-labelledby="writeTitle">
    <div class="wrap">
      <div class="contact-cta">
        <div>
          <h2 id="writeTitle">{{ __('Bizə yazın') }}</h2>
          <p>{{ __('Sual, təklif və ya əməkdaşlıq üçün formu doldurun — tezliklə sizinlə əlaqə saxlayacağıq.') }}</p>
        </div>
        <button type="button" class="btn btn-sun" data-contact-open>{{ __('Formu doldur') }}</button>
      </div>
      @if (session('contact_success'))
        <div class="form-ok" role="status">{{ session('contact_success') }}</div>
      @endif
    </div>
  </section>
</main>

<footer id="contact">
  <div class="wrap">
    <div>
      <a href="{{ url('/') }}" class="logo" style="margin-bottom:14px"><img class="logo-img" src="{{ asset('assets/site/logo.png') }}" alt="Orizon Labs"></a>
      <p style="margin:0;max-width:34ch">{{ __('Dərman vasitələri, qida əlavələri və kosmetika istehsalçısı. Bakı, Azərbaycan.') }}</p>
    </div>
    <div>
      <h4>{{ __('Məhsullar') }}</h4>
      <ul id="footCats"></ul>
    </div>
    <div>
      <h4>{{ __('Şirkət') }}</h4>
      <ul>
        <li><a href="#about">{{ __('Haqqımızda') }}</a></li>
        <li><a href="{{ count($data['photos']) || count($data['videos']) ? '#gallery' : '#top' }}">{{ __('Qalereya') }}</a></li>
        @if (count($data['news']))<li><a href="#news">{{ __('Xəbərlər') }}</a></li>@endif
        <li><a href="#partners">{{ __('Tərəfdaşlar üçün') }}</a></li>
        @foreach ($site['social'] as $name => $link)
          <li><a href="{{ $link }}" target="_blank" rel="noopener">{{ $name }}</a></li>
        @endforeach
      </ul>
    </div>
    <div>
      <h4>{{ __('Əlaqə') }}</h4>
      <ul>
        @if ($site['phone'])<li><a href="{{ $site['phoneHref'] }}">{{ $site['phone'] }}</a></li>@endif
        @if ($site['email'])<li><a href="mailto:{{ $site['email'] }}">{{ $site['email'] }}</a></li>@endif
        @if ($site['address'])<li>{{ $site['address'] }}</li>@endif
        @if ($site['whatsappUrl'])<li><a href="{{ $site['whatsappUrl'] }}" target="_blank" rel="noopener">{{ __('WhatsApp ilə yazın') }}</a></li>@endif
      </ul>
    </div>
  </div>
  <div class="foot-bottom">
    <div class="wrap">
      <span>© {{ date('Y') }} Orizon Labs Pharmaceuticals. {{ __('Bütün hüquqlar qorunur.') }}</span>
      <span>{{ __('Saytdakı məlumat tibbi məsləhət deyil. İstifadədən əvvəl həkimə müraciət edin.') }}</span>
    </div>
  </div>
</footer>

@if ($site['whatsappUrl'])
<a class="wa" href="{{ $site['whatsappUrl'] }}" target="_blank" rel="noopener" aria-label="{{ __('WhatsApp ilə yazın') }}">
  <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm0 18.2a8.2 8.2 0 0 1-4.2-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8s-.4-.1-.6.1-.7.8-.8 1-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.3-.4.2-.4.7-1.3a.5.5 0 0 0 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 3 3 0 0 0-.9 2.2 5.2 5.2 0 0 0 1.1 2.7 11.8 11.8 0 0 0 4.5 4c1.7.7 2.3.8 3.2.6a2.7 2.7 0 0 0 1.8-1.2 2.2 2.2 0 0 0 .1-1.2c0-.1-.2-.2-.4-.3z"></path></svg>
</a>
@endif

<!-- Yuxarı qalx -->
<button type="button" class="to-top" id="toTop" aria-label="{{ __('Yuxarı qalx') }}">
  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
</button>

@if (count($data['certs']))
<!-- Sertifikatlar — sol üzən zolaq -->
<aside class="cert-strip" id="certStrip" aria-label="{{ __('Sertifikatlar') }}"></aside>
@endif

@if (count($site['social']))
<!-- Sosial şəbəkələr — sağ üzən zolaq (Ayarlardan) -->
<aside class="social-strip" aria-label="{{ __('Sosial şəbəkələr') }}">
  @foreach ($site['social'] as $name => $link)
    <a href="{{ $link }}" target="_blank" rel="noopener" class="social-ico social-{{ strtolower($name) }}" aria-label="{{ $name }}" title="{{ $name }}">
      @switch(strtolower($name))
        @case('instagram')
          <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.2c3.2 0 3.58.01 4.85.07 1.17.05 1.8.25 2.23.41.56.22.96.48 1.38.9.42.42.68.82.9 1.38.16.42.36 1.06.41 2.23.06 1.27.07 1.65.07 4.85s-.01 3.58-.07 4.85c-.05 1.17-.25 1.8-.41 2.23-.22.56-.48.96-.9 1.38-.42.42-.82.68-1.38.9-.42.16-1.06.36-2.23.41-1.27.06-1.65.07-4.85.07s-3.58-.01-4.85-.07c-1.17-.05-1.8-.25-2.23-.41a3.7 3.7 0 0 1-1.38-.9 3.7 3.7 0 0 1-.9-1.38c-.16-.42-.36-1.06-.41-2.23C2.21 15.58 2.2 15.2 2.2 12s.01-3.58.07-4.85c.05-1.17.25-1.8.41-2.23.22-.56.48-.96.9-1.38.42-.42.82-.68 1.38-.9.42-.16 1.06-.36 2.23-.41C8.42 2.21 8.8 2.2 12 2.2zm0 1.8c-3.15 0-3.5.01-4.73.07-.9.04-1.39.19-1.72.32-.43.17-.74.37-1.06.69-.32.32-.52.63-.69 1.06-.13.33-.28.82-.32 1.72C3.41 9.09 3.4 9.44 3.4 12s.01 2.91.07 4.14c.04.9.19 1.39.32 1.72.17.43.37.74.69 1.06.32.32.63.52 1.06.69.33.13.82.28 1.72.32 1.23.06 1.58.07 4.73.07s3.5-.01 4.73-.07c.9-.04 1.39-.19 1.72-.32.43-.17.74-.37 1.06-.69.32-.32.52-.63.69-1.06.13-.33.28-.82.32-1.72.06-1.23.07-1.58.07-4.14s-.01-2.91-.07-4.14c-.04-.9-.19-1.39-.32-1.72a2.85 2.85 0 0 0-.69-1.06 2.85 2.85 0 0 0-1.06-.69c-.33-.13-.82-.28-1.72-.32C15.5 4.01 15.15 4 12 4zm0 3.07A4.93 4.93 0 1 1 12 17a4.93 4.93 0 0 1 0-9.86zm0 1.8a3.13 3.13 0 1 0 0 6.26 3.13 3.13 0 0 0 0-6.26zm5.13-.96a1.15 1.15 0 1 1 0 2.3 1.15 1.15 0 0 1 0-2.3z"/></svg>
          @break
        @case('facebook')
          <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M22 12a10 10 0 1 0-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.89h-2.34v6.99A10 10 0 0 0 22 12z"/></svg>
          @break
        @case('linkedin')
          <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.45 20.45h-3.56v-5.57c0-1.33-.03-3.04-1.85-3.04-1.86 0-2.14 1.45-2.14 2.94v5.67H9.34V9h3.42v1.56h.05c.48-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.46v6.28zM5.34 7.43a2.07 2.07 0 1 1 0-4.14 2.07 2.07 0 0 1 0 4.14zM7.12 20.45H3.56V9h3.56v11.45zM22.22 0H1.77C.8 0 0 .78 0 1.75v20.5C0 23.22.8 24 1.77 24h20.45c.98 0 1.78-.78 1.78-1.75V1.75C24 .78 23.2 0 22.22 0z"/></svg>
          @break
        @default
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c3 3 3 15 0 18M12 3c-3 3-3 15 0 18"/></svg>
      @endswitch
    </a>
  @endforeach
</aside>
@endif

<!-- Bizə yazın — modal forma -->
<div class="modal" id="contactModal" role="dialog" aria-modal="true" aria-labelledby="contactTitle" aria-hidden="true" @if ($errors->any()) data-autoopen="1" @endif>
  <div class="contact-box">
    <button type="button" class="modal-close" data-contact-close aria-label="{{ __('Bağla') }}"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M6 6l12 12M18 6 6 18"/></svg></button>
    <h2 id="contactTitle">{{ __('Bizə yazın') }}</h2>
    <p class="contact-sub">{{ __('Formu doldurun, tezliklə sizinlə əlaqə saxlayacağıq.') }}</p>
    <form method="POST" action="{{ route('contact.send') }}" class="contact-form">
      @csrf
      <div class="row-2">
        <div class="field">
          <label for="c_name">{{ __('Ad') }} <span class="req">*</span></label>
          <input id="c_name" name="name" type="text" value="{{ old('name') }}" required maxlength="255" autocomplete="given-name">
          @error('name')<div class="error-text">{{ $message }}</div>@enderror
        </div>
        <div class="field">
          <label for="c_surname">{{ __('Soyad') }} <span class="req">*</span></label>
          <input id="c_surname" name="surname" type="text" value="{{ old('surname') }}" required maxlength="255" autocomplete="family-name">
          @error('surname')<div class="error-text">{{ $message }}</div>@enderror
        </div>
      </div>
      <div class="row-2">
        <div class="field">
          <label for="c_email">{{ __('E-poçt') }}</label>
          <input id="c_email" name="email" type="email" value="{{ old('email') }}" maxlength="255" autocomplete="email">
          @error('email')<div class="error-text">{{ $message }}</div>@enderror
        </div>
        <div class="field">
          <label for="c_phone">{{ __('Telefon') }}</label>
          <input id="c_phone" name="phone" type="tel" value="{{ old('phone') }}" maxlength="50" autocomplete="tel">
          @error('phone')<div class="error-text">{{ $message }}</div>@enderror
        </div>
      </div>
      <div class="field">
        <label for="c_message">{{ __('Mesaj') }} <span class="req">*</span></label>
        <textarea id="c_message" name="message" rows="4" required maxlength="5000">{{ old('message') }}</textarea>
        @error('message')<div class="error-text">{{ $message }}</div>@enderror
      </div>
      <button type="submit" class="btn btn-navy">{{ __('Göndər') }}</button>
    </form>
  </div>
</div>

<!-- Məhsul pəncərəsi -->
<div class="modal" id="modal" role="dialog" aria-modal="true" aria-labelledby="mTitle" aria-hidden="true">
  <div class="modal-box" id="modalBox"></div>
</div>

<!-- Şəkil qalereyası (lightbox) -->
<div class="lightbox" id="lightbox" role="dialog" aria-modal="true" aria-label="{{ __('Şəkil') }}" aria-hidden="true">
  <span class="lb-count" id="lbCount"></span>
  <button type="button" class="lb-btn lb-close" id="lbClose" aria-label="{{ __('Bağla') }}"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M6 6l12 12M18 6 6 18"/></svg></button>
  <button type="button" class="lb-btn lb-prev" id="lbPrev" aria-label="{{ __('Əvvəlki şəkil') }}"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m15 5-7 7 7 7"/></svg></button>
  <figure><img id="lbImg" src="" alt=""><figcaption id="lbCap"></figcaption></figure>
  <button type="button" class="lb-btn lb-next" id="lbNext" aria-label="{{ __('Növbəti şəkil') }}"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m9 5 7 7-7 7"/></svg></button>
</div>

<script>window.ORIZON = @json($data);</script>
<script src="{{ asset('assets/site/site.js') }}?v={{ filemtime(public_path('assets/site/site.js')) }}"></script>
</body>
</html>
