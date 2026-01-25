<div id="settings-view" style="display:none">
<div class="header-bar"><h1><?= __("settings") ?></h1><div class="header-actions"><a href="#" onclick="switchView('main'); return false;" class="btn btn-secondary">🏠 <?= __("main") ?></a></div></div>
<div class="tab">
  <?php foreach(['General'=>__("general"), 'Groups'=>__("groups"),'Hosts'=>__("hosts"),'Playbooks'=>__("playbooks"),'Config'=>__("config"),'Users'=>__("users")] as $k=>$v): ?>
  <button class="tablinks" onclick="openTab(event, '<?= $k ?>')" id="<?= $k==='General'?'defaultOpen':'' ?>"><?= $v ?></button>
  <?php endforeach; ?>
</div>
<div id="General" class="tabcontent"><div class="panel">
    <h2><?= __("general") ?></h2>
    <div class="edit-box" style="max-width: 500px;">
        <form method="POST" onsubmit="saveForm(event, this)" data-reload="true">
            <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
            <input type="hidden" name="save_general" value="1">
            <label><div class="tooltip"><span class="tooltip-icon">?</span><span class="tooltip-text"><?= __("lang_tooltip") ?></span></div> <?= __("language") ?></label>
            <select name="lang" <?= count($availableLangs) <= 1 ? 'disabled' : '' ?>>
                <?php foreach ($availableLangs as $code => $info): ?>
                <option value="<?= htmlspecialchars($code) ?>" <?= $lang==$code?'selected':'' ?>><?= htmlspecialchars($info['label']) ?></option>
                <?php endforeach; ?>
            </select>
            <label><div class="tooltip"><span class="tooltip-icon">?</span><span class="tooltip-text"><?= __("path_tooltip") ?></span></div> <?= __("ansible_path") ?></label>
            <input type="text" name="ansible_path" value="<?= htmlspecialchars($cr['ansible_path'] ?? '') ?>" placeholder="<?= __("path_placeholder") ?>">
            <label><div class="tooltip"><span class="tooltip-icon">?</span><span class="tooltip-text"><?= __("rotate_tooltip") ?></span></div> <?= __("rotate_keep") ?></label>
            <input type="number" name="rotate_keep" value="<?= htmlspecialchars((string)($cr['rotate_keep'] ?? 100)) ?>" min="1">
            <div class="form-btn-row"><button class="btn btn-primary"><?= __("save") ?></button></div>
        </form>
    </div>
</div></div>
<div id="Groups" class="tabcontent"><div class="panel">
    <div id="groups-edit" style="display:none">
        <h2><?= __("editing") ?><span id="edit_group_title"></span></h2>
        <div class="edit-box"><form method="POST" onsubmit="saveForm(event, this)" data-reload="true"><input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
            <input type="hidden" name="update_group_name" value="">
            <p><?= __("vars") ?>:</p><textarea name="group_vars" id="group_vars" class="vars-editor editor-textarea"></textarea>
            <div class="form-btn-row"><button class="btn btn-primary"><?= __("save") ?></button><button type="button" onclick="cancelEditGroup()" class="btn btn-secondary settings-actions"><?= __("cancel") ?></button></div>
        </form></div>
    </div>
    <div id="groups-list">
        <h2><?= __("groups") ?></h2>
        <form method="POST" class="add-form inline-form" onsubmit="saveForm(event, this)" data-reload="true"><input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>"><input type="text" name="group_name" placeholder="<?= __("name") ?>" required><button class="btn btn-primary"><?= __("add") ?></button></form>
        <table><thead><tr><th><?= __("name") ?></th><th><?= __("vars") ?></th><th></th></tr></thead><tbody>
            <?php foreach ($groups as $g): ?><tr>
                <td class="td-top"><strong><?= htmlspecialchars($g['name']) ?></strong></td>
                <td><pre class="vars-pre"><?= htmlspecialchars($g['vars']) ?></pre></td>
                <td><button type="button" onclick="editGroup('<?= htmlspecialchars($g['name']) ?>')" class="btn btn-warning btn-sm"><?= __("edit") ?></button>
                <form method="POST" class="inline-form-action" onsubmit="saveForm(event, this)" data-confirm="<?= __("delete_confirm") ?>" data-reload="true"><input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>"><input type="hidden" name="delete_group_name" value="<?= htmlspecialchars($g['name']) ?>"><button class="btn btn-danger btn-sm">X</button></form></td>
            </tr><?php endforeach; ?>
        </tbody></table>
    </div>
