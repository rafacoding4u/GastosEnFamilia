<?php if (isset($_SESSION['error_mensaje'])): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($_SESSION['error_mensaje']) ?>
    </div>
    <?php unset($_SESSION['error_mensaje']); // Limpiar el mensaje después de mostrarlo ?>
<?php endif; ?>
