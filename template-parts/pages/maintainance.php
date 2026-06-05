<div class="hero-content flex-col lg:flex-row w-full flex-1">

  <div class="lg:w-1/5 p-6">
    <img src="<?= get_template_directory_uri(); ?>/assets/images/maintenance.png"
      class="max-w-full rounded-lg shadow-2xl"
      alt=""
    />
  </div>

  <div class="lg:w-4/5 p-6">
    <h1 class="text-2xl font-bold p-4">Maintenance Page</h1>

    <div class="mockup-code w-full">
      <pre data-prefix="ㆍ"><code>if( theme_is_maintenance_mode() ) {</code></pre>
      <pre data-prefix="ㆍ"><code>  theme_render_template('maintenance');</code></pre>
      <pre data-prefix="ㆍ" class="text-success"><code>  log_info('Template successfully loaded');</code></pre>
      <pre data-prefix="ㆍ"><code>}</code></pre>
    </div>
  </div>

</div>