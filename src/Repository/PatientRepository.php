<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class PatientRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function search(string $term = ""): array
    {
        $sql = 'SELECT * FROM patients';
        $parameters = [];

        if ($term !== '') {
            $searchTerm = '%' . $term . '%';
            $sql .= ' WHERE document_number LIKE :term_document
                OR first_name LIKE :term_first_name
                OR last_name LIKE :term_last_name
                OR phone LIKE :term_phone
                OR CONCAT_WS(\' \', first_name, last_name) LIKE :term_full_name';
            $parameters = [
                'term_document' => $searchTerm,
                'term_first_name' => $searchTerm,
                'term_last_name' => $searchTerm,
                'term_phone' => $searchTerm,
                'term_full_name' => $searchTerm,
            ];

            $normalizedTerm = mb_strtolower($term);
            if ($normalizedTerm === 'activo' || $normalizedTerm === 'activa') {
                $sql .= ' OR active = 1';
            } elseif ($normalizedTerm === 'inactivo' || $normalizedTerm === 'inactiva') {
                $sql .= ' OR active = 0';
            }
        }

        $sql .= ' ORDER BY last_name, first_name LIMIT 100';
        $statement = $this->pdo->prepare($sql);
        $statement->execute($parameters);
        return $statement->fetchAll();
    }

    public function findByDocument(string $document): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM patients WHERE document_number = :document LIMIT 1'
        );
        $statement->execute(['document' => $document]);
        $patient = $statement->fetch();
        return $patient === false ? null : $patient;
    }

    public function findActiveByDocument(string $document): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM patients
            WHERE document_number = :document AND active = 1 LIMIT 1'
        );
        $statement->execute(['document' => $document]);
        $patient = $statement->fetch();
        return $patient === false ? null : $patient;
    }

    public function isActive(int $id): bool
    {
        $statement = $this->pdo->prepare(
            'SELECT COUNT(*) FROM patients WHERE id = :id AND active = 1'
        );
        $statement->execute(['id' => $id]);
        return (int) $statement->fetchColumn() === 1;
    }

    public function setActive(int $id, bool $active): bool
    {
        $statement = $this->pdo->prepare(
            'UPDATE patients SET active = :active WHERE id = :id'
        );
        $statement->execute([
            'id' => $id,
            'active' => $active ? 1 : 0,
        ]);
        return $statement->rowCount() === 1;
    }

    public function create(array $data): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO patients
            (document_type, document_number, first_name, last_name, birth_date, sex, phone, email)
            VALUES
            (:document_type, :document_number, :first_name, :last_name, :birth_date, :sex, :phone, :email)'
        );
        $statement->execute($data);
        return (int) $this->pdo->lastInsertId();
    }

    public function count(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM patients')->fetchColumn();
    }
    public function findById(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM patients WHERE id = :id LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $patient = $statement->fetch();
        return $patient === false ? null : $patient;
    }
    public function update(int $id, array $data): bool
    {
        $statement = $this->pdo->prepare(
            'UPDATE patients 
            SET document_type = :document_type,
            document_number = :document_number,
            first_name = :first_name,
            last_name = :last_name,
            birth_date = :birth_date,
            sex = :sex,
            phone = :phone,
            email = :email,
            active = :active
           WHERE id = :id'
        );
        return $statement->execute([
            'id' => $id,
            'document_type' => $data['document_type'],
            'document_number' => $data['document_number'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'birth_date' => $data['birth_date'],
            'sex' => $data['sex'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'active' => $data['active'],
        ]);
    }
}