"""
Build the TopicalBacklink WordPress theme from the static HTML site.

    python build-wp-theme.py

Output:
    dist/topicalbacklink/          the theme folder
    dist/topicalbacklink.zip       upload this in WP: Appearance > Themes > Add New > Upload
    dist/TopicalBacklink-Handoff-UNZIP-FIRST.zip
                                   theme zip + docs/README.txt + docs/guide.html, for hand-off

Re-run after editing any .html / css / js / images file. Hand-written PHP
(functions.php, page.php, ...) lives in wp-theme/ and is copied as-is.
The homepage body is hand-written (wp-theme/parts/home/*.php, ACF-driven),
so edits to index.html's <main> must be mirrored there by hand.
"""
import html
import os
import re
import shutil
import sys
import zipfile

ROOT = os.path.dirname(os.path.abspath(__file__))
SRC_PHP = os.path.join(ROOT, "wp-theme")
DIST = os.path.join(ROOT, "dist")
SLUG = "topicalbacklink"
OUT = os.path.join(DIST, SLUG)

# key: (html file, page title, slug, parent key, create page on activation, template label)
PAGES = [
    ("home",               "index.html",              "Home",                         "home",                      None,           True,  "Home"),
    ("services",           "services.html",           "Services",                     "services",                  None,           True,  "Services"),
    ("pricing",            "pricing.html",            "Pricing",                      "pricing",                   None,           True,  "Pricing"),
    ("case-studies",       "case-studies.html",       "Case studies",                 "case-studies",              None,           True,  "Case studies"),
    ("about",              "about.html",              "About",                        "about",                     None,           True,  "About"),
    ("contact",            "contact.html",            "Contact",                      "contact",                   None,           True,  "Contact"),
    ("service-white-label", "service-detail.html",    "White-label Link Building",    "white-label-link-building", "services",     True,  "Service detail (White-label)"),
    ("case-study-collab",  "case-study-detail.html",  "Collab Management Case Study", "collab-management",         "case-studies", True,  "Case study detail (Collab Management)"),
]

HTML_TO_KEY = {p[1]: p[0] for p in PAGES}

# Pages whose body is hand-written in wp-theme/parts/ (ACF-driven) instead of
# generated from the HTML. Their HTML still supplies the SEO title/description.
HANDWRITTEN = {"home", "services", "case-studies"}

# Detail designs that are now WordPress posts (Services / Case studies in the
# dashboard). No page or template is generated for them; links to their HTML
# files resolve to the post with that slug (see tb_page_url()).
POST_BACKED = {"service-white-label": "tb_service", "case-study-collab": "tb_case_study"}

errors = []


def read(name):
    with open(os.path.join(ROOT, name), encoding="utf-8") as f:
        text = f.read()
    # PHP open tags inside the design would execute on the server.
    if "<?" in text:
        errors.append(f"{name}: contains '<?', which PHP would execute")
    return text


def php_str(s):
    return "'" + s.replace("\\", "\\\\").replace("'", "\\'") + "'"


def convert(fragment, where):
    """Rewrite local links and asset paths in an HTML fragment to PHP calls."""

    def repl_href(m):
        target = m.group(1)
        page, _, anchor = target.partition("#")
        if page not in HTML_TO_KEY:
            errors.append(f"{where}: unknown page link {target}")
            return m.group(0)
        args = php_str(HTML_TO_KEY[page])
        if anchor:
            args += ", " + php_str("#" + anchor)
        return f'href="<?php tb_link( {args} ); ?>"'

    fragment = re.sub(r'href="([a-z0-9-]+\.html(?:#[^"]*)?)"', repl_href, fragment)

    fragment = re.sub(r'(src|href|srcset|poster)="(images|css|js)/', r'\1="<?php tb_asset(); ?>\2/', fragment)

    # Legal links: Privacy -> WP privacy page (Settings > Privacy).
    fragment = fragment.replace('<li><a href="#">Privacy</a></li>', '<li><a href="<?php tb_privacy_url(); ?>">Privacy</a></li>')

    # Guard against anything local we failed to convert.
    for m in re.finditer(r'(?:src|href)="(?!https?:|mailto:|tel:|#|<\?php|data:)([^"]+)"', fragment):
        errors.append(f"{where}: unconverted local reference {m.group(1)}")
    return fragment


def between(text, start_pat, end_pat, where, include_end=True):
    s = text.find(start_pat)
    e = text.find(end_pat, s)
    if s < 0 or e < 0:
        errors.append(f"{where}: markers {start_pat!r} / {end_pat!r} not found")
        return ""
    return text[s:e + (len(end_pat) if include_end else 0)]


