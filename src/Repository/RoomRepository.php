<?php
declare(strict_types=1);
namespace App\Repository;
use PDO;
final class RoomRepository
{
 public function __construct(private PDO $pdo)
 {
 }
 public function active(): array
 {
 return $this->pdo->query(
 'SELECT id, code, name FROM rooms WHERE active = 1 ORDER BY code'
 )->fetchAll();
 }
 public function findActive(int $id): ?array
 {
 $statement = $this->pdo->prepare(
 'SELECT id, code, name FROM rooms WHERE id = :id AND active = 1 LIMIT 1'
 );
 $statement->execute(['id' => $id]);
 $room = $statement->fetch();
 return $room === false ? null : $room;
 }

 public function all(): array
 {
 return $this->pdo->query(
 'SELECT id, code, name, description, active FROM rooms ORDER BY code'
 )->fetchAll();
 }

 public function findById(int $id): ?array
 {
 $statement = $this->pdo->prepare(
 'SELECT id, code, name, description, active FROM rooms WHERE id = :id LIMIT 1'
 );
 $statement->execute(['id' => $id]);
 $room = $statement->fetch();
 return $room === false ? null : $room;
 }

 public function create(array $data): int
 {
 $statement = $this->pdo->prepare(
 'INSERT INTO rooms (code, name, description, active)
  VALUES (:code, :name, :description, :active)'
 );
 $statement->execute($data);
 return (int) $this->pdo->lastInsertId();
 }

 public function update(array $data): bool
 {
 $statement = $this->pdo->prepare(
 'UPDATE rooms
  SET code = :code, name = :name, description = :description, active = :active
  WHERE id = :id'
 );
 return $statement->execute($data);
 }

 public function setActive(int $id, bool $active): bool
 {
 $statement = $this->pdo->prepare(
 'UPDATE rooms SET active = :active WHERE id = :id'
 );
 $statement->execute([
 'id' => $id,
 'active' => $active ? 1 : 0,
 ]);
 return $statement->rowCount() === 1;
 }
}