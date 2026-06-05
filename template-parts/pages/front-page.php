<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">

  <!-- Card 1 -->
  <div class="card bg-base-200 shadow-xl">
    <div class="card-body">
      <h2 class="card-title">Customizer builder based on JSON configuration</h2>
      <div class="mockup-code">
        <pre data-prefix="ㆍ"><code>"controls": {</code></pre>
        <pre data-prefix="ㆍ"><code>  "control_site_icon": {</code></pre>
        <pre data-prefix="ㆍ"><code>    "type": "image",</code></pre>
        <pre data-prefix="ㆍ"><code>    "mime_type": "image",</code></pre>
        <pre data-prefix="ㆍ"><code>    "label": "Icon",</code></pre>
        <pre data-prefix="ㆍ"><code>    "section": "section_site_common",</code></pre>
        <pre data-prefix="ㆍ"><code>    "settings": "site_icon"</code></pre>
        <pre data-prefix="ㆍ"><code>  }</code></pre>
        <pre data-prefix="ㆍ"><code>}</code></pre>
      </div>
    </div>
  </div>

  <!-- Card 2 -->
  <div class="card bg-base-200 shadow-xl">
    <div class="card-body">
      <h2 class="card-title">Centralized & flexible templates system</h2>
      <div class="mockup-code">
        <pre data-prefix="ㆍ"><code>theme_render_header();</code></pre>
        <pre data-prefix="ㆍ"><code>theme_render_menu();</code></pre>
        <pre data-prefix="ㆍ"><code>theme_render_template('pages/front-page');</code></pre>
        <pre data-prefix="ㆍ"><code>theme_render_footer();</code></pre>
        <pre data-prefix=""><code>────────────────────────────</code></pre>
        <pre data-prefix="ㆍ"><code>theme_render_header('maintenance');</code></pre>
        <pre data-prefix="ㆍ"><code>theme_render_menu('minimal');</code></pre>
        <pre data-prefix="ㆍ"><code>theme_render_template('pages/maintenance-page');</code></pre>
        <pre data-prefix="ㆍ"><code>theme_render_footer();</code></pre>
        </code>
      </div>
    </div>
  </div>

  <!-- Card 3 -->
  <div class="card bg-base-200 shadow-xl">
    <div class="card-body">
      <h2 class="card-title">Centralized AJAX dispatcher with nonce support</h2>
      <div class="mockup-code">
        <pre data-prefix=""><code></code></pre>
        <pre data-prefix=""><code></code></pre>
        <pre data-prefix="ㆍ"><code>switch ($action) {</code></pre>
        <pre data-prefix="ㆍ"><code>  case 'verify_recaptcha':</code></pre>
        <pre data-prefix="ㆍ"><code>      return theme_verify_recaptcha_ajax();</code></pre>
        <pre data-prefix="ㆍ"><code>      break;</code></pre>
        <pre data-prefix="ㆍ"><code>}</code></pre>
        <pre data-prefix=""><code></code></pre>
        <pre data-prefix=""><code></code></pre>
      </div>
    </div>
  </div>

  <!-- Card 4 -->
  <div class="card bg-base-200 shadow-xl">
    <div class="card-body">
      <h2 class="card-title">Centralized & flexible enqueue of theme assets</h2>
      <div class="mockup-code">
        <pre data-prefix=""><code></code></pre>
        <pre data-prefix=""><code></code></pre>
        <pre data-prefix="ㆍ"><code>theme_enqueue_css('/core/menu-common.css');</code></pre>
        <pre data-prefix="ㆍ"><code>theme_enqueue_js('theme.js');</code></pre>
        <pre data-prefix="ㆍ"><code>theme_enqueue_js_for('news', 'news_ajax.js');</code></pre>
        <pre data-prefix="ㆍ"><code>theme_enqueue_package('daisy-ui');</code></pre>
        <pre data-prefix="ㆍ"><code>theme_enqueue_google_font('Roboto', [400,500,700]);</code></pre>
        <pre data-prefix=""><code></code></pre>
        <pre data-prefix=""><code></code></pre>
      </div>
    </div>
  </div>

  <!-- Card 5 -->
  <div class="card bg-base-200 shadow-xl">
    <div class="card-body">
      <h2 class="card-title">Centralized admin dashboard pages registration & utilities</h2>
      <div class="mockup-code">
        <pre data-prefix=""><code></code></pre>
        <pre data-prefix=""><code></code></pre>
        <pre data-prefix="ㆍ"><code>tgmpa($plugins);</code></pre>
        <pre data-prefix="ㆍ"><code>theme_tgma_check_supported_plugins();</code></pre>
        <pre data-prefix="ㆍ"><code>theme_notice_supported_plugins();</code></pre>
        <pre data-prefix="ㆍ"><code>theme_register_admin_page('aura-logs', ...);</code></pre>
        <pre data-prefix="ㆍ"><code>theme_enqueue_admin_css_for('aura-logs', 'admin.css');</code></pre>
        <pre data-prefix=""><code></code></pre>
        <pre data-prefix=""><code></code></pre>
      </div>
    </div>
  </div>

  <!-- Card 6 -->
  <div class="card bg-base-200 shadow-xl">
    <div class="card-body">
      <h2 class="card-title">Theme development logging support</h2>
      <div class="mockup-code">
        <pre data-prefix=""><code></code></pre>
        <pre data-prefix=""><code></code></pre>
        <pre data-prefix=""><code></code></pre>
        <pre data-prefix="ㆍ" class="text-error"><code>log_error("Call to undefined function {$callback}");</code></pre>
        <pre data-prefix="ㆍ" class="text-warning"><code>log_warn("Template {$template_file} was not found");</code></pre>
        <pre data-prefix="ㆍ" class="text-success"><code>log_info("Theme bootstrap initialization completed");</code></pre>
        <pre data-prefix=""><code></code></pre>
        <pre data-prefix=""><code></code></pre>
        <pre data-prefix=""><code></code></pre>
      </div>
    </div>
  </div>

</div>