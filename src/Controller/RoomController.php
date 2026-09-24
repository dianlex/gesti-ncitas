<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\View;
use App\Repository\RoomRepository;
use PDOException;

final class RoomController
{
    public function __construct(private RoomRepository $rooms)
    {
    }

    public function index(): void
    {
        Auth::requireLogin();

        View::render('rooms/index', [
            'title' => 'Consultorios',
            'rooms' => $this->rooms->all(),
        ]);
    }

    public function create(): void
    {
        Auth::requireLogin();

        View::render('rooms/create', [
            'title' => 'Registrar consultorio',
            'data' => [],
            'errors' => [],
        ]);
    }

    public function store(): void
    {
        Auth::requireLogin();
        Csrf::requireValid($_POST['_token'] ?? null);

        $data = $this->formData();
        $errors = $this->validate($data);

        if ($errors !== []) {
            View::render('rooms/create', [
                'title' => 'Registrar consultorio',
                'data' => $data,
                'errors' => $errors,
            ]);
            return;
        }

        try {
            $this->rooms->create($data);
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                $errors['code'] = 'Ya existe un consultorio con ese número.';
                View::render('rooms/create', [
                    'title' => 'Registrar consultorio',
                    'data' => $data,
                    'errors' => $errors,
                ]);
                return;
            }
            throw $exception;
        }

        flash('success', 'Consultorio registrado correctamente.');
        redirect('/rooms');
    }

    public function edit(string $id): void
    {
        Auth::requireLogin();
        $room = $this->rooms->findById((int) $id);

        if ($room === null) {
            http_response_code(404);
            View::render('errors/404', ['title' => 'Consultorio no encontrado']);
            return;
        }

        View::render('rooms/edit', [
            'title' => 'Editar consultorio',
            'id' => (int) $id,
            'data' => $room,
            'errors' => [],
        ]);
    }

    public function update(string $id): void
    {
        Auth::requireLogin();
        Csrf::requireValid($_POST['_token'] ?? null);

        $roomId = (int) $id;
        if ($this->rooms->findById($roomId) === null) {
            http_response_code(404);
            View::render('errors/404', ['title' => 'Consultorio no encontrado']);
            return;
        }

        $data = $this->formData();
        $data['id'] = $roomId;
        $errors = $this->validate($data);

        if ($errors !== []) {
            View::render('rooms/edit', [
                'title' => 'Editar consultorio',
                'id' => $roomId,
                'data' => $data,
                'errors' => $errors,
            ]);
            return;
        }

        try {
            $this->rooms->update($data);
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                $errors['code'] = 'Ya existe otro consultorio con ese número.';
                View::render('rooms/edit', [
                    'title' => 'Editar consultorio',
                    'id' => $roomId,
                    'data' => $data,
                    'errors' => $errors,
                ]);
                return;
            }
            throw $exception;
        }

        flash('success', 'Consultorio actualizado correctamente.');
        redirect('/rooms');
    }

    public function activate(string $id): void
    {
        $this->setActive($id, true);
    }

    public function deactivate(string $id): void
    {
        $this->setActive($id, false);
    }

    private function setActive(string $id, bool $active): void
    {
        Auth::requireLogin();
        Csrf::requireValid($_POST['_token'] ?? null);

        $changed = $this->rooms->setActive((int) $id, $active);
        $message = $active
            ? 'Consultorio activado correctamente.'
            : 'Consultorio inactivado correctamente.';
        $error = $active
            ? 'El consultorio no existe o ya está activo.'
            : 'El consultorio no existe o ya está inactivo.';

        flash($changed ? 'success' : 'error', $changed ? $message : $error);
        redirect('/rooms');
    }

    private function formData(): array
    {
        return [
            'code' => trim((string) ($_POST['code'] ?? '')),
            'name' => trim((string) ($_POST['name'] ?? '')),
            'description' => trim((string) ($_POST['description'] ?? '')) ?: null,
            'active' => isset($_POST['active']) ? 1 : 0,
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];

        if (strlen($data['code']) < 1 || strlen($data['code']) > 20) {
            $errors['code'] = 'El número debe tener entre 1 y 20 caracteres.';
        }

        if (mb_strlen($data['name']) < 2 || mb_strlen($data['name']) > 100) {
            $errors['name'] = 'El nombre debe tener entre 2 y 100 caracteres.';
        }

        if ($data['description'] !== null && mb_strlen($data['description']) > 500) {
            $errors['description'] = 'La descripción no puede superar 500 caracteres.';
        }

        return $errors;
    }
}