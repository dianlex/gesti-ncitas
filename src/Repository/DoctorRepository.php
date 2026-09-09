<?php
declare(strict_types=1);
namespace App\Repository;
use PDO;
final class DoctorRepository
{
 public function __construct(private PDO $pdo)
 {
 }
 public function active(): array
 {
 return $this->pdo->query(
 'SELECT id, license_number, first_name, last_name, specialty, active
 FROM doctors
 WHERE active = 1
 ORDER BY first_name, last_name'
 )->fetchAll();
 }
 public function findActive(int $id): ?array
 {
 $statement = $this->pdo->prepare(
 'SELECT id, license_number, first_name, last_name, specialty, active
 FROM doctors WHERE id = :id AND active = 1 LIMIT 1'
 );
 $statement->execute(['id' => $id]);
 $doctor = $statement->fetch();
 return $doctor === false ? null : $doctor;
 }

 public function findById(int $id): ?array
 {
 $statement = $this->pdo->prepare(
 'SELECT id, license_number, first_name, last_name, specialty, active
 FROM doctors WHERE id = :id LIMIT 1'
 );
 $statement->execute(['id' => $id]);
 $doctor = $statement->fetch();
 return $doctor === false ? null : $doctor;
 }

 public function create(array $data): int
 {
 $statement = $this->pdo->prepare(
 'INSERT INTO doctors
 (license_number, first_name, last_name, specialty, active)
 VALUES (:license_number, :first_name, :last_name, :specialty, :active)'
 );
 $statement->execute($data);
 return (int) $this->pdo->lastInsertId();
 }

 public function update(array $data): bool
 {
 $statement = $this->pdo->prepare(
 'UPDATE doctors
 SET license_number = :license_number,
	 first_name = :first_name,
	 last_name = :last_name,
	 specialty = :specialty,
	 active = :active
 WHERE id = :id'
 );
 return $statement->execute($data);
 }
}
