<?php
declare(strict_types=1);
function getDefaultEnglishTranslations(): array {
    static $cache = null;
    if ($cache !== null) return $cache;
    $cache = [
        '__meta__' => ['label' => 'English'],
        'title' => 'WebAns',
        'login' => 'Login',
        'password' => 'Password',
        'enter' => 'Enter',
        'create' => 'Create',
        'create_admin' => 'Create Administrator',
        'login_title' => 'Login to WebAns',
        'config_not_found' => 'Configuration file not found.<br>Please create a user.',
        'error_write_perm' => 'Error: No write access to the project folder.<br>Please grant write permissions to the folder containing index.php.',
        'main' => 'Main',
        'reports' => 'Reports',
        'settings' => 'Settings',
        'logout' => 'Logout',
        'select_playbook' => '🚀 Select Playbook:',
        'select_hosts' => '🖥 Select Hosts',
        'select_all' => 'Select All',
        'run' => 'Run',
        'clear_all' => 'Clear All',
        'delete' => 'Delete',
        'delete_confirm' => 'Delete?',
        'unavailable_hosts' => 'Unavailable hosts:',
        'report_not_found' => 'Report not found',
        'select_report' => 'Select a report',
        'general' => 'General',
        'groups' => 'Groups',
        'hosts' => 'Hosts',
        'playbooks' => 'Playbooks',
        'config' => 'Ansible.cfg',
        'users' => 'Users',
        'language' => 'Language',
        'save' => 'Save',
        'cancel' => 'Cancel',
        'add' => 'Add',
        'edit' => 'Edit',
        'name' => 'Name',
        'vars' => 'Variables',
        'ip' => 'IP',
        'params' => 'Parameters',
        'group' => 'Group',
        'filename' => 'Filename',
        'new_playbook' => '-- New --',
        'actions' => 'Actions',
        'new_password' => 'New Password',
        'update' => 'Update',
        'delete_user_confirm' => 'Delete user?',
        'delete_last_user_confirm' => "Delete the last user?\nSystem settings will be reset!",
        'filter' => '📂 Filter:',
        'all' => 'All',
        'error' => 'Error',
        'loading' => 'Loading...',
        'error_loading' => 'Loading error: ',
        'show_raw_log' => 'Show raw log',
        'success' => 'Success',
        'csrf_error' => 'Security error (CSRF). Please refresh the page.',
        'lang_empty' => 'Language files not found in includes/lang/',
        'refresh_page' => 'Refresh Page',
        'system_errors' => 'System Errors',
        'settings_saved' => 'Settings saved',
        'user_added' => 'User added',
        'user_exists' => 'User already exists',
        'user_deleted' => 'User deleted',
        'password_updated' => 'Password updated',
        'group_added' => 'Group added',
        'host_added' => 'Host added',
        'playbook_deleted' => 'Playbook deleted',
        'playbook_saved' => 'Playbook saved',
        'host_deleted' => 'Host deleted',
        'group_deleted' => 'Group deleted',
        'group_updated' => 'Group updated',
        'host_updated' => 'Host updated',
        'config_saved' => 'Configuration saved',
        'session_lifetime' => 'Session Timeout (sec)',
        'session_tooltip' => 'Seconds of inactivity before auto-logout.',
        'admin_created' => 'Administrator created. Please login.',
        'invalid_data' => 'Invalid data',
        'all_hosts_unavailable' => 'All selected hosts are unavailable',
        'target_not_found' => "Playbook target '%s' not found in inventory.",
        'playbook_target_error' => 'Failed to determine playbook target: "hosts:" directive not found.',
        'playbook_empty_target' => 'Playbook "hosts:" directive cannot be empty.',
        'playbook_all_disabled' => 'Running playbooks on all hosts ("all") is disabled for security reasons.',
        'playbook_not_found' => 'Playbook file not found or empty.',
        'syntax_error_title' => "YAML Syntax Error:\n",
        'editing' => 'Editing: ',
        'invalid_host_name' => 'Invalid host name',
        'invalid_ip' => 'Invalid IP address or host name',
        'invalid_params' => 'Parameters cannot contain newlines',
        'invalid_group_name' => 'Invalid group name',
        'group_exists' => 'Group exists',
        'invalid_filename' => 'Invalid filename (allowed a-z, 0-9, . _ -)',
        'error_mkdir' => 'Error creating directory',
        'error_write' => 'Error writing file',
        'syntax_error' => 'Syntax Error',
        'close' => 'Close',
        'delete_playbook_confirm' => 'Delete playbook ',
        'error_save' => 'Save error',
        'error_delete' => 'Delete error',
        'ansible_path' => 'Ansible Path (ansible-playbook)',
        'rotate_keep' => 'Keep Reports (count)',
        'path_placeholder' => 'E.g. /usr/bin/ansible-playbook',
        'select_language' => 'Select Language',
        'no_hosts_selected' => 'No hosts selected for execution',
        'lang_tooltip' => 'Application interface language.',
        'path_tooltip' => 'Full path to the ansible-playbook executable. If not specified, the system path is used.',
        'rotate_tooltip' => 'Number of recent reports to keep. Older reports are automatically deleted.',
        'error_post_empty' => 'Error: POST data is empty. Check post_max_size.',
        'csrf_error_auth' => 'CSRF Error (Auth).',
        'csrf_error_settings' => 'CSRF Error (Settings).',
        'json_error' => 'JSON Error: ',
        'error_in' => 'Error in ',
        'unknown_play' => 'Unknown Play',
        'unknown_task' => 'Unknown Task',
        'item_failed' => 'Item failed',
        'failed_items' => 'Failed items: ',
        'loop_items' => 'Loop: %s items',
        'changed_items' => ' (%s changed)',
        'table_host' => 'Host',
        'table_ok' => 'OK',
        'table_changed' => 'Changed',
        'table_unreachable' => 'Unreachable',
        'table_failed' => 'Failed',
        'table_skipped' => 'Skipped',
        'yaml_placeholder' => 'YAML content...',
        'err_shell_exec' => "Function 'shell_exec' is disabled in PHP settings. The application cannot work.",
        'err_php_json' => "PHP extension 'json' is not loaded.",
        'err_ansible_dir_not_found' => "Directory 'ansible' not found.",
        'err_ansible_dir_perm' => "Directory 'ansible' is not writable. Check permissions.",
        'err_reports_mkdir' => "Failed to create directory 'ansible/reports'.",
        'err_reports_perm' => "Directory 'ansible/reports' is not writable.",
        'err_playbooks_mkdir' => "Failed to create directory 'ansible/playbooks'.",
        'err_playbooks_perm' => "Directory 'ansible/playbooks' is not writable.",
        'err_file_perm' => "File '%s' is not writable.",
    ];
    $enPath = __DIR__ . '/../includes/lang/en.php';
    if (file_exists($enPath)) {
        $loaded = include $enPath;
        if (is_array($loaded)) {
            $cache = array_merge($cache, $loaded);
        }
    }
    return $cache;
}
function loadLanguage(string $lang): void {
    global $translations;
    $defaults = getDefaultEnglishTranslations();
    $avail = getAvailableLanguages();
    if (!isset($avail[$lang])) {
        $lang = array_key_first($avail);
    }
    if ($avail[$lang]['path'] === null) {
        $translations = $defaults;
        return;
    }
    if ($lang !== 'en' && isset($avail[$lang]['path']) && file_exists($avail[$lang]['path'])) {
        $loaded = include $avail[$lang]['path'];
        $translations = array_merge($defaults, is_array($loaded) ? $loaded : []);
    } else {
        $translations = $defaults;
    }
}
function __(string $key): string {
    global $translations;
    return $translations[$key] ?? $key;
}
require_once __DIR__ . '/reports.php';
require_once __DIR__ . '/files.php';
function checkSystemHealth(): array {
    $errors = [];
    $base = __DIR__ . '/../ansible';
    if (!function_exists('shell_exec') || in_array('shell_exec', array_map('trim', explode(',', (string)ini_get('disable_functions'))))) {
        $errors[] = __("err_shell_exec");
    }
    if (!extension_loaded('json')) $errors[] = __("err_php_json");
    if (!is_dir($base)) {
        $errors[] = __("err_ansible_dir_not_found");
    } elseif (!is_writable($base)) {
        $errors[] = __("err_ansible_dir_perm");
    } else {
        $rep = "$base/reports";
        if (!is_dir($rep) && !@mkdir($rep, 0700, true)) {
            $errors[] = __("err_reports_mkdir");
        } elseif (is_dir($rep) && !is_writable($rep)) {
            $errors[] = __("err_reports_perm");
        }
        $pbDir = "$base/playbooks";
        if (!is_dir($pbDir) && !@mkdir($pbDir, 0700, true)) {
            $errors[] = __("err_playbooks_mkdir");
        } elseif (is_dir($pbDir) && !is_writable($pbDir)) {
            $errors[] = __("err_playbooks_perm");
        }
        foreach (['hosts.ini', 'ansible.cfg'] as $f) {
            $fp = "$base/$f";
            if (file_exists($fp) && !is_writable($fp)) $errors[] = sprintf(__("err_file_perm"), $f);
        }
    }
    return $errors;
}
function setupErrorLogging(): void {
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../ansible/php_errors.log');
}
function isValidName(string $name): bool {
    return !empty($name) && preg_match('/^[a-zA-Z0-9._-]+$/', $name);
}
function isValidAddress(string $addr): bool {
    return !empty($addr) && (filter_var($addr, FILTER_VALIDATE_IP) !== false || isValidName($addr));
}
function checkCsrf(?string $errorMessage = null): void {
    if ($errorMessage === null) {
        $errorMessage = __("csrf_error");
    }
    if (empty($_POST['csrf']) || !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'])) {
        http_response_code(403);
        $isAjax = !empty($_POST['ajax']);
        if ($isAjax) {
            header('Content-Type: application/json');
            die(json_encode(['success' => false, 'message' => $errorMessage], JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE));
        } else {
            die($errorMessage . ' <a href="">' . __("refresh_page") . '</a>');
        }
    }
}
function checkHostsAvailability(array $hosts): array {
    $valid = [];
    $skipped = [];
    if (empty($hosts)) {
        return ['valid' => [], 'skipped' => []];
    }
    $inv = getInventoryData();
    $hMap = array_column($inv['hosts'], null, 'hostname');
    $gMap = array_column($inv['groups'], null, 'name');
    $defPort = 22;
    if (preg_match('/(?:ansible_|ssh_|remote_)?port\s*=\s*(\d+)/i', getAnsibleConfig(), $m)) {
        $defPort = $m[1];
    }
    $sockets = [];
    $hostDetails = [];
    foreach ($hosts as $h) {
        if (isset($hMap[$h])) {
            $inf = $hMap[$h];
            $addr = ($inf['ip_address'] && $inf['ip_address'] !== '0.0.0.0') ? $inf['ip_address'] : $h;
            $port = $defPort;
            if (isset($gMap[$inf['group_name']]) && preg_match('/(?:ansible_)?(?:ssh_)?port\s*[=:]\s*(\d+)/i', $gMap[$inf['group_name']]['vars'], $m)) {
                $port = $m[1];
            }
            if (preg_match('/(?:ansible_)?(?:ssh_)?port=(\d+)/i', $inf['params'], $m)) {
                $port = $m[1];
            }
            $socket = @stream_socket_client("tcp://$addr:$port", $errno, $errstr, 0, STREAM_CLIENT_ASYNC_CONNECT);
            if ($socket) {
                $sockets[(int)$socket] = $socket;
                $hostDetails[(int)$socket] = ['name' => $h];
            } else {
                $skipped[] = $h;
            }
        } else {
            $skipped[] = $h;
        }
    }
    if (!empty($sockets)) {
        $totalTimeout = 2;
        $startTime = time();
        while (!empty($sockets) && (time() - $startTime) < $totalTimeout) {
            $write = $sockets; $read = null; $except = $sockets;
            if (@stream_select($read, $write, $except, 0, 200000) > 0) {
                foreach ($write as $socket) {
                    $socketId = (int)$socket;
                    if (isset($hostDetails[$socketId])) {
                        if (@stream_socket_get_name($socket, true)) { 
                            $valid[] = $hostDetails[$socketId]['name']; 
                        } else { 
                            $skipped[] = $hostDetails[$socketId]['name']; 
                        }
                        fclose($socket); unset($sockets[$socketId]);
                    }
                }
                foreach ($except as $socket) {
                    $socketId = (int)$socket;
                    if (isset($hostDetails[$socketId])) {
                        $skipped[] = $hostDetails[$socketId]['name'];
                        fclose($socket); unset($sockets[$socketId]);
                    }
                }
            }
        }
    }
    foreach ($sockets as $socket) {
        $socketId = (int)$socket;
        $skipped[] = $hostDetails[$socketId]['name'];
        fclose($socket);
    }
    return ['valid' => $valid, 'skipped' => $skipped];
}
function runPlaybook(string $f, array $hosts = []): array {
    $analysis = analyzePlaybookTarget($f);
    $use_extra_vars = false;
    $hostVar = 'host';
    if ($analysis['type'] === 'error') {
        return ['success' => false, 'message' => $analysis['message']];
    }
    if ($analysis['type'] === 'dynamic') {
        $use_extra_vars = true;
        if (!empty($analysis['variable'])) $hostVar = $analysis['variable'];
        if (empty($hosts)) {
            return ['success' => false, 'message' => __("no_hosts_selected")];
        }
    } else {
        $target = $analysis['target'];
        $inv = getInventoryData();
        $resolvedHosts = [];
        $isGroup = in_array($target, array_column($inv['groups'], 'name'));
        if ($isGroup) {
            $resolvedHosts = array_column(array_filter($inv['hosts'], fn($h) => $h['group_name'] === $target), 'hostname');
        } elseif (in_array($target, array_column($inv['hosts'], 'hostname'))) {
            $resolvedHosts = [$target];
        }
        if (empty($resolvedHosts)) {
            return ['success' => false, 'message' => sprintf(__("target_not_found"), htmlspecialchars($target, ENT_QUOTES, 'UTF-8'))];
        }
        $hosts = $resolvedHosts;
    }
    $repDir = __DIR__ . '/../ansible/reports';
    if (!is_dir($repDir)) mkdir($repDir, 0700, true);
    $availability = checkHostsAvailability($hosts);
    $valid = $availability['valid'];
    $skipped = $availability['skipped'];
    if (!empty($hosts) && empty($valid)) {
        return ['success' => false, 'error' => 'NO_HOSTS_AVAILABLE', 'skipped' => array_unique(array_merge($skipped, $hosts))];
    }
    $id = date('Y-m-d_H-i-s');
    $meta = ['id'=>$id, 'playbook'=>$f, 'start'=>time(), 'skipped'=>$skipped, 'hosts'=>$valid, 'is_read'=>true];
    file_put_contents("$repDir/$id.json", json_encode($meta));
    $conf = getWebAnsConfig();
    $keep = isset($conf['rotate_keep']) ? (int)$conf['rotate_keep'] : 100;
    rotateReports($keep);
    $bin = !empty($conf['ansible_path']) ? $conf['ansible_path'] : 'ansible-playbook';
    $cmd = "cd " . escapeshellarg(__DIR__ . '/../ansible') . " && " . escapeshellarg($bin) . " -i hosts.ini " . escapeshellarg('playbooks/' . $f);
    if ($use_extra_vars && !empty($valid)) {
        $cmd .= " -e " . escapeshellarg($hostVar . "=" . implode(',', $valid));
    } elseif (!empty($valid)) {
        $cmd .= " --limit " . escapeshellarg(implode(',', $valid));
    }
    $log = "$repDir/$id.log"; $exit = "$repDir/$id.exit";
    $full = "export ANSIBLE_STDOUT_CALLBACK=json; $cmd > " . escapeshellarg($log) . " 2>&1; echo $? > " . escapeshellarg($exit);
    shell_exec("nohup sh -c " . escapeshellarg($full) . " > /dev/null 2>&1 < /dev/null &");
    return ['success' => true, 'id' => $id];
}
function getAvailableLanguages(): array {
    static $cache = null;
    if ($cache !== null) return $cache;
    $langs = [];
    $files = glob(__DIR__ . '/../includes/lang/*.php');
    if ($files) {
        foreach ($files as $f) {
            try {
                $data = include $f;
            } catch (Throwable $e) {
                continue;
            }
            if (is_array($data)) {
                $code = basename($f, '.php');
                $label = $data['__meta__']['label'] ?? ucfirst($code);
                $langs[$code] = ['label' => $label, 'path' => $f];
            }
        }
    }
    if (empty($langs)) {
        $langs['en'] = ['label' => 'English', 'path' => null];
    }
    $cache = $langs;
    return $cache;
}
