<?php
if ($viewMode === 'header'):
    if ($meta): ?>
        <div class="panel">
            <h3><?= htmlspecialchars($meta['playbook']) ?> <small>(<?= date('d.m.Y H:i:s', $meta['start']) ?>)</small>
                <form method="POST" class="delete-form">
                    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
                    <button name="del_rep" value="<?= htmlspecialchars($meta['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('<?= __("delete_confirm") ?>')"><?= __("delete") ?></button>
                </form>
            </h3>
            <?php if (!empty($meta['skipped'])): ?>
                <div class="alert alert-error"><strong><?= __("unavailable_hosts") ?></strong> <?= implode(', ', $meta['skipped']) ?></div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="panel"><?= __("report_not_found") ?></div>
    <?php endif;

elseif ($viewMode === 'body'):
    if (!empty($parsed['play']) || !empty($parsed['recap'])):
        if ($parsed['play']): ?>
            <div class="log-play">PLAY: <?= htmlspecialchars($parsed['play']) ?></div>
        <?php endif;
        if ($parsed['recap']): ?>
            <table class="recap-table">
                <thead><tr><th><?= __("table_host") ?></th><th><?= __("table_ok") ?></th><th><?= __("table_changed") ?></th><th><?= __("table_unreachable") ?></th><th><?= __("table_failed") ?></th><th><?= __("table_skipped") ?></th></tr></thead>
                <tbody>
                <?php foreach ($parsed['recap'] as $h => $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($h) ?></td>
                        <?php foreach(['ok'=>'st-ok','changed'=>'st-changed','unreachable'=>'st-error','failed'=>'st-error','skipped'=>'st-skip'] as $k=>$c): ?>
                            <td class="<?= (($s[$k]??0)>0?$c:'') ?>"><?= ($s[$k]??0) ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif;

        $stMap = ['ok'=>'st-ok', 'changed'=>'st-changed', 'unreachable'=>'st-error', 'failed'=>'st-error', 'skipped'=>'st-skip'];
        foreach ($parsed['tasks'] as $t):
            $stats = ['ok'=>0, 'changed'=>0, 'unreachable'=>0, 'failed'=>0, 'skipped'=>0];
            $hasError = false;
            foreach ($t['hosts'] as $h) {
                if (isset($stats[$h['status']])) $stats[$h['status']]++;
                if ($h['status'] === 'failed' || $h['status'] === 'unreachable') $hasError = true;
            }
            $summary = [];
            foreach($stats as $k=>$v) if($v > 0) $summary[] = "$v $k";
            $summaryStr = implode(', ', $summary);
            $borderColor = $hasError ? '#ffcccc' : '#eee';
            $bgColor = $hasError ? '#fff5f5' : '#fff';
            ?>
            <details class="log-task log-task-details" open style="border:1px solid <?= $borderColor ?>;background:<?= $bgColor ?>;">
                <summary class="task-details-summary">TASK: <?= htmlspecialchars($t['name']) ?> <span class="task-summary-text">(<?= $summaryStr ?>)</span></summary>
                <div class="task-details task-details-body">
                    <?php foreach ($t['hosts'] as $h): ?>
                        <div class="task-host <?= ($stMap[$h['status']]??'st-'.$h['status']) ?>">
                            <?= strtoupper($h['status']) ?>: <?= htmlspecialchars($h['host']) ?>
                            <span class="task-msg"><?= htmlspecialchars(substr($h['msg'],0,150)) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </details>
        <?php endforeach;
    endif;
endif;
?>