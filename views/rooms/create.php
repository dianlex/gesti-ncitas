<section class="page-header">
    <div>
        <h1>Registrar consultorio</h1>
        <p>Complete los datos del consultorio.</p>
    </div>
    <a class="button secondary" href="<?= e(url('/rooms')) ?>">Volver</a>
</section>

<form class="panel form-grid" method="post" action="<?= e(url('/rooms')) ?>" novalidate>
    <?= csrf_field() ?>

    <div>
        <label for="code">Número</label>
        <input id="code" name="code" maxlength="20" value="<?= e($data['code'] ?? '') ?>" required>
        <?php if (isset($errors['code'])): ?><small class="field-error"><?= e($errors['code']) ?></small><?php endif; ?>
    </div>

    <div>
        <label for="name">Nombre</label>
        <input id="name" name="name" maxlength="100" value="<?= e($data['name'] ?? '') ?>" required>
        <?php if (isset($errors['name'])): ?><small class="field-error"><?= e($errors['name']) ?></small><?php endif; ?>
    </div>

    <div class="full-width">
        <label for="description">Descripción</label>
        <textarea id="description" name="description" maxlength="500" rows="4"><?= e($data['description'] ?? '') ?></textarea>
        <?php if (isset($errors['description'])): ?><small class="field-error"><?= e($errors['description']) ?></small><?php endif; ?>
    </div>

    <div class="full-width">
        <label><input type="checkbox" name="active" value="1" <?= (($data['active'] ?? 1) ? 'checked' : '') ?>> Activo</label>
    </div>

    <div class="form-actions full-width">
        <a class="button secondary" href="<?= e(url('/rooms')) ?>">Cancelar</a>
        <button class="button primary" type="submit">Guardar consultorio</button>
    </div>
</form>