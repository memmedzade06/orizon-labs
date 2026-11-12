(function () {
  "use strict";

  /* =========================================================
     MƏLUMATLAR — admin paneldən (HomeController → window.ORIZON)
     ========================================================= */
  const DATA     = window.ORIZON || {};
  const SLIDES   = DATA.slides   || [];
  const CATS     = DATA.cats     || [];
  const ALLCATS  = DATA.allcats  || CATS;
  const PRODUCTS = DATA.products || [];
  const NEWS     = DATA.news     || [];
  const PHOTOS   = DATA.photos   || [];
  const VIDEOS   = DATA.videos   || [];
  const CERTS    = DATA.certs    || [];
  const WHATSAPP = DATA.whatsapp || "";
  const I18N     = DATA.i18n     || {};

  // Tərcümə köməkçisi: açar yoxdursa azərbaycanca fallback işlədilir
  const t = (key, fallback) => (I18N[key] != null ? I18N[key] : fallback);

  const $ = (s, el = document) => el.querySelector(s);
  const esc = s => String(s == null ? "" : s).replace(/[&<>"']/g, c => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c]));
  const catOf = id => ALLCATS.find(c => c.id === id) || { name: "" };
  const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  // Şəkil və ya (şəkil yoxdursa) boş yer tutucu
  const media = (src, alt, cls) => src
    ? `<img src="${esc(src)}" alt="${esc(alt || "")}" loading="lazy"${cls ? ` class="${cls}"` : ""}>`
    : `<span class="ph" aria-hidden="true"></span>`;

  /* =========================================================
     SLAYDER
     ========================================================= */
  if (SLIDES.length && $("#slides")) {
    let current = 0, timer = null;

    $("#slides").innerHTML = SLIDES.map((s, i) => `
      <div class="slide${i === 0 ? " is-active" : ""}" role="group" aria-roledescription="slide" aria-label="${i + 1} / ${SLIDES.length}">
        ${s.img
          ? `<span class="slide-shade" aria-hidden="true"></span>
             <div class="slide-bg" style="background-image:url('${esc(s.img)}')"></div>
             <span class="slide-shade-in" aria-hidden="true"></span>`
          : `<div class="slide-noimg" aria-hidden="true"></div>`}
        <div class="wrap">
          ${s.eyebrow ? `<div class="slide-step">${esc(s.eyebrow)}</div>` : ""}
          ${i === 0 ? `<h1>${esc(s.title)}</h1>` : `<h2>${esc(s.title)}</h2>`}
          ${s.text ? `<p>${esc(s.text)}</p>` : ""}
          <div class="slide-ctas">
            ${s.btnText && s.btnUrl ? `<a href="${esc(s.btnUrl)}" class="btn btn-sun">${esc(s.btnText)}</a>` : ""}
            ${i === 0 ? `<a href="#about" class="btn btn-ghost">${esc(t("aboutCompany", "Şirkət haqqında"))}</a>` : ""}
          </div>
        </div>
      </div>`).join("");

    $("#dots").innerHTML = SLIDES.map((s, i) =>
      `<button type="button" class="dot" data-go="${i}" aria-label="${i + 1}-ci slayd: ${esc(s.title)}" aria-current="${i === 0}"></button>`).join("");

    const single = SLIDES.length < 2;
    if (single) $(".slider-ui").style.display = "none";

    const go = i => {
      current = (i + SLIDES.length) % SLIDES.length;
      document.querySelectorAll(".slide").forEach((el, k) => el.classList.toggle("is-active", k === current));
      document.querySelectorAll(".dot").forEach((d, k) => d.setAttribute("aria-current", k === current));
      $("#counter").textContent = `${current + 1} / ${SLIDES.length}`;
    };
    const stop = () => clearInterval(timer);
    const play = () => { if (!reduceMotion && !single) { stop(); timer = setInterval(() => go(current + 1), 6000); } };

    $("#prev").addEventListener("click", () => { go(current - 1); play(); });
    $("#next").addEventListener("click", () => { go(current + 1); play(); });
    $("#dots").addEventListener("click", e => { const d = e.target.closest("[data-go]"); if (d) { go(+d.dataset.go); play(); } });
    const slider = $(".slider");
    slider.addEventListener("mouseenter", stop);
    slider.addEventListener("mouseleave", play);
    slider.addEventListener("focusin", stop);
    slider.addEventListener("focusout", play);
    let touchX = null;
    slider.addEventListener("touchstart", e => { touchX = e.touches[0].clientX; }, { passive: true });
    slider.addEventListener("touchend", e => {
      if (touchX === null || single) return;
      const dx = e.changedTouches[0].clientX - touchX;
      if (Math.abs(dx) > 40) { go(current + (dx < 0 ? 1 : -1)); play(); }
      touchX = null;
    });
    go(0); play();
  }

  /* =========================================================
     KATEQORİYALAR VƏ MƏHSULLAR
     ========================================================= */
  let activeCat = "all", query = "", sortBy = "popular", limit = 12;

  // Kartdakı say: əsas kateqoriya + onun bütün alt-kateqoriyalarındakı məhsullar
  const countInRoot = slug => PRODUCTS.filter(p => p.root === slug).length;

  function renderCats() {
    $("#cats").innerHTML = CATS.length ? CATS.map(c => {
      // Kartda yalnız qısa təsvir göstərilir; alt-kateqoriyalar yalnız menyu mega-panelindədir
      const panel = c.desc
        ? `<span class="cat-desc"><span class="cat-desc-text">${esc(c.desc)}</span></span>`
        : "";
      return `
      <button type="button" class="cat" data-cat="${esc(c.id)}" aria-pressed="${activeCat === c.id}">
        <span class="cat-text"><span class="cat-name">${esc(c.name)}</span></span>
        ${c.img ? `<span class="cat-art" aria-hidden="true"><img src="${esc(c.img)}" alt="" loading="lazy"></span>` : ""}
        <span class="cat-count">${countInRoot(c.id)} ${esc(t("product", "məhsul"))}</span>
        ${panel}
      </button>`;
    }).join("")
      : `<div class="empty"><b>${esc(t("noCategory", "Kateqoriya yoxdur"))}</b>${esc(t("comingSoon", "Tezliklə əlavə olunacaq."))}</div>`;
  }

  function renderTabs() {
    const all = [{ id: "all", name: t("all", "Hamısı") }].concat(CATS);
    $("#tabs").innerHTML = all.map(c =>
      `<button type="button" role="tab" class="tab" data-cat="${esc(c.id)}" aria-selected="${c.id === activeCat}">${esc(c.name)}</button>`).join("");
  }

  function card(p) {
    const c = catOf(p.cat);
    const badge = p.isNew ? `<span class="badge badge-new">${esc(t("badgeNew", "Yeni"))}</span>` : p.top ? `<span class="badge badge-own">${esc(t("badgeFeatured", "Ən çox seçilən"))}</span>` : "";
    const chips = [p.type, p.size].filter(Boolean).map(t2 => `<span class="chip">${esc(t2)}</span>`).join("");
    return `<article class="card">
      <div class="card-media">${badge}${media(p.img, p.name)}</div>
      <div class="card-cat">${esc(c.name)}</div>
      <h3 class="card-name">${esc(p.name)}</h3>
      <div class="card-meta">${chips}</div>
      <button type="button" class="btn btn-line btn-sm" data-open="${p.id}">${esc(t("details", "Ətraflı bax"))}</button>
    </article>`;
  }

  function filtered() {
    const q = query.trim().toLocaleLowerCase("az");
    let list = PRODUCTS.filter(p =>
      (activeCat === "all" || p.cat === activeCat || p.root === activeCat) &&
      (!q || [p.name, p.size, p.type, catOf(p.cat).name].join(" ").toLocaleLowerCase("az").includes(q)));
    if (sortBy === "popular") list.sort((a, b) => b.pop - a.pop);
    if (sortBy === "new") list.sort((a, b) => (b.isNew ? 1 : 0) - (a.isNew ? 1 : 0) || b.id - a.id);
    if (sortBy === "az") list.sort((a, b) => String(a.name).localeCompare(String(b.name), "az"));
    return list;
  }

  function renderGrid() {
    const list = filtered();
    const shown = list.slice(0, limit);
    const catName = activeCat === "all" ? t("allProducts", "Bütün məhsullar") : catOf(activeCat).name;
    $("#prodTitle").textContent = query.trim() ? `"${query.trim()}" ${t("searchFor", "üzrə axtarış")}` : catName;
    $("#resultNote").textContent = `${list.length} ${t("found", "məhsul tapıldı")}`;
    $("#grid").innerHTML = shown.length ? shown.map(card).join("")
      : `<div class="empty"><b>${esc(PRODUCTS.length ? t("noResults", "Bu sorğu üzrə məhsul tapılmadı") : t("noProducts", "Məhsul yoxdur"))}</b>${esc(PRODUCTS.length ? t("tryOther", 'Başqa söz yazın və ya "Hamısı" kateqoriyasını seçin.') : t("comingSoon", "Tezliklə əlavə olunacaq."))}</div>`;
    $("#showMore").parentElement.style.display = list.length > limit ? "" : "none";
  }

  function setCategory(id, scroll) {
    activeCat = id; limit = 12;
    renderCats(); renderTabs(); renderGrid();
    if (scroll) $("#products").scrollIntoView({ behavior: reduceMotion ? "auto" : "smooth" });
  }

  document.addEventListener("click", e => {
    const c = e.target.closest("[data-cat]");
    if (c) setCategory(c.dataset.cat, !!c.closest("#cats, #footCats, #catMega"));
  });

  $("#sort").addEventListener("change", e => { sortBy = e.target.value; renderGrid(); });
  $("#showMore").addEventListener("click", () => { limit += 12; renderGrid(); });
  $("#q").addEventListener("input", e => {
    query = e.target.value; limit = 12;
    if (query.trim() && activeCat !== "all") { activeCat = "all"; renderCats(); renderTabs(); }
    renderGrid();
  });
  $("#searchForm").addEventListener("submit", e => {
    e.preventDefault(); query = $("#q").value; renderGrid();
    sugHide();
    $("#products").scrollIntoView({ behavior: reduceMotion ? "auto" : "smooth" });
  });

  /* ---- Anında axtarış təklifləri (dropdown) ---- */
  const sugBox = $("#searchSug");
  function sugHide() { if (sugBox) { sugBox.hidden = true; } }
  function renderSuggestions() {
    if (!sugBox) return;
    const q = $("#q").value.trim().toLocaleLowerCase("az");
    if (!q) { sugBox.innerHTML = ""; sugBox.hidden = true; return; }
    const matches = PRODUCTS.filter(p =>
      [p.name, p.type, p.size, catOf(p.cat).name].join(" ").toLocaleLowerCase("az").includes(q)
    ).slice(0, 6);
    if (!matches.length) {
      sugBox.innerHTML = `<div class="sug-empty">${esc(t("noResults", "Bu sorğu üzrə məhsul tapılmadı"))}</div>`;
    } else {
      sugBox.innerHTML = matches.map(p => `
        <button type="button" class="sug-item" role="option" data-open="${p.id}">
          <span class="sug-thumb">${media(p.img, p.name)}</span>
          <span class="sug-text">
            <span class="sug-name">${esc(p.name)}</span>
            <span class="sug-cat">${esc(catOf(p.cat).name)}${p.type ? " · " + esc(p.type) : ""}</span>
          </span>
        </button>`).join("");
    }
    sugBox.hidden = false;
  }
  if (sugBox) {
    $("#q").addEventListener("input", renderSuggestions);
    $("#q").addEventListener("focus", renderSuggestions);
    $("#q").addEventListener("keydown", e => { if (e.key === "Escape") { sugHide(); $("#q").blur(); } });
    sugBox.addEventListener("click", () => sugHide()); // təklifə klik: məhsul açılır (data-open) + dropdown bağlanır
    document.addEventListener("click", e => { if (!e.target.closest(".search")) sugHide(); });
  }

  $("#footCats").innerHTML = CATS.map(c => `<li><a href="#products" data-cat="${esc(c.id)}">${esc(c.name)}</a></li>`).join("");

  /* =========================================================
     MENYU MEGA-PANELİ — "Kateqoriyalar" üzərinə hover / mobil toxunma
     ========================================================= */
  (function initMega() {
    const li   = document.querySelector("[data-mega]");
    const mega = $("#catMega");
    if (!li || !mega || !CATS.length) return;

    const trigger = li.querySelector("a");

    mega.innerHTML = `<div class="mega-inner">${CATS.map(c => {
      const subs = c.subs || [];
      const head = subs.length
        ? `<button type="button" class="mega-head mega-toggle" aria-expanded="false">
             ${c.img ? `<span class="mega-ico"><img src="${esc(c.img)}" alt="" loading="lazy"></span>` : ""}
             <span class="mega-head-text">${esc(c.name)}<span class="mega-count">${countInRoot(c.id)} ${esc(t("product", "məhsul"))}</span></span>
             <span class="mega-caret" aria-hidden="true"></span>
           </button>`
        : `<a class="mega-head" href="#products" data-cat="${esc(c.id)}">
             ${c.img ? `<span class="mega-ico"><img src="${esc(c.img)}" alt="" loading="lazy"></span>` : ""}
             <span class="mega-head-text">${esc(c.name)}<span class="mega-count">${countInRoot(c.id)} ${esc(t("product", "məhsul"))}</span></span>
           </a>`;
      const list = subs.length
        ? `<ul class="mega-subs">
             <li><a class="mega-all" href="#products" data-cat="${esc(c.id)}">${esc(t("allOf", "Bütün"))} ${esc(c.name)}</a></li>
             ${subs.map(s => `<li><a href="#products" data-cat="${esc(s.id)}">${esc(s.name)}</a></li>`).join("")}
           </ul>`
        : "";
      return `<div class="mega-col">${head}${list}</div>`;
    }).join("")}</div>`;

    const mq = window.matchMedia("(max-width:860px)");
    let timer = null;
    const open = () => {
      clearTimeout(timer);
      mega.classList.add("open");
      mega.setAttribute("aria-hidden", "false");
      trigger.setAttribute("aria-expanded", "true");
    };
    const close = () => {
      mega.classList.remove("open");
      mega.setAttribute("aria-hidden", "true");
      trigger.setAttribute("aria-expanded", "false");
    };
    const closeSoon = () => { clearTimeout(timer); timer = setTimeout(close, 140); };

    // Masaüstü: hover ilə açılır
    li.addEventListener("mouseenter", () => { if (!mq.matches) open(); });
    li.addEventListener("mouseleave", () => { if (!mq.matches) closeSoon(); });
    mega.addEventListener("mouseenter", () => { if (!mq.matches) open(); });
    mega.addEventListener("mouseleave", () => { if (!mq.matches) closeSoon(); });
    li.addEventListener("focusin", () => { if (!mq.matches) open(); });

    // Mobil: "Kateqoriyalar"a toxununca panel açılır/bağlanır
    trigger.addEventListener("click", e => {
      if (mq.matches) {
        e.preventDefault();
        mega.classList.contains("open") ? close() : open();
      }
    });

    mega.addEventListener("click", e => {
      // Mobil akkordeon: başlığa toxununca alt-kateqoriyalar açılır/yığılır
      const tog = e.target.closest(".mega-toggle");
      if (tog && mq.matches) {
        e.preventDefault();
        const col = tog.closest(".mega-col");
        const isOpen = col.classList.toggle("open");
        tog.setAttribute("aria-expanded", isOpen ? "true" : "false");
        return;
      }
      // Kateqoriya / alt-kateqoriya seçildi → menyunu bağla
      if (e.target.closest("[data-cat]")) close();
    });

    document.addEventListener("keydown", e => { if (e.key === "Escape") close(); });
  })();

  /* =========================================================
     MƏHSUL PƏNCƏRƏSİ
     ========================================================= */
  const modal = $("#modal");
  let lastFocus = null;

  const section = (title, text) => text ? `<h3>${title}</h3><p class="rich">${esc(text)}</p>` : "";

  function openProduct(id) {
    const p = PRODUCTS.find(x => x.id === id);
    if (!p) return;
    const c = catOf(p.cat);
    const label = p.size ? `${p.name} (${p.size})` : p.name;
    const waText = `${t("waHello", "Salam,")} "${label}" ${t("waProduct", "məhsulu haqqında məlumat almaq istəyirəm.")}`;
    const wa = WHATSAPP ? "https://wa.me/" + WHATSAPP + "?text=" + encodeURIComponent(waText) : "";
    const imgs = p.images || [];
    const hasInfo = p.description || p.composition || p.usage || p.warnings || p.short;

    $("#modalBox").className = "modal-box";
    $("#modalBox").innerHTML = `
      <button type="button" class="modal-close" id="mClose" aria-label="${esc(t("close", "Bağla"))}"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M6 6l12 12M18 6 6 18"/></svg></button>
      <div class="modal-media">
        ${imgs.length ? `<img class="main-img" id="mImg" src="${esc(imgs[0])}" alt="${esc(p.name)}">` : `<span class="ph" aria-hidden="true"></span>`}
        ${imgs.length > 1 ? `<div class="thumbs">${imgs.map((src, i) =>
          `<button type="button" data-thumb="${esc(src)}" aria-label="${i + 1}" aria-current="${i === 0}"><img src="${esc(src)}" alt=""></button>`).join("")}</div>` : ""}
      </div>
      <div class="modal-body">
        <div class="card-cat">${esc(c.name)}</div>
        <h2 id="mTitle">${esc(p.name)}</h2>
        <dl class="spec">
          ${p.type ? `<dt>${esc(t("form", "Forma"))}</dt><dd>${esc(p.type)}</dd>` : ""}
          ${p.size ? `<dt>${esc(t("package", "Qablaşdırma"))}</dt><dd>${esc(p.size)}</dd>` : ""}
          <dt>${esc(t("maker", "İstehsalçı"))}</dt><dd>${esc(t("makerName", "Orizon Labs, Azərbaycan"))}</dd>
        </dl>
        ${p.short && !p.description ? section(esc(t("descTitle", "Təsviri")), p.short) : section(esc(t("descTitle", "Təsviri")), p.description)}
        ${section(esc(t("composition", "Tərkibi")), p.composition)}
        ${section(esc(t("usage", "İstifadə qaydası")), p.usage)}
        ${section(esc(t("warnings", "Əks göstərişlər")), p.warnings)}
        ${hasInfo ? "" : `<h3>${esc(t("descTitle", "Təsviri"))}</h3><p>${esc(t("noInfo", "Məhsul haqqında ətraflı məlumat üçün bizimlə əlaqə saxlayın."))}</p>`}
        <h3>${esc(t("whereBuy", "Harada almaq olar"))}</h3>
        <p>${esc(t("whereBuyText", "Azərbaycandakı tərəfdaş apteklərdə. Ən yaxın satış nöqtəsini öyrənmək üçün bizə yazın."))}</p>
        ${wa ? `<div class="modal-ctas"><a href="${wa}" class="btn btn-teal btn-sm" target="_blank" rel="noopener">${esc(t("askWhatsapp", "WhatsApp ilə soruş"))}</a></div>` : ""}
      </div>`;
    lastFocus = document.activeElement;
    modal.classList.add("open");
    modal.setAttribute("aria-hidden", "false");
    document.body.classList.add("lock");
    $("#mClose").focus();
  }
  function closeProduct() {
    modal.classList.remove("open");
    modal.setAttribute("aria-hidden", "true");
    document.body.classList.remove("lock");
    if (lastFocus) lastFocus.focus();
  }
  document.addEventListener("click", e => {
    const o = e.target.closest("[data-open]");
    if (o) openProduct(+o.dataset.open);
    if (e.target.closest("#mClose") || e.target === modal) closeProduct();
    const th = e.target.closest("[data-thumb]");
    if (th) {
      $("#mImg").src = th.dataset.thumb;
      document.querySelectorAll("[data-thumb]").forEach(b => b.setAttribute("aria-current", b === th));
    }
  });

  // Pəncərədə Tab fokusunu içəridə saxlamaq
  function trapFocus(e, box) {
    const f = box.querySelectorAll("a, button");
    if (!f.length) return;
    const first = f[0], last = f[f.length - 1];
    if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
    else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
  }

  /* =========================================================
     İSTEHSALAT QALEREYASI
     ========================================================= */
  const lightbox = $("#lightbox");
  let lbIndex = 0, photoLimit = 8, lbLastFocus = null;

  function renderPhotos() {
    const grid = $("#photoGrid");
    if (!grid) return;
    grid.innerHTML = PHOTOS.slice(0, photoLimit).map((ph, i) => `
      <button type="button" class="photo-item" data-photo="${i}" aria-label="${esc(ph.title || t("zoomPhoto", "Şəkli böyüt"))}">
        <img src="${esc(ph.img)}" alt="${esc(ph.title || "")}" loading="lazy">
        ${ph.title ? `<span class="cap">${esc(ph.title)}</span>` : ""}
      </button>`).join("");
    $("#morePhotos").parentElement.style.display = PHOTOS.length > photoLimit ? "" : "none";
  }

  function renderVideos() {
    const grid = $("#videoGrid");
    if (!grid) return;
    grid.innerHTML = VIDEOS.map((v, i) => `
      <article class="video-item">
        <div class="video-frame" id="vf${i}">
          ${v.thumb ? `<img src="${esc(v.thumb)}" alt="" loading="lazy">` : ""}
          <button type="button" class="video-play" data-video="${i}" aria-label="${esc(t("playVideo", "Videonu oynat"))}: ${esc(v.title)}">
            <span><svg width="22" height="22" viewBox="0 0 24 24" fill="#153F70" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg></span>
          </button>
        </div>
        <h3>${esc(v.title)}</h3>
      </article>`).join("");
  }

  function showPhoto(i) {
    lbIndex = (i + PHOTOS.length) % PHOTOS.length;
    const ph = PHOTOS[lbIndex];
    $("#lbImg").src = ph.img;
    $("#lbImg").alt = ph.title || "";
    $("#lbCap").textContent = ph.title || "";
    $("#lbCount").textContent = `${lbIndex + 1} / ${PHOTOS.length}`;
    const many = PHOTOS.length > 1;
    $("#lbPrev").hidden = !many;
    $("#lbNext").hidden = !many;
  }
  function openLightbox(i) {
    lbLastFocus = document.activeElement;
    showPhoto(i);
    lightbox.classList.add("open");
    lightbox.setAttribute("aria-hidden", "false");
    document.body.classList.add("lock");
    $("#lbClose").focus();
  }
  function closeLightbox() {
    lightbox.classList.remove("open");
    lightbox.setAttribute("aria-hidden", "true");
    document.body.classList.remove("lock");
    if (lbLastFocus) lbLastFocus.focus();
  }

  if (lightbox) {
    document.addEventListener("click", e => {
      const ph = e.target.closest("[data-photo]");
      if (ph) openLightbox(+ph.dataset.photo);

      const v = e.target.closest("[data-video]");
      if (v) {
        const video = VIDEOS[+v.dataset.video];
        const sep = video.embed.includes("?") ? "&" : "?";
        $("#vf" + v.dataset.video).innerHTML =
          `<iframe src="${esc(video.embed)}${sep}autoplay=1" title="${esc(video.title)}" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>`;
      }

      const tab = e.target.closest("[data-gtab]");
      if (tab) {
        document.querySelectorAll("[data-gtab]").forEach(b => b.setAttribute("aria-selected", b === tab));
        document.querySelectorAll("[data-gpane]").forEach(p => { p.hidden = p.dataset.gpane !== tab.dataset.gtab; });
      }
    });
    $("#lbClose").addEventListener("click", closeLightbox);
    $("#lbPrev").addEventListener("click", () => showPhoto(lbIndex - 1));
    $("#lbNext").addEventListener("click", () => showPhoto(lbIndex + 1));
    lightbox.addEventListener("click", e => { if (e.target === lightbox || e.target.tagName === "FIGURE") closeLightbox(); });
    const more = $("#morePhotos");
    if (more) more.addEventListener("click", () => { photoLimit += 8; renderPhotos(); });
  }

  document.addEventListener("keydown", e => {
    if (modal.classList.contains("open")) {
      if (e.key === "Escape") closeProduct();
      if (e.key === "Tab") trapFocus(e, modal);
      return;
    }
    if (lightbox && lightbox.classList.contains("open")) {
      if (e.key === "Escape") closeLightbox();
      if (e.key === "ArrowLeft") showPhoto(lbIndex - 1);
      if (e.key === "ArrowRight") showPhoto(lbIndex + 1);
      if (e.key === "Tab") trapFocus(e, lightbox);
    }
  });

  /* =========================================================
     XƏBƏRLƏR — kart + tam mətn modalı + "Bütün Xəbərlər"
     ========================================================= */
  const newsGrid = $("#newsGrid");
  let newsLimit = 3;

  // Xəbərin tam mətnini məhsul modalında göstərir
  function openNews(i) {
    const n = NEWS[i];
    if (!n) return;
    $("#modalBox").className = "modal-box";
    $("#modalBox").innerHTML = `
      <button type="button" class="modal-close" id="mClose" aria-label="${esc(t("close", "Bağla"))}"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M6 6l12 12M18 6 6 18"/></svg></button>
      <div class="modal-media">${n.img ? `<img class="main-img" src="${esc(n.img)}" alt="${esc(n.title)}">` : `<span class="ph" aria-hidden="true"></span>`}</div>
      <div class="modal-body">
        <div class="news-meta">${n.tag ? `<span class="news-tag">${esc(n.tag)}</span>` : ""}${n.date ? `<time datetime="${esc(n.iso)}">${esc(n.date)}</time>` : ""}</div>
        <h2 id="mTitle">${esc(n.title)}</h2>
        ${n.body ? `<div class="rich">${esc(n.body)}</div>` : (n.text ? `<div class="rich">${esc(n.text)}</div>` : "")}
      </div>`;
    lastFocus = document.activeElement;
    modal.classList.add("open");
    modal.setAttribute("aria-hidden", "false");
    document.body.classList.add("lock");
    $("#mClose").focus();
  }

  function renderNews() {
    if (!newsGrid) return;
    newsGrid.innerHTML = NEWS.slice(0, newsLimit).map((n, i) => {
      const clickable = n.body || n.text;
      return `
      <article class="news-card${i === 0 ? " lead" : ""}${clickable ? " is-link" : ""}"${clickable ? ` data-news="${i}" role="button" tabindex="0"` : ""}>
        <div class="news-thumb">${n.img ? `<img src="${esc(n.img)}" alt="" loading="lazy">` : `<span class="ph" aria-hidden="true"></span>`}</div>
        <div class="news-body">
          <div class="news-meta">${n.tag ? `<span class="news-tag">${esc(n.tag)}</span>` : ""}${n.date ? `<time datetime="${esc(n.iso)}">${esc(n.date)}</time>` : ""}</div>
          <h3>${esc(n.title)}</h3>
          ${n.text ? `<p>${esc(n.text)}</p>` : ""}
          ${clickable ? `<span class="news-read">${esc(t("readMore", "Ətraflı oxu"))} →</span>` : ""}
        </div>
      </article>`;
    }).join("");
    const more = $("#newsMore");
    if (more) more.style.display = NEWS.length > newsLimit ? "" : "none";
  }

  if (newsGrid) {
    newsGrid.addEventListener("click", e => {
      const nw = e.target.closest("[data-news]");
      if (nw) openNews(+nw.dataset.news);
    });
    newsGrid.addEventListener("keydown", e => {
      const nw = e.target.closest("[data-news]");
      if (nw && (e.key === "Enter" || e.key === " ")) { e.preventDefault(); openNews(+nw.dataset.news); }
    });
    const smn = $("#showMoreNews");
    if (smn) smn.addEventListener("click", () => { newsLimit = NEWS.length; renderNews(); });
  }

  /* =========================================================
     SERTİFİKATLAR — sol üzən zolaq
     ========================================================= */
  const certStrip = $("#certStrip");
  const certGrid  = $("#certGrid");

  // Açılan modalda yalnız sertifikatın şəkli olsun
  function openCert(i) {
    const c = CERTS[i];
    if (!c) return;
    const box = $("#modalBox");
    box.className = "modal-box modal-cert";
    box.innerHTML = `
      <button type="button" class="modal-close" id="mClose" aria-label="${esc(t("close", "Bağla"))}"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M6 6l12 12M18 6 6 18"/></svg></button>
      <img class="cert-full" src="${esc(c.img)}" alt="${esc(c.title || "")}">`;
    lastFocus = document.activeElement;
    modal.classList.add("open");
    modal.setAttribute("aria-hidden", "false");
    document.body.classList.add("lock");
    $("#mClose").focus();
  }

  const certBadge = (c, i) =>
    `<button type="button" class="cert-badge" data-cert="${i}" title="${esc(c.title || "")}" aria-label="${esc(c.title || t("certificates", "Sertifikatlar"))}"><img src="${esc(c.img)}" alt="${esc(c.title || "")}" loading="lazy"></button>`;

  function renderCerts() {
    if (!CERTS.length) return;
    const badges = CERTS.map(certBadge).join("");
    // Masaüstü: sol üzən zolaq
    if (certStrip) certStrip.innerHTML = `<span class="cert-strip-title">${esc(t("certificates", "Sertifikatlar"))}</span>` + badges;
    // Mobil: səhifə içindəki bölmə
    if (certGrid) certGrid.innerHTML = badges;
  }

  // Həm zolaq, həm də mobil bölmədə klik işləsin
  document.addEventListener("click", e => {
    const ce = e.target.closest("[data-cert]");
    if (ce) openCert(+ce.dataset.cert);
  });

  /* =========================================================
     "BİZƏ YAZIN" — MODAL FORMA
     ========================================================= */
  (function initContact() {
    const cm = $("#contactModal");
    if (!cm) return;
    let cmLastFocus = null;

    const openCm = () => {
      cmLastFocus = document.activeElement;
      cm.classList.add("open");
      cm.setAttribute("aria-hidden", "false");
      document.body.classList.add("lock");
      const f = cm.querySelector("input, textarea");
      if (f) f.focus();
    };
    const closeCm = () => {
      cm.classList.remove("open");
      cm.setAttribute("aria-hidden", "true");
      document.body.classList.remove("lock");
      if (cmLastFocus) cmLastFocus.focus();
    };

    document.addEventListener("click", e => {
      if (e.target.closest("[data-contact-open]")) { e.preventDefault(); openCm(); }
      if (e.target.closest("[data-contact-close]") || e.target === cm) closeCm();
    });
    document.addEventListener("keydown", e => {
      if (e.key === "Escape" && cm.classList.contains("open")) closeCm();
      if (e.key === "Tab" && cm.classList.contains("open")) trapFocus(e, cm);
    });

    // Validasiya xətası olanda modal avtomatik açılsın
    if (cm.dataset.autoopen === "1") openCm();

    // Uğur bildirişi 2 saniyə sonra avtomatik yox olsun
    const ok = $(".form-ok");
    if (ok) setTimeout(() => {
      ok.style.transition = "opacity .4s ease";
      ok.style.opacity = "0";
      setTimeout(() => ok.remove(), 400);
    }, 2000);
  })();

  renderCats(); renderTabs(); renderGrid();
  renderPhotos(); renderVideos(); renderNews(); renderCerts();

  /* =========================================================
     SCROLL ANİMASİYALARI — bölmələr görünəndə yumşaq açılır
     ========================================================= */
  (function initReveal() {
    if (reduceMotion || !("IntersectionObserver" in window)) return;
    const targets = document.querySelectorAll("main > section:not(.slider), footer .wrap > div");
    if (!targets.length) return;
    targets.forEach(el => el.classList.add("reveal"));
    // Hər dəfə bölmə ekrana girəndə təkrar işləsin (girəndə əlavə, çıxanda silinir)
    const io = new IntersectionObserver((entries) => {
      entries.forEach(e => e.target.classList.toggle("in", e.isIntersecting));
    }, { threshold: 0.12, rootMargin: "0px 0px -8% 0px" });
    targets.forEach(el => io.observe(el));
  })();

  /* =========================================================
     ANİMASİYALI RƏQƏM SAYĞACLARI — statistika
     ========================================================= */
  (function initCounters() {
    const nums = document.querySelectorAll(".facts .fact b");
    if (!nums.length) return;

    const run = el => {
      const m = String(el.textContent).trim().match(/^(\D*)([\d\s]+)(.*)$/);
      if (!m) return;
      const prefix = m[1], suffix = m[3];
      const target = parseInt(m[2].replace(/\s/g, ""), 10);
      if (!isFinite(target)) return;
      if (reduceMotion) { el.textContent = prefix + target + suffix; return; }
      const dur = 1300, t0 = performance.now();
      const tick = now => {
        const p = Math.min(1, (now - t0) / dur);
        const val = Math.floor((1 - Math.pow(1 - p, 3)) * target);
        el.textContent = prefix + val + (p >= 1 ? suffix : "");
        if (p < 1) requestAnimationFrame(tick);
      };
      requestAnimationFrame(tick);
    };

    if (!("IntersectionObserver" in window)) { nums.forEach(run); return; }
    const io = new IntersectionObserver((entries, obs) => {
      entries.forEach(e => { if (e.isIntersecting) { run(e.target); obs.unobserve(e.target); } });
    }, { threshold: 0.4 });
    nums.forEach(n => io.observe(n));
  })();

  /* =========================================================
     HEADER SCROLL KÖLGƏSİ + YUXARI QALX DÜYMƏSİ
     ========================================================= */
  (function initChrome() {
    const header = $(".header");
    const toTop = $("#toTop");
    const onScroll = () => {
      const y = window.scrollY || window.pageYOffset || 0;
      if (header) header.classList.toggle("is-stuck", y > 10);
      if (toTop) toTop.classList.toggle("show", y > 500);
    };
    window.addEventListener("scroll", onScroll, { passive: true });
    onScroll();
    if (toTop) toTop.addEventListener("click", () =>
      window.scrollTo({ top: 0, behavior: reduceMotion ? "auto" : "smooth" }));

    // Menyunun sonuna çatanda (və ya sürüşmə yoxdursa) sağdakı işarəni gizlət
    const nav = $(".mainnav");
    const navList = nav && nav.querySelector("ul");
    if (navList) {
      const onNavScroll = () => nav.classList.toggle("at-end",
        navList.scrollLeft + navList.clientWidth >= navList.scrollWidth - 4);
      navList.addEventListener("scroll", onNavScroll, { passive: true });
      window.addEventListener("resize", onNavScroll);
      onNavScroll();
    }

    // Menyu keçidləri: bölmə yapışqan başlığın altında qalmasın — boşluq başlığın real hündürlüyü qədər
    const stickyHead = $(".header");
    if (stickyHead) {
      const setHeadH = () => document.documentElement.style.setProperty("--head-h", stickyHead.offsetHeight + "px");
      window.addEventListener("resize", setHeadH);
      if ("ResizeObserver" in window) new ResizeObserver(setHeadH).observe(stickyHead);
      setHeadH();
    }

    // Tərəfdaş ölkə bayraqları: həmişə sonsuz sürüşən zolaq
    const flagsView = $("#utilFlags");
    const flagsList = flagsView && flagsView.querySelector(".util-flags");
    if (flagsList) {
      const items = Array.from(flagsList.children);
      const STEP = 34; // bayraq eni (24px) + aralıq (10px)
      const clone = li => {
        const c = li.cloneNode(true);
        c.setAttribute("aria-hidden", "true"); // surətlər ekran oxuyucularından gizli
        c.querySelector("img").alt = "";
        return c;
      };
      const buildFlags = () => {
        flagsView.classList.add("is-sliding");
        flagsList.replaceChildren(...items);
        // Bir "dəst" zolağı tam doldurmalıdır, yoxsa az bayraqda boşluq görünür
        const reps = Math.max(1, Math.ceil(flagsView.clientWidth / (items.length * STEP)));
        const set = [];
        for (let r = 1; r < reps; r++) items.forEach(li => set.push(clone(li)));
        const half = items.length + set.length;
        flagsList.append(...set, ...items.map(clone), ...set.map(c => c.cloneNode(true)));
        flagsView.style.setProperty("--flags-dur", (half * 2.2) + "s");
      };
      let lastW = 0;
      window.addEventListener("resize", () => {
        if (flagsView.clientWidth !== lastW) { lastW = flagsView.clientWidth; buildFlags(); }
      });
      lastW = flagsView.clientWidth;
      buildFlags();
    }
  })();
})();
