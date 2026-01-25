<footer class="app-footer">
    <div class="app-container">
        <span>
            <strong>WebAns v0.1.0</strong> &copy; <?= date('Y') ?> 
            <a href="https://github.com/s-v-a-t" target="_blank" rel="noopener">s-v-a-t</a>. 
            All rights reserved.
        </span>
        <div class="footer-license">
            <small>Licensed for Personal & Internal Use.</small>
        </div>
    </div>
</footer>
<div id="toast" class="toast"></div>
<script>
const LANG = <?= json_encode($GLOBALS['translations'] ?? [], JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE) ?: '{}' ?>;
function t(key) { return LANG[key] || key; }
</script>
<script src="./includes/lib/app.js?v=<?= file_exists(__DIR__ . '/../lib/app.js') ? filemtime(__DIR__ . '/../lib/app.js') : time() ?>"></script>
<?php
if (isset($_SESSION['toast_message'])) {
    $toast = $_SESSION['toast_message'];
    echo "<script>document.addEventListener('DOMContentLoaded', () => showToast(" . (json_encode($toast['text'], JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE) ?: '""') . ", " . (json_encode($toast['type'], JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE) ?: '""') . "));</script>";
    unset($_SESSION['toast_message']);
}
?>
</body>
</html>