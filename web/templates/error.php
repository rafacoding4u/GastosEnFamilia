<?php ob_start(); ?>
<div class="container text-center p-4">
    <h3>Ha habido un error</h3>
    <?php if (isset($params['mensaje'])): ?>
        <p><b><span style="color: rgba(200, 119, 119, 1);">
            <?= htmlspecialchars($params['mensaje']) ?>
        </span></b></p>
    <?php endif; ?>
</div>
<?php $contenido = ob_get_clean(); ?>
<?php include 'layout.php'; ?>
