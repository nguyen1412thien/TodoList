USE `todolist`;

-- Seed Users (Mật khẩu mặc định cho cả hai tài khoản là: 123456)
INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `role`) VALUES
(1, 'Thien Nguyen', 'thien', 'nt.thien@example.com', '$2y$10$ujLIqgFYaFxrwh5oFJKAmOTgvrFh.Dt2oP4vVu2JPPuowwpBF7Mnu', 'admin'),
(2, 'Test User', 'testuser', 'test@example.com', '$2y$10$ujLIqgFYaFxrwh5oFJKAmOTgvrFh.Dt2oP4vVu2JPPuowwpBF7Mnu', 'admin');

-- Seed Todos
INSERT INTO `todos` (`id`, `user_id`, `title`, `description`, `status`, `priority`, `due_date`) VALUES
(1, 1, 'Learn Advanced PHP', 'Study REST API', 'completed', 'high', '2026-05-20 18:00:00'),
(2, 1, 'Learn MySQL', 'Study database schema and queries', 'pending', 'medium', NULL),
(3, 2, 'Do exercises', 'Complete math homework', 'pending', 'medium', NULL);