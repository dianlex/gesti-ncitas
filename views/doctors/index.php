<section class="page-header">
    <div>
        <h1>Médicos</h1>
        <p>Listado de médicos del sistema.</p>
    </div>
    <a class="button primary" href="<?= e(url('/doctors/create')) ?>">Registrar médico</a>
</section>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Licencia</th>
                <th>Nombre completo</th>
                <th>Especialidad</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($doctors as $doctor): ?>
                <tr>
                    <td><?= e($doctor['license_number']) ?></td>
                    <td><?= e($doctor['first_name'] . ' ' . $doctor['last_name']) ?></td>
                    <td><?= e($doctor['specialty']) ?></td>
                    <td>
                        <span class="status <?= ((int) ($doctor['active'] ?? 0) === 1) ? 'active' : 'inactive' ?>">
                            <?= ((int) ($doctor['active'] ?? 0) === 1) ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?= e(url('/doctors/' . $doctor['id'] . '/edit')) ?>" class="button secondary">
                            Editar
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if ($doctors === []): ?>
                <tr>
                    <td colspan="5">No hay médicos registrados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>