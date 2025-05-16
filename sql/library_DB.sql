-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 16, 2025 at 05:44 AM
-- Server version: 9.3.0
-- PHP Version: 8.4.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `library_DB`
--

-- --------------------------------------------------------

--
-- Table structure for table `Author`
--

CREATE TABLE `Author` (
  `ID` int NOT NULL,
  `Name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Author`
--

INSERT INTO `Author` (`ID`, `Name`) VALUES
(1, 'J.K. Rowling'),
(2, 'Isaac Asimov'),
(3, 'Toni Morrison'),
(4, 'Yuval Noah Harari'),
(5, 'Agatha Christie'),
(6, 'Michelle Obama'),
(7, 'Jane Austen'),
(8, 'Erik Larson'),
(9, 'Neil Gaiman'),
(10, 'Ursula K. Le Guin'),
(11, 'Haruki Murakami'),
(12, 'Chimamanda Ngozi Adichie'),
(13, 'Stephen King'),
(14, 'Malala Yousafzai'),
(15, 'Colson Whitehead'),
(16, 'Sally Rooney'),
(17, 'Andy Weir'),
(18, 'Zadie Smith'),
(19, 'Kazuo Ishiguro'),
(20, 'Ann Patchett');

-- --------------------------------------------------------

--
-- Table structure for table `Book`
--

CREATE TABLE `Book` (
  `ID` int NOT NULL,
  `Title` varchar(200) NOT NULL,
  `ISBN` varchar(50) DEFAULT NULL,
  `PublicationYear` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Book`
--

INSERT INTO `Book` (`ID`, `Title`, `ISBN`, `PublicationYear`) VALUES
(1, 'Harry Potter and the Sorcerer\'s Stonep', '9780590353427', 1997),
(3, 'Beloved', '9781400033416', 1987),
(4, 'Sapiens: A Brief History of Humankind', '9780062316097', 2014),
(5, 'Murder on the Orient Express', '9780062693662', 1934),
(6, 'Becoming', '9781524763138', 2018),
(7, 'Pride and Prejudice', '9780141439518', 1813),
(8, 'The Devil in the White City', '9780375725609', 2003),
(9, 'American Gods', '9780062572233', 2001),
(10, 'A Wizard of Earthsea', '9780547773742', 1968),
(11, 'Norwegian Wood', '9780375704024', 1987),
(12, 'Half of a Yellow Sun', '9781400095209', 2006),
(13, 'The Shining', '9780307743657', 1977),
(14, 'I Am Malala', '9780316322409', 2013),
(15, 'The Underground Railroad', '9780385542364', 2016),
(16, 'Normal People', '9781984822185', 2018),
(17, 'Project Hail Mary', '9780593135204', 2021),
(18, 'White Teeth', '9780375703867', 2000),
(19, 'Never Let Me Go', '9781400078776', 2005);

--
-- Triggers `Book`
--
DELIMITER $$
CREATE TRIGGER `after_book_insert` AFTER INSERT ON `Book` FOR EACH ROW BEGIN
    INSERT INTO BookCopy (Book_ID, CopyNumber, IsAvailable)
    VALUES (NEW.ID, 1, TRUE);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `BookCopy`
--

CREATE TABLE `BookCopy` (
  `Book_ID` int NOT NULL,
  `CopyNumber` int NOT NULL,
  `IsAvailable` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `BookCopy`
--

INSERT INTO `BookCopy` (`Book_ID`, `CopyNumber`, `IsAvailable`) VALUES
(1, 1, 0),
(1, 2, 1),
(1, 3, 1),
(1, 4, 1),
(1, 5, 1),
(3, 1, 1),
(4, 1, 1),
(5, 1, 1),
(6, 1, 1),
(7, 1, 1),
(8, 1, 1),
(9, 1, 1),
(10, 1, 1),
(11, 1, 1),
(12, 1, 1),
(13, 1, 1),
(14, 1, 1),
(15, 1, 1),
(16, 1, 1),
(17, 1, 1),
(18, 1, 1),
(19, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `BookGenre`
--

CREATE TABLE `BookGenre` (
  `Book_ID` int NOT NULL,
  `Genre_ID` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `BookGenre`
--

INSERT INTO `BookGenre` (`Book_ID`, `Genre_ID`) VALUES
(3, 1),
(11, 1),
(18, 1),
(19, 1),
(17, 2),
(1, 3),
(9, 3),
(10, 3),
(4, 4),
(5, 5),
(13, 5),
(6, 6),
(14, 6),
(7, 7),
(16, 7),
(8, 8),
(12, 8),
(15, 8);

-- --------------------------------------------------------

--
-- Table structure for table `Genre`
--

CREATE TABLE `Genre` (
  `ID` int NOT NULL,
  `Name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Genre`
--

INSERT INTO `Genre` (`ID`, `Name`) VALUES
(1, 'Fiction'),
(2, 'Sci-Fi'),
(3, 'Fantasy'),
(4, 'Non-Fiction'),
(5, 'Mystery'),
(6, 'Biography'),
(7, 'Romance'),
(8, 'Historical');

-- --------------------------------------------------------

--
-- Table structure for table `Member`
--

CREATE TABLE `Member` (
  `ID` int NOT NULL,
  `FName` varchar(50) NOT NULL,
  `LName` varchar(50) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Address` varchar(200) NOT NULL,
  `JoinDate` date NOT NULL,
  `PNumber` varchar(20) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `Role` enum('member','librarian') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Member`
--

INSERT INTO `Member` (`ID`, `FName`, `LName`, `Email`, `Address`, `JoinDate`, `PNumber`, `Password`, `Role`) VALUES
(1, 'Emma', 'Davis', 'emma.davis@example.com', '123 Maple St, Springfield', '2025-01-01', '555-0101', 'password1', 'member'),
(2, 'Liam', 'Wilson', 'liam.wilson@example.com', '456 Oak Ave, Springfield', '2025-01-02', '555-0102', 'password2', 'member'),
(3, 'Olivia', 'Brown', 'olivia.brown@example.com', '789 Pine Rd, Springfield', '2025-01-03', '555-0103', 'password3', 'member'),
(4, 'Charlotte', 'White', 'charlotte.white@example.com', '101 Elm St, Springfield', '2025-01-04', '555-0104', 'libpass1', 'librarian'),
(5, 'Henry', 'Clark', 'henry.clark@example.com', '202 Birch Ln, Springfield', '2025-01-05', '555-0105', 'libpass2', 'librarian'),
(7, 'AVISHEK', 'DAS', 'avishek@ksu.edu', '1600 Roof Drive', '2025-05-16', '7853174779', 'avishek', 'member');

-- --------------------------------------------------------

--
-- Table structure for table `Reservation`
--

CREATE TABLE `Reservation` (
  `ID` int NOT NULL,
  `StartDate` date NOT NULL,
  `EndDate` date NOT NULL,
  `Status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Reservation`
--

INSERT INTO `Reservation` (`ID`, `StartDate`, `EndDate`, `Status`) VALUES
(2, '2025-05-13', '2025-05-27', 'returned'),
(3, '2025-05-13', '2025-05-27', 'returned'),
(4, '2025-05-13', '2025-05-27', 'returned'),
(5, '2025-05-13', '2025-05-27', 'returned'),
(6, '2025-05-16', '2025-05-30', 'returned');

-- --------------------------------------------------------

--
-- Table structure for table `ReservationBookCopy`
--

CREATE TABLE `ReservationBookCopy` (
  `Reservation_ID` int NOT NULL,
  `Book_ID` int NOT NULL,
  `CopyNumber` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ReservationBookCopy`
--

INSERT INTO `ReservationBookCopy` (`Reservation_ID`, `Book_ID`, `CopyNumber`) VALUES
(2, 3, 1),
(4, 3, 1),
(6, 4, 1),
(3, 6, 1),
(5, 6, 1);

-- --------------------------------------------------------

--
-- Table structure for table `ReservationMember`
--

CREATE TABLE `ReservationMember` (
  `Reservation_ID` int NOT NULL,
  `Member_ID` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ReservationMember`
--

INSERT INTO `ReservationMember` (`Reservation_ID`, `Member_ID`) VALUES
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1);

-- --------------------------------------------------------

--
-- Table structure for table `WrittenBy`
--

CREATE TABLE `WrittenBy` (
  `Book_ID` int NOT NULL,
  `Author_ID` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `WrittenBy`
--

INSERT INTO `WrittenBy` (`Book_ID`, `Author_ID`) VALUES
(1, 1),
(3, 3),
(4, 4),
(5, 5),
(6, 6),
(7, 7),
(8, 8),
(9, 9),
(10, 10),
(11, 11),
(12, 12),
(13, 13),
(14, 14),
(15, 15),
(16, 16),
(17, 17),
(18, 18),
(19, 19);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Author`
--
ALTER TABLE `Author`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `Book`
--
ALTER TABLE `Book`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `ISBN` (`ISBN`);

--
-- Indexes for table `BookCopy`
--
ALTER TABLE `BookCopy`
  ADD PRIMARY KEY (`Book_ID`,`CopyNumber`);

--
-- Indexes for table `BookGenre`
--
ALTER TABLE `BookGenre`
  ADD PRIMARY KEY (`Book_ID`,`Genre_ID`),
  ADD KEY `Genre_ID` (`Genre_ID`);

--
-- Indexes for table `Genre`
--
ALTER TABLE `Genre`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `Member`
--
ALTER TABLE `Member`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `Reservation`
--
ALTER TABLE `Reservation`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `ReservationBookCopy`
--
ALTER TABLE `ReservationBookCopy`
  ADD PRIMARY KEY (`Reservation_ID`,`Book_ID`,`CopyNumber`),
  ADD KEY `Book_ID` (`Book_ID`,`CopyNumber`);

--
-- Indexes for table `ReservationMember`
--
ALTER TABLE `ReservationMember`
  ADD PRIMARY KEY (`Reservation_ID`,`Member_ID`),
  ADD KEY `Member_ID` (`Member_ID`);

--
-- Indexes for table `WrittenBy`
--
ALTER TABLE `WrittenBy`
  ADD PRIMARY KEY (`Book_ID`,`Author_ID`),
  ADD KEY `Author_ID` (`Author_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Author`
--
ALTER TABLE `Author`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `Book`
--
ALTER TABLE `Book`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `Genre`
--
ALTER TABLE `Genre`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `Member`
--
ALTER TABLE `Member`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `Reservation`
--
ALTER TABLE `Reservation`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `BookCopy`
--
ALTER TABLE `BookCopy`
  ADD CONSTRAINT `bookcopy_ibfk_1` FOREIGN KEY (`Book_ID`) REFERENCES `Book` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `BookGenre`
--
ALTER TABLE `BookGenre`
  ADD CONSTRAINT `bookgenre_ibfk_1` FOREIGN KEY (`Book_ID`) REFERENCES `Book` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookgenre_ibfk_2` FOREIGN KEY (`Genre_ID`) REFERENCES `Genre` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `ReservationBookCopy`
--
ALTER TABLE `ReservationBookCopy`
  ADD CONSTRAINT `reservationbookcopy_ibfk_1` FOREIGN KEY (`Reservation_ID`) REFERENCES `Reservation` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservationbookcopy_ibfk_2` FOREIGN KEY (`Book_ID`,`CopyNumber`) REFERENCES `BookCopy` (`Book_ID`, `CopyNumber`) ON DELETE CASCADE;

--
-- Constraints for table `ReservationMember`
--
ALTER TABLE `ReservationMember`
  ADD CONSTRAINT `reservationmember_ibfk_1` FOREIGN KEY (`Reservation_ID`) REFERENCES `Reservation` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservationmember_ibfk_2` FOREIGN KEY (`Member_ID`) REFERENCES `Member` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `WrittenBy`
--
ALTER TABLE `WrittenBy`
  ADD CONSTRAINT `writtenby_ibfk_1` FOREIGN KEY (`Book_ID`) REFERENCES `Book` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `writtenby_ibfk_2` FOREIGN KEY (`Author_ID`) REFERENCES `Author` (`ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
