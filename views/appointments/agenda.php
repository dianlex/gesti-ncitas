<section class="page-header">
    <div>
        <h1>Agenda diaria</h1>
        <?php if ($parsedDate !== null): ?>
            <p>Agenda del <?= e(format_date($date)) ?></p>
        <?php else: ?>
            <p>Consulte las citas correspondientes a una fecha.</p>
        <?php endif; ?>
    </div>
</section>

<form class="search-form" method="get" action="<?= e(url('/agenda')) ?>">
    <label for="agenda-date">Fecha</label>
    <input id="agenda-date" name="date" type="date" value="<?= e($date) ?>" required>
    <button class="button secondary" type="submit">Consultar</button>
</form>

<?php if ($dateError !== null): ?>
    <div class="alert error" role="alert"><?= e($dateError) ?></div>
<?php elseif ($appointments === []): ?>
    <div class="alert" role="status">No hay citas para la fecha seleccionada.</div>
<?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Hora</th>
                    <th>Paciente</th>
                    <th>Médico</th>
                    <th>Consultorio</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $appointment): ?>
                    <tr>
                        <td><?= e(format_time($appointment['appointment_time'])) ?></td>
                        <td><?= e($appointment['patient_first_name'] . ' ' . $appointment['patient_last_name']) ?></td>
                        <td><?= e($appointment['doctor_first_name'] . ' ' . $appointment['doctor_last_name']) ?></td>
                        <td><?= e($appointment['room_code']) ?></td>
                        <td>
                            <span class="status <?= e($appointment['status']) ?>">
                                <?= e(appointment_status_label($appointment['status'])) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>