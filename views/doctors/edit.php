<?php $doctor = $data ?? []; ?>

<section class="page-header">
    <div>
        <h1>Editar médico</h1>
        <p>Actualice los datos del médico.</p>
    </div>
    <a class="button secondary" href="<?= e(url('/doctors')) ?>">Volver</a>
</section>

<form class="panel form-grid" method="post" action="<?= e(url('/doctors/' . (int) ($id ?? 0) . '/edit')) ?>" novalidate>
    <?= csrf_field() ?>

    <div>
        <label for="license_number">Número de licencia</label>
        <input id="license_number" name="license_number" maxlength="40" 
               value="<?= e($doctor['license_number'] ?? '') ?>" required>
        <?php if (isset($errors['license_number'])): ?>
            <small class="field-error"><?= e($errors['license_number']) ?></small>
        <?php endif; ?>
    </div>

    <div>
        <label for="first_name">Nombres</label>
        <input id="first_name" name="first_name" maxlength="80" 
               value="<?= e($doctor['first_name'] ?? '') ?>" required>
        <?php if (isset($errors['first_name'])): ?>
            <small class="field-error"><?= e($errors['first_name']) ?></small>
        <?php endif; ?>
    </div>

    <div>
        <label for="last_name">Apellidos</label>
        <input id="last_name" name="last_name" maxlength="80" 
               value="<?= e($doctor['last_name'] ?? '') ?>" required>
        <?php if (isset($errors['last_name'])): ?>
            <small class="field-error"><?= e($errors['last_name']) ?></small>
        <?php endif; ?>
    </div>

    <div>
        <label for="specialty">Especialidad</label>
        <select id="specialty" name="specialty" required>
            <option value="">Seleccione una especialidad</option>
            <?php foreach ([
                'Odontología general',
                'Ortodoncia',
                'Endodoncia',
                'Cirugía oral',
                'Periodoncia',
            ] as $specialty): ?>
                <option value="<?= e($specialty) ?>"
                    <?= ($doctor['specialty'] ?? '') === $specialty ? 'selected' : '' ?>>
                    <?= e($specialty) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['specialty'])): ?>
            <small class="field-error"><?= e($errors['specialty']) ?></small>
        <?php endif; ?>
    </div>

    <div class="full-width">
        <label>
            <input type="checkbox" name="active" value="1" <?= ($doctor['active'] ?? 0) ? 'checked' : '' ?>>
            Activo
        </label>
    </div>

    <div class="form-actions full-width">
        <a class="button secondary" href="<?= e(url('/doctors')) ?>">Cancelar</a>
        <button class="button primary" type="submit">Actualizar médico</button>
    </div>
</form>