def meta(text, where):
    t = re.search(r"<title>(.*?)</title>", text, re.S)
    d = re.search(r'<meta name="description" content="([^"]*)"', text)
    if not t:
        errors.append(f"{where}: no <title>")
    return (html.unescape(t.group(1).strip()) if t else "",
            html.unescape(d.group(1).strip()) if d else "")


def write(rel, content):
    path = os.path.join(OUT, rel)
    os.makedirs(os.path.dirname(path), exist_ok=True)
    with open(path, "w", encoding="utf-8", newline="\n") as f:
        f.write(content)


def inject(text, after, line, where):
    """Insert `line` right after the single occurrence of `after`."""
    if text.count(after) != 1:
        errors.append(f"{where}: {after.strip()!r} not found exactly once")
        return text
    return text.replace(after, after + line)


def build_header(about):
    body = between(about, "<!-- ============================== NAV", '<main id="top">', "about.html header", include_end=False)
    # Nav "current page" state is decided at runtime.
    body = body.replace(' class="is-here" aria-current="page"', "")
    for key, file in (("services", "services.html"), ("pricing", "pricing.html"),
                      ("case-studies", "case-studies.html"), ("about", "about.html")):
        label = {"services": "Services", "pricing": "Pricing", "case-studies": "Case studies", "about": "About"}[key]
        old = f'      <a href="{file}">{label}</a>'
        if body.count(old) != 1:
            errors.append(f"header: nav link for {key} not found exactly once")
        body = body.replace(old, f'      <a href="{file}"<?php tb_here( \'{key}\' ); ?>>{label}</a>')
    # The blog only exists in WordPress (there is no blog.html), so its links are added here.
    body = inject(body, "<?php tb_here( 'case-studies' ); ?>>Case studies</a>\n",
                  "      <a href=\"<?php tbt_blog_link(); ?>\"<?php tb_here( 'blog' ); ?>>Blog</a>\n", "header nav")
    body = inject(body, '<a href="case-studies.html"><i>03</i>Case studies</a>\n',
                  '    <a href="<?php tbt_blog_link(); ?>"><i>04</i>Blog</a>\n', "mobile nav")
    body = body.replace('<a href="about.html"><i>04</i>About</a>', '<a href="about.html"><i>05</i>About</a>')
    body = convert(body, "header.php")
    return (
        "<?php\n/**\n * Site header.\n *\n * @package TopicalBacklink\n */\n?>\n"
        "<!doctype html>\n"
        "<html <?php language_attributes(); ?>>\n"
        "<head>\n"
        '<meta charset="<?php bloginfo( \'charset\' ); ?>">\n'
        '<meta name="viewport" content="width=device-width, initial-scale=1">\n'
        "<?php wp_head(); ?>\n"
        "</head>\n"
        "<body <?php body_class(); ?>>\n"
        "<?php wp_body_open(); ?>\n\n"
        + body.rstrip() + "\n"
    )


def build_footer(about):
    foot = between(about, "<!-- ============================== FOOTER", "</footer>", "about.html footer")
    foot = inject(foot, '<li><a href="case-studies.html">Case studies</a></li>\n',
                  '            <li><a href="<?php tbt_blog_link(); ?>">Blog</a></li>\n', "footer")
    foot = convert(foot, "footer.php")
    foot = foot.replace('<span id="yr">2026</span>', "<span id=\"yr\"><?php echo esc_html( gmdate( 'Y' ) ); ?></span>")
    return (
        "<?php\n/**\n * Site footer.\n *\n * @package TopicalBacklink\n */\n?>\n\n"
        + foot + "\n\n<?php wp_footer(); ?>\n</body>\n</html>\n"
    )