</div></div>
<div id="Hosts" class="tabcontent"><div class="panel">
    <div id="hosts-edit" style="display:none">
        <h2><?= __("editing") ?><span id="edit_host_title"></span></h2>
        <div class="edit-box"><form method="POST" class="add-form" onsubmit="saveForm(event, this)" data-reload="true"><input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
            <input type="hidden" name="update_host_original_name" value="">
            <input type="hidden" name="update_host_group" value="">
            <label><?= __("name") ?>:</label><input type="text" name="hostname" value="" required>
            <label><?= __("ip") ?>:</label><input type="text" name="ip_address" value="" required>
            <label><?= __("group") ?>:</label><select name="new_group_name"><?php foreach ($groups as $g): ?><option value="<?= htmlspecialchars($g['name']) ?>"><?= htmlspecialchars($g['name']) ?></option><?php endforeach; ?></select>
            <label><?= __("params") ?>:</label><input type="text" name="params" value="">
            <div class="form-btn-row"><button class="btn btn-primary"><?= __("save") ?></button><button type="button" onclick="cancelEditHost()" class="btn btn-secondary settings-actions"><?= __("cancel") ?></button></div>
        </form></div>
    </div>
    <div id="hosts-list">
        <h2><?= __("hosts") ?></h2>
        <form method="POST" class="add-form inline-form" onsubmit="saveForm(event, this)" data-reload="true"><input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
            <input type="text" name="hostname" placeholder="<?= __("name") ?>" required><input type="text" name="ip_address" placeholder="<?= __("ip") ?>" required>
            <select name="group_name"><?php foreach ($groups as $g): ?><option value="<?= htmlspecialchars($g['name']) ?>"><?= htmlspecialchars($g['name']) ?></option><?php endforeach; ?></select>
            <button class="btn btn-primary"><?= __("add") ?></button>
        </form>
        <div class="filter-box">
            <span class="filter-label"><?= __("filter") ?></span>
            <select id="hostGroupFilter" onchange="filterHosts()" class="filter-select"><option value=""><?= __("all") ?></option><?php foreach ($groups as $g): ?><option value="<?= htmlspecialchars($g['name']) ?>"><?= htmlspecialchars($g['name']) ?></option><?php endforeach; ?></select>
        </div>
        <table><thead><tr><th><?= __("hosts") ?></th><th><?= __("ip") ?></th><th><?= __("params") ?></th><th><?= __("group") ?></th><th></th></tr></thead><tbody>
            <?php foreach ($hosts as $h): ?><tr data-group="<?= htmlspecialchars($h['group_name']) ?>">
                <td><?= htmlspecialchars($h['hostname']) ?></td><td><?= htmlspecialchars($h['ip_address']) ?></td><td><?= htmlspecialchars($h['params']) ?></td><td><?= htmlspecialchars($h['group_name']) ?></td>
                <td><button type="button" onclick="editHost('<?= htmlspecialchars($h['hostname']) ?>', '<?= htmlspecialchars($h['group_name']) ?>')" class="btn btn-warning btn-sm"><?= __("edit") ?></button>
                <form method="POST" class="inline-form-action" onsubmit="saveForm(event, this)" data-confirm="<?= __("delete_confirm") ?>" data-reload="true"><input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>"><input type="hidden" name="delete_host_name" value="<?= htmlspecialchars($h['hostname']) ?>"><input type="hidden" name="delete_host_group" value="<?= htmlspecialchars($h['group_name']) ?>"><button class="btn btn-danger btn-sm">X</button></form></td>
            </tr><?php endforeach; ?>
        </tbody></table>
    </div>
