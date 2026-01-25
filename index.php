<?php
declare(strict_types=1);
date_default_timezone_set('UTC');
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self' data:; connect-src 'self';");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=utf-8");
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
}
session_set_cookie_params([
    'lifetime' => 1800,
    'path' => '/',
    'domain' => '',
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Strict'
]);
session_start();
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
if (isset($_SESSION['last_act']) && (time() - $_SESSION['last_act'] > 1800)) {
    session_unset();
    session_destroy();
    header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}
$_SESSION['last_act'] = time();
require_once __DIR__ . '/etc/functions.php';
setupErrorLogging();
ensureAnsibleDir();
loadLanguage('en');
$systemErrors = checkSystemHealth();
if (!extension_loaded('json')) {
    header("Content-Type: text/html; charset=utf-8");
    die('<div style="font-family:sans-serif;padding:20px;color:#721c24;background:#f8d7da;border:1px solid #f5c6cb;border-radius:5px;"><strong>' . __("system_errors") . ':</strong> ' . __("err_php_json") . '</div>');
}
$ansibleDir = __DIR__ . '/ansible';
$writeError = !is_dir($ansibleDir) || !is_writable($ansibleDir);
$cr = getWebAnsConfig();
$lang = 'en';
$currentUser = $_SESSION['user_login'] ?? null;
$userLang = null;
$userFound = false;
if ($currentUser && !empty($cr['users'])) {
    foreach ($cr['users'] as $u) {
        if (isset($u['l']) && $u['l'] === $currentUser) {
            $userLang = $u['lang'] ?? ($cr['lang'] ?? 'en');
            $userFound = true;
            break;
        }
    }
}
$availableLangs = getAvailableLanguages();
if ($userLang && array_key_exists($userLang, $availableLangs)) {
    $lang = $userLang;
} elseif (isset($cr['lang']) && array_key_exists($cr['lang'], $availableLangs)) {
    $lang = $cr['lang'];
} else {
    $browserLang = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;
    if ($browserLang && array_key_exists($browserLang, $availableLangs)) {
        $lang = $browserLang;
    } else {
        $lang = isset($availableLangs['en']) ? 'en' : array_key_first($availableLangs);
    }
}
loadLanguage($lang);
$systemErrors = checkSystemHealth();
if (isset($cr['l'], $cr['p']) && !isset($cr['users'])) {
    $cr['users'] = [['l' => $cr['l'], 'p' => $cr['p']]];
    unset($cr['l'], $cr['p']);
    $cf = __DIR__ . '/ansible/webans.cfg';
    file_put_contents($cf, json_encode($cr));
}
$set = !is_array($cr) || empty($cr['users']) || !isset($cr['ansible_path'], $cr['rotate_keep']);
if ($set && isset($_SESSION['auth'])) {
    unset($_SESSION['auth']);
}
$auth = !$set && ($_SESSION['auth'] ?? false) === true;
if ($auth && !$userFound) {
    session_destroy();
    header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['act'])) {
    if (empty($_POST)) die(__("error_post_empty"));
    checkCsrf(__("csrf_error_auth"));
    if ($_POST['act'] === 'set' && $set && $_POST['l'] && $_POST['p']) {
        $saveLang = $_POST['save_lang'] ?? 'en';
        $cf = __DIR__ . '/ansible/webans.cfg';
        file_put_contents($cf, json_encode(['users' => [['l' => $_POST['l'], 'p' => password_hash($_POST['p'], PASSWORD_DEFAULT), 'lang' => $saveLang]], 'ansible_path' => '', 'rotate_keep' => 100]));
        $_SESSION['toast_message'] = ['type' => 'success', 'text' => __("admin_created")];
        session_write_close();
        header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
        exit;
    } elseif ($_POST['act'] === 'in' && !$set) {
        $found = false;
        foreach ($cr['users'] as $u) {
            if ($u['l'] === $_POST['l'] && password_verify($_POST['p'], $u['p'])) { 
                $found = true; 
                $_SESSION['user_login'] = $u['l'];
                break; 
            }
        }
        if ($found) {
            session_regenerate_id(true);
            $_SESSION['auth'] = true;
            session_write_close();
            header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
            exit;
        } else {
            sleep(1);
            $_SESSION['toast_message'] = ['type' => 'error', 'text' => __("invalid_data")];
        }
    } elseif ($_POST['act'] === 'out') {
        session_destroy();
        header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
        exit;
    }
}
if ($set || !$auth) {
    require __DIR__ . '/includes/templates/header.php';
    require __DIR__ . '/includes/templates/auth.php';
    require __DIR__ . '/includes/templates/footer.php';
    exit;
}
if (isset($_GET['check_auth'])) exit('OK');
if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($_GET['act'] ?? '') === 'get_playbook' && !empty($_GET['file'])) {
    echo getPlaybookContent($_GET['file']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($_GET['act'] ?? '') === 'get_report' && !empty($_GET['id'])) {
    $id = $_GET['id']; 
    $offset = isset($_GET['offset']) ? max(0, (int)$_GET['offset']) : 0;
    $meta = getReports()[$id] ?? null; 
    markReportRead($id);
    $logFull = getReportLog($id);
    $logLen = strlen($logFull);
    $delta = '';
    if ($offset === 0) {
        $delta = $logFull;
    } elseif ($offset < $logLen) {
        $delta = substr($logFull, $offset);
    }
    $parsed = parseAnsibleLog($logFull);
    ob_start();
    $viewMode = 'header';
    require __DIR__ . '/includes/templates/view.php';
    $headerHtml = ob_get_clean() ?: '';
    ob_start();
    $viewMode = 'body';
    require __DIR__ . '/includes/templates/view.php';
    $parsedHtml = ob_get_clean() ?: '';
    header('Content-Type: application/json');
    $json = json_encode([
        'header' => $headerHtml,
        'parsed' => $parsedHtml,
        'delta' => $delta,
        'length' => $logLen,
        'status' => $meta['status'] ?? 'unknown'
    ], JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        echo json_encode(['header' => '<div class="panel">'.__("json_error").json_last_error_msg().'</div>', 'parsed' => '', 'delta' => '', 'length' => 0, 'status' => 'error']);
    } else {
        echo $json;
    }
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($_GET['act'] ?? '') === 'refresh_state') {
    $playbooks = getPlaybooksFromFiles();
    $inv = getInventoryData();
    $hosts = $inv['hosts'];
    $groups = $inv['groups'];
    $hostsByGroup = [];
    foreach ($hosts as $h) {
        $hostsByGroup[$h['group_name']][] = $h;
    }
    ob_start();
    require __DIR__ . '/includes/templates/main.php';
    $mainHtml = ob_get_clean();
    $users = $cr['users'] ?? [];
    $config = getAnsibleConfig();
    ob_start();
    require __DIR__ . '/includes/templates/settings.php';
    $settingsHtml = ob_get_clean();
    header('Content-Type: application/json');
    echo json_encode([
        'main' => $mainHtml, 'settings' => $settingsHtml, 'groups' => $groups, 'hosts' => $hosts
    ], JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE);
    exit;
}
$playbooks = getPlaybooksFromFiles();
$inv = getInventoryData();
$hosts = $inv['hosts'];
$groups = $inv['groups'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST)) die(__("error_post_empty"));
    checkCsrf();
    if (isset($_POST['del_rep'])) {
        deleteReport($_POST['del_rep']);
        session_write_close();
        header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
        exit;
    }
    if (isset($_POST['clear_reps'])) {
        clearReports();
        session_write_close();
        header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
        exit;
    }
    if (isset($_POST['playbook'])) {
        $selected_hosts = array_intersect($_POST['hosts'] ?? [], array_column($hosts, 'hostname'));
        $result = runPlaybook($_POST['playbook'] ?? '', $selected_hosts);
        if (!empty($result['success']) && isset($result['id'])) {
            setcookie('open_report', $result['id'], time() + 30, '/');
        } elseif (isset($result['error']) && $result['error'] === 'NO_HOSTS_AVAILABLE') {
            $skipped_hosts_str = !empty($result['skipped']) ? ': ' . htmlspecialchars(implode(', ', $result['skipped'])) : '.';
            $_SESSION['toast_message'] = ['type' => 'error', 'text' => __("all_hosts_unavailable") . $skipped_hosts_str];
        } elseif (!empty($result['message'])) {
            $_SESSION['toast_message'] = ['type' => 'error', 'text' => $result['message']];
        }
        session_write_close();
        header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
        exit;
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['playbook_content'])) {
    checkCsrf();
    $tmpFile = __DIR__ . '/ansible/playbooks/.syntax_check_' . bin2hex(random_bytes(4)) . '.yml';
    file_put_contents($tmpFile, $_POST['playbook_content']);
    $extraVars = '';
    $analysis = analyzePlaybookContent($_POST['playbook_content']);
    if ($analysis['type'] === 'dynamic' && !empty($analysis['variable'])) {
        $extraVars = " -e " . escapeshellarg($analysis['variable'] . "=localhost");
    }
    $bin = !empty($cr['ansible_path']) ? $cr['ansible_path'] : 'ansible-playbook';
    $cmd = escapeshellarg($bin) . " --syntax-check " . escapeshellarg($tmpFile) . $extraVars . " 2>&1; echo $?";
    $out = shell_exec($cmd) ?? '';
    $output = explode("\n", trim($out));
    $ret = (int)array_pop($output);
    unlink($tmpFile);
    if ($ret !== 0) {
        $cleanOutput = array_map(function($line) use ($tmpFile) {
            return str_replace([$tmpFile, 'The error appears to be in'], ['playbook.yml', 'Ошибка в'], $line);
        }, $output);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false, 
            'error_type' => 'syntax',
            'message' => __("syntax_error_title") . implode("\n", $cleanOutput)
        ], JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE);
        exit;
    }
}
require __DIR__ . '/etc/ansible.php';
$reps = getReports();
ob_start();
$users = $cr['users'] ?? [];
$config = getAnsibleConfig();
require __DIR__ . '/includes/templates/settings.php';
$settingsHtml = ob_get_clean();
require __DIR__ . '/includes/templates/header.php';
?>
<div class="app-container">
<?php 
$hostsByGroup = [];
foreach ($hosts as $h) {
    $hostsByGroup[$h['group_name']][] = $h;
}
require __DIR__ . '/includes/templates/main.php'; 
?>
<?= $settingsHtml ?>
<?php require __DIR__ . '/includes/templates/reports.php'; ?>
</div>
<script>
var groupsData = <?= json_encode($groups, JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE) ?: '[]' ?>;
var hostsData = <?= json_encode($hosts, JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE) ?: '[]' ?>;
</script>
<?php
if(!empty($systemErrors)): ?>
    <div class="alert alert-error system-errors">
        <strong><?= __("system_errors") ?>:</strong>
        <ul class="system-errors-list"><?php foreach($systemErrors as $e) echo '<li>'.htmlspecialchars($e).'</li>'; ?></ul>
    </div>
<?php endif;
require __DIR__ . '/includes/templates/footer.php'; ?>