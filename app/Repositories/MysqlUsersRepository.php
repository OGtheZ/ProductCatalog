<?php

namespace App\Repositories;

use App\ConfigGetter;
use App\Models\User;
use PDO;
use PDOException;
use Symfony\Component\Translation\Exception\ProviderException;

class MysqlUsersRepository implements UsersRepository
{
    private PDO $connection;

    public function __construct()
    {
        $config = ConfigGetter::getConfig();
        $dsn = "mysql:host={$config["host"]};dbname={$config["db"]};charset=UTF8";
        try {
            $this->connection = new PDO($dsn, $config["user"], $config["password"]);
        } catch(PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function save(User $user): void
    {
            $sql = "INSERT INTO users (id, email, username, password, created_at) VALUES (?, ?, ?, ?, ?)";
            $statement = $this->connection->prepare($sql);
            $statement->execute([
                $user->getId(),
                $user->getEmail(),
                $user->getUsername(),
                $user->getPassword(),
                $user->getCreatedAt(),
            ]);
    }

    public function getOne(string $email): ?User
    {
        $sql = "SELECT * FROM users WHERE email = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([
            $email
        ]);
        $data = $statement->fetch(PDO::FETCH_ASSOC);
        if($data === false) {
            return null;
        } else {
            $user = new User($data['id'],
                $data['email'],
                $data['username'],
                $data['password'],
                $data['created_at'],
            );
        }
        return $user;
    }
}