<div class="app-container auth-container">
    <div class="panel">
        <h2 class="auth-title"><?= ($set ? __("create_admin") : __("login_title")) ?></h2>
        <?php if ($set): ?>
            <?php if (!empty($writeError)): ?>
                <p class="auth-subtitle" style="color: #dc3545;">
                    <?= __("error_write_perm") ?>
                </p>
            <?php else: ?>
                <p class="auth-subtitle">
                    <?= __("config_not_found") ?>
                </p>
            <?php endif; ?>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
            <input type="hidden" name="save_lang" value="<?= htmlspecialchars($lang) ?>">
            <input type="hidden" name="act" value="<?= ($set ? 'set' : 'in') ?>">
            <label for="login"><?= __("login") ?></label>
            <input type="text" id="login" name="l" required>
            <label for="password"><?= __("password") ?></label>
            <input type="password" id="password" name="p" required>
            <button type="submit" class="btn btn-primary auth-btn" <?= (!empty($writeError) && $set) ? 'disabled' : '' ?>>
                <?= ($set ? __("create") : __("enter")) ?>
            </button>
        </form>
    </div>
</div>