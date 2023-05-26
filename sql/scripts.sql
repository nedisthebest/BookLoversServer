DROP DATABASE `bookreview`;

CREATE DATABASE IF NOT EXISTS `bookreview` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE `bookreview`;


CREATE TABLE `clubs` (
  `clubid` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `clubname` varchar(100) NOT NULL,
  `suburb` varchar(255) NOT NULL,
  `state` varchar(11) NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'requested',
  PRIMARY KEY (`clubid`)
);


INSERT INTO `clubs` (`clubid`, `clubname`, `suburb`, `state`, `status`) VALUES
(1, "Parliamentary Library", 'Braden', 'ACT', 'approved'),
(2, "St Joseph's College Book Club", '4000', 'QLD', 'approved');


CREATE TABLE `meetings` (
  `meetingid` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `clubid` INTEGER UNSIGNED NOT NULL,
  `meetinglocation` varchar(255) NOT NULL,
  `meetingtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `chosenbookid` varchar(20) NOT NULL,
  PRIMARY KEY (`meetingid`),
  FOREIGN KEY (`clubid`) REFERENCES `clubs` (`clubid`) ON DELETE CASCADE,
  FOREIGN KEY (`chosenbookid`) REFERENCES `books` (`bookid`) ON DELETE NO ACTION
);

INSERT INTO `meetings` (`meetingid`, `clubid`, `meetinglocation`, `meetingtime`, `chosenbookid`) VALUES
(1, 1, 'Parliament House', '2023-05-29 15:02:00', 1),
(2, 1, 'Parliament House', '2023-05-22 01:10:39', 2),
(12, 2, "St Joseph's College Library", '2024-01-15 20:41:00', 7),
(15, 2, "St Joseph's College Library", '2023-12-18 20:47:00', 9),
(29, 2, "St Joseph's College Library", '2023-08-18 13:14:00', 17),
(32, 2, "St Joseph's College Library", '2023-06-02 14:14:00', 20),
(33, 2, "St Joseph's College Library", '2023-05-25 15:10:00', 21);


CREATE TABLE `users` (
  `userid` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(200) NOT NULL UNIQUE,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `permission` varchar(100) NOT NULL DEFAULT 'user',
  `postcode` varchar(5) NOT NULL,
  `lastaccess` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `token` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`userid`)
);

INSERT INTO `users` (`userid`, `email`, `firstname`, `lastname`, `password`, `permission`, `postcode`, `lastaccess`) VALUES
(1, 'admin@admin', 'Peter', 'Whitehouse', SHA2('password', 256), 'admin', '', '2023-05-18 03:53:23'),
(2, 'albo@albo', 'Albo', 'Albo', SHA2('password', 256), 'clubadmin', '0000', '2023-05-19 03:04:58'),
(3, 'student@student', 'student', 'student', SHA2('password', 256), 'user', '6000', '2023-05-21 02:44:57'),
(4, 'clubadmin@clubadmin', 'clubadmin', 'clubadmin', SHA2('password', 256), 'clubadmin', '5000', '2023-05-21 03:56:01'),
(5, 'member@member', 'member', 'member', SHA2('password', 256), 'user', '4000', '2023-05-21 04:01:09'),
(6, 'bart@simpson', 'Bart', 'Simpson', SHA2('password', 256), 'clubadmin', '6000', '2023-05-21 22:26:48');


CREATE TABLE `clubmembership` (
  `membershipid` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` INTEGER UNSIGNED NOT NULL,
  `clubid` INTEGER UNSIGNED NOT NULL,
  `role` VARCHAR(6) NOT NULL DEFAULT 'member',
  `status` VARCHAR(10) NOT NULL DEFAULT 'requested',
  PRIMARY KEY (`membershipid`),
  FOREIGN KEY (`userid`) REFERENCES `users` (`userid`) ON DELETE CASCADE,
  FOREIGN KEY (`clubid`) REFERENCES `clubs` (`clubid`) ON DELETE CASCADE
);


INSERT INTO `clubmembership` (`membershipid`, `userid`, `clubid`, `role`, `status`) VALUES
(1, 2, 1, 'admin', 'approved'),
(2, 3, 1, 'member', 'requested'),
(3, 4, 2, 'admin', 'approved'),
(5, 5, 2, 'member', 'requested');


CREATE TABLE `votes` (
  `voteid` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` INTEGER UNSIGNED NOT NULL,
  `bookid` VARCHAR(50),
  `vote` varchar(10) NOT NULL DEFAULT 'up',
  PRIMARY KEY (`voteid`),
  FOREIGN KEY (`userid`) REFERENCES `users` (`userid`) ON DELETE CASCADE,
);

