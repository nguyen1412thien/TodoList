<?php

require_once __DIR__ . "/../models/Todo.php";

class TodoController
{
    private $todoModel;
    private $current_user;

    public function __construct($conn, $current_user = null)
    {
        $this->todoModel = new Todo($conn);
        $this->current_user = $current_user;
    }

    /*
    |--------------------------------------------------------------------------
    | Get All Todos By User
    |--------------------------------------------------------------------------
    */

    public function index(int $user_id): array
    {
        try {

            $todos = $this->todoModel->getAllByUser($user_id);

            return [
                "success" => true,
                "message" => "Todos fetched successfully",
                "data" => $todos
            ];

        } catch (Exception $e) {

            return [
                "success" => false,
                "error" => "Failed to fetch todos",
                "code" => 500
            ];

        }
    }

    /*
    |--------------------------------------------------------------------------
    | Create Todo
    |--------------------------------------------------------------------------
    */

    public function store(int $user_id, array $data): array
    {
        try {

            if (
                !isset($data["title"]) ||
                empty(trim($data["title"]))
            ) {

                return [
                    "success" => false,
                    "error" => "Title is required",
                    "code" => 400
                ];

            }

            $title = trim($data["title"]);

            $description =
                $data["description"] ?? "";

            $status =
                $data["status"] ?? "pending";
                
            $priority =
                $data["priority"] ?? "medium";
                
            $due_date =
                $data["due_date"] ?? null;

            $created = $this->todoModel->create(
                $user_id,
                $title,
                $description,
                $status,
                $priority,
                $due_date
            );

            if (!$created) {

                return [
                    "success" => false,
                    "error" => "Failed to create todo",
                    "code" => 500
                ];

            }

            return [
                "success" => true,
                "message" => "Todo created successfully"
            ];

        } catch (Exception $e) {

            return [
                "success" => false,
                "error" => "Server error",
                "code" => 500
            ];

        }
    }

    /*
    |--------------------------------------------------------------------------
    | Update Todo
    |--------------------------------------------------------------------------
    */

    public function update(int $user_id, array $data): array
    {
        try {

            if (
                !isset($data["id"]) ||
                empty($data["id"])
            ) {

                return [
                    "success" => false,
                    "error" => "Todo ID is required",
                    "code" => 400
                ];

            }

            if (
                !isset($data["title"]) ||
                empty(trim($data["title"]))
            ) {

                return [
                    "success" => false,
                    "error" => "Title is required",
                    "code" => 400
                ];

            }

            $id = (int) $data["id"];

            $todo = $this->todoModel->findById($id);

            if (!$todo) {

                return [
                    "success" => false,
                    "error" => "Todo not found",
                    "code" => 404
                ];

            }

            if ((int)$todo["user_id"] !== $user_id) {

                return [
                    "success" => false,
                    "error" => "Forbidden",
                    "code" => 403
                ];

            }

            $title = trim($data["title"]);

            $description =
                $data["description"] ?? "";

            $status =
                $data["status"] ?? "pending";

            $priority =
                $data["priority"] ?? "medium";

            $updated = $this->todoModel->update(
                $id,
                $title,
                $description,
                $status,
                $priority
            );

            if (!$updated) {

                return [
                    "success" => false,
                    "error" => "Failed to update todo",
                    "code" => 500
                ];

            }

            return [
                "success" => true,
                "message" => "Todo updated successfully"
            ];

        } catch (Exception $e) {

            return [
                "success" => false,
                "error" => "Server error",
                "code" => 500
            ];

        }
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Todo
    |--------------------------------------------------------------------------
    */

    public function destroy(int $user_id, int $id): array
    {
        try {

            if (empty($id)) {

                return [
                    "success" => false,
                    "error" => "Todo ID is required",
                    "code" => 400
                ];

            }

            $todo = $this->todoModel->findById($id);

            if (!$todo) {

                return [
                    "success" => false,
                    "error" => "Todo not found",
                    "code" => 404
                ];

            }

            if ((int)$todo["user_id"] !== $user_id) {

                return [
                    "success" => false,
                    "error" => "Forbidden",
                    "code" => 403
                ];

            }

            $deleted = $this->todoModel->delete($id);

            if (!$deleted) {

                return [
                    "success" => false,
                    "error" => "Failed to delete todo",
                    "code" => 500
                ];

            }

            return [
                "success" => true,
                "message" => "Todo deleted successfully"
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