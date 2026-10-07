/* =============================================================
   Block editor: "Card & article settings" panel in the Post sidebar.
   Card summary (the excerpt), key takeaways and the hide-TOC switch.
   Values save with the post (meta registered in inc/blog.php).
   ============================================================= */
(function (wp) {
  "use strict";

  var el = wp.element.createElement;
  var useSelect = wp.data.useSelect;
  var useDispatch = wp.data.useDispatch;
  var C = wp.components;
  var Panel = (wp.editor && wp.editor.PluginDocumentSettingPanel) || (wp.editPost && wp.editPost.PluginDocumentSettingPanel);
  if (!Panel || !wp.plugins) return;

  function Settings() {
    var data = useSelect(function (select) {
      var ed = select("core/editor");
      return {
        type: ed.getCurrentPostType(),
        excerpt: ed.getEditedPostAttribute("excerpt") || "",
        meta: ed.getEditedPostAttribute("meta") || {}
      };
    }, []);
    var editPost = useDispatch("core/editor").editPost;
    if (data.type !== "post") return null;

    function setMeta(key, value) {
      var m = {};
      m[key] = value;
      editPost({ meta: m });
    }

    var n = data.excerpt.length;
    var lines = (data.meta._tb_takeaways || "").split("\n").filter(function (l) { return l.trim(); }).length;

    return el(Panel, { name: "settings", title: "Card & article settings", initialOpen: true },
      el(C.TextareaControl, {
        label: "Card summary (excerpt)",
        help: "Shown on the blog cards and the featured article. Aim for 140-160 characters (" + n + " now). Empty = first lines of the article.",
        value: data.excerpt,
        rows: 4,
        onChange: function (v) { editPost({ excerpt: v }); }
      }),
      el(C.TextareaControl, {
        label: "Key takeaways",
        help: "One point per line" + (lines ? " (" + lines + " now)" : "") + ". Shown in a box at the top of the article. Empty = no box.",
        value: data.meta._tb_takeaways || "",
        rows: 5,
        onChange: function (v) { setMeta("_tb_takeaways", v); }
      }),
      el(C.ToggleControl, {
        label: "Hide table of contents",
        help: "The contents list is built from your H2/H3 headings (needs at least two).",
        checked: !!data.meta._tb_hide_toc,
        onChange: function (v) { setMeta("_tb_hide_toc", v); }
      })
    );
  }

  wp.plugins.registerPlugin("tb-article-settings", { render: Settings });

  // One place to write the excerpt: hide the editor's own Excerpt panel.
  // Our panel opens on every load (newer WordPress ignores initialOpen).
  wp.domReady(function () {
    var key = "tb-article-settings/settings";
    var sel = wp.data.select("core/editor"), d = wp.data.dispatch("core/editor");
    if (sel.isEditorPanelOpened && !sel.isEditorPanelOpened(key)) d.toggleEditorPanelOpened(key);
    if (d && d.removeEditorPanel) d.removeEditorPanel("post-excerpt");
    else if (wp.data.dispatch("core/edit-post").removeEditorPanel) wp.data.dispatch("core/edit-post").removeEditorPanel("post-excerpt");
  });
})(window.wp);
