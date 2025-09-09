/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.5.27-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: p_s25_01_db
-- ------------------------------------------------------
-- Server version	10.5.27-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'admin01','newpass1'),(2,'admin02','testreset'),(3,'a','a'),(4,'b','b');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assignments`
--

DROP TABLE IF EXISTS `assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assignments` (
  `assignmentID` int(11) NOT NULL AUTO_INCREMENT,
  `assignmentName` varchar(255) NOT NULL,
  `dueDate` date NOT NULL,
  `assignmentDescription` text NOT NULL,
  `assignmentPoints` int(11) NOT NULL,
  PRIMARY KEY (`assignmentID`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assignments`
--

LOCK TABLES `assignments` WRITE;
/*!40000 ALTER TABLE `assignments` DISABLE KEYS */;
INSERT INTO `assignments` VALUES (1,'test','0002-02-02','test',22),(2,'2','6666-04-05','3',6),(3,'3','0333-03-31','3',333),(4,'4','0444-04-04','4',444),(44,'TestFall2024','2025-12-01','Working Assignment Creation with Delete Button :)',100),(46,'testing1','5555-05-05','testesting1',56);
/*!40000 ALTER TABLE `assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assignmentsexp`
--

DROP TABLE IF EXISTS `assignmentsexp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assignmentsexp` (
  `assignmentID` int(11) NOT NULL AUTO_INCREMENT,
  `assignmentexpName` varchar(255) DEFAULT NULL,
  `expDueDate` date DEFAULT NULL,
  `assignmentexpDescription` varchar(255) DEFAULT NULL,
  `assignmentexpPoints` int(11) DEFAULT NULL,
  PRIMARY KEY (`assignmentID`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assignmentsexp`
--

LOCK TABLES `assignmentsexp` WRITE;
/*!40000 ALTER TABLE `assignmentsexp` DISABLE KEYS */;
INSERT INTO `assignmentsexp` VALUES (1,'Test Assignment-Experiments','2024-11-29','Testing-Double Submission?',10),(2,'TESTTTTTING','2024-11-29','test again',10),(3,'SUB TEST','2024-11-29','Testing',100),(4,'TestFall2024','2024-11-29','test again',10),(5,'COMP TEST','2024-11-30','TEST',10),(13,'TestFall2024','2025-11-01','Working Assignment Creation with Delete Button :) :0',100),(25,'COMPARE','2026-12-30','COMPARE',100);
/*!40000 ALTER TABLE `assignmentsexp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comparisons`
--

DROP TABLE IF EXISTS `comparisons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comparisons` (
  `mechanism_id` int(11) NOT NULL,
  `client_code` varchar(255) NOT NULL,
  `algorithm` varchar(255) NOT NULL,
  `component_id` int(11) NOT NULL,
  PRIMARY KEY (`mechanism_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comparisons`
--

LOCK TABLES `comparisons` WRITE;
/*!40000 ALTER TABLE `comparisons` DISABLE KEYS */;
INSERT INTO `comparisons` VALUES (1,'001','CPU Scheduling',1),(2,'002','Memory Allocation',2),(3,'003','Page Replacement',3),(4,'004','File Allocation',4),(5,'005','Disk Scheduling',5);
/*!40000 ALTER TABLE `comparisons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `components`
--

DROP TABLE IF EXISTS `components`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `components` (
  `component_id` int(11) NOT NULL AUTO_INCREMENT,
  `component_name` varchar(255) NOT NULL,
  PRIMARY KEY (`component_id`),
  UNIQUE KEY `component_name` (`component_name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `components`
--

LOCK TABLES `components` WRITE;
/*!40000 ALTER TABLE `components` DISABLE KEYS */;
INSERT INTO `components` VALUES (1,'CPU Scheduling'),(5,'Disk Scheduling'),(4,'File Allocation'),(2,'Memory Allocation'),(3,'Page Replacement');
/*!40000 ALTER TABLE `components` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `experiments`
--

DROP TABLE IF EXISTS `experiments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `experiments` (
  `experiment_id` int(11) NOT NULL AUTO_INCREMENT,
  `family_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `input_path` varchar(255) NOT NULL,
  `output_path` varchar(255) NOT NULL,
  `code_path` varchar(255) NOT NULL,
  `restrict_view` tinyint(1) NOT NULL,
  PRIMARY KEY (`experiment_id`),
  KEY `mechanism_id` (`family_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `experiments_ibfk_1` FOREIGN KEY (`family_id`) REFERENCES `mechanisms` (`mechanism_id`),
  CONSTRAINT `experiments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`UserID`)
) ENGINE=InnoDB AUTO_INCREMENT=525 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `experiments`
--

LOCK TABLES `experiments` WRITE;
/*!40000 ALTER TABLE `experiments` DISABLE KEYS */;
INSERT INTO `experiments` VALUES (400,3,2,'experiments/2_400_3/','experiments/2_400_3/','/var/www/p/s25-01/html/cgi-bin/core-e/m-003',1),(401,2,2,'experiments/2_401_2/','experiments/2_401_2/','/var/www/p/s25-01/html/cgi-bin/core-e/m-002',1),(402,2,2,'experiments/2_402_2/','experiments/2_402_2/','/var/www/p/s25-01/html/cgi-bin/core-e/m-002',1),(403,4,2,'experiments/2_403_4/','experiments/2_403_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(404,4,2,'experiments/2_404_4/','experiments/2_404_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(405,5,2,'experiments/2_405_5/','experiments/2_405_5/','/var/www/p/s25-01/html/cgi-bin/core-e/m-005',1),(406,1,2,'experiments/2_406_1/','experiments/2_406_1/','/var/www/p/s25-01/html/cgi-bin/core-e/m-001',1),(407,2,2,'experiments/2_407_2/','experiments/2_407_2/','/var/www/p/s25-01/html/cgi-bin/core-e/m-002',1),(408,1,2,'experiments/2_408_1/','experiments/2_408_1/','/var/www/p/s25-01/html/cgi-bin/core-e/m-001',1),(409,5,2,'experiments/2_409_5/','experiments/2_409_5/','/var/www/p/s25-01/html/cgi-bin/core-e/m-005',1),(410,1,2,'experiments/2_410_1/','experiments/2_410_1/','/var/www/p/s25-01/html/cgi-bin/core-e/m-001',1),(411,1,2,'experiments/2_411_1/','experiments/2_411_1/','/var/www/p/s25-01/html/cgi-bin/core-e/m-001',1),(412,5,2,'experiments/2_412_5/','experiments/2_412_5/','/var/www/p/s25-01/html/cgi-bin/core-e/m-005',1),(413,4,2,'experiments/2_413_4/','experiments/2_413_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(414,5,2,'experiments/2_414_5/','experiments/2_414_5/','/var/www/p/s25-01/html/cgi-bin/core-e/m-005',1),(415,4,2,'experiments/2_415_4/','experiments/2_415_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(416,1,2,'experiments/2_416_1/','experiments/2_416_1/','/var/www/p/s25-01/html/cgi-bin/core-e/m-001',1),(417,4,2,'experiments/2_417_4/','experiments/2_417_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(418,5,2,'experiments/2_418_5/','experiments/2_418_5/','/var/www/p/s25-01/html/cgi-bin/core-e/m-005',1),(419,5,2,'experiments/2_419_5/','experiments/2_419_5/','/var/www/p/s25-01/html/cgi-bin/core-e/m-005',1),(420,4,2,'experiments/2_420_4/','experiments/2_420_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(421,4,2,'experiments/2_421_4/','experiments/2_421_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(422,5,2,'experiments/2_422_5/','experiments/2_422_5/','/var/www/p/s25-01/html/cgi-bin/core-e/m-005',1),(423,5,2,'experiments/2_423_5/','experiments/2_423_5/','/var/www/p/s25-01/html/cgi-bin/core-e/m-005',1),(424,5,2,'experiments/2_424_5/','experiments/2_424_5/','/var/www/p/s25-01/html/cgi-bin/core-e/m-005',1),(425,5,2,'experiments/2_425_5/','experiments/2_425_5/','/var/www/p/s25-01/html/cgi-bin/core-e/m-005',1),(426,5,2,'experiments/2_426_5/','experiments/2_426_5/','/var/www/p/s25-01/html/cgi-bin/core-e/m-005',1),(427,5,2,'experiments/2_427_5/','experiments/2_427_5/','/var/www/p/s25-01/html/cgi-bin/core-e/m-005',1),(428,2,2,'experiments/2_428_2/','experiments/2_428_2/','/var/www/p/s25-01/html/cgi-bin/core-e/m-002',1),(429,1,2,'experiments/2_429_1/','experiments/2_429_1/','/var/www/p/s25-01/html/cgi-bin/core-e/m-001',1),(430,2,2,'experiments/2_430_2/','experiments/2_430_2/','/var/www/p/s25-01/html/cgi-bin/core-e/m-002',1),(431,2,2,'experiments/2_431_2/','experiments/2_431_2/','/var/www/p/s25-01/html/cgi-bin/core-e/m-002',1),(432,1,2,'experiments/2_432_1/','experiments/2_432_1/','/var/www/p/s25-01/html/cgi-bin/core-e/m-001',1),(433,2,2,'experiments/2_433_2/','experiments/2_433_2/','/var/www/p/s25-01/html/cgi-bin/core-e/m-002',1),(434,2,2,'experiments/2_434_2/','experiments/2_434_2/','/var/www/p/s25-01/html/cgi-bin/core-e/m-002',1),(435,1,2,'experiments/2_435_1/','experiments/2_435_1/','/var/www/p/s25-01/html/cgi-bin/core-e/m-001',1),(436,2,2,'experiments/2_436_2/','experiments/2_436_2/','/var/www/p/s25-01/html/cgi-bin/core-e/m-002',1),(437,2,2,'experiments/2_437_2/','experiments/2_437_2/','/var/www/p/s25-01/html/cgi-bin/core-e/m-002',1),(438,1,2,'experiments/2_438_1/','experiments/2_438_1/','/var/www/p/s25-01/html/cgi-bin/core-e/m-001',1),(439,4,2,'experiments/2_439_4/','experiments/2_439_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(440,1,2,'experiments/2_440_1/','experiments/2_440_1/','/var/www/p/s25-01/html/cgi-bin/core-e/m-001',1),(441,2,2,'experiments/2_441_2/','experiments/2_441_2/','/var/www/p/s25-01/html/cgi-bin/core-e/m-002',1),(442,1,2,'experiments/2_442_1/','experiments/2_442_1/','/var/www/p/s25-01/html/cgi-bin/core-e/m-001',1),(443,1,2,'experiments/2_443_1/','experiments/2_443_1/','/var/www/p/s25-01/html/cgi-bin/core-e/m-001',1),(444,1,2,'experiments/2_444_1/','experiments/2_444_1/','/var/www/p/s25-01/html/cgi-bin/core-e/m-001',1),(445,2,2,'experiments/2_445_2/','experiments/2_445_2/','/var/www/p/s25-01/html/cgi-bin/core-e/m-002',1),(446,2,2,'experiments/2_446_2/','experiments/2_446_2/','/var/www/p/s25-01/html/cgi-bin/core-e/m-002',1),(447,2,2,'experiments/2_447_2/','experiments/2_447_2/','/var/www/p/s25-01/html/cgi-bin/core-e/m-002',1),(448,4,2,'experiments/2_448_4/','experiments/2_448_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(449,2,2,'experiments/2_449_2/','experiments/2_449_2/','/var/www/p/s25-01/html/cgi-bin/core-e/m-002',1),(450,2,2,'experiments/2_450_2/','experiments/2_450_2/','/var/www/p/s25-01/html/cgi-bin/core-e/m-002',1),(451,5,2,'experiments/2_451_5/','experiments/2_451_5/','/var/www/p/s25-01/html/cgi-bin/core-e/m-005',1),(452,3,2,'experiments/2_452_3/','experiments/2_452_3/','/var/www/p/s25-01/html/cgi-bin/core-e/m-003',1),(453,3,2,'experiments/2_453_3/','experiments/2_453_3/','/var/www/p/s25-01/html/cgi-bin/core-e/m-003',1),(454,4,2,'experiments/2_454_4/','experiments/2_454_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(455,4,2,'experiments/2_455_4/','experiments/2_455_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(456,1,2,'experiments/2_456_1/','experiments/2_456_1/','/var/www/p/s25-01/html/cgi-bin/core-e/m-001',1),(457,4,2,'experiments/2_457_4/','experiments/2_457_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(458,2,2,'experiments/2_458_2/','experiments/2_458_2/','/var/www/p/s25-01/html/cgi-bin/core-e/m-002',1),(459,1,2,'experiments/2_459_1/','experiments/2_459_1/','/var/www/p/s25-01/html/cgi-bin/core-e/m-001',1),(460,1,2,'experiments/2_460_1/','experiments/2_460_1/','/var/www/p/s25-01/html/cgi-bin/core-e/m-001',1),(461,3,2,'experiments/2_461_3/','experiments/2_461_3/','/var/www/p/s25-01/html/cgi-bin/core-e/m-003',1),(462,3,2,'experiments/2_462_3/','experiments/2_462_3/','/var/www/p/s25-01/html/cgi-bin/core-e/m-003',1),(463,3,2,'experiments/2_463_3/','experiments/2_463_3/','/var/www/p/s25-01/html/cgi-bin/core-e/m-003',1),(464,2,2,'experiments/2_464_2/','experiments/2_464_2/','/var/www/p/s25-01/html/cgi-bin/core-e/m-002',1),(465,2,2,'experiments/2_465_2/','experiments/2_465_2/','/var/www/p/s25-01/html/cgi-bin/core-e/m-002',1),(466,1,2,'experiments/2_466_1/','experiments/2_466_1/','/var/www/p/s25-01/html/cgi-bin/core-e/m-001',1),(467,1,2,'experiments/2_467_1/','experiments/2_467_1/','/var/www/p/s25-01/html/cgi-bin/core-e/m-001',1),(468,1,2,'experiments/2_468_1/','experiments/2_468_1/','/var/www/p/s25-01/html/cgi-bin/core-e/m-001',1),(469,4,2,'experiments/2_469_4/','experiments/2_469_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(470,1,2,'experiments/2_470_1/','experiments/2_470_1/','/var/www/p/s25-01/html/cgi-bin/core-e/m-001',1),(471,3,2,'experiments/2_471_3/','experiments/2_471_3/','/var/www/p/s25-01/html/cgi-bin/core-e/m-003',1),(472,4,2,'experiments/2_472_4/','experiments/2_472_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(473,4,2,'experiments/2_473_4/','experiments/2_473_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(474,4,2,'experiments/2_474_4/','experiments/2_474_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(475,1,2,'experiments/2_475_1/','experiments/2_475_1/','/var/www/p/s25-01/html/cgi-bin/core-e/m-001',1),(476,1,2,'experiments/2_476_1/','experiments/2_476_1/','/var/www/p/s25-01/html/cgi-bin/core-e/m-001',1),(477,5,2,'experiments/2_477_5/','experiments/2_477_5/','/var/www/p/s25-01/html/cgi-bin/core-e/m-005',1),(478,3,2,'experiments/2_478_3/','experiments/2_478_3/','/var/www/p/s25-01/html/cgi-bin/core-e/m-003',1),(479,2,2,'experiments/2_479_2/','experiments/2_479_2/','/var/www/p/s25-01/html/cgi-bin/core-e/m-002',1),(480,5,2,'experiments/2_480_5/','experiments/2_480_5/','/var/www/p/s25-01/html/cgi-bin/core-e/m-005',1),(481,3,2,'experiments/2_481_3/','experiments/2_481_3/','/var/www/p/s25-01/html/cgi-bin/core-e/m-003',1),(482,4,2,'experiments/2_482_4/','experiments/2_482_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(483,5,2,'experiments/2_483_5/','experiments/2_483_5/','/var/www/p/s25-01/html/cgi-bin/core-e/m-005',1),(484,4,2,'experiments/2_484_4/','experiments/2_484_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(485,4,2,'experiments/2_485_4/','experiments/2_485_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(486,5,2,'experiments/2_486_5/','experiments/2_486_5/','/var/www/p/s25-01/html/cgi-bin/core-e/m-005',1),(487,1,2,'experiments/2_487_1/','experiments/2_487_1/','/var/www/p/s25-01/html/cgi-bin/core-e/m-001',1),(488,4,2,'experiments/2_488_4/','experiments/2_488_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(489,4,2,'experiments/2_489_4/','experiments/2_489_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(490,3,2,'experiments/2_490_3/','experiments/2_490_3/','/var/www/p/s25-01/html/cgi-bin/core-e/m-003',1),(491,4,2,'experiments/2_491_4/','experiments/2_491_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(492,3,2,'experiments/2_492_3/','experiments/2_492_3/','/var/www/p/s25-01/html/cgi-bin/core-e/m-003',1),(493,5,2,'experiments/2_493_5/','experiments/2_493_5/','/var/www/p/s25-01/html/cgi-bin/core-e/m-005',1),(494,1,2,'experiments/2_494_1/','experiments/2_494_1/','/var/www/p/s25-01/html/cgi-bin/core-e/m-001',1),(495,4,2,'experiments/2_495_4/','experiments/2_495_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(496,4,2,'experiments/2_496_4/','experiments/2_496_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(497,4,2,'experiments/2_497_4/','experiments/2_497_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(498,4,2,'experiments/2_498_4/','experiments/2_498_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(499,5,2,'experiments/2_499_5/','experiments/2_499_5/','/var/www/p/s25-01/html/cgi-bin/core-e/m-005',1),(500,4,2,'experiments/2_500_4/','experiments/2_500_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(501,4,2,'experiments/2_501_4/','experiments/2_501_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(502,4,2,'experiments/2_502_4/','experiments/2_502_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(503,4,2,'experiments/2_503_4/','experiments/2_503_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(504,4,2,'experiments/2_504_4/','experiments/2_504_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(505,1,2,'experiments/2_505_1/','experiments/2_505_1/','/var/www/p/s25-01/html/cgi-bin/core-e/m-001',1),(506,4,2,'experiments/2_506_4/','experiments/2_506_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(507,4,2,'experiments/2_507_4/','experiments/2_507_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(508,4,2,'experiments/2_508_4/','experiments/2_508_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(509,4,2,'experiments/2_509_4/','experiments/2_509_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(510,5,2,'experiments/2_510_5/','experiments/2_510_5/','/var/www/p/s25-01/html/cgi-bin/core-e/m-005',1),(511,4,2,'experiments/2_511_4/','experiments/2_511_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(512,3,2,'experiments/2_512_3/','experiments/2_512_3/','/var/www/p/s25-01/html/cgi-bin/core-e/m-003',1),(513,4,2,'experiments/2_513_4/','experiments/2_513_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(514,1,2,'experiments/2_514_1/','experiments/2_514_1/','/var/www/p/s25-01/html/cgi-bin/core-e/m-001',1),(515,4,2,'experiments/2_515_4/','experiments/2_515_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(516,2,2,'experiments/2_516_2/','experiments/2_516_2/','/var/www/p/s25-01/html/cgi-bin/core-e/m-002',1),(517,1,2,'experiments/2_517_1/','experiments/2_517_1/','/var/www/p/s25-01/html/cgi-bin/core-e/m-001',1),(518,5,2,'experiments/2_518_5/','experiments/2_518_5/','/var/www/p/s25-01/html/cgi-bin/core-e/m-005',1),(519,4,2,'experiments/2_519_4/','experiments/2_519_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(520,4,2,'experiments/2_520_4/','experiments/2_520_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(521,4,2,'experiments/2_521_4/','experiments/2_521_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1),(522,2,2,'experiments/2_522_2/','experiments/2_522_2/','/var/www/p/s25-01/html/cgi-bin/core-e/m-002',1),(523,1,2,'experiments/2_523_1/','experiments/2_523_1/','/var/www/p/s25-01/html/cgi-bin/core-e/m-001',1),(524,4,2,'experiments/2_524_4/','experiments/2_524_4/','/var/www/p/s25-01/html/cgi-bin/core-e/m-004',1);
/*!40000 ALTER TABLE `experiments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups` (
  `GroupID` int(50) NOT NULL AUTO_INCREMENT,
  `GroupName` varchar(255) NOT NULL,
  `Manager` int(50) NOT NULL,
  PRIMARY KEY (`GroupID`),
  UNIQUE KEY `GroupName` (`GroupName`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groups`
--

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
INSERT INTO `groups` VALUES (7,'Fall2021OS',37),(13,'Spring21OS',44),(28,'Fall2024OS',1),(29,'TEST',45),(34,'B',76),(37,'Spring25OS',2);
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mechanisms`
--

DROP TABLE IF EXISTS `mechanisms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mechanisms` (
  `mechanism_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_code` varchar(255) NOT NULL,
  `algorithm` varchar(255) NOT NULL,
  `component_id` int(11) NOT NULL,
  PRIMARY KEY (`mechanism_id`),
  UNIQUE KEY `client_code` (`client_code`),
  KEY `component_id` (`component_id`),
  CONSTRAINT `mechanisms_ibfk_1` FOREIGN KEY (`component_id`) REFERENCES `components` (`component_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mechanisms`
--

LOCK TABLES `mechanisms` WRITE;
/*!40000 ALTER TABLE `mechanisms` DISABLE KEYS */;
INSERT INTO `mechanisms` VALUES (1,'001','Non-Preemptive First Come First Serve',1),(2,'002','Non-Preemptive Shortest Job First',1),(3,'003','Non-Preemptive Priority High',1),(4,'004','Non-Preemptive Priority Low',1),(5,'005','Round Robin',1),(6,'006','Preemptive Shortest Job First',1),(7,'007','Preemptive Priority High',1),(8,'008','Preemptive Priority Low',1),(9,'011','First Fit',2),(10,'012','Best Fit',2),(11,'013','Worst Fit',2),(12,'021','First In First Out',3),(13,'023','Least Recently Used',3),(14,'022','Optimal',3),(15,'024','Least Frequently Used',3),(16,'025','Most Frequently Used',3),(17,'031','Contiguous',4),(18,'032','Linked',4),(19,'033','Indexed',4),(20,'041','First Come First Serve',5),(21,'042','Shortest Seek Time First',5),(22,'043','Circular Scan',5),(23,'044','Look',5),(24,'045','Circular Look',5);
/*!40000 ALTER TABLE `mechanisms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mechanisms_v3`
--

DROP TABLE IF EXISTS `mechanisms_v3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mechanisms_v3` (
  `mechanism_id` int(11) NOT NULL AUTO_INCREMENT,
  `mechanism_code` varchar(255) NOT NULL,
  `algorithm` varchar(255) NOT NULL,
  `component_id` int(11) NOT NULL,
  PRIMARY KEY (`mechanism_id`),
  UNIQUE KEY `mechanisim_code` (`mechanism_code`),
  KEY `component_id` (`component_id`),
  CONSTRAINT `mechanisms_v3_ibfk_1` FOREIGN KEY (`component_id`) REFERENCES `components` (`component_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mechanisms_v3`
--

LOCK TABLES `mechanisms_v3` WRITE;
/*!40000 ALTER TABLE `mechanisms_v3` DISABLE KEYS */;
INSERT INTO `mechanisms_v3` VALUES (1,'001','Non-Preemptive First Come First Serve',1),(2,'002','Non-Preemptive Shortest Job First',1),(3,'003','Non-Preemptive Priority High',1),(4,'004','Non-Preemptive Priority Low',1),(5,'005','Round Robin',1),(6,'006','Preemptive Shortest Job First',1),(7,'007','Preemptive Priority High',1),(8,'008','Preemptive Priority Low',1),(9,'011','First Fit',2),(10,'012','Best Fit',2),(11,'013','Worst Fit',2),(12,'021','First In First Out',3),(13,'023','Least Recently Used',3),(14,'022','Optimal',3),(15,'024','Least Frequently Used',3),(16,'025','Most Frequently Used',3),(17,'031','Contiguous',4),(18,'032','Linked',4),(19,'033','Indexed',4),(20,'041','First Come First Serve',5),(21,'042','Shortest Seek Time First',5),(22,'043','Circular Scan',5),(23,'044','Look',5),(24,'045','Circular Look',5),(25,'026','Most Recently Used',3);
/*!40000 ALTER TABLE `mechanisms_v3` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modes`
--

DROP TABLE IF EXISTS `modes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `modes` (
  `ModeID` int(50) NOT NULL AUTO_INCREMENT,
  `Title` varchar(255) NOT NULL,
  `Description` varchar(255) NOT NULL,
  `Permissions` text NOT NULL,
  PRIMARY KEY (`ModeID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modes`
--

LOCK TABLES `modes` WRITE;
/*!40000 ALTER TABLE `modes` DISABLE KEYS */;
INSERT INTO `modes` VALUES (1,'Basic','Can only view','R'),(2,'Learn','Learning','R'),(3,'Research','Research/code/other stuff','R/W'),(4,'Manage','For groups','R/W/E'),(5,'Admin','Admin','R/W/E/ALL');
/*!40000 ALTER TABLE `modes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `p_users`
--

DROP TABLE IF EXISTS `p_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `p_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `p_users`
--

LOCK TABLES `p_users` WRITE;
/*!40000 ALTER TABLE `p_users` DISABLE KEYS */;
INSERT INTO `p_users` VALUES (1,'j','j'),(2,'test','dan'),(3,'a','a');
/*!40000 ALTER TABLE `p_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `submissions`
--

DROP TABLE IF EXISTS `submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `submissions` (
  `submission_id` int(11) NOT NULL AUTO_INCREMENT,
  `mechanism_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `input_path` varchar(255) NOT NULL,
  `output_path` varchar(255) NOT NULL,
  `code_path` varchar(255) NOT NULL,
  `restrict_view` tinyint(1) NOT NULL,
  PRIMARY KEY (`submission_id`),
  KEY `mechanism_id` (`mechanism_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `submissions_ibfk_1` FOREIGN KEY (`mechanism_id`) REFERENCES `mechanisms` (`mechanism_id`),
  CONSTRAINT `submissions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`UserID`)
) ENGINE=InnoDB AUTO_INCREMENT=336 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `submissions`
--

LOCK TABLES `submissions` WRITE;
/*!40000 ALTER TABLE `submissions` DISABLE KEYS */;
INSERT INTO `submissions` VALUES (249,2,2,'/var/www/projects/f24-02/html/files/core-s/m-002/in-002.dat','/var/www/projects/f24-02/html/files/core-s/m-002/out-002.dat','/var/www/projects/f24-02/html/cgi-bin/core-s/m-002',1),(253,4,3,'/var/www/projects/f24-02/html/files/core-s/m-004/in-004.dat','/var/www/projects/f24-02/html/files/core-s/m-004/out-004.dat','/var/www/projects/f24-02/html/cgi-bin/core-s/m-004',1),(255,5,3,'/var/www/projects/f24-02/html/files/core-s/m-005/in-005.dat','/var/www/projects/f24-02/html/files/core-s/m-005/out-005.dat','/var/www/projects/f24-02/html/cgi-bin/core-s/m-005',1),(271,6,1,'/var/www/projects/f24-02/html/files/core-s/m-006/in-006.dat','/var/www/projects/f24-02/html/files/core-s/m-006/out-006.dat','/var/www/projects/f24-02/html/cgi-bin/core-s/m-006',1),(279,1,1,'/var/www/projects/f24-02/html/files/core-s/m-001/in-001.dat','/var/www/projects/f24-02/html/files/core-s/m-001/out-001.dat','/var/www/projects/f24-02/html/cgi-bin/core-s/m-001',1),(294,1,2,'/var/www/projects/f24-02/html/files/core-s/m-001/in-001.dat','/var/www/projects/f24-02/html/files/core-s/m-001/out-001.dat','/var/www/projects/f24-02/html/cgi-bin/core-s/m-001',1),(315,2,1,'/var/www/projects/f24-02/html/files/core-s/m-002/in-002.dat','/var/www/projects/f24-02/html/files/core-s/m-002/out-002.dat','/var/www/projects/f24-02/html/cgi-bin/core-s/m-002',1),(316,3,1,'/var/www/projects/f24-02/html/files/core-s/m-003/in-003.dat','/var/www/projects/f24-02/html/files/core-s/m-003/out-003.dat','/var/www/projects/f24-02/html/cgi-bin/core-s/m-003',1),(317,3,1,'/var/www/projects/f24-02/html/files/core-s/m-003/in-003.dat','/var/www/projects/f24-02/html/files/core-s/m-003/out-003.dat','/var/www/projects/f24-02/html/cgi-bin/core-s/m-003',1),(319,9,1,'/var/www/projects/f24-02/html/files/core-s/m-011/in-011.dat','/var/www/projects/f24-02/html/files/core-s/m-011/out-011.dat','/var/www/projects/f24-02/html/cgi-bin/core-s/m-011',1),(325,2,2,'/var/www/projects/f24-02/html/files/core-s/m-002/in-002.dat','/var/www/projects/f24-02/html/files/core-s/m-002/out-002.dat','/var/www/projects/f24-02/html/cgi-bin/core-s/m-002',1),(326,3,2,'/var/www/projects/f24-02/html/files/core-s/m-003/in-003.dat','/var/www/projects/f24-02/html/files/core-s/m-003/out-003.dat','/var/www/projects/f24-02/html/cgi-bin/core-s/m-003',1),(330,1,2,'/var/www/projects/f24-02/html/files/core-s/m-001/in-001.dat','/var/www/projects/f24-02/html/files/core-s/m-001/out-001.dat','/var/www/projects/f24-02/html/cgi-bin/core-s/m-001',1),(331,1,2,'/var/www/projects/f24-02/html/files/core-s/m-001/in-001.dat','/var/www/projects/f24-02/html/files/core-s/m-001/out-001.dat','/var/www/projects/f24-02/html/cgi-bin/core-s/m-001',1),(332,12,2,'/var/www/projects/f24-02/html/files/core-s/m-021/in-021.dat','/var/www/projects/f24-02/html/files/core-s/m-021/out-021.dat','/var/www/projects/f24-02/html/cgi-bin/core-s/m-021',1),(333,1,2,'/var/www/projects/f24-02/html/files/core-s/m-001/in-001.dat','/var/www/projects/f24-02/html/files/core-s/m-001/out-001.dat','/var/www/projects/f24-02/html/cgi-bin/core-s/m-001',1),(334,1,2,'/var/www/projects/s25-01/html/files/core-s/m-001/in-001.dat','/var/www/projects/s25-01/html/files/core-s/m-001/out-001.dat','/var/www/projects/s25-01/html/cgi-bin/core-s/m-001',1),(335,17,2,'/var/www/projects/s25-01/html/files/core-s/m-031/in-031.dat','/var/www/projects/s25-01/html/files/core-s/m-031/out-031.dat','/var/www/projects/s25-01/html/cgi-bin/core-s/m-031',1);
/*!40000 ALTER TABLE `submissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tokens`
--

DROP TABLE IF EXISTS `tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tokens` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Email` text NOT NULL,
  `Selector` text NOT NULL,
  `Token` longtext NOT NULL,
  `Expires` text NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tokens`
--

LOCK TABLES `tokens` WRITE;
/*!40000 ALTER TABLE `tokens` DISABLE KEYS */;
INSERT INTO `tokens` VALUES (5,'asdf@asdf','74304115bc38c758','$2y$10$2FumFQgwsoL08llp74uDeeM.37lzVlHZ635lEFICPMOfMRzh.Pz3C','1646630981'),(8,'bradyc4@newpaltz.edu','6b588b785293e52c','$2y$10$miUE1sPjwgKzBQdROeeVOeVOsT58UzxtuxwpqxchPDAgg120L7C6W','1646759232'),(9,'a@a.com','7d617aa706eb24ad','$2y$10$p9OdROpJsi8opEiwlTDyhOJHwtnRVES8eBaEYmL7qn2lyNSDmlyz.','1650294388');
/*!40000 ALTER TABLE `tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `UserID` int(50) NOT NULL AUTO_INCREMENT,
  `Password` varchar(60) DEFAULT NULL,
  `Email` varchar(255) NOT NULL,
  `ModeID` int(50) NOT NULL,
  `GroupID` int(50) DEFAULT NULL,
  `userType` int(11) NOT NULL,
  PRIMARY KEY (`UserID`)
) ENGINE=InnoDB AUTO_INCREMENT=92627977 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'m','marco@m.m',5,28,1),(2,'a','a@a.a',5,28,1),(3,'t','t@t.t',1,28,0),(4,'s','s@s.s',1,28,0),(5,'c','c@c.c',1,28,0),(6,'w','w@w.w',1,28,0),(35,'r','randomUser@r.r',1,28,0),(37,'a','a@a.com',5,7,1),(44,'b','b@b.b',1,13,0),(49,'test','test@test.com',1,7,0),(65,'u','user@u.u',1,28,0),(76,'v','v@v.v',5,28,1),(87,'w','ww@w.w',1,28,0),(18737661,'$2y$10$MJzLn.yobufI8Azt9h7XzekO4F27Q8FWR16dbGCGX3ywGRvtHXFbe','test@gmail.com',0,0,0),(40356998,'$2y$10$EgyXnanKRph40yn2Fh4vkuWH5Ok9BqBfD0Lk9ZBz8qH8pc1/Vwn2e','test2@gmail.com',0,0,0),(59352186,'$2y$10$cAbJ.nihCXnYlJBvKWTL5u1qRPAZzjwOXaLx4zOjFZSHrleEHHxZ6','xyza@gmail.com',0,0,0),(75850575,'$2y$10$GOTiJVlGJC6oGFLjcTDPB.WxF8C2SKRJthXwrG1I2OQE1Stj25l12','test3@gmail.com',0,0,0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-09-08 23:45:30
