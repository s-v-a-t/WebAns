<?php
declare(strict_types=1);
function getReports($limit = 100) {
    $d = __DIR__ . '/../ansible/reports'; $r = [];
    $files = glob("$d/*.json");
    rsort($files);
    $files = array_slice($files, 0, $limit);
    foreach ($files as $f) {
        $content = file_get_contents($f);
        if ($content === false) continue;
        $m = json_decode($content, true);
        if (!is_array($m) || !isset($m['id'])) continue;
        $id = $m['id']; $e = "$d/$id.exit";
        $isFinished = file_exists($e);
        $m['status'] = $isFinished ? ((int)trim(file_get_contents($e)) === 0 ? 'success' : 'error') : 'running';
        $m['end'] = $isFinished ? filemtime($e) : null;
        if ($isFinished && ($m['is_read'] ?? false) === true && !isset($m['completion_handled'])) {
            $m['is_read'] = false;
            $m['completion_handled'] = true;
            file_put_contents($f, json_encode($m));
        }
        $r[$id] = $m;
    }
    return $r;
}
function markReportRead(string $id): bool {
    $d = __DIR__ . '/../ansible/reports';
    $file = "$d/" . basename($id) . '.json';
    $exit = "$d/" . basename($id) . '.exit';
    if (!file_exists($file)) return false;
    $content = file_get_contents($file);
    if (!$content) return false;
    $m = json_decode($content, true);
    if (!is_array($m)) return false;
    $changed = false;
    if (($m['is_read'] ?? false) === false) {
        $m['is_read'] = true;
        $changed = true;
    }
    if (file_exists($exit) && !isset($m['completion_handled'])) {
        $m['completion_handled'] = true;
        $changed = true;
    }
    if ($changed) {
        return file_put_contents($file, json_encode($m)) !== false;
    }
    return true;
}
function getReportLog(string $id, int $offset = 0): string {
    $file = __DIR__ . '/../ansible/reports/' . basename($id) . '.log';
    if (!file_exists($file)) return '';
    if ($offset > 0) {
        return file_get_contents($file, false, null, $offset);
    }
    return file_get_contents($file);
}
function parseAnsibleLog($log) {
    $parsed = ['play' => '', 'tasks' => [], 'recap' => []];
    $jsonStart = strpos($log, '{'); 
    $jsonEnd = strrpos($log, '}');
    if ($jsonStart !== false && $jsonEnd !== false) {
        $jsonStr = substr($log, $jsonStart, $jsonEnd - $jsonStart + 1);
        $data = json_decode($jsonStr, true);
        if ($data) {
            if (isset($data['stats'])) {
                foreach ($data['stats'] as $host => $s) {
                    $parsed['recap'][$host] = [
                        'ok' => $s['ok'] ?? 0,
                        'changed' => $s['changed'] ?? 0,
                        'unreachable' => $s['unreachable'] ?? 0,
                        'failed' => $s['failures'] ?? 0,
                        'skipped' => $s['skipped'] ?? 0,
                        'rescued' => $s['rescued'] ?? 0,
                        'ignored' => $s['ignored'] ?? 0
                    ];
                }
            }
            if (isset($data['plays'])) {
                foreach ($data['plays'] as $play) {
                    if (empty($parsed['play'])) {
                        $parsed['play'] = $play['play']['name'] ?? __("unknown_play");
                    }
                    foreach ($play['tasks'] ?? [] as $task) {
                        $taskName = $task['task']['name'] ?? __("unknown_task");
                        $tData = ['name' => $taskName, 'hosts' => []];
                        foreach ($task['hosts'] ?? [] as $host => $res) {
                            $st = 'ok';
                            if (!empty($res['unreachable'])) $st = 'unreachable';
                            elseif (!empty($res['failed'])) $st = 'failed';
                            elseif (!empty($res['skipped'])) $st = 'skipped';
                            elseif (!empty($res['changed'])) $st = 'changed';
                            $msgs = [];
                            if (isset($res['msg'])) {
                                $msgs[] = is_string($res['msg']) ? $res['msg'] : json_encode($res['msg'], JSON_UNESCAPED_UNICODE);
                            }
                            if (!empty($res['stdout'])) $msgs[] = "STDOUT: " . trim($res['stdout']);
                            if (!empty($res['stderr'])) $msgs[] = "STDERR: " . trim($res['stderr']);
                            if (!empty($res['exception'])) $msgs[] = "EXCEPTION: " . trim($res['exception']);
                            if (isset($res['results']) && is_array($res['results'])) {
                                $failedItems = [];
                                $changedCount = 0;
                                foreach ($res['results'] as $item) {
                                    if (!empty($item['failed']) || !empty($item['unreachable'])) {
                                        $iMsg = $item['msg'] ?? ($item['item'] ?? __("item_failed"));
                                        if (is_array($iMsg)) $iMsg = json_encode($iMsg, JSON_UNESCAPED_UNICODE);
                                        $failedItems[] = $iMsg;
                                    }
                                    if (!empty($item['changed'])) $changedCount++;
                                }
                                if (!empty($failedItems)) {
                                    $msgs[] = __("failed_items") . implode("; ", array_slice($failedItems, 0, 3)) . (count($failedItems)>3?"...":"");
                                } else {
                                    $msgs[] = sprintf(__("loop_items"), count($res['results'])) . ($changedCount ? sprintf(__("changed_items"), $changedCount) : "");
                                }
                            }
                            $finalMsg = implode(" | ", $msgs);
                            if ($finalMsg === '') $finalMsg = __("table_ok");
                            $tData['hosts'][] = ['status' => $st, 'host' => $host, 'msg' => $finalMsg];
                        }
                        $parsed['tasks'][] = $tData;
                    }
                }
            }
        }
    }
    return $parsed;
}
function deleteReport(string $id): void {
    $id = basename($id);
    $d = __DIR__ . '/../ansible/reports';
    @unlink("$d/$id.json");
    @unlink("$d/$id.log");
    @unlink("$d/$id.exit");
}
function rotateReports(int $keep = 100): void {
    $d = __DIR__ . '/../ansible/reports';
    $files = glob("$d/*.json");
    if (count($files) <= $keep) return;
    rsort($files);
    $toDelete = array_slice($files, $keep);
    foreach ($toDelete as $f) {
        $id = basename($f, '.json');
        deleteReport($id);
    }
}
function clearReports() { array_map('unlink', glob(__DIR__.'/../ansible/reports/*')); }
