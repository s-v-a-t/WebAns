<?php
declare(strict_types=1);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(empty($_POST))die(__("error_post_empty"));
    checkCsrf(__("csrf_error_settings"));
    $result=null; $message=__("success"); $handled=false; $force_reload=false; $post=array_map(fn($v) => is_string($v) ? trim($v) : $v, $_POST);
    if(!empty($post['delete_playbook_name'])){ 
        $name = $post['delete_playbook_name'];
        if (substr($name, -4) !== '.yml') $name .= '.yml';
        $result=deletePlaybookFile($name); $message=__("playbook_deleted"); $handled=true; 
    }
    elseif(!empty($post['playbook_filename'])){ 
        $name = $post['playbook_filename'];
        if (substr($name, -4) !== '.yml') $name .= '.yml';
        $result=savePlaybookFile($name,$post['playbook_content']); $message=__("playbook_saved"); $handled=true; 
    }
    elseif(!empty($post['delete_host_name'])){ $result=deleteHostFile($post['delete_host_name'],$post['delete_host_group']); $message=__("host_deleted"); $handled=true; }
    elseif(!empty($post['delete_group_name'])){ $result=deleteGroupFile($post['delete_group_name']); $message=__("group_deleted"); $handled=true; }
    elseif(!empty($post['update_group_name'])){ $result=updateGroupFile($post['update_group_name'],$post['group_vars']); $message=__("group_updated"); $handled=true; }
    elseif(!empty($post['update_host_original_name'])){ $result=updateHostFile($post['update_host_original_name'],$post['update_host_group'],$post['hostname'],$post['ip_address'],$post['params'],$post['new_group_name']??null); $message=__("host_updated"); $handled=true; }
    elseif(!empty($post['hostname'])){ $result=addHostFile($post['hostname'],$post['ip_address'],$post['group_name']); $message=__("host_added"); $handled=true; }
    elseif(!empty($post['group_name'])){ $result=addGroupFile($post['group_name']); $message=__("group_added"); $handled=true; }
    elseif(isset($post['ansible_cfg'])){ $result=saveAnsibleConfig($post['ansible_cfg']); $message=__("config_saved"); $handled=true; }
    elseif(!empty($post['add_user_login']) && !empty($post['add_user_pass'])) {
        $cr = getWebAnsConfig();
        $exists = false;
        foreach(($cr['users']??[]) as $u) if($u['l'] === $post['add_user_login']) $exists = true;
        if($exists) { $result = __("user_exists"); $handled = true; }
        else {
            $cr['users'][] = ['l' => $post['add_user_login'], 'p' => password_hash($post['add_user_pass'], PASSWORD_DEFAULT), 'lang' => $post['add_user_lang'] ?? array_key_first($availableLangs)];
            $result = saveWebAnsConfigData($cr); $message = __("user_added"); $handled = true;
        }
    }
    elseif(!empty($post['delete_user_login'])) {
        $cr = getWebAnsConfig();
        $cr['users'] = array_values(array_filter($cr['users']??[], fn($u) => $u['l'] !== $post['delete_user_login']));
        $result = saveWebAnsConfigData($cr); $message = __("user_deleted"); $handled = true;
        if (empty($cr['users'])) {
            session_destroy();
            $force_reload = true;
        }
    }
    elseif(!empty($post['update_user_login'])) {
        $cr = getWebAnsConfig();
        $updated = false;
        foreach($cr['users'] as &$u) {
            if($u['l'] === $post['update_user_login']) {
                if (!empty($post['update_user_pass'])) {
                    $u['p'] = password_hash($post['update_user_pass'], PASSWORD_DEFAULT);
                    $message = __("password_updated");
                    $updated = true;
                }
            }
        }
        if ($updated) { $result = saveWebAnsConfigData($cr); $handled = true; }
    }
    elseif(isset($post['save_general'])) {
        $cr = getWebAnsConfig();
        if(isset($post['lang']) && isset($_SESSION['user_login'])) {
            foreach($cr['users'] as &$u) {
                if($u['l'] === $_SESSION['user_login']) {
                    $u['lang'] = $post['lang'];
                    break;
                }
            }
        }
        if(isset($post['ansible_path'])) $cr['ansible_path'] = trim($post['ansible_path']);
        if(isset($post['rotate_keep'])) $cr['rotate_keep'] = (int)$post['rotate_keep'];
        $result = saveWebAnsConfigData($cr);
        $message = __("settings_saved");
        $handled = true;
        $force_reload = true;
    }
    if($handled){ 
        if(!empty($post['ajax'])) { 
            header('Content-Type: application/json'); 
            echo json_encode(['success'=>$result===true, 'message'=>$result===true?$message:$result, 'force_reload'=>$force_reload], JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE); 
            exit; 
        }
        $_SESSION['toast_message'] = ['type' => $result===true?'success':'error', 'text' => $result===true?$message:$result]; 
        session_write_close();
        header("Location: ".strtok($_SERVER["REQUEST_URI"],'?')); 
        exit; 
    }
}
