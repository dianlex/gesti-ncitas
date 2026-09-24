<section class="page-header">
    <div>
        <h1>Consultorios</h1>
        <p>Administre los consultorios disponibles para las citas.</p>
    </div>
    <a class="button primary" href="<?= e(url('/rooms/create')) ?>">Registrar consultorio</a>
</section>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Número</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rooms as $room): ?>
                <?php $isActive = (int) $room['active'] === 1; ?>
                <tr>
                    <td><?= e($room['code']) ?></td>
                    <td><?= e($room['name']) ?></td>
                    <td><?= e($room['description'] ?: 'Sin descripción') ?></td>
                    <td>
                        <span class="status <?= $isActive ? 'active' : 'inactive' ?>">
                            <?= $isActive ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </td>
                    <td>
                        <div class="actions">
                            <a class="button secondary" href="<?= e(url('/rooms/' . $room['id'] . '/edit')) ?>">
                                Editar
                            </a>
                            <form method="post" action="<?= e(url('/rooms/' . $room['id'] . '/' . ($isActive ? 'deactivate' : 'activate'))) ?>">
                                <?= csrf_field() ?>
                                <button class="button <?= $isActive ? 'danger' : 'success' ?>" type="submit">
                                    <?= $isActive ? 'Inactivar' : 'Activar' ?>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if ($rooms === []): ?>
                <tr><td colspan="5">No hay consultorios registrados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>