def main():
    # Empty the output folder rather than deleting it, so a terminal or file
    # explorer that has it open (Windows locks the folder) can't stop the build.
    os.makedirs(OUT, exist_ok=True)
    for entry in os.listdir(OUT):
        path = os.path.join(OUT, entry)
        if os.path.isdir(path):
            shutil.rmtree(path)
        else:
            os.remove(path)

    # Hand-written PHP / CSS.
    shutil.copytree(SRC_PHP, OUT, dirs_exist_ok=True)

    # Site assets.
    for d in ("css", "js", "images"):
        shutil.copytree(os.path.join(ROOT, d), os.path.join(OUT, d), dirs_exist_ok=True)

    about = read("about.html")
    write("header.php", build_header(about))
    write("footer.php", build_footer(about))

    registry = []
    for key, file, title, slug, parent, create, label in PAGES:
        src = read(file)
        doc_title, desc = meta(src, file)
        if key in POST_BACKED:
            pass
        elif key in HANDWRITTEN:
            if not os.path.isfile(os.path.join(OUT, "parts", f"content-{key}.php")):
                errors.append(f"wp-theme/parts/content-{key}.php is missing")
        else:
            main_html = between(src, '<main id="top">', "</main>", file)
            main_html = convert(main_html, file)
            write(f"parts/content-{key}.php",
                  f"<?php\n/**\n * Page body for \"{title}\" (generated from {file} by build-wp-theme.py).\n *\n * @package TopicalBacklink\n */\n?>\n"
                  + main_html + "\n")
        if key not in POST_BACKED:
            write(f"templates/{key}.php",
                  f"<?php\n/**\n * Template Name: TB - {label}\n *\n * @package TopicalBacklink\n */\n\n"
                  f"tb_set_page( {php_str(key)} );\nget_header();\nget_template_part( {php_str('parts/content-' + key)} );\nget_footer();\n")

        path = slug if not parent else next(p[3] for p in PAGES if p[0] == parent) + "/" + slug
        registry.append(
            f"\t\t{php_str(key)} => array(\n"
            f"\t\t\t'title'       => {php_str(title)},\n"
            f"\t\t\t'slug'        => {php_str(slug)},\n"
            f"\t\t\t'path'        => {php_str(path)},\n"
            f"\t\t\t'parent'      => {php_str(parent) if parent else 'null'},\n"
            f"\t\t\t'template'    => {php_str('templates/' + key + '.php')},\n"
            f"\t\t\t'create'      => {'true' if create and key not in POST_BACKED else 'false'},\n"
            f"\t\t\t'post_type'   => {php_str(POST_BACKED[key]) if key in POST_BACKED else 'null'},\n"
            f"\t\t\t'doc_title'   => {php_str(doc_title)},\n"
            f"\t\t\t'description' => {php_str(desc)},\n"
            f"\t\t),\n"
        )

    write("inc/pages.php",
          "<?php\n/**\n * Designed pages (generated by build-wp-theme.py). Parents must come before children.\n *\n * @package TopicalBacklink\n */\n\n"
          "if ( ! defined( 'ABSPATH' ) ) {\n\texit;\n}\n\n"
          "function tb_pages() {\n\treturn array(\n" + "".join(registry) + "\t);\n}\n")

    # Screenshot shown in Appearance > Themes (optional; made separately).
    shot = os.path.join(ROOT, "wp-screenshot.png")
    if os.path.isfile(shot):
        shutil.copy(shot, os.path.join(OUT, "screenshot.png"))

    # Drop stray OS files.
    for dirpath, _, files in os.walk(OUT):
        for f in files:
            if f in (".DS_Store", "Thumbs.db") or f.endswith(".log"):
                os.remove(os.path.join(dirpath, f))

    # Every image a template references must exist in the theme.
    for dirpath, _, files in os.walk(OUT):
        for f in files:
            if not f.endswith(".php"):
                continue
            text = open(os.path.join(dirpath, f), encoding="utf-8").read()
            for rel in re.findall(r"<\?php tb_asset\(\); \?>([^\"]+)\"", text):
                if not os.path.isfile(os.path.join(OUT, rel)):
                    errors.append(f"{f}: references missing asset {rel}")

    if errors:
        print("BUILD FAILED:")
        for e in errors:
            print("  -", e)
        sys.exit(1)

    zpath = os.path.join(DIST, SLUG + ".zip")
    if os.path.exists(zpath):
        os.remove(zpath)
    with zipfile.ZipFile(zpath, "w", zipfile.ZIP_DEFLATED) as z:
        for dirpath, _, files in os.walk(OUT):
            for f in sorted(files):
                full = os.path.join(dirpath, f)
                arc = os.path.relpath(full, DIST).replace(os.sep, "/")
                z.write(full, arc)
    n = sum(len(f) for _, _, f in os.walk(OUT))
    print(f"OK: {n} files -> {zpath} ({os.path.getsize(zpath) // 1024} KB)")

    # Hand-off package: theme zip + guide (docs/).
    docs = os.path.join(ROOT, "docs")
    if os.path.isdir(docs):
        ppath = os.path.join(DIST, "TopicalBacklink-Handoff-UNZIP-FIRST.zip")
        with zipfile.ZipFile(ppath, "w", zipfile.ZIP_DEFLATED) as z:
            z.write(os.path.join(docs, "README.txt"), "TopicalBacklink/README.txt")
            z.write(zpath, "TopicalBacklink/1-Theme/topicalbacklink.zip")
            z.write(os.path.join(docs, "guide.html"), "TopicalBacklink/2-Guide/TopicalBacklink-Guide.html")
        print(f"OK: package -> {ppath} ({os.path.getsize(ppath) // 1024} KB)")


if __name__ == "__main__":
    main()
