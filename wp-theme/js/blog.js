/* =============================================================
   Blog article: reading progress, table of contents highlighting,
   floating "Contents" button, back-to-top and copy-link.
   Loaded on single posts only (inc/blog.php).
   ============================================================= */
(function () {
  "use strict";

  function $(s, c) { return (c || document).querySelector(s); }
  function $$(s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); }

  var reduce = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  function init() {
    var body = $("#bprose");
    var bar = $("#rprog");
    var toc = $("#btoc");
    var tocBar = $(".btoc__prog i");
    var float = $("#bfloat");
    var floatToc = $("#bfloatToc");
    var sheet = $("#bsheet");
    var links = $$(".tocl a");

    // Headings in reading order, one per TOC entry.
    var heads = [];
    links.forEach(function (a) {
      var h = document.getElementById(a.getAttribute("data-id"));
      if (h && heads.indexOf(h) < 0) heads.push(h);
    });

    /* ---- scroll-driven state ---- */
    function progress() {
      if (!body) return 0;
      var r = body.getBoundingClientRect(), vh = window.innerHeight;
      var span = r.height - vh * .5;
      if (span <= 0) return r.top < vh * .25 ? 1 : 0;
      return Math.max(0, Math.min(1, (vh * .25 - r.top) / span));
    }

    var current = null;
    function setActive(id) {
      if (id === current) return;
      current = id;
      links.forEach(function (a) {
        var on = a.getAttribute("data-id") === id;
        a.classList.toggle("is-on", on);
        if (on) a.setAttribute("aria-current", "location");
        else a.removeAttribute("aria-current");
      });
      // Keep the highlighted entry visible inside the sidebar's own scroll area.
      var list = toc && $(".tocl", toc), act = list && $("a.is-on", list);
      if (act && list.scrollHeight > list.clientHeight) {
        var lr = list.getBoundingClientRect(), ar = act.getBoundingClientRect();
        if (ar.top < lr.top || ar.bottom > lr.bottom) {
          list.scrollTop += ar.top - lr.top - (lr.height - ar.height) / 2;
        }
      }
    }

    function tocInView() {
      if (!toc || toc.offsetParent === null) return false; // hidden below 1024px
      var r = toc.getBoundingClientRect();
      return r.bottom > 0 && r.top < window.innerHeight;
    }

    var ticking = false;
    function paint() {
      ticking = false;
      var p = progress();
      if (bar) bar.style.transform = "scaleX(" + p + ")";
      if (tocBar) tocBar.style.transform = "scaleX(" + p + ")";

      if (heads.length) {
        var line = Math.max(140, window.innerHeight * .25), id = null;
        for (var i = 0; i < heads.length; i++) {
          if (heads[i].getBoundingClientRect().top <= line) id = heads[i].id;
          else break;
        }
        setActive(id);
      }

      if (float) {
        float.classList.toggle("is-on", (window.scrollY || 0) > 480);
        if (floatToc) float.classList.toggle("show-toc", !tocInView());
      }
    }
    function onScroll() {
      if (!ticking) { ticking = true; requestAnimationFrame(paint); }
    }
    paint();
    window.addEventListener("scroll", onScroll, { passive: true });
    window.addEventListener("resize", onScroll);

    /* ---- floating contents sheet ---- */
    if (floatToc && sheet) {
      var open = function (v, refocus) {
        sheet.hidden = !v;
        floatToc.setAttribute("aria-expanded", v ? "true" : "false");
        if (v) {
          var a = $("a.is-on", sheet) || $("a", sheet);
          if (a) { a.focus({ preventScroll: true }); a.scrollIntoView({ block: "nearest" }); }
        } else if (refocus) {
          floatToc.focus({ preventScroll: true });
        }
      };
      floatToc.addEventListener("click", function () { open(sheet.hidden, true); });
      $("#bsheetClose").addEventListener("click", function () { open(false, true); });
      sheet.addEventListener("click", function (e) { if (e.target === sheet) open(false, true); });
      $$("a", sheet).forEach(function (a) {
        a.addEventListener("click", function () { open(false, false); });
      });
      document.addEventListener("keydown", function (e) {
        if (e.key === "Escape" && !sheet.hidden) open(false, true);
      });
    }

    /* ---- back to top ---- */
    var top = $("#btop");
    if (top) {
      top.addEventListener("click", function () {
        window.scrollTo({ top: 0, behavior: reduce ? "auto" : "smooth" });
      });
    }

    /* ---- copy link ---- */
    var msg = $("#bshareMsg");
    $$("[data-copy]").forEach(function (b) {
      b.addEventListener("click", function () {
        var url = b.getAttribute("data-copy");
        function done() {
          b.classList.add("is-done");
          if (msg) msg.textContent = "Link copied to clipboard";
          setTimeout(function () { b.classList.remove("is-done"); if (msg) msg.textContent = ""; }, 1800);
        }
        function fallback() {
          var t = document.createElement("textarea");
          t.value = url;
          t.setAttribute("readonly", "");
          t.style.position = "fixed";
          t.style.opacity = "0";
          document.body.appendChild(t);
          t.select();
          try { if (document.execCommand("copy")) done(); } catch (err) { /* nothing to do */ }
          document.body.removeChild(t);
        }
        if (navigator.clipboard && window.isSecureContext) navigator.clipboard.writeText(url).then(done, fallback);
        else fallback();
      });
    });
  }

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", init);
  else init();
})();
