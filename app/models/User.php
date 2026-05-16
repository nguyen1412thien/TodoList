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

    public function findById($id)
    {
        $stmt = mysqli_prepare($this->conn, "SELECT * FROM users WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    }

    public function updateSecurityInfo($id, $field, $value)
    {
        $allowed_fields = ['email', 'phone', 'password'];
        if (!in_array($field, $allowed_fields)) return false;

        $stmt = mysqli_prepare($this->conn, "UPDATE users SET $field = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $value, $id);
        return mysqli_stmt_execute($stmt);
    }

    public function updateStatus($id, $status)
    {
        $stmt = mysqli_prepare($this->conn, "UPDATE users SET status = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $status, $id);
        return mysqli_stmt_execute($stmt);
    }

    public function findByIdentifier($identifier)
    {
        $sql = "
        SELECT *
        FROM $this->table
        WHERE email = ? OR username = ?
        LIMIT 1
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $identifier, $identifier);
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
