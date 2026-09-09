<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\View;
use App\Repository\DoctorRepository;
use PDOException;

final class DoctorController
{
    public function __construct(private DoctorRepository $doctors)
    {
    }

    public function index(): void
    {
        Auth::requireLogin();
        
        View::render('doctors/index', [
            'title' => 'Médicos',
            'doctors' => $this->doctors->active(),
        ]);
    }

    public function create(): void
    {
        Auth::requireLogin();
        
        View::render('doctors/create', [
            'title' => 'Registrar médico',
            'data' => [],
            'errors' => [],
        ]);
    }

    public function store(): void
    {
        Auth::requireLogin();
        Csrf::requireValid($_POST['_token'] ?? null);

        $data = [
            'license_number' => trim((string) ($_POST['license_number'] ?? '')),
            'first_name' => trim((string) ($_POST['first_name'] ?? '')),
            'last_name' => trim((string) ($_POST['last_name'] ?? '')),
            'specialty' => trim((string) ($_POST['specialty'] ?? '')),
            'active' => isset($_POST['active']) ? 1 : 0,
        ];

        $errors = $this->validate($data);

        if ($errors !== []) {
            View::render('doctors/create', [
                'title' => 'Registrar médico',
                'data' => $data,
                'errors' => $errors,
            ]);
            return;
        }

        try {
            $this->doctors->create($data);
            flash('success', 'Médico registrado correctamente.');
            redirect('/doctors');
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                $errors['license_number'] = 'Ya existe un médico con ese número de licencia.';
                View::render('doctors/create', [
                    'title' => 'Registrar médico',
                    'data' => $data,
                    'errors' => $errors,
                ]);
                return;
            }
            throw $exception;
        }
    }

    public function edit(string $id): void
    {
        Auth::requireLogin();
        
        $doctor = $this->doctors->findById((int) $id);
        
        if ($doctor === null) {
            http_response_code(404);
            View::render('errors/404', ['title' => 'Médico no encontrado']);
            return;
        }

        View::render('doctors/edit', [
            'title' => 'Editar médico',
            'id' => (int) $id,
            'data' => $doctor,
            'errors' => [],
        ]);
    }

    public function update(string $id): void
    {
        Auth::requireLogin();
        Csrf::requireValid($_POST['_token'] ?? null);

        $doctor = $this->doctors->findById((int) $id);
        
        if ($doctor === null) {
            http_response_code(404);
            View::render('errors/404', ['title' => 'Médico no encontrado']);
            return;
        }

        $data = [
            'id' => (int) $id,
            'license_number' => trim((string) ($_POST['license_number'] ?? '')),
            'first_name' => trim((string) ($_POST['first_name'] ?? '')),
            'last_name' => trim((string) ($_POST['last_name'] ?? '')),
            'specialty' => trim((string) ($_POST['specialty'] ?? '')),
            'active' => isset($_POST['active']) ? 1 : 0,
        ];

        $errors = $this->validate($data);

        if ($errors !== []) {
            View::render('doctors/edit', [
                'title' => 'Editar médico',
                'id' => (int) $id,
                'data' => $data,
                'errors' => $errors,
            ]);
            return;
        }

        try {
            $this->doctors->update($data);
            flash('success', 'Médico actualizado correctamente.');
            redirect('/doctors');
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                $errors['license_number'] = 'Ya existe otro médico con ese número de licencia.';
                View::render('doctors/edit', [
                    'title' => 'Editar médico',
                    'id' => (int) $id,
                    'data' => $data,
                    'errors' => $errors,
                ]);
                return;
            }
            throw $exception;
        }
    }

    private function validate(array $data): array
    {
        $errors = [];

        if (strlen($data['license_number']) < 3 || strlen($data['license_number']) > 40) {
            $errors['license_number'] = 'La licencia debe tener entre 3 y 40 caracteres.';
        }

        if (strlen($data['first_name']) < 2 || strlen($data['first_name']) > 80) {
            $errors['first_name'] = 'El nombre debe tener entre 2 y 80 caracteres.';
        }

        if (strlen($data['last_name']) < 2 || strlen($data['last_name']) > 80) {
            $errors['last_name'] = 'El apellido debe tener entre 2 y 80 caracteres.';
        }

        if (strlen($data['specialty']) < 2 || strlen($data['specialty']) > 100) {
            $errors['specialty'] = 'La especialidad debe tener entre 2 y 100 caracteres.';
        }

        return $errors;
    }
}