<?php

class User
{
    private $conn;
    private $table = "users";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function findByEmail($email)
    {
        $sql = "
        SELECT *
        FROM $this->table
        WHERE email = ?
        LIMIT 1
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    public function create($name, $username, $email, $password)
    {
        $sql = "
        INSERT INTO $this->table (
            name,
            username,
            email,
            password
        )
        VALUES (
            ?,
            ?,
            ?,
            ?
        )
        ";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $username, $email, $password);
        return $stmt->execute();
    }
}