</div></div>
<div id="Playbooks" class="tabcontent"><div class="panel">
    <h2><?= __("playbooks") ?></h2>
    <form method="POST" class="add-form" onsubmit="saveForm(event, this)" data-reload="true"><input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
        <div class="playbook-add-row">
            <input type="text" name="playbook_filename" placeholder="<?= __("filename") ?>" value="" required class="playbook-name-input">
            <select onchange="loadPlaybook(this.value)" class="playbook-load-select">
                <option value=""><?= __("new_playbook") ?></option>
                <?php foreach ($playbooks as $pb): 
                    $disp = (substr($pb['name'], -4) === '.yml') ? substr($pb['name'], 0, -4) : $pb['name'];
                ?><option value="<?= htmlspecialchars($pb['name']) ?>"><?= htmlspecialchars($disp) ?></option><?php endforeach; ?>
            </select>
        </div>
        <textarea name="playbook_content" id="playbook_content" class="pb-editor" placeholder="YAML..."></textarea>
        <div class="save-row">
            <button class="btn btn-primary"><?= __("save") ?></button>
            <button type="button" id="btn-delete-playbook" class="btn btn-danger" style="display:none;" onclick="deleteCurrentPlaybook()"><?= __("delete") ?></button>
        </div>
    </form>
</div></div>
<div id="Config" class="tabcontent"><div class="panel">
    <h2><?= __("config") ?></h2>
    <form method="POST" onsubmit="saveForm(event, this)"><input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>"><textarea name="ansible_cfg" id="ansible_cfg" class="cfg-editor"><?= htmlspecialchars($config) ?></textarea><br><br><button class="btn btn-primary"><?= __("save") ?></button></form>
</div></div>
<div id="Users" class="tabcontent"><div class="panel">
    <h2><?= __("users") ?></h2>
    <form method="POST" class="add-form inline-form" onsubmit="saveForm(event, this)" data-reload="true"><input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
        <input type="text" name="add_user_login" placeholder="<?= __("login") ?>" required><input type="password" name="add_user_pass" placeholder="<?= __("password") ?>" required>
        <select name="add_user_lang" class="users-add-select" <?= count($availableLangs) <= 1 ? 'disabled' : '' ?>>
            <?php foreach ($availableLangs as $code => $info): ?>
            <option value="<?= htmlspecialchars($code) ?>" <?= $lang==$code?'selected':'' ?>><?= htmlspecialchars($info['label']) ?></option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn-primary"><?= __("create") ?></button>
    </form>
    <table><thead><tr><th><?= __("login") ?></th><th><?= __("language") ?></th><th><?= __("actions") ?></th></tr></thead><tbody>
        <?php $uCount = count($users); foreach ($users as $u): ?><tr>
            <td><?= htmlspecialchars($u['l']) ?></td>
            <td><?= htmlspecialchars($availableLangs[$u['lang'] ?? 'en']['label'] ?? ($u['lang'] ?? 'en')) ?></td>
            <td><div class="users-table-actions">
            <form method="POST" class="users-update-form" onsubmit="saveForm(event, this)" data-reload="true"><input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>"><input type="hidden" name="update_user_login" value="<?= htmlspecialchars($u['l']) ?>"><input type="password" name="update_user_pass" placeholder="<?= __("new_password") ?>" required class="users-mini-input"><button class="btn btn-warning btn-sm"><?= __("update") ?></button></form>
            <form method="POST" class="inline-form-action" onsubmit="saveForm(event, this)" data-confirm="<?= $uCount === 1 ? __("delete_last_user_confirm") : __("delete_user_confirm") ?>" data-reload="true"><input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>"><input type="hidden" name="delete_user_login" value="<?= htmlspecialchars($u['l']) ?>"><button class="btn btn-danger btn-sm">X</button></form>
            </div></td>
        </tr><?php endforeach; ?>
    </tbody></table>
</div></div>
</div>