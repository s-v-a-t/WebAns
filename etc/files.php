<?php
declare(strict_types=1);
function ensureAnsibleDir(): void {
    $ansibleDir = __DIR__ . '/../ansible';
    $playbooksDir = $ansibleDir . '/playbooks';
    $defaultDir = __DIR__ . '/../includes/lib/init';
    if (!is_dir($ansibleDir)) {
        if (is_dir($defaultDir) && function_exists('shell_exec') && !in_array('shell_exec', array_map('trim', explode(',', (string)ini_get('disable_functions'))))) {
            shell_exec("cp -r " . escapeshellarg($defaultDir) . " " . escapeshellarg($ansibleDir));
            shell_exec("chmod -R 700 " . escapeshellarg($ansibleDir));
        } else {
            @mkdir($ansibleDir, 0700, true);
        }
    }
    if (!is_dir($playbooksDir)) {
        @mkdir($playbooksDir, 0700, true);
    }
    foreach (glob("$ansibleDir/*.yml") as $file) {
        $base = basename($file);
        @rename($file, "$playbooksDir/$base");
    }
    $files = [
        '.htaccess' => "Deny from all",
        'hosts.ini' => "",
        'ansible.cfg' => "[defaults]\nhost_key_checking = False\nretry_files_enabled = False\n"
    ];
    foreach ($files as $file => $content) {
        $path = "$ansibleDir/$file";
        if (!file_exists($path)) {
            @file_put_contents($path, $content);
        }
    }
}
function getPlaybooksFromFiles() {
    $files = glob(__DIR__ . '/../ansible/playbooks/*.yml');
    if ($files === false) return [];
    $validFiles = array_filter($files, fn($file) => isValidName(basename($file)));
    return array_values(array_map(fn($file) => [
        'name' => basename($file),
        'file_path' => basename($file)
    ], $validFiles));
}
function getPlaybookContent($filename) {
    $path = __DIR__ . '/../ansible/playbooks/' . basename($filename);
    return file_exists($path) ? file_get_contents($path) : '';
}
function writeFile($path, $content) {
    $dir = dirname($path);
    if (!is_dir($dir) && !@mkdir($dir, 0700, true)) {
        return __("error_mkdir");
    }
    if (@file_put_contents($path, $content, LOCK_EX) === false) {
        return __("error_write");
    }
    @chmod($path, 0700);
    return true;
}
function savePlaybookFile($filename, $content) {
    if (!isValidName($filename)) {
        return __("invalid_filename");
    }
    return writeFile(__DIR__ . '/../ansible/playbooks/' . basename($filename), $content);
}
function deletePlaybookFile($filename) {
    $path = __DIR__ . '/../ansible/playbooks/' . basename($filename);
    return file_exists($path) && !@unlink($path) ? __("error_delete") : true;
}
function getInventoryData() {
    static $cache = null;
    if ($cache !== null) return $cache;
    if (!file_exists($file = __DIR__ . '/../ansible/hosts.ini')) {
        return ['groups' => [], 'hosts' => []];
    }
    $groups = [];
    $hosts = [];
    $currentGroup = null;
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return ['groups' => [], 'hosts' => []];
    }
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || $line[0] === ';') {
            continue;
        }
        if (preg_match('/^\[(.+)\]$/', $line, $matches)) {
            $section = $matches[1];
            $isVars = str_ends_with($section, ':vars');
            $groupName = $isVars ? substr($section, 0, -5) : $section;
            if (!isset($groups[$groupName])) {
                $groups[$groupName] = ['name' => $groupName, 'vars' => ''];
            }
            $currentGroup = ['name' => $groupName, 'type' => $isVars ? 'vars' : 'hosts'];
        } elseif ($currentGroup) {
            if ($currentGroup['type'] === 'vars') {
                $groups[$currentGroup['name']]['vars'] .= "$line\n";
            } else {
                preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\'(?:\\\\.|[^\\\\\'])*\'|[^\s]+/', $line, $tokens);
                $parts = $tokens[0] ?? [];
                if (empty($parts)) {
                    continue;
                }
                $hostname = array_shift($parts);
                $ip = '';
                $params = [];
                foreach ($parts as $val) {
                    if (str_starts_with($val, 'ansible_host=')) {
                        $ip = substr($val, 13);
                    } else {
                        $params[] = $val;
                    }
                }
                $hosts[] = [
                    'hostname' => $hostname,
                    'ip_address' => $ip ?: '0.0.0.0',
                    'params' => implode(' ', $params),
                    'group_name' => $currentGroup['name']
                ];
            }
        }
    }
    foreach ($groups as &$group) {
        $group['vars'] = trim($group['vars']);
    }
    $cache = ['groups' => array_values($groups), 'hosts' => $hosts];
    return $cache;
}
function saveInventoryData($groups, $hosts) {
    $content = "";
    $hostsByGroup = [];
    foreach ($hosts as $host) {
        $hostsByGroup[$host['group_name']][] = $host;
    }
    foreach ($groups as $group) {
        $content .= "[{$group['name']}]\n";
        foreach ($hostsByGroup[$group['name']] ?? [] as $host) {
            $line = $host['hostname'];
            if ($host['ip_address'] && $host['ip_address'] !== '0.0.0.0') {
                $line .= " ansible_host={$host['ip_address']}";
            }
            if ($host['params']) {
                $line .= " {$host['params']}";
            }
            $content .= "$line\n";
        }
        if ($group['vars']) {
            $content .= "[{$group['name']}:vars]\n{$group['vars']}\n";
        }
        $content .= "\n";
    }
    return writeFile(__DIR__ . '/../ansible/hosts.ini', $content);
}
function addGroupFile($name) {
    if (!isValidName($name)) {
        return __("invalid_group_name");
    }
    $data = getInventoryData();
    foreach ($data['groups'] as $group) {
        if ($group['name'] === $name) {
            return __("group_exists");
        }
    }
    $data['groups'][] = ['name' => $name, 'vars' => ''];
    return saveInventoryData($data['groups'], $data['hosts']);
}
function deleteGroupFile($name) {
    $data = getInventoryData();
    $data['groups'] = array_filter($data['groups'], fn($g) => $g['name'] !== $name);
    $data['hosts'] = array_filter($data['hosts'], fn($h) => $h['group_name'] !== $name);
    return saveInventoryData($data['groups'], $data['hosts']);
}
function updateGroupFile($name, $vars) {
    $data = getInventoryData();
    foreach ($data['groups'] as &$group) {
        if ($group['name'] === $name) {
            $group['vars'] = $vars;
        }
    }
    return saveInventoryData($data['groups'], $data['hosts']);
}
function addHostFile($hostname, $ip, $group, $params = '') {
    if (!isValidName($hostname)) return __("invalid_host_name");
    if (!isValidAddress($ip)) return __("invalid_ip");
    if (preg_match('/[\r\n]/', $params)) return __("invalid_params");
    $data = getInventoryData();
    $data['hosts'][] = [
        'hostname' => $hostname,
        'ip_address' => $ip,
        'params' => $params,
        'group_name' => $group
    ];
    return saveInventoryData($data['groups'], $data['hosts']);
}
function deleteHostFile($hostname, $group) {
    $data = getInventoryData();
    $data['hosts'] = array_filter($data['hosts'], fn($x) => !($x['hostname'] === $hostname && $x['group_name'] === $group));
    return saveInventoryData($data['groups'], $data['hosts']);
}
function updateHostFile($oldHostname, $oldGroup, $newHostname, $newIp, $newParams, $newGroup = null) {
    if (!isValidName($newHostname)) return __("invalid_host_name");
    if (!isValidAddress($newIp)) return __("invalid_ip");
    if (preg_match('/[\r\n]/', $newParams)) return __("invalid_params");
    $data = getInventoryData();
    foreach ($data['hosts'] as &$host) {
        if ($host['hostname'] === $oldHostname && $host['group_name'] === $oldGroup) {
            $host['hostname'] = $newHostname;
            $host['ip_address'] = $newIp;
            $host['params'] = $newParams;
            if ($newGroup) {
                $host['group_name'] = $newGroup;
            }
            break;
        }
    }
    return saveInventoryData($data['groups'], $data['hosts']);
}
function getAnsibleConfig() {
    static $cache = null;
    if ($cache !== null) return $cache;
    $path = __DIR__ . '/../ansible/ansible.cfg';
    if (file_exists($path)) {
        $content = file_get_contents($path);
        $cache = $content !== false ? $content : '';
    } else {
        $cache = "[defaults]\nforks = 30\ninventory = ./hosts.ini\nremote_user = root\nask_pass = False\nbecome = True\nprivate_key_file = ~/.ssh/id_rsa\nlog_path = ./ansible.log\nssh_port = 22\nresult_format=yaml\ndeprecation_warnings=False\nignore_unreachable=yes\nhost_key_checking = False\n";
    }
    return $cache;
}
function saveAnsibleConfig($content) {
    return writeFile(__DIR__ . '/../ansible/ansible.cfg', $content);
}
function getWebAnsConfig() {
    static $cache = null;
    if ($cache !== null) return $cache;
    $path = __DIR__ . '/../ansible/webans.cfg';
    $cache = file_exists($path) ? json_decode(file_get_contents($path), true) : [];
    return $cache;
}
function saveWebAnsConfigData($data) {
    return writeFile(__DIR__ . '/../ansible/webans.cfg', json_encode($data));
}
function analyzePlaybookContent(string $content): array {
    if (!preg_match('/(?:^\s*-\s*hosts|^\s*hosts):\s*([^#\r\n]+)/m', $content, $matches)) {
        return ['type' => 'error', 'message' => __("playbook_target_error")];
    }
    $target = trim($matches[1]);
    if (empty($target)) {
        return ['type' => 'error', 'message' => __("playbook_empty_target")];
    }
    if ((str_starts_with($target, '"') && str_ends_with($target, '"')) || (str_starts_with($target, "'") && str_ends_with($target, "'"))) {
        $target = substr($target, 1, -1);
    }
    if (preg_match('/^\{\{\s*([a-zA-Z0-9_-]+)\s*\}\}$/', $target, $m)) {
        return ['type' => 'dynamic', 'variable' => $m[1]];
    }
    if ($target === 'all') {
         return ['type' => 'error', 'message' => __("playbook_all_disabled")];
    }
    return ['type' => 'static', 'target' => $target];
}
function analyzePlaybookTarget(string $playbookFilename): array {
    $content = getPlaybookContent($playbookFilename);
    if (!$content) {
        return ['type' => 'error', 'message' => __("playbook_not_found")];
    }
    return analyzePlaybookContent($content);
}
