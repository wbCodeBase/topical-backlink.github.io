/* =============================================================
   TopicalBacklink, interactions
   No framework. GSAP (CDN) drives reveals and the graph timeline,
   but every section degrades to a readable static state without it.
   ============================================================= */
(function () {
  "use strict";

  // Marks the document as script-capable. CSS hides reveal targets only under
  // .js, so a blocked or broken script leaves a fully readable page.
  document.documentElement.classList.add("js");

  var SVGNS = "http://www.w3.org/2000/svg";
  var hasGSAP = typeof window.gsap !== "undefined";
  var reduce = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  var motionOn = hasGSAP && !reduce;

  if (hasGSAP && window.ScrollTrigger) gsap.registerPlugin(ScrollTrigger);

  function el(name, attrs) {
    var n = document.createElementNS(SVGNS, name);
    for (var k in attrs) if (attrs[k] !== undefined) n.setAttribute(k, attrs[k]);
    return n;
  }
  function $(s, c) { return (c || document).querySelector(s); }
  function $$(s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); }

  /* -----------------------------------------------------------
     Reveals. Without GSAP the base [data-rise] styles would leave
     content invisible, so clear them unconditionally first.
     ----------------------------------------------------------- */
  function initReveals() {
    var items = $$("[data-rise]");
    if (!motionOn) {
      items.forEach(function (n) { n.style.opacity = 1; n.style.transform = "none"; });
      return;
    }
    items.forEach(function (n) {
      gsap.to(n, {
        opacity: 1, y: 0, duration: .85, ease: "power3.out",
        scrollTrigger: { trigger: n, start: "top 88%", once: true }
      });
    });
  }

  /* ----------------------------- NAV ----------------------------- */
  function initNav() {
    var nav = $("#nav"), prog = $("#navProgress");
    var sheet = $("#sheet"), burger = $("#burger"), close = $("#sheetClose");

    function onScroll() {
      var y = window.scrollY || 0;
      nav.classList.toggle("is-scrolled", y > 16);
      var max = document.documentElement.scrollHeight - window.innerHeight;
      prog.style.transform = "scaleX(" + (max > 0 ? Math.min(y / max, 1) : 0) + ")";
    }
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });

    function open(v) {
      sheet.hidden = !v;
      burger.setAttribute("aria-expanded", String(v));
      document.body.style.overflow = v ? "hidden" : "";
      if (v && motionOn) {
        gsap.fromTo($$(".sheet__links a, .sheet__cta .btn", sheet),
          { opacity: 0, y: 18 },
          { opacity: 1, y: 0, duration: .45, stagger: .06, ease: "power3.out" });
      }
    }
    burger.addEventListener("click", function () { open(true); });
    close.addEventListener("click", function () { open(false); });
    $$(".sheet__links a, .sheet__cta a", sheet).forEach(function (a) {
      a.addEventListener("click", function () { open(false); });
    });
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && !sheet.hidden) open(false);
    });
  }

  /* --------------------------- LINK GRAPH --------------------------- */
  var SIZE = 640, C = SIZE / 2;
  var SIGNAL = "#7c3aed", TIER2 = "#8a82a0";

  var RAW = [
    { d: "healthline.com", dr: 91, rel: 96, a: -104, r: 152, t: 1 },
    { d: "techcrunch.com", dr: 93, rel: 74, a: -34, r: 170, t: 1 },
    { d: "verywellfit.com", dr: 78, rel: 94, a: 30, r: 150, t: 1 },
    { d: "outsideonline.com", dr: 74, rel: 88, a: 92, r: 166, t: 1 },
    { d: "menshealth.com", dr: 84, rel: 91, a: 148, r: 156, t: 1 },
    { d: "self.com", dr: 81, rel: 86, a: -156, r: 172, t: 1 },
    { d: "runnersworld.com", dr: 69, rel: 92, a: -72, r: 222, t: 2 },
    { d: "garagegymlab.com", dr: 58, rel: 97, a: -6, r: 226, t: 2 },
    { d: "barbend.com", dr: 64, rel: 95, a: 58, r: 218, t: 2 },
    { d: "stack.com", dr: 55, rel: 83, a: 124, r: 224, t: 2 },
    { d: "breakingmuscle.com", dr: 61, rel: 90, a: -136, r: 220, t: 2 }
  ];
  var MESH = [[0, 5], [1, 2], [3, 4], [6, 10], [7, 8]];

  function polar(deg, r) {
    var t = deg * Math.PI / 180;
    return { x: C + r * Math.cos(t), y: C + r * Math.sin(t) };
  }

  function initGraph() {
    var host = $("#graph");
    if (!host) return;

    var nodes = RAW.map(function (n) {
      var p = polar(n.a, n.r);
      return { d: n.d, dr: n.dr, rel: n.rel, t: n.t, x: p.x, y: p.y };
    });

    var svg = el("svg", { viewBox: "0 0 " + SIZE + " " + SIZE, "aria-hidden": "true" });

    var defs = el("defs");
    defs.innerHTML =
      '<linearGradient id="eg" x1="0" y1="0" x2="1" y2="1">' +
        '<stop offset="0%" stop-color="#7c3aed" stop-opacity=".95"/>' +
        '<stop offset="100%" stop-color="#7c3aed" stop-opacity=".5"/></linearGradient>' +
      '<radialGradient id="cg"><stop offset="0%" stop-color="#a78bfa"/>' +
        '<stop offset="52%" stop-color="#7c3aed"/><stop offset="100%" stop-color="#5b21b6"/></radialGradient>' +
      '<filter id="ns" x="-70%" y="-70%" width="240%" height="240%">' +
        '<feDropShadow dx="0" dy="2" stdDeviation="3" flood-color="#7c3aed" flood-opacity=".4"/></filter>' +
      '<filter id="cs" x="-90%" y="-90%" width="280%" height="280%">' +
        '<feDropShadow dx="0" dy="5" stdDeviation="9" flood-color="#7c3aed" flood-opacity=".45"/></filter>';
    svg.appendChild(defs);

    // orbit guides
    var guides = el("g", { fill: "none", stroke: "#150d2b", "stroke-opacity": ".08", "stroke-dasharray": "2 7" });
    [158, 222, 282].forEach(function (r) { guides.appendChild(el("circle", { cx: C, cy: C, r: r })); });
    svg.appendChild(guides);

    // radar sweep
    var sweep = null;
    if (motionOn) {
      sweep = el("g", { style: "transform-origin:" + C + "px " + C + "px" });
      sweep.appendChild(el("line", {
        x1: C, y1: C, x2: C + 282, y2: C,
        stroke: "url(#eg)", "stroke-opacity": ".45", "stroke-width": "1.5"
      }));
      svg.appendChild(sweep);
    }

    // outer ticks
    var ticks = el("g", { stroke: "#150d2b", "stroke-opacity": ".22", "stroke-width": "1.5" });
    for (var i = 0; i < 48; i++) {
      var a = i * 360 / 48, p1 = polar(a, 282), p2 = polar(a, i % 4 === 0 ? 272 : 277);
      ticks.appendChild(el("line", { x1: p1.x, y1: p1.y, x2: p2.x, y2: p2.y }));
    }
    svg.appendChild(ticks);

    // peer mesh
    var meshG = el("g", { fill: "none", stroke: "#150d2b", "stroke-opacity": ".14", "stroke-width": "1" });
    var meshLines = MESH.map(function (pair) {
      var A = nodes[pair[0]], B = nodes[pair[1]];
      var l = el("line", { x1: A.x, y1: A.y, x2: B.x, y2: B.y });
      meshG.appendChild(l); return l;
    });
    svg.appendChild(meshG);

    // primary edges
    var edgeG = el("g");
    var edges = nodes.map(function (n) {
      var l = el("line", {
        x1: n.x, y1: n.y, x2: C, y2: C,
        stroke: "url(#eg)",
        "stroke-width": n.t === 1 ? 1.9 : 1.4,
        "stroke-opacity": n.t === 1 ? .82 : .52,
        "stroke-linecap": "round"
      });
      edgeG.appendChild(l); return l;
    });
    svg.appendChild(edgeG);

    // packets
    var packetG = el("g");
    var packets = [];
    if (motionOn) {
      nodes.forEach(function (n) {
        var c = el("circle", { r: n.t === 1 ? 3.2 : 2.4, fill: SIGNAL, cx: n.x, cy: n.y, opacity: 0 });
        packetG.appendChild(c); packets.push(c);
      });
    }
    svg.appendChild(packetG);

    // core pulses
    var pulseG = el("g");
    var pulses = [];
    if (motionOn) {
      for (var k = 0; k < 2; k++) {
        var pc = el("circle", {
          cx: C, cy: C, r: 42, fill: "none", stroke: SIGNAL, "stroke-width": "1.4",
          style: "transform-origin:" + C + "px " + C + "px"
        });
        pulseG.appendChild(pc); pulses.push(pc);
      }
    }
    svg.appendChild(pulseG);

    // nodes
    var nodeGroups = nodes.map(function (n, idx) {
      var g = el("g", { class: "graph__node", "data-i": idx });
      var rad = n.t === 1 ? 8.5 : 6.5;
      g.appendChild(el("circle", { cx: n.x, cy: n.y, r: 22, fill: "transparent" }));
      var halo = el("circle", { cx: n.x, cy: n.y, r: rad + 9, fill: SIGNAL, "fill-opacity": "0", });
      g.appendChild(halo);
      var ring = el("circle", {
        cx: n.x, cy: n.y, r: rad, fill: "#ffffff",
        stroke: n.t === 1 ? SIGNAL : TIER2, "stroke-width": n.t === 1 ? 1.9 : 2,
        filter: n.t === 1 ? "url(#ns)" : undefined
      });
      g.appendChild(ring);
      g.appendChild(el("circle", { cx: n.x, cy: n.y, r: n.t === 1 ? 2.8 : 2.1, fill: n.t === 1 ? SIGNAL : TIER2 }));

      if (n.t === 1) {
        var right = n.x > C;
        var tx = el("text", {
          x: right ? n.x + rad + 9 : n.x - rad - 9, y: n.y + 4,
          "text-anchor": right ? "start" : "end",
          "font-size": "11", fill: "#574f6b",
          "font-family": "'Plus Jakarta Sans',sans-serif", "letter-spacing": ".06em"
        });
        tx.textContent = "DR" + n.dr;
        g.appendChild(tx);
      }
      g._halo = halo; g._ring = ring;
      svg.appendChild(g);
      return g;
    });

    // core
    var coreG = el("g");
    coreG.appendChild(el("circle", { cx: C, cy: C, r: 46, fill: SIGNAL, "fill-opacity": ".1" }));
    coreG.appendChild(el("circle", { cx: C, cy: C, r: 30, fill: "url(#cg)", filter: "url(#cs)" }));
    coreG.appendChild(el("circle", { cx: C, cy: C, r: 30, fill: "none", stroke: "#fff", "stroke-opacity": ".65" }));
    svg.appendChild(coreG);

    host.insertBefore(svg, $(".graph__core", host));

    /* ---- hover inspector ---- */
    var tip = $("#graphTip");
    nodeGroups.forEach(function (g, idx) {
      g.addEventListener("mouseenter", function () {
        var n = nodes[idx];
        g._halo.setAttribute("fill-opacity", ".14");
        g._ring.setAttribute("stroke-width", "2.6");
        edges[idx].setAttribute("stroke-opacity", "1");
        edges[idx].setAttribute("stroke-width", "2.4");

        tip.innerHTML =
          "<b>" + n.d + "</b>" +
          '<div class="meter"><div class="meter__k"><span>AUTHORITY</span><em>' + n.dr + '</em></div>' +
          '<div class="meter__t"><div class="meter__f"></div></div></div>' +
          '<div class="meter"><div class="meter__k"><span>TOPICAL FIT</span><em>' + n.rel + '</em></div>' +
          '<div class="meter__t"><div class="meter__f meter__f--gain"></div></div></div>';
        tip.style.left = (n.x / SIZE * 100) + "%";
        tip.style.top = (n.y / SIZE * 100) + "%";
        tip.style.marginTop = (n.y > C ? 18 : -112) + "px";
        tip.hidden = false;

        var fills = $$(".meter__f", tip);
        requestAnimationFrame(function () {
          fills[0].style.width = n.dr + "%";
          fills[1].style.width = n.rel + "%";
        });
      });
      g.addEventListener("mouseleave", function () {
        var n = nodes[idx];
        g._halo.setAttribute("fill-opacity", "0");
        g._ring.setAttribute("stroke-width", "1.8");
        edges[idx].setAttribute("stroke-opacity", n.t === 1 ? ".82" : ".52");
        edges[idx].setAttribute("stroke-width", n.t === 1 ? 1.9 : 1.4);
        tip.hidden = true;
      });
    });

    /* ---- timeline ---- */
    if (!motionOn) return;

    function draw(line, dur, delay) {
      var L = Math.hypot(
        +line.getAttribute("x2") - +line.getAttribute("x1"),
        +line.getAttribute("y2") - +line.getAttribute("y1")
      );
      gsap.set(line, { attr: { "stroke-dasharray": L, "stroke-dashoffset": L } });
      gsap.to(line, { attr: { "stroke-dashoffset": 0 }, duration: dur, delay: delay, ease: "power2.out" });
    }
    edges.forEach(function (l, i) { draw(l, .9, .25 + i * .07); });
    meshLines.forEach(function (l, i) { draw(l, 1.1, 1.2 + i * .09); });

    gsap.from(coreG, { opacity: 0, scale: .6, duration: .7, ease: "back.out(1.7)", transformOrigin: C + "px " + C + "px" });
    nodeGroups.forEach(function (g, i) {
      gsap.from(g, {
        opacity: 0, scale: .4, duration: .5, delay: .75 + i * .07,
        ease: "back.out(2)", transformOrigin: nodes[i].x + "px " + nodes[i].y + "px"
      });
    });

    gsap.to(sweep, { rotation: 360, duration: 26, repeat: -1, ease: "none", transformOrigin: C + "px " + C + "px" });

    packets.forEach(function (p, i) {
      var n = nodes[i];
      var tl = gsap.timeline({ repeat: -1, repeatDelay: 1.4, delay: 1.6 + i * .42 });
      tl.set(p, { attr: { cx: n.x, cy: n.y }, opacity: 0 })
        .to(p, { opacity: 1, duration: .2 })
        .to(p, { attr: { cx: C, cy: C }, duration: n.t === 1 ? 2.2 : 3, ease: "power1.in" }, 0)
        .to(p, { opacity: 0, duration: .35 }, ">-0.35");
    });

    pulses.forEach(function (pc, i) {
      gsap.fromTo(pc,
        { scale: .7, opacity: .5 },
        { scale: 2.1, opacity: 0, duration: 3.4, repeat: -1, delay: i * 1.7, ease: "power2.out",
          transformOrigin: C + "px " + C + "px" });
    });

    // DR count-up
    var drEl = $("#drCount");
    var obj = { v: 31 };
    gsap.to(obj, {
      v: 72, duration: 1.8, delay: .9, ease: "expo.out",
      onUpdate: function () { drEl.textContent = Math.round(obj.v); }
    });

    // parallax
    var tiltX = gsap.quickTo(svg, "rotationX", { duration: .6, ease: "power3" });
    var tiltY = gsap.quickTo(svg, "rotationY", { duration: .6, ease: "power3" });
    gsap.set(svg, { transformPerspective: 1400, transformOrigin: "50% 50%" });
    host.addEventListener("mousemove", function (e) {
      var r = host.getBoundingClientRect();
      tiltY(((e.clientX - r.left) / r.width - .5) * -18);
      tiltX(((e.clientY - r.top) / r.height - .5) * 14);
    });
    host.addEventListener("mouseleave", function () { tiltX(0); tiltY(0); });
  }

  /* ----------------------------- CHART ----------------------------- */
  var VW = 560, VH = 230, PAD = { l: 44, r: 24, t: 16, b: 44 }, YMAX = 140;
  var MONTHS = ["JAN", "MAR", "MAY", "JUL", "SEP", "NOV"];
  var LIVE = [8, 28, 48, 66, 92, 128];
  var PREV = [8, 14, 19, 25, 31, 38];
  var GRIDV = [0, 40, 80, 120];
  var LABELLED = [2, 5];               // endpoint + the point the callout explains
  var pw = VW - PAD.l - PAD.r, ph = VH - PAD.t - PAD.b;

  function xAt(i) { return PAD.l + pw * i / (LIVE.length - 1); }
  function yAt(v) { return PAD.t + ph - (v / YMAX) * ph; }
  function pts(arr) { return arr.map(function (v, i) { return xAt(i) + "," + yAt(v); }).join(" "); }

  function initChart() {
    var host = $("#chart");
    if (!host) return;

    var svg = el("svg", {
      viewBox: "0 0 " + VW + " " + VH, role: "img",
      "aria-label": "Live links grew from 8 in January to 128 in November, well ahead of the previous period which reached 38."
    });

    var defs = el("defs");
    defs.innerHTML = '<linearGradient id="lf" x1="0" y1="0" x2="0" y2="1">' +
      '<stop offset="0%" stop-color="#7c3aed" stop-opacity=".18"/>' +
      '<stop offset="100%" stop-color="#7c3aed" stop-opacity="0"/></linearGradient>';
    svg.appendChild(defs);

    // gridlines: solid hairlines, one shade off the surface, never dashed
    var g1 = el("g", { stroke: "#ece8f4", "stroke-width": "1" });
    GRIDV.forEach(function (v) {
      g1.appendChild(el("line", { x1: PAD.l, y1: yAt(v), x2: VW - PAD.r, y2: yAt(v) }));
    });
    svg.appendChild(g1);

    var gy = el("g", { "font-size": "10", fill: "#6b6480", "text-anchor": "end", "font-family": "'Plus Jakarta Sans',sans-serif" });
    GRIDV.forEach(function (v) {
      var t = el("text", { x: PAD.l - 10, y: yAt(v) + 3.5 }); t.textContent = v; gy.appendChild(t);
    });
    svg.appendChild(gy);

    svg.appendChild(el("polygon", {
      points: xAt(0) + "," + yAt(0) + " " + pts(LIVE) + " " + xAt(LIVE.length - 1) + "," + yAt(0),
      fill: "url(#lf)"
    }));

    // comparison baseline, the dash carries identity, not colour alone
    svg.appendChild(el("polyline", {
      points: pts(PREV), fill: "none", stroke: "#a59dbb",
      "stroke-width": "2", "stroke-dasharray": "5 5", "stroke-linecap": "round"
    }));

    var liveLine = el("polyline", {
      points: pts(LIVE), fill: "none", stroke: "#7c3aed",
      "stroke-width": "2.5", "stroke-linejoin": "round", "stroke-linecap": "round"
    });
    svg.appendChild(liveLine);

    var cross = el("line", {
      y1: PAD.t, y2: PAD.t + ph, stroke: "#150d2b",
      "stroke-opacity": ".16", "stroke-width": "1", opacity: 0
    });
    svg.appendChild(cross);

    var marks = LIVE.map(function (v, i) {
      var c = el("circle", { cx: xAt(i), cy: yAt(v), r: 4.5, fill: "#fff", stroke: "#7c3aed", "stroke-width": "2" });
      svg.appendChild(c); return c;
    });

    // selective direct labels only
    var gl = el("g", { "font-size": "12", "font-weight": "700", fill: "#150d2b", "text-anchor": "middle", "font-family": "'Plus Jakarta Sans',sans-serif" });
    LABELLED.forEach(function (i) {
      var t = el("text", { x: xAt(i), y: yAt(LIVE[i]) - 13 }); t.textContent = LIVE[i]; gl.appendChild(t);
    });
    svg.appendChild(gl);

    // callout
    var cal = el("g");
    cal.appendChild(el("line", {
      x1: xAt(2), y1: yAt(48) - 20, x2: xAt(2) - 26, y2: yAt(48) - 44,
      stroke: "#150d2b", "stroke-opacity": ".28", "stroke-width": "1", "stroke-dasharray": "3 3"
    }));
    cal.appendChild(el("rect", { x: xAt(2) - 128, y: yAt(48) - 62, width: 104, height: 22, rx: 7, fill: "#150d2b" }));
    var ct = el("text", { x: xAt(2) - 76, y: yAt(48) - 47, "text-anchor": "middle", "font-size": "10", fill: "#fff" });
    ct.textContent = "first links go live";
    cal.appendChild(ct);
    svg.appendChild(cal);

    var gx = el("g", { "font-size": "10", fill: "#6b6480", "text-anchor": "middle", "font-family": "'Plus Jakarta Sans',sans-serif" });
    MONTHS.forEach(function (m, i) {
      var t = el("text", { x: xAt(i), y: PAD.t + ph + 20 }); t.textContent = m; gx.appendChild(t);
    });
    svg.appendChild(gx);

    host.insertBefore(svg, $("#chartTip"));

    /* hover: whole column is the hit target */
    var tip = $("#chartTip");
    svg.addEventListener("mousemove", function (e) {
      var r = svg.getBoundingClientRect();
      var x = (e.clientX - r.left) / r.width * VW;
      var i = Math.round((x - PAD.l) / pw * (LIVE.length - 1));
      if (i < 0 || i >= LIVE.length) return;
      cross.setAttribute("x1", xAt(i)); cross.setAttribute("x2", xAt(i));
      cross.setAttribute("opacity", 1);
      marks.forEach(function (m, j) { m.setAttribute("r", j === i ? 6 : 4.5); });
      tip.innerHTML = "<u>" + MONTHS[i] + "</u><b><i></i>" + LIVE[i] + " <em>live</em></b>";
      tip.style.left = (xAt(i) / VW * 100) + "%";
      tip.style.top = (yAt(LIVE[i]) / VH * 100) + "%";
      tip.hidden = false;
    });
    svg.addEventListener("mouseleave", function () {
      cross.setAttribute("opacity", 0);
      marks.forEach(function (m) { m.setAttribute("r", 4.5); });
      tip.hidden = true;
    });

    if (!motionOn) return;
    var L = liveLine.getTotalLength();
    gsap.set(liveLine, { attr: { "stroke-dasharray": L, "stroke-dashoffset": L } });
    gsap.to(liveLine, {
      attr: { "stroke-dashoffset": 0 }, duration: 1.6, ease: "power2.out",
      scrollTrigger: { trigger: host, start: "top 82%", once: true }
    });
    gsap.from(marks, {
      opacity: 0, duration: .3, stagger: .12, delay: .3,
      scrollTrigger: { trigger: host, start: "top 82%", once: true }
    });
  }

  /* ----------------------------- TICKER ----------------------------- */
  var TICKER = [
    ["", "Live Reporting Dashboard", ""],
    ["", "No Minimum Order", ""],
    ["", "Fully White-Label Reports", ""],
    ["50+", "Link Builders", ""],
    ["", "Clients Across ", "52 Nations"],
    ["183", "Industries Covered", ""],
    ["", "Every Link Guaranteed 12 Months", ""]
  ];

  function initTicker() {
    var track = $("#tickerTrack");
    if (!track) return;
    var html = "";
    TICKER.forEach(function (t) {
      html += '<span class="ticker__item">' +
        (t[0] ? "<b>" + t[0] + "</b>" : "") + t[1] +
        (t[2] ? "<b>" + t[2] + "</b>" : "") + "</span>";
    });
    // duplicated so the -50% loop is seamless
    track.innerHTML = html + html;
  }

  /* ----------------------------- FIGURES ----------------------------- */
  function initFigures() {
    var figs = $$(".fig");
    if (!figs.length) return;

    figs.forEach(function (f) {
      var target = parseInt(f.getAttribute("data-to"), 10);
      var out = $("[data-val]", f);

      // keyboard + touch parity with hover
      f.addEventListener("focus", function () { f.classList.add("is-on"); });
      f.addEventListener("blur", function () { f.classList.remove("is-on"); });
      f.addEventListener("click", function () { f.classList.toggle("is-on"); });

      if (!motionOn) return;

      var obj = { v: 0 };
      out.textContent = "0+";
      gsap.to(obj, {
        v: target, duration: 1.8, ease: "expo.out",
        scrollTrigger: { trigger: f, start: "top 86%", once: true },
        onUpdate: function () { out.textContent = Math.round(obj.v).toLocaleString("en-US") + "+"; }
      });
    });
  }

  /* ----------------------------- SERVICES ----------------------------- */
  var ICONS = {
    brief: '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
    lang: '<path d="m5 8 6 6"/><path d="m4 14 6-6 2-3"/><path d="M2 5h12"/><path d="M7 2h1"/><path d="m22 22-5-10-5 10"/><path d="M14 18h6"/>',
    pin: '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/>',
    news: '<path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/>',
    bulb: '<path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/><path d="M9 18h6"/><path d="M10 22h4"/>',
    arrow: '<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>',
    spark: '<path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3Z"/>'
  };

  // WordPress passes the ACF service rows as window.TB_SERVICES.
  var SERVICES = (window.TB_SERVICES && window.TB_SERVICES.length) ? window.TB_SERVICES : [
    { t: "White-Label Link Building", i: "brief",
      b: "Scalable, agency-ready link building with genuine editorial outreach and zero PBNs. We become your link-building department and offer complete transparency. Every placement is tracked live. Every link is guaranteed for a year." },
    { t: "Multi-Lingual Link Building", i: "lang", n: true,
      b: "Native-language outreach across 28 markets, run by in-country editors rather than translation tools. Anchor strategy, local relevance and tone are handled per market, so the link reads as though it was always meant to be there." },
    { t: "Local Link Building (USA)", i: "pin",
      b: "City and state-level authority for multi-location brands. Chamber listings, regional press, local resource pages and genuine community partnerships, the citations and links that move the map pack, not just the blue links." },
    { t: "Media Placements", i: "news", n: true,
      b: "Editorial coverage in publications your buyers already read. Journalist-led pitching against live queries, with placements on titles that carry real newsroom standards and real traffic, never sponsored-content farms." },
    { t: "AI Search Optimization", i: "bulb", n: true,
      b: "We track your brand's visibility across ChatGPT, Perplexity, Gemini, Copilot, Grok, Claude, DeepSeek and AI Overviews, then build the signals that get you recommended. Includes LLM monitoring, AI search progression, sentiment tracking, AEO for specific platforms and AI trust-signal engineering.",
      tag: "FOR AI VISIBILITY → GEO & AEO", show: true }
  ];

  function pad(n) { return String(n + 1).padStart(2, "0"); }

  // Service text may come from the CMS, so it is escaped before innerHTML.
  function esc(v) {
    return String(v == null ? "" : v).replace(/[&<>"']/g, function (c) {
      return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c];
    });
  }

  function initServices() {
    var index = $("#svcIndex"), stage = $("#svcStage"), crumb = $("#svcCrumb");
    if (!index) return;

    SERVICES.forEach(function (s, i) {
      var b = document.createElement("button");
      b.className = "svc__item";
      b.setAttribute("role", "tab");
      b.setAttribute("aria-selected", i === 0 ? "true" : "false");
      b.innerHTML =
        '<span class="svc__n">' + pad(i) + "</span>" +
        '<span class="svc__t"><b>' + esc(s.t) + "</b>" + (s.n ? '<span class="flag">NEW</span>' : "") + "</span>" +
        '<svg class="ico" viewBox="0 0 24 24" aria-hidden="true">' + ICONS.arrow + "</svg>";
      b.addEventListener("click", function () { select(i); });
      index.appendChild(b);
    });

    var items = $$(".svc__item", index);

    // arrow-key navigation, as a tablist should have
    index.addEventListener("keydown", function (e) {
      var cur = items.indexOf(document.activeElement);
      if (cur < 0) return;
      var next = e.key === "ArrowDown" || e.key === "ArrowRight" ? cur + 1
               : e.key === "ArrowUp" || e.key === "ArrowLeft" ? cur - 1 : -1;
      if (next < 0 || next >= items.length) return;
      e.preventDefault();
      items[next].focus();
      select(next);
    });

    function select(i) {
      var s = SERVICES[i];
      items.forEach(function (b, j) {
        b.classList.toggle("is-on", j === i);
        b.setAttribute("aria-selected", j === i ? "true" : "false");
      });
      crumb.textContent = "services / " + pad(i) + " of " + pad(SERVICES.length - 1);

      stage.innerHTML =
        '<div class="svc__head">' +
          '<span class="svc__badge"><svg class="ico" viewBox="0 0 24 24" aria-hidden="true">' + (ICONS[s.i] || ICONS.brief) + "</svg></span>" +
          "<div>" +
            '<div class="svc__kicker">SERVICE / ' + pad(i) + (s.n ? ' <em>· NEW</em>' : "") + "</div>" +
            '<h3 class="svc__title">' + esc(s.t) + "</h3>" +
          "</div>" +
        "</div>" +
        '<p class="svc__body">' + esc(s.b) + "</p>" +
        (s.show
          ? '<div class="svc__show"><svg class="ico" viewBox="0 0 24 24" aria-hidden="true">' + ICONS.spark + "</svg>" +
            '<div class="svc__skel"><i style="width:78%"></i><i style="width:58%"></i><i style="width:34%"></i></div></div>'
          : "") +
        '<div class="svc__foot">' +
          (s.tag ? '<span class="svc__tag">' + esc(s.tag) + "</span>" : "<span></span>") +
          '<a href="' + esc(s.u || "#pricing") + '" class="svc__go">Explore service' +
            '<i><svg class="ico" viewBox="0 0 24 24" aria-hidden="true">' + ICONS.arrow + "</svg></i></a>" +
        "</div>";

      if (motionOn) {
        gsap.fromTo(stage.children,
          { opacity: 0, y: 12 },
          { opacity: 1, y: 0, duration: .38, stagger: .05, ease: "power2.out" });
      }
    }

    select(0);
  }


  /* ----------------------------- ORDER CARD ----------------------------- */
  function initOrder() {
    var root = $("#order");
    if (!root) return;

    var textEl = $("#orderText"), statusEl = $("#orderStatus");
    var cols = $$(".ocol", root);
    if (!textEl || !statusEl || !cols.length) return;

    // NOTE: these strings contain literal UTF-8 (middle dot, check mark).
    var STRINGS = [
      'agency-client.io/pricing · "compare plans"',
      'yourclient.com/buyers-guide · "best [category] tools"'
    ];
    var STAGES = ["Queued", "Outreach", "Content", "Live ✓"];

    function paint(stage) {
      cols.forEach(function (c, i) {
        c.classList.toggle("is-on", i === stage);
        c.classList.toggle("is-done", i < stage);
      });
      statusEl.textContent = STAGES[stage];
      statusEl.classList.toggle("is-live", stage === 3);
    }

    // Reduced motion gets the finished state rather than a frozen empty field.
    if (reduce) {
      textEl.textContent = STRINGS[0];
      paint(3);
      return;
    }

    var timer = null, si = 0;
    function stop() { if (timer) { clearTimeout(timer); timer = null; } }

    function run() {
      stop();
      var s = STRINGS[si];
      paint(0);

      function type(i) {
        textEl.textContent = s.slice(0, i);
        if (i < s.length) { timer = setTimeout(function () { type(i + 1); }, 38); return; }
        timer = setTimeout(function () { stages(0); }, 480);
      }

      function stages(k) {
        paint(k);
        if (k < 3) { timer = setTimeout(function () { stages(k + 1); }, 1150); return; }
        timer = setTimeout(erase, 1900);   // hold on LIVE
      }

      function erase() {
        textEl.textContent = textEl.textContent.slice(0, -1);
        if (textEl.textContent.length) { timer = setTimeout(erase, 16); return; }
        si = (si + 1) % STRINGS.length;
        timer = setTimeout(run, 320);
      }

      type(0);
    }

    // Start unconditionally, gating the start on the observer means the card
    // never animates anywhere the callback is delayed or never delivered.
    run();

    // The observer only idles it while off-screen.
    if (typeof IntersectionObserver === "function") {
      new IntersectionObserver(function (entries) {
        entries.forEach(function (e) {
          if (e.isIntersecting) { if (!timer) run(); }
          else stop();
        });
      }, { threshold: .15 }).observe(root);
    }
  }

  /* ----------------------------- APPROACH ----------------------------- */
  var AW = 520, AH = 270, APAD = { l: 58, r: 16, t: 18, b: 34 };
  var apw = AW - APAD.l - APAD.r, aph = AH - APAD.t - APAD.b;

  function monthLabels(m, y, n, step) {
    var M = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    var out = [];
    for (var i = 0; i < n; i++) { out.push(M[m] + " " + y); m += step; while (m > 11) { m -= 12; y++; } }
    return out;
  }
  var PT = monthLabels(10, 2020, 13, 2);   // Nov 2020 → Nov 2022, bi-monthly

  var APPROACH = [
    {
      max: 100,
      ticks: [{ v: 20, l: "20" }, { v: 40, l: "40" }, { v: 60, l: "60" }, { v: 80, l: "80" }],
      vals: [22, 24, 28, 33, 41, 52, 61, 66, 63, 68, 72, 75, 78],
      fmt: function (v) { return "DR " + v; },
      aria: "Domain rating climbing from 22 in November 2020 to 78 in November 2022."
    },
    {
      max: 40,
      ticks: [{ v: 10, l: "10%" }, { v: 20, l: "20%" }, { v: 30, l: "30%" }, { v: 40, l: "40%" }],
      vals: [2, 3, 5, 6, 9, 13, 16, 19, 22, 25, 28, 31, 34],
      fmt: function (v) { return v + "%"; },
      aria: "Share of answer-engine citations rising from 2% to 34% over two years."
    },
    {
      max: 1500,
      ticks: [{ v: 350, l: "350K" }, { v: 700, l: "700K" }, { v: 1100, l: "1.1M" }, { v: 1400, l: "1.4M" }],
      vals: [60, 80, 120, 200, 420, 850, 1180, 1020, 1090, 980, 1010, 1080, 1400],
      fmt: function (v) { return v >= 1000 ? (v / 1000).toFixed(1) + "M" : v + "K"; },
      aria: "Organic search traffic growing from 60 thousand to 1.4 million monthly visits."
    }
  ];

  function axAt(i, n) { return APAD.l + apw * i / (n - 1); }
  function ayAt(v, max) { return APAD.t + aph - (v / max) * aph; }

  /* Catmull-Rom → cubic bezier: the organic curve the reference uses. */
  function smooth(pts) {
    if (pts.length < 2) return "";
    var d = "M" + pts[0][0].toFixed(1) + "," + pts[0][1].toFixed(1);
    for (var i = 0; i < pts.length - 1; i++) {
      var p0 = pts[i - 1] || pts[i], p1 = pts[i], p2 = pts[i + 1], p3 = pts[i + 2] || p2;
      var c1x = p1[0] + (p2[0] - p0[0]) / 6, c1y = p1[1] + (p2[1] - p0[1]) / 6;
      var c2x = p2[0] - (p3[0] - p1[0]) / 6, c2y = p2[1] - (p3[1] - p1[1]) / 6;
      d += " C" + c1x.toFixed(1) + "," + c1y.toFixed(1) + " " +
           c2x.toFixed(1) + "," + c2y.toFixed(1) + " " +
           p2[0].toFixed(1) + "," + p2[1].toFixed(1);
    }
    return d;
  }

  function buildApprChart(host, cfg, idx) {
    var n = cfg.vals.length;
    var pts = cfg.vals.map(function (v, i) { return [axAt(i, n), ayAt(v, cfg.max)]; });
    var line = smooth(pts);

    var svg = el("svg", { viewBox: "0 0 " + AW + " " + AH, role: "img", "aria-label": cfg.aria });

    var defs = el("defs");
    defs.innerHTML = '<linearGradient id="af' + idx + '" x1="0" y1="0" x2="0" y2="1">' +
      '<stop offset="0%" stop-color="#7c3aed" stop-opacity=".22"/>' +
      '<stop offset="100%" stop-color="#7c3aed" stop-opacity="0"/></linearGradient>';
    svg.appendChild(defs);

    // gridlines: solid hairlines one shade off the surface, never dashed
    var g = el("g", { stroke: "#ece8f4", "stroke-width": "1" });
    cfg.ticks.forEach(function (t) {
      g.appendChild(el("line", { x1: APAD.l, y1: ayAt(t.v, cfg.max), x2: AW - APAD.r, y2: ayAt(t.v, cfg.max) }));
    });
    svg.appendChild(g);

    var gy = el("g", { "font-size": "11", fill: "#6b6480", "text-anchor": "end",
                       "font-family": "'Plus Jakarta Sans',sans-serif", "font-weight": "600" });
    cfg.ticks.forEach(function (t) {
      var e = el("text", { x: APAD.l - 12, y: ayAt(t.v, cfg.max) + 4 });
      e.textContent = t.l; gy.appendChild(e);
    });
    svg.appendChild(gy);

    svg.appendChild(el("path", {
      d: line + " L" + axAt(n - 1, n) + "," + (APAD.t + aph) + " L" + APAD.l + "," + (APAD.t + aph) + " Z",
      fill: "url(#af" + idx + ")"
    }));

    var path = el("path", {
      d: line, fill: "none", stroke: "#7c3aed", "stroke-width": "2.5",
      "stroke-linecap": "round", "stroke-linejoin": "round"
    });
    svg.appendChild(path);

    var cross = el("line", { y1: APAD.t, y2: APAD.t + aph, stroke: "#150d2b",
                             "stroke-opacity": ".16", "stroke-width": "1", opacity: 0 });
    svg.appendChild(cross);

    var dot = el("circle", { r: 5, fill: "#fff", stroke: "#7c3aed", "stroke-width": "2.5", opacity: 0 });
    svg.appendChild(dot);

    var gx = el("g", { "font-size": "11", fill: "#6b6480", "text-anchor": "middle",
                       "font-family": "'Plus Jakarta Sans',sans-serif", "font-weight": "600" });
    [0, 6, 12].forEach(function (i) {
      // centre-anchoring the end labels pushes them outside the viewBox
      var e = el("text", {
        x: axAt(i, n), y: APAD.t + aph + 22,
        "text-anchor": i === 0 ? "start" : i === n - 1 ? "end" : "middle"
      });
      e.textContent = PT[i]; gx.appendChild(e);
    });
    svg.appendChild(gx);

    var tip = document.createElement("div");
    tip.className = "appr__tip";
    tip.hidden = true;

    host.appendChild(svg);
    host.appendChild(tip);

    svg.addEventListener("mousemove", function (e) {
      var r = svg.getBoundingClientRect();
      var x = (e.clientX - r.left) / r.width * AW;
      var i = Math.round((x - APAD.l) / apw * (n - 1));
      if (i < 0 || i >= n) return;
      cross.setAttribute("x1", axAt(i, n)); cross.setAttribute("x2", axAt(i, n));
      cross.setAttribute("opacity", 1);
      dot.setAttribute("cx", axAt(i, n)); dot.setAttribute("cy", ayAt(cfg.vals[i], cfg.max));
      dot.setAttribute("opacity", 1);
      tip.innerHTML = "<u>" + PT[i] + "</u><b><i></i>" + cfg.fmt(cfg.vals[i]) + "</b>";
      tip.style.left = (axAt(i, n) / AW * 100) + "%";
      tip.style.top = (ayAt(cfg.vals[i], cfg.max) / AH * 100) + "%";
      tip.hidden = false;
    });
    svg.addEventListener("mouseleave", function () {
      cross.setAttribute("opacity", 0);
      dot.setAttribute("opacity", 0);
      tip.hidden = true;
    });

    return path;
  }

  function initApproach() {
    var tabs = $$("#apprTabs .appr__tab");
    if (!tabs.length) return;

    var panes = [$("#appr-p1"), $("#appr-p2"), $("#appr-p3")];
    var paths = $$(".appr__chart").map(function (host, i) {
      return buildApprChart(host, APPROACH[i], i);
    });

    function select(i, focus) {
      tabs.forEach(function (t, j) {
        t.classList.toggle("is-on", j === i);
        t.setAttribute("aria-selected", j === i ? "true" : "false");
        t.tabIndex = j === i ? 0 : -1;
      });
      panes.forEach(function (p, j) { p.hidden = j !== i; });
      if (focus) tabs[i].focus();

      if (!motionOn) return;
      // getTotalLength is unreliable inside display:none, so draw after unhiding
      var p = paths[i], L = p.getTotalLength();
      gsap.fromTo(p, { attr: { "stroke-dasharray": L, "stroke-dashoffset": L } },
        { attr: { "stroke-dashoffset": 0 }, duration: 1.15, ease: "power2.out" });
      gsap.fromTo(panes[i].querySelector(".appr__copy").children,
        { opacity: 0, y: 12 }, { opacity: 1, y: 0, duration: .4, stagger: .06, ease: "power2.out" });
    }

    tabs.forEach(function (t, i) {
      t.addEventListener("click", function () { select(i); });
    });

    $("#apprTabs").addEventListener("keydown", function (e) {
      var cur = tabs.indexOf(document.activeElement);
      if (cur < 0) return;
      var next = e.key === "ArrowRight" || e.key === "ArrowDown" ? cur + 1
               : e.key === "ArrowLeft" || e.key === "ArrowUp" ? cur - 1
               : e.key === "Home" ? 0
               : e.key === "End" ? tabs.length - 1 : -1;
      if (next < 0 || next >= tabs.length) return;
      e.preventDefault();
      select(next, true);
    });

    select(0);
  }

  /* ----------------------------- SWOOSH ----------------------------- */
  function initSwoosh() {
    var inks = $$(".swoosh__ink");
    if (!inks.length) return;
    // CSS parks these at scaleX(0); without GSAP they must still be drawn.
    if (!motionOn) {
      inks.forEach(function (n) { n.style.transform = "scaleX(1)"; });
      return;
    }
    inks.forEach(function (n) {
      gsap.to(n, {
        scaleX: 1, duration: .85, ease: "power2.out", transformOrigin: "left center",
        scrollTrigger: { trigger: n, start: "top 92%", once: true }
      });
    });
  }

  /* ----------------------------- FAQ ----------------------------- */
  function initFaq() {
    var items = $$(".qa");
    if (!items.length) return;

    items.forEach(function (item) {
      var btn = $(".qa__q", item);
      if (!btn) return;
      btn.addEventListener("click", function () {
        var open = item.classList.contains("is-open");
        // accordion: one answer at a time keeps the list scannable
        items.forEach(function (other) {
          other.classList.remove("is-open");
          var b = $(".qa__q", other);
          if (b) b.setAttribute("aria-expanded", "false");
        });
        if (!open) {
          item.classList.add("is-open");
          btn.setAttribute("aria-expanded", "true");
        }
      });
    });
  }

  /* ----------------------------- CTA FORM ----------------------------- */
  function initAudit() {
    var form = $("#auditForm");
    if (!form) return;

    var input = $("#auditDomain"), btn = $("#auditBtn"), msg = $("#auditMsg");
    if (!input || !btn || !msg) return;

    // Deliberately permissive: enough to catch a blank or obvious typo, not so
    // strict that a valid unusual TLD gets rejected.
    var LOOKS_LIKE_DOMAIN = /^[a-z0-9][a-z0-9.-]*\.[a-z]{2,}$/i;

    function clean(v) {
      return v.trim().toLowerCase()
        .replace(/^https?:\/\//, "")
        .replace(/^www\./, "")
        .replace(/\/.*$/, "");
    }

    function say(text, isError) {
      msg.innerHTML = text;
      msg.classList.toggle("is-err", !!isError);
      msg.hidden = false;
    }

    form.addEventListener("submit", function (e) {
      e.preventDefault();
      var domain = clean(input.value);

      if (!domain) {
        input.setAttribute("aria-invalid", "true");
        say("Enter a domain so we know what to audit.", true);
        input.focus();
        return;
      }
      if (!LOOKS_LIKE_DOMAIN.test(domain)) {
        input.setAttribute("aria-invalid", "true");
        say("That doesn&rsquo;t look like a domain. Try something like yourdomain.com", true);
        input.focus();
        return;
      }

      input.removeAttribute("aria-invalid");
      btn.disabled = true;
      say("Queueing your link map&hellip;");

      // No backend yet: this only confirms locally. Wire the POST here.
      setTimeout(function () {
        say("Queued. Your link map for <b>" + domain +
            "</b> is being prepared. We&rsquo;ll email it within 72 hours.");
        btn.disabled = false;
        form.reset();
      }, 900);
    });

    input.addEventListener("input", function () {
      input.removeAttribute("aria-invalid");
      msg.hidden = true;
    });
  }

  /* ----------------------------- FOOTER YEAR ----------------------------- */
  function initYear() {
    var el = $("#yr");
    if (el) el.textContent = String(new Date().getFullYear());
  }

  /* ----------------------------- LEAD FORM ----------------------------- */
  function initLead() {
    var form = $("#leadForm");
    if (!form) return;
    var btn = $("#lfBtn"), out = $("#lfMsg2");
    var EMAIL = /^[^\s@]+@[^\s@]+\.[a-z]{2,}$/i;
    var DOMAIN = /^[a-z0-9][a-z0-9.-]*\.[a-z]{2,}$/i;

    function fail(field, text) {
      field.setAttribute("aria-invalid", "true");
      out.textContent = text;
      out.classList.add("is-err");
      out.hidden = false;
      field.focus();
    }

    form.addEventListener("submit", function (e) {
      e.preventDefault();
      var name = $("#lfName"), email = $("#lfEmail"), dom = $("#lfDomain");
      [name, email, dom].forEach(function (f) { f.removeAttribute("aria-invalid"); });

      if (!name.value.trim()) return fail(name, "Tell us your name so we know who to reply to.");
      if (!EMAIL.test(email.value.trim())) return fail(email, "That email address does not look right.");

      var d = dom.value.trim().toLowerCase()
        .replace(/^https?:\/\//, "").replace(/^www\./, "").replace(/\/.*$/, "");
      if (!DOMAIN.test(d)) return fail(dom, "Enter the domain you want audited, e.g. yourdomain.com");

      btn.disabled = true;
      out.classList.remove("is-err");
      out.textContent = "Sending…";
      out.hidden = false;

      function done() {
        out.textContent = "Thanks! Your link-gap analysis for " + d +
          " is queued. We will email it within 72 hours.";
        btn.disabled = false;
        form.reset();
      }
      function failSend(text) {
        btn.disabled = false;
        out.classList.add("is-err");
        out.textContent = text || "Something went wrong sending your request. Please email us instead.";
      }

      // WordPress: window.TB.ajax is printed by the theme. The static site has
      // no backend, so it only confirms locally.
      if (window.TB && window.TB.ajax && window.fetch && window.FormData) {
        var data = new FormData(form);
        data.set("domain", d);
        data.append("action", "tb_lead");
        fetch(window.TB.ajax, { method: "POST", body: data, credentials: "same-origin" })
          .then(function (r) { return r.json().catch(function () { return null; }); })
          .then(function (res) {
            if (res && res.success) return done();
            failSend(res && res.data && res.data.message);
          })
          .catch(function () { failSend(); });
        return;
      }
      setTimeout(done, 900);
    });

    form.addEventListener("input", function (e) {
      if (e.target && e.target.removeAttribute) e.target.removeAttribute("aria-invalid");
      out.hidden = true;
    });
  }

  /* ----------------------------- TESTIMONIAL MARQUEE ----------------------------- */
  function initTmarq() {
    // The rows translate by -50%, so each needs its content duplicated for the
    // loop to be seamless. The clone is decorative only. Covers both the
    // testimonial rows and the logo rows.
    $$(".tmarq__row, .lmarq__row").forEach(function (row) {
      var clone = row.cloneNode(true);
      while (clone.firstChild) {
        var node = clone.firstChild;
        clone.removeChild(node);
        if (node.nodeType === 1) node.setAttribute("aria-hidden", "true");
        row.appendChild(node);
      }
    });
  }

  /* ----------------------------- HERO FX ----------------------------- */
  // Cursor spotlight on the ambient field, plus parallax on the proof chips.
  // Fine pointers only: on touch there is no hover to respond to.
  function initHeroFx() {
    var fields = $$(".hfx");
    if (!fields.length || reduce || !window.matchMedia("(pointer: fine)").matches) return;

    fields.forEach(function (fx) {
      var host = fx.parentNode;
      var chips = $$("[data-depth]", host).map(function (c) {
        return { el: c, d: parseFloat(c.getAttribute("data-depth")) || 0 };
      });
      var raf = 0, px = 0, py = 0;

      function paint() {
        raf = 0;
        chips.forEach(function (c) {
          c.el.style.transform = "translate3d(" + (px * c.d).toFixed(1) + "px," + (py * c.d).toFixed(1) + "px,0)";
        });
      }
      host.addEventListener("pointermove", function (e) {
        var r = host.getBoundingClientRect();
        var x = e.clientX - r.left, y = e.clientY - r.top;
        fx.style.setProperty("--mx", x + "px");
        fx.style.setProperty("--my", y + "px");
        fx.classList.add("is-live");
        px = x / r.width - .5; py = y / r.height - .5;
        if (!raf) raf = requestAnimationFrame(paint);
      });
      host.addEventListener("pointerleave", function () {
        fx.classList.remove("is-live");
        px = 0; py = 0;
        if (!raf) raf = requestAnimationFrame(paint);
      });
    });
  }

  /* ----------------------------- CARD GLOW ----------------------------- */
  function initGlow() {
    $$(".fcard").forEach(function (c) {
      c.addEventListener("pointermove", function (e) {
        var r = c.getBoundingClientRect();
        c.style.setProperty("--gx", (e.clientX - r.left) + "px");
        c.style.setProperty("--gy", (e.clientY - r.top) + "px");
      });
    });
  }

  /* ----------------------------- COUNT-UPS ----------------------------- */
  // <b data-count="156" data-prefix="+" data-suffix="%">+156%</b>
  // The markup already holds the final value, so no-JS and reduced motion
  // both read correctly; the tween only runs when motion is on.
  function initCount() {
    var nodes = $$("[data-count]");
    if (!nodes.length || !motionOn) return;
    nodes.forEach(function (n) {
      var to = parseFloat(n.getAttribute("data-count"));
      var from = parseFloat(n.getAttribute("data-from") || "0");
      var dec = parseInt(n.getAttribute("data-dec") || "0", 10);
      var pre = n.getAttribute("data-prefix") || "", suf = n.getAttribute("data-suffix") || "";
      var obj = { v: from };
      function fmt(v) {
        return pre + v.toLocaleString("en-US", { minimumFractionDigits: dec, maximumFractionDigits: dec }) + suf;
      }
      n.textContent = fmt(from);
      gsap.to(obj, {
        v: to, duration: 1.8, ease: "expo.out",
        scrollTrigger: { trigger: n, start: "top 90%", once: true },
        onUpdate: function () { n.textContent = fmt(obj.v); }
      });
    });
  }

  /* ----------------------------- IN-VIEW FLAG ----------------------------- */
  // Adds .is-in once, for CSS-driven reveals (before/after bars, timeline dots).
  function initInview() {
    var nodes = $$("[data-inview]");
    if (!nodes.length) return;
    if (reduce || typeof IntersectionObserver !== "function") {
      nodes.forEach(function (n) { n.classList.add("is-in"); });
      return;
    }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (!e.isIntersecting) return;
        e.target.classList.add("is-in");
        io.unobserve(e.target);
      });
    }, { rootMargin: "0px 0px -18% 0px" });
    nodes.forEach(function (n) { io.observe(n); });
  }

  /* ----------------------------- PROCESS STEPPER ----------------------------- */
  // The progress bar is a CSS animation; its animationend is the clock that
  // advances the step, so pausing the animation (hover, off-screen) pauses
  // the stepper with no timer bookkeeping.
  function initProc() {
    var root = $("#proc");
    if (!root) return;
    var steps = $$(".proc__step", root), panes = $$(".proc__pane", root);
    var cur = 0;

    function select(i, focus) {
      cur = i;
      steps.forEach(function (s, j) {
        s.classList.toggle("is-on", j === i);
        s.setAttribute("aria-selected", j === i ? "true" : "false");
        s.tabIndex = j === i ? 0 : -1;
      });
      panes.forEach(function (p, j) { p.hidden = j !== i; });
      if (focus) steps[i].focus();
      if (motionOn) {
        gsap.fromTo(panes[i].children, { opacity: 0, y: 14 },
          { opacity: 1, y: 0, duration: .45, stagger: .06, ease: "power2.out" });
      }
    }

    steps.forEach(function (s, i) {
      s.addEventListener("click", function () { root.classList.add("is-manual"); select(i); });
      if (!reduce) {
        $(".proc__bar i", s).addEventListener("animationend", function () {
          if (!root.classList.contains("is-manual")) select((cur + 1) % steps.length);
        });
      }
    });

    $(".proc__rail", root).addEventListener("keydown", function (e) {
      var next = e.key === "ArrowDown" || e.key === "ArrowRight" ? cur + 1
               : e.key === "ArrowUp" || e.key === "ArrowLeft" ? cur - 1 : -1;
      if (next < 0 || next >= steps.length) return;
      e.preventDefault();
      root.classList.add("is-manual");
      select(next, true);
    });

    if (typeof IntersectionObserver === "function") {
      new IntersectionObserver(function (entries) {
        root.classList.toggle("is-paused", !entries[0].isIntersecting);
      }, { threshold: .25 }).observe(root);
    }

    select(0);
  }

  /* ----------------------------- PRICE ESTIMATOR ----------------------------- */
  function initCalc() {
    var root = $("#calc");
    if (!root) return;
    var range = $("#calcRange"), qtyOut = $("#calcQty"), total = $("#calcTotal");
    var rateOut = $("#calcRate"), leadOut = $("#calcLead"), planOut = $("#calcPlan");
    var segs = $$(".seg__b", root);
    var rate = 480, lead = "", shown = 0;

    // Programme sizes come from pricing.html: Starter ~8, Growth ~22, Scale 40+.
    function plan(q) { return q <= 8 ? "Starter" : q <= 22 ? "Growth" : "Scale"; }
    function money(v) { return "$" + Math.round(v).toLocaleString("en-US"); }

    function update() {
      var q = parseInt(range.value, 10);
      var pct = (q - range.min) / (range.max - range.min) * 100;
      range.style.setProperty("--p", pct + "%");
      qtyOut.textContent = q;
      rateOut.textContent = money(rate);
      leadOut.textContent = lead;
      planOut.textContent = plan(q);

      var target = q * rate;
      if (motionOn) {
        var obj = { v: shown };
        gsap.to(obj, { v: target, duration: .5, ease: "power2.out", overwrite: true,
          onUpdate: function () { total.firstChild.nodeValue = money(obj.v); } });
      } else {
        total.firstChild.nodeValue = money(target);
      }
      shown = target;
    }

    function pick(b) {
      segs.forEach(function (s) { s.setAttribute("aria-checked", s === b ? "true" : "false"); s.tabIndex = s === b ? 0 : -1; });
      rate = parseFloat(b.getAttribute("data-rate"));
      lead = b.getAttribute("data-lead");
      update();
    }

    segs.forEach(function (b, i) {
      b.addEventListener("click", function () { pick(b); });
      b.addEventListener("keydown", function (e) {
        var n = e.key === "ArrowRight" || e.key === "ArrowDown" ? i + 1
              : e.key === "ArrowLeft" || e.key === "ArrowUp" ? i - 1 : -1;
        if (n < 0 || n >= segs.length) return;
        e.preventDefault(); segs[n].focus(); pick(segs[n]);
      });
    });
    range.addEventListener("input", update);

    pick($('.seg__b[aria-checked="true"]', root) || segs[0]);
  }

  /* ----------------------------- CASE STUDY: TOC ----------------------------- */
  function initToc() {
    var toc = $(".toc");
    if (!toc) return;
    var links = $$("a[href^='#']", toc);
    var secs = links.map(function (a) { return $(a.getAttribute("href")); });
    var bar = $(".toc__prog i", toc), article = $(".prose");

    function onScroll() {
      var line = window.innerHeight * .3, on = 0;
      secs.forEach(function (s, i) { if (s && s.getBoundingClientRect().top <= line) on = i; });
      links.forEach(function (a, i) { a.classList.toggle("is-on", i === on); });
      if (bar && article) {
        var r = article.getBoundingClientRect();
        var p = (window.innerHeight * .3 - r.top) / (r.height - window.innerHeight * .4);
        bar.style.transform = "scaleX(" + Math.max(0, Math.min(1, p)) + ")";
      }
    }
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
  }

  /* ----------------------------- CASE STUDY: TIMELINE ----------------------------- */
  function initTline() {
    var root = $(".tline");
    if (!root) return;
    var fill = $(".tline__fill", root), items = $$(".tl", root);

    function onScroll() {
      var r = root.getBoundingClientRect(), mark = window.innerHeight * .62;
      var p = Math.max(0, Math.min(1, (mark - r.top) / r.height));
      fill.style.setProperty("--p", p.toFixed(3));
      items.forEach(function (t) { t.classList.toggle("is-in", t.getBoundingClientRect().top < mark); });
    }
    if (reduce) {
      fill.style.setProperty("--p", 1);
      items.forEach(function (t) { t.classList.add("is-in"); });
      return;
    }
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
  }

  /* ----------------------------- CASE STUDY: RESULTS CHART ----------------------------- */
  // Series live in the markup (data-vals on each tab) so the template can be
  // re-populated per case study without touching this file.
  var CW = 640, CH = 280, CPAD = { l: 48, r: 18, t: 18, b: 34 };
  var cpw = CW - CPAD.l - CPAD.r, cph = CH - CPAD.t - CPAD.b;

  function initCsChart() {
    var root = $("#csChart");
    if (!root) return;
    var plot = $(".cschart__plot", root), now = $(".cschart__now", root);
    var tabs = $$(".mtab", root);
    var labels = (root.getAttribute("data-labels") || "").split(",");

    var svg = el("svg", { viewBox: "0 0 " + CW + " " + CH, role: "img" });
    var defs = el("defs");
    defs.innerHTML = '<linearGradient id="csf" x1="0" y1="0" x2="0" y2="1">' +
      '<stop offset="0%" stop-color="#7c3aed" stop-opacity=".24"/>' +
      '<stop offset="100%" stop-color="#7c3aed" stop-opacity="0"/></linearGradient>';
    svg.appendChild(defs);
    var gGrid = el("g", { stroke: "#ece8f4", "stroke-width": "1" });
    var gY = el("g", { "font-size": "11", fill: "#6b6480", "text-anchor": "end", "font-family": "'Plus Jakarta Sans',sans-serif", "font-weight": "600" });
    var area = el("path", { fill: "url(#csf)" });
    var line = el("path", { fill: "none", stroke: "#7c3aed", "stroke-width": "2.75", "stroke-linecap": "round", "stroke-linejoin": "round" });
    var cross = el("line", { y1: CPAD.t, y2: CPAD.t + cph, stroke: "#150d2b", "stroke-opacity": ".16", opacity: 0 });
    var dot = el("circle", { r: 5.5, fill: "#fff", stroke: "#7c3aed", "stroke-width": "2.75", opacity: 0 });
    var endDot = el("circle", { r: 5, fill: "#7c3aed" });
    var gX = el("g", { "font-size": "11", fill: "#6b6480", "font-family": "'Plus Jakarta Sans',sans-serif", "font-weight": "600" });
    [gGrid, gY, area, line, cross, dot, endDot, gX].forEach(function (n) { svg.appendChild(n); });

    var tip = document.createElement("div");
    tip.className = "appr__tip"; tip.hidden = true;
    plot.appendChild(svg); plot.appendChild(tip);

    var cfg = null;
    function xAt(i, n) { return CPAD.l + cpw * i / (n - 1); }
    function yAt(v) { return CPAD.t + cph - ((v - cfg.min) / (cfg.max - cfg.min)) * cph; }
    function fmt(v) { return (cfg.pre || "") + v.toLocaleString("en-US") + (cfg.suf || ""); }

    function draw(animate) {
      var n = cfg.vals.length;
      var pts = cfg.vals.map(function (v, i) { return [xAt(i, n), yAt(v)]; });
      var d = smooth(pts);
      line.setAttribute("d", d);
      area.setAttribute("d", d + " L" + xAt(n - 1, n) + "," + (CPAD.t + cph) + " L" + CPAD.l + "," + (CPAD.t + cph) + " Z");
      endDot.setAttribute("cx", pts[n - 1][0]); endDot.setAttribute("cy", pts[n - 1][1]);

      gGrid.innerHTML = ""; gY.innerHTML = ""; gX.innerHTML = "";
      for (var k = 0; k <= 3; k++) {
        var v = cfg.min + (cfg.max - cfg.min) * k / 3, y = yAt(v);
        gGrid.appendChild(el("line", { x1: CPAD.l, x2: CW - CPAD.r, y1: y, y2: y }));
        var t = el("text", { x: CPAD.l - 10, y: y + 4 }); t.textContent = fmt(Math.round(v)); gY.appendChild(t);
      }
      [0, Math.floor((n - 1) / 2), n - 1].forEach(function (i) {
        var t = el("text", { x: xAt(i, n), y: CPAD.t + cph + 22, "text-anchor": i === 0 ? "start" : i === n - 1 ? "end" : "middle" });
        t.textContent = labels[i] || ""; gX.appendChild(t);
      });
      svg.setAttribute("aria-label", cfg.aria);
      now.innerHTML = fmt(cfg.vals[n - 1]) + "<small>" + cfg.name + " &middot; " + (labels[n - 1] || "") + "</small>";

      if (!animate) return;
      var L = line.getTotalLength();
      gsap.fromTo(line, { attr: { "stroke-dasharray": L, "stroke-dashoffset": L } },
        { attr: { "stroke-dashoffset": 0 }, duration: 1.2, ease: "power2.out" });
      gsap.fromTo(area, { opacity: 0 }, { opacity: 1, duration: .8, delay: .3 });
      gsap.fromTo(endDot, { attr: { r: 0 } }, { attr: { r: 5 }, duration: .4, delay: 1.1, ease: "back.out(3)" });
    }

    function select(tab, focus, animate) {
      tabs.forEach(function (t) {
        var on = t === tab;
        t.setAttribute("aria-selected", on ? "true" : "false");
        t.tabIndex = on ? 0 : -1;
      });
      if (focus) tab.focus();
      cfg = {
        vals: tab.getAttribute("data-vals").split(",").map(Number),
        min: parseFloat(tab.getAttribute("data-min") || "0"),
        max: parseFloat(tab.getAttribute("data-max")),
        pre: tab.getAttribute("data-prefix"), suf: tab.getAttribute("data-suffix"),
        name: tab.textContent.trim(), aria: tab.getAttribute("data-aria") || ""
      };
      draw(animate === undefined ? motionOn : animate);
    }

    tabs.forEach(function (t, i) {
      t.addEventListener("click", function () { select(t); });
      t.addEventListener("keydown", function (e) {
        var n = e.key === "ArrowRight" ? i + 1 : e.key === "ArrowLeft" ? i - 1 : -1;
        if (n < 0 || n >= tabs.length) return;
        e.preventDefault(); select(tabs[n], true);
      });
    });

    svg.addEventListener("mousemove", function (e) {
      var r = svg.getBoundingClientRect(), n = cfg.vals.length;
      var i = Math.round(((e.clientX - r.left) / r.width * CW - CPAD.l) / cpw * (n - 1));
      if (i < 0 || i >= n) return;
      var x = xAt(i, n), y = yAt(cfg.vals[i]);
      cross.setAttribute("x1", x); cross.setAttribute("x2", x); cross.setAttribute("opacity", 1);
      dot.setAttribute("cx", x); dot.setAttribute("cy", y); dot.setAttribute("opacity", 1);
      tip.innerHTML = "<u>" + (labels[i] || "") + "</u><b><i></i>" + fmt(cfg.vals[i]) + "</b>";
      tip.style.left = (x / CW * 100) + "%"; tip.style.top = (y / CH * 100) + "%";
      tip.hidden = false;
    });
    svg.addEventListener("mouseleave", function () {
      cross.setAttribute("opacity", 0); dot.setAttribute("opacity", 0); tip.hidden = true;
    });

    // Draw once on arrival so the line animation is actually seen.
    var first = $('.mtab[aria-selected="true"]', root) || tabs[0];
    // Render statically first so the card is never empty, then replay the
    // draw-in once the chart scrolls into view.
    select(first, false, false);
    if (motionOn && window.ScrollTrigger) {
      ScrollTrigger.create({ trigger: root, start: "top 80%", once: true, onEnter: function () { draw(true); } });
    }
  }

  /* ----------------------------- boot ----------------------------- */
  // One failing section must not take the rest of the page down with it.
  function safe(name, fn) {
    try { fn(); } catch (err) {
      if (window.console) console.error("[TopicalBacklink] " + name + " failed:", err);
    }
  }

  function boot() {
    safe("nav", initNav);
    safe("graph", initGraph);
    safe("chart", initChart);
    safe("ticker", initTicker);
    safe("figures", initFigures);
    safe("services", initServices);
    safe("approach", initApproach);
    safe("order", initOrder);
    safe("swoosh", initSwoosh);
    safe("tmarq", initTmarq);
    safe("faq", initFaq);
    safe("audit", initAudit);
    safe("lead", initLead);
    safe("year", initYear);
    safe("herofx", initHeroFx);
    safe("glow", initGlow);
    safe("count", initCount);
    safe("inview", initInview);
    safe("proc", initProc);
    safe("calc", initCalc);
    safe("toc", initToc);
    safe("tline", initTline);
    safe("cschart", initCsChart);
    safe("reveals", initReveals);
    if (hasGSAP && window.ScrollTrigger) ScrollTrigger.refresh();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", boot);
  } else {
    boot();
  }
})();
