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
  `meetinglocation` varchar(255) NOT NULL DEFAULT "No location provided",
  `meetingtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `bid` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`meetingid`),
  FOREIGN KEY (`clubid`) REFERENCES `clubs` (`clubid`) ON DELETE CASCADE
);

INSERT INTO `meetings` (`meetingid`, `clubid`, `meetinglocation`, `meetingtime`, `bid`) VALUES
(1, 1, 'Parliament House', '2023-05-29 15:02:00', "hvOdCwAAQBAJ"),
(2, 1, 'Parliament House', '2023-05-22 01:10:39', "hvOdCwAAQBAJ"),
(12, 2, "St Joseph's College Library", '2024-01-15 20:41:00', "hvOdCwAAQBAJ"),
(15, 2, "St Joseph's College Library", '2023-12-18 20:47:00', "hvOdCwAAQBAJ"),
(29, 2, "St Joseph's College Library", '2023-08-18 13:14:00', "hvOdCwAAQBAJ"),
(32, 2, "St Joseph's College Library", '2023-06-02 14:14:00', "hvOdCwAAQBAJ"),
(33, 2, "St Joseph's College Library", '2023-05-25 15:10:00', "hvOdCwAAQBAJ");


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

INSERT INTO `users` (`email`, `firstname`, `lastname`, `password`, `permission`, `postcode`, `lastaccess`) VALUES
('admin@admin', 'Peter', 'Whitehouse', SHA2(CONCAT('admin@adminh',SHA2('password', 256)),256), 'admin', '', '2023-05-18 03:53:23'),
('albo@albo', 'Albo', 'Albo', SHA2(CONCAT('albo@albo',SHA2('password', 256)),256), 'clubadmin', '0000', '2023-05-19 03:04:58'),
('student@student', 'student', 'student', SHA2(CONCAT('student@student', SHA2('password', 256)), 256), 'user', '6000', '2023-05-21 02:44:57'),
('clubadmin@clubadmin', 'clubadmin', 'clubadmin', SHA2(CONCAT('clubadmin@clubadmin',SHA2('password', 256)),256), 'clubadmin', '5000', '2023-05-21 03:56:01'),
('member@member', 'member', 'member', SHA2(CONCAT('member@member',SHA2('password', 256)),256), 'user', '4000', '2023-05-21 04:01:09'),
('bart@simpson', 'Bart', 'Simpson', SHA2(CONCAT('bart@simpson',SHA2('password', 256)),256), 'clubadmin', '6000', '2023-05-21 22:26:48');


CREATE TABLE `clubmembership` (
  `userid` INTEGER UNSIGNED NOT NULL,
  `clubid` INTEGER UNSIGNED NOT NULL,
  `role` VARCHAR(6) NOT NULL DEFAULT 'member',
  `status` VARCHAR(10) NOT NULL DEFAULT 'requested',
  PRIMARY KEY (`userid`, `clubid`),
  FOREIGN KEY (`userid`) REFERENCES `users` (`userid`) ON DELETE CASCADE,
  FOREIGN KEY (`clubid`) REFERENCES `clubs` (`clubid`) ON DELETE CASCADE
);


INSERT INTO `clubmembership` (`userid`, `clubid`, `role`, `status`) VALUES
(2, 1, 'admin', 'approved'),
(3, 1, 'member', 'requested'),
(5, 1, 'member', 'requested'),
(4, 2, 'admin', 'approved'),
(5, 2, 'member', 'requested');


CREATE TABLE `votes` (
  `userid` INTEGER UNSIGNED NOT NULL,
  `clubid` INTEGER UNSIGNED NOT NULL,
  `bookid` VARCHAR(50) NOT NULL,
  `vote` TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (`userid`, `clubid`, `bookid`),
  FOREIGN KEY (`userid`) REFERENCES `users` (`userid`) ON DELETE CASCADE,
  FOREIGN KEY (`clubid`) REFERENCES `clubs` (`clubid`) ON DELETE CASCADE
);

INSERT INTO `votes` (`userid`, `clubid`, `bookid`, `vote`) VALUES
(5, 1, 'hvOdCwAAQBAJ', 1),
(5, 1, 'n7egCgAAQBAJ', -1),
(5, 2, '0kIenwEACAAJ', 1),
(5, 2, '1k8mAQAAIAAJ', 1),
(5, 2, 'jFUQvgAACAAJ', -1);


CREATE TABLE `reviews` (
  `userid` INTEGER UNSIGNED NOT NULL,
  `meetingid` INTEGER UNSIGNED NOT NULL,
  `rating` FLOAT NOT NULL,
  `body` TEXT NOT NULL,
  `title` varchar(500) NOT NULL,
  PRIMARY KEY (`userid`, `meetingid`),
  FOREIGN KEY (`userid`) REFERENCES `users` (`userid`) ON DELETE CASCADE,
  FOREIGN KEY (`meetingid`) REFERENCES `meetings` (`meetingid`) ON DELETE CASCADE
 );


INSERT INTO `reviews` (`userid`, `meetingid`, `rating`, `body`, `title`) VALUES
(4, 33, 1, 'I hate this book with a f***ing passion and you deserve to die in f***ing hell for choosing this book', 'This book is utter s***'),
(5, 33, 4, 'Review Contents', 'New Review');
