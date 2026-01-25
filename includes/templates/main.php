<div id="main-view">
    <div class="header-bar">
        <h1><?= __("title") ?></h1>
        <div class="header-actions">
            <a href="#" onclick="switchView('reports'); return false;" class="btn btn-secondary">📄 <?= __("reports") ?></a>
            <a href="#" onclick="switchView('settings'); return false;" class="btn btn-primary">⚙️ <?= __("settings") ?></a>
            <form method="POST" class="delete-form">
                <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
                <input type="hidden" name="act" value="out">
                <button class="btn btn-danger"><?= __("logout") ?></button>
            </form>
        </div>
    </div>
    <div class="panel">
        <form method="POST">
            <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
            <div class="playbook-select-row">
                <h3 class="playbook-label"><?= __("select_playbook") ?></h3>
                <select name="playbook" class="playbook-select">
                    <?php foreach ($playbooks as $pb): 
                        $disp = (substr($pb['name'], -4) === '.yml') ? substr($pb['name'], 0, -4) : $pb['name'];
                    ?>
                        <option value="<?= htmlspecialchars($pb['file_path']) ?>"><?= htmlspecialchars($disp) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <h3><?= __("select_hosts") ?></h3>
            <div class="hosts-container">
                <?php foreach ($hostsByGroup as $gName => $gHosts): ?>
                    <details class="host-group">
                        <summary><?= htmlspecialchars($gName) ?> <span class="host-count host-count-label">(0/<?= count($gHosts) ?>)</span></summary>
                        <div class="group-content">
                            <label class="select-all-label"><input type="checkbox" onchange="toggleGroup(this)"> <?= __("select_all") ?></label>
                            <?php foreach ($gHosts as $h): ?>
                                <label class="host-label">
                                    <input type="checkbox" name="hosts[]" value="<?= htmlspecialchars($h['hostname']) ?>" onchange="updateGroupCount(this)">
                                    <span><?= htmlspecialchars($h['hostname']) ?> <small class="host-ip">(<?= htmlspecialchars($h['ip_address']) ?>)</small></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </details>
                <?php endforeach; ?>
            </div>
            <button type="submit" class="btn btn-success run-btn"><?= __("run") ?></button>
        </form>
    </div>
</div>