<?php include 'layout.php'; ?>

<div class="container text-center p-4">
    <h3>Ha habido un error</h3>

    <?php if (isset($params['mensaje'])): ?>
        <b><span style="color: rgba(200, 119, 119, 1);">
            <?= htmlspecialchars($params['mensaje']) ?>
        </span></b>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
