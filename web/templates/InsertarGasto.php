<?php ob_start(); ?>

<div class="container text-center py-2">
    <div class="col-md-12">
        <?php if (isset($params['mensaje'])) : ?>
            <b><span style="color: rgba(200, 119, 119, 1);"><?php echo $params['mensaje'] ?></span></b>
        <?php endif; ?>
    </div>
</div>

<div class="col-md-12">
    <?php if (isset($errores)) : ?>
        <?php foreach ($errores as $error) { ?>
            <b><span style="color: rgba(200, 119, 119, 1);"><?php echo $error . "<br>"; ?></span></b>
        <?php } ?>
    <?php endif; ?>
</div>

<div class="container-fluid text-center">
    <div class="container">
        <?php
        // Genera un token 
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        ?>
        <form action="index.php?ctl=insertarGasto" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <!-- Campo para el concepto del gasto -->
            <p>* <input type="text" name="concepto" placeholder="Concepto del gasto" required><br></p>
            
            <!-- Campo para el monto del gasto -->
            <p>* <input type="number" step="0.01" name="monto" placeholder="Monto" required><br></p>
            
            <!-- Campo para la fecha del gasto -->
            <p>* <input type="date" name="fecha" required><br></p>
            
            <input type="submit" name="bInsertarGasto" value="Aceptar"><br>
        </form>
    </div>
</div>

<?php $contenido = ob_get_clean() ?>

<?php include 'layout.php' ?>
