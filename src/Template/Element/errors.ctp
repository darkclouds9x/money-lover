<?php if (isset($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?>
        <?php echo $error ?> <br>
        <?php endforeach ?>
    </div>
<?php endif; ?>


