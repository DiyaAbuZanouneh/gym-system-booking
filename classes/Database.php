<?php
// One shared PDO connection (singleton). Change the credentials below if needed.
class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $this->pdo = new PDO('mysql:host=localhost;dbname=gym_db;charset=utf8mb4', 'root', '', [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetch(string $sql, array $params = []): ?array
    {
        return $this->query($sql, $params)->fetch() ?: null;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function value(string $sql, array $params = [])
    {
        return $this->query($sql, $params)->fetchColumn();
    }

    public function lastId(): int       { return (int)$this->pdo->lastInsertId(); }
    public function begin(): void       { $this->pdo->beginTransaction(); }
    public function commit(): void      { $this->pdo->commit(); }
    public function rollBack(): void    { $this->pdo->rollBack(); }
}
