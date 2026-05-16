<?php

require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../../vendor/autoload.php";

use Firebase\JWT\JWT;

class AuthController
{
    private $userModel;
    private $secret_key = "thien_jwt_secret_key_for_todolist_project";

    public function __construct($conn)
    {
        $this->userModel = new User($conn);
    }

    public function login(array $data): array
    {
        try {
            if (!isset($data["email"]) || !isset($data["password"])) {
                return [
                    "success" => false,
                    "error" => "Email and password are required",
                    "code" => 400
                ];
            }

            $email = trim($data["email"]);
            $password = trim($data["password"]);

            $user = $this->userModel->findByEmail($email);

            if (!$user) {
                return [
                    "success" => false,
                    "error" => "User not found",
                    "code" => 404
                ];
            }

            if (!password_verify($password, $user["password"])) {
                return [
                    "success" => false,
                    "error" => "Invalid password",
                    "code" => 401
                ];
            }

            $payload = [
                "user_id" => $user["id"],
                "name" => $user["name"],
                "username" => $user["username"],
                "email" => $user["email"],
                "iat" => time(),
                "exp" => time() + 3600
            ];

            $jwt = JWT::encode($payload, $this->secret_key, 'HS256');

            return [
                "success" => true,
                "message" => "Login successful",
                "token" => $jwt,
                "user" => [
                    "id" => $user["id"],
                    "name" => $user["name"],
                    "username" => $user["username"],
                    "email" => $user["email"],
                    "avatar" => $user["avatar"] ?? null,
                    "created_at" => $user["created_at"] ?? null
                ]
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "error" => "Server error",
                "code" => 500
            ];
        }
    }

    public function register(array $data): array
    {
        try {
            if (
                !isset($data["name"]) ||
                !isset($data["username"]) ||
                !isset($data["email"]) ||
                !isset($data["password"])
            ) {
                return [
                    "success" => false,
                    "error" => "Name, username, email, and password are required",
                    "code" => 400
                ];
            }

            $name = trim($data["name"]);
            $username = trim($data["username"]);
            $email = trim($data["email"]);
            $password = trim($data["password"]);

            // Check if email already exists
            $existingUser = $this->userModel->findByEmail($email);
            if ($existingUser) {
                return [
                    "success" => false,
                    "error" => "Email already exists",
                    "code" => 409
                ];
            }

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $created = $this->userModel->create($name, $username, $email, $hashed_password);

            if (!$created) {
                return [
                    "success" => false,
                    "error" => "Failed to register user",
                    "code" => 500
                ];
            }

            return [
                "success" => true,
                "message" => "User registered successfully",
                "code" => 201
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "error" => "Server error",
                "code" => 500
            ];
        }
    }
}
