<div id="reports-view" style="display:none">
    <div class="header-bar">
        <h1><?= __("reports") ?></h1>
        <div class="header-actions">
            <a href="#" onclick="switchView('main'); return false;" class="btn btn-secondary">🏠 <?= __("main") ?></a>
            <form method="POST" class="delete-form">
                <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
                <button name="clear_reps" value="1" class="btn btn-danger" onclick="return confirm('<?= __("delete_confirm") ?>')"><?= __("clear_all") ?></button>
            </form>
        </div>
    </div>
    <div class="reports-container">
        <div class="reports-list">
            <?php foreach($reps as $id=>$r): ?>
                <a href="#" onclick="loadReport('<?= $id ?>'); return false;" class="report-item report-item-flex <?= empty($r['is_read']) ? 'unread' : '' ?>" id="rep-<?= $id ?>">
                    <div class="report-info">
                        <span class="report-name"><?= htmlspecialchars($r['playbook']) ?></span>
                        <span class="report-date"><?= date('d.m.y H:i',$r['start']) ?></span>
                    </div>
                    <div class="status-<?= $r['status'] ?> report-status-badge"><?= $r['status'] ?></div>
                </a>
            <?php endforeach; ?>
        </div>
        <div class="report-detail" id="report-content"><div class="panel"><?= __("select_report") ?></div></div>
    </div>
</div>