-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for osx10.10 (x86_64)
--
-- Host: 127.0.0.1    Database: todolist
-- ------------------------------------------------------
-- Server version	9.7.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `todos`
--

LOCK TABLES `todos` WRITE;
/*!40000 ALTER TABLE `todos` DISABLE KEYS */;
INSERT INTO `todos` VALUES (1,1,'Test','','pending','medium','2026-05-16 15:00:00','2026-05-12 05:20:25'),(2,1,'Learn API','Study POST request','pending','medium',NULL,'2026-05-13 17:06:01'),(3,2,'Học PHP MVC','Làm bài Todo API','completed','medium',NULL,'2026-05-14 12:15:15'),(4,2,'Learn Authorization','Only owner can create','completed','medium',NULL,'2026-05-14 12:16:27'),(5,2,'Create model Todo.php theo cấu trúc MVC','Tạo model Todo.php, cập nhật và tái cấu trúc lại api todos: đưa SQL vào trong model','completed','medium',NULL,'2026-05-15 02:13:26'),(10,8,'Đi làm','','completed','medium',NULL,'2026-05-16 02:56:48'),(11,2,'Fix UI','','completed','high','2026-05-16 13:00:00','2026-05-16 03:39:00'),(12,2,'Tesk hoạt động gần đây','Test','completed','medium',NULL,'2026-05-16 08:20:45'),(13,8,'Helloooooooo','','pending','medium','2026-05-22 16:00:00','2026-05-16 08:57:45'),(14,2,'Test 2 hoạt động gần đây','','completed','medium',NULL,'2026-05-16 09:34:06'),(15,2,'Đi về','','completed','high','2026-05-16 17:30:00','2026-05-16 10:00:28');
/*!40000 ALTER TABLE `todos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','admin','nt.thien@example.com','123456','admin','2026-05-12 04:49:38',NULL),(2,'Admin','nguyen1412thien','nguyenthanhthien.1412thien@gmail.com','$2y$10$zck6.WZS7tfHjlcTn9TAA.Q9tpgN4yDUgug1jYbEfTc0wcd7N89va','admin','2026-05-14 03:39:43','uploads/avatars/20231231_003009_177.jpg'),(3,'Nguyen Van An','vanan','vanan@gmail.com','123456','user','2026-05-16 02:54:14',NULL),(4,'Nguyễn Thanh Thiên','user02','nguyenthanhthien2.1412thien@gmail.com','$2y$10$63GYJcJckTPcATssIy4LH.FTu6F/3v8RKFmMT6qIIJAqqOdEL4qL.','user','2026-05-14 11:57:23',NULL),(5,'Tran Minh Khoa','minhkhoa','minhkhoa@gmail.com','123456','user','2026-05-16 02:54:14',NULL),(6,'Le Hoang Phuc','hoangphuc','hoangphuc@gmail.com','123456','user','2026-05-16 02:54:14',NULL),(7,'Pham Gia Bao','giabao','giabao@gmail.com','123456','user','2026-05-16 02:54:14',NULL),(8,'Phanh bò','phanhbocutes1tg','phuonganhnguye01082007@gmail.com','$2y$10$Cjmoub7L/zHjgLPWMC1WleXt1jtqz/eTsazEV.xKqdlA7UB4ep3Ky','user','2026-05-16 02:51:00','uploads/avatars/IMG_1184.jpg');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-16 18:52:01
