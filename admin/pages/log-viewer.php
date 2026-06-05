<?php
    $levels = [
        'NONE'    => 0,
        'ERROR'   => 1,
        'WARNING' => 2,
        'INFO'    => 3,
    ];

    $current_level = $levels[THEME_LOG_LEVEL] ?? 3;

    $log_file = THEME_LOG_FILE;
    $log_file_exists = file_exists($log_file);

    if (!$log_file_exists) {
        echo "<div>The log file " . THEME_LOG_FILE . " was not found</div>";
        return;
    }

    $log_entries = [];

    // Read file line-by-line
    $lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if ($lines !== false) {
        foreach ($lines as $line) {
            $decoded = json_decode($line, true);

            if (is_array($decoded)) {
                $log_entries[] = $decoded;
            }
        }
    }

    // Filter log level
    $log_entries = array_filter($log_entries, function ($entry) use ($levels, $current_level) {

        $entry_level = $levels[$entry['level'] ?? 'INFO'] ?? 3;
        return $entry_level <= $current_level;
    });

    // Show newest logs first
    $log_entries = array_reverse($log_entries);
?>

<script>
    function toggleStack(icon) {
        const trace = icon.nextElementSibling;
        const isOpen = trace && trace.style.display === 'block';

        document.querySelectorAll('.log-trace').forEach(el => {
            el.style.display = 'none';
        });

        if (trace && !isOpen) {
            trace.style.display = 'block';
        }
    }
</script>

<?php if (WP_DEBUG): ?>
    <div class="report">
        <div class="report-header">
            <div>
                <h1>Theme Log</h1>
                <p>View log information of theme processes</p>
            </div>

            <div class="report-actions">
                <p class="report-message"></p>
                <img class="report-spinner" src="<?= THEME_URL . '/assets/images/spinner.gif' ?>"/>

                <button type="button" class="clear-btn">
                    ❌ Clear
                </button>
            </div>
        </div>

        <div class="log-level">Current Log Level: <?php echo THEME_LOG_LEVEL; ?></div>
        
        <?php if(!$log_file_exists) : ?>
            <div class="log-error">⚠️ No log file available.</div>
        <?php elseif(empty($log_entries)) : ?>
            <div class="log-empty">⚠️ No log entries available.</div>
        <?php else : ?>
            <div class="log-header">
                <div>Level</div>
                <div>Timestamp</div>
                <div>Domain</div>
                <div>Caller</div>
                <div>Invoker</div>
                <div>Message</div>
            </div>
            <div class="log-wrapper">
            <?php foreach($log_entries as $log_entry):
                if (!$log_entry) continue;

                $level    = htmlspecialchars($log_entry['level'] ?? 'INFO');
                $time     = htmlspecialchars($log_entry['time'] ?? '');
                $domain   = htmlspecialchars($log_entry['domain'] ?? '');
                $caller   = htmlspecialchars($log_entry['caller'] ?? '');
                $invoker  = htmlspecialchars($log_entry['invoker'] ?? '');
                $stack    = htmlspecialchars($log_entry['stack'] ?? '');
                $message  = htmlspecialchars($log_entry['msg'] ?? '');

                // Regular expression to add class for message part (<...>)
                $message = preg_replace('/&lt;(.*?)&gt;/','<span class="log-highlight">&lt;$1&gt;</span>', $message);
            ?>
                <div class="log-entry">
                        <div class="<?= $level ?>"><?= $level ?></div>
                        <div><?= $time ?></div>
                        <div><?= $domain ?></div>
                        <div><?= $caller ?></div>
                        <div>
                            <div style="display:inline; position:relative;">
                                <span style="padding-right:4px;" onclick="toggleStack(this);">💡</span>
                                <span class="log-trace"><?= $stack ?></span>
                            </div>
                            <span><?= $invoker ?></span>
                        </div>
                        <div><?= $message ?></div>
                </div>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const btn = document.querySelector('.clear-btn');
    const spinner = document.querySelector('.report-spinner');
    const message = document.querySelector('.report-message');

    if (!btn || !spinner || !message) return;

    async function runUpdate() {

        // Reset messages
        message.textContent = '';
        message.style.color = '';

        // Show spinner
        spinner.style.display = 'inline-block';
        btn.disabled = true;

        try {
            const data = await themeAjaxRequest("clear_log");

            // Hide spinner
            spinner.style.display = 'none';
            btn.disabled = false;

            if (data.success) {
                message.textContent = data.data?.message || 'Clear completed';
                message.style.color = 'green';

                // Refresh page
                setTimeout(() => location.reload(), 1500);

            } else {
                message.textContent = data.data?.message || 'Clear failed';
                message.style.color = 'red';
            }

        } catch (err) {
            spinner.style.display = 'none';
            btn.disabled = false;

            paddock.log("Log Clear request error:", err);
            message.textContent = 'Request error';
            message.style.color = 'red';
        }
    }

    btn.addEventListener('click', runUpdate);
});
</script>