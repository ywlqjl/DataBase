-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 22, 2017 at 05:38 PM
-- Server version: 5.7.16-0ubuntu0.16.04.1
-- PHP Version: 7.0.8-0ubuntu0.16.04.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbproject_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `FOLLOW`
--

CREATE TABLE `FOLLOW` (
  `ID_USER_HOST` int(11) NOT NULL,
  `ID_USER_FOLLOWER` int(11) NOT NULL,
  `DATE_FOLLOW` datetime NOT NULL,
  `READ_DATE_FOLLOW` datetime DEFAULT NULL,
  `READ_FOLLOW` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `FOLLOW`
--

INSERT INTO `FOLLOW` (`ID_USER_HOST`, `ID_USER_FOLLOWER`, `DATE_FOLLOW`, `READ_DATE_FOLLOW`, `READ_FOLLOW`) VALUES
(1, 2, '2017-01-22 17:17:58', '2017-01-22 17:21:37', 1),
(1, 3, '2017-01-22 17:20:00', '2017-01-22 17:21:37', 1),
(2, 3, '2017-01-22 17:19:38', NULL, 0),
(2, 4, '2017-01-22 17:33:01', NULL, 0),
(3, 1, '2017-01-22 17:21:47', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `LIKES`
--

CREATE TABLE `LIKES` (
  `ID_TWEET` int(11) NOT NULL,
  `ID_USER` int(11) NOT NULL,
  `DATE_LIKE` datetime NOT NULL,
  `READ_DATE_LIKE` datetime DEFAULT NULL,
  `READ_LIKE` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `LIKES`
--

INSERT INTO `LIKES` (`ID_TWEET`, `ID_USER`, `DATE_LIKE`, `READ_DATE_LIKE`, `READ_LIKE`) VALUES
(1, 2, '2017-01-22 17:18:02', '2017-01-22 17:21:37', 1);

-- --------------------------------------------------------

--
-- Table structure for table `MAKE_TAG`
--

CREATE TABLE `MAKE_TAG` (
  `ID_TWEET` int(11) NOT NULL,
  `ID_TAG` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `MAKE_TAG`
--

INSERT INTO `MAKE_TAG` (`ID_TWEET`, `ID_TAG`) VALUES
(1, 1),
(1, 2),
(3, 3),
(7, 4),
(7, 5),
(7, 6),
(8, 7),
(8, 8);

-- --------------------------------------------------------

--
-- Table structure for table `MENTION`
--

CREATE TABLE `MENTION` (
  `ID_USER` int(11) NOT NULL,
  `ID_TWEET` int(11) NOT NULL,
  `READ_DATE_MENTION` datetime DEFAULT NULL,
  `READ_MENTION` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `MENTION`
--

INSERT INTO `MENTION` (`ID_USER`, `ID_TWEET`, `READ_DATE_MENTION`, `READ_MENTION`) VALUES
(1, 2, '2017-01-22 17:21:37', 1),
(1, 4, '2017-01-22 17:21:37', 1),
(1, 5, '2017-01-22 17:21:37', 1),
(1, 7, NULL, 0),
(2, 5, NULL, 0),
(2, 8, NULL, 0),
(3, 6, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `TAG`
--

CREATE TABLE `TAG` (
  `ID_TAG` int(11) NOT NULL,
  `NAME_TAG` varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `TAG`
--

INSERT INTO `TAG` (`ID_TAG`, `NAME_TAG`) VALUES
(1, 'PHP'),
(2, 'program'),
(3, 'PHP'),
(4, 'UNIX'),
(5, 'program'),
(6, 'unhappy'),
(7, 'reine'),
(8, 'name');

-- --------------------------------------------------------

--
-- Table structure for table `TWEET`
--

CREATE TABLE `TWEET` (
  `ID_TWEET` int(11) NOT NULL,
  `ID_TWEET_RESPONDED` int(11) DEFAULT NULL,
  `ID_USER` int(11) NOT NULL,
  `DATE_PUB` datetime NOT NULL,
  `TEXT` varchar(255) NOT NULL,
  `DATE_RESPONSE` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `TWEET`
--

INSERT INTO `TWEET` (`ID_TWEET`, `ID_TWEET_RESPONDED`, `ID_USER`, `DATE_PUB`, `TEXT`, `DATE_RESPONSE`) VALUES
(1, NULL, 1, '2017-01-22 17:16:48', 'I like PHP ! #PHP #program', NULL),
(2, 1, 2, '2017-01-22 17:18:08', '@wangtianxue Me too !!!!!', NULL),
(3, NULL, 3, '2017-01-22 17:19:27', 'I don\'t know PHP. #PHP', NULL),
(4, 1, 3, '2017-01-22 17:20:26', '@wangtianxue :(', NULL),
(5, NULL, 3, '2017-01-22 17:21:18', 'Let\'s play games ! @wangtianxue @yanwenli', NULL),
(6, 5, 1, '2017-01-22 17:22:25', '@huangtianqi No ! We have projects !', NULL),
(7, NULL, 2, '2017-01-22 17:28:42', 'The project of UNIX is too difficult :( #UNIX #program #unhappy @wangtianxue', NULL),
(8, NULL, 4, '2017-01-22 17:33:43', 'Our names are similar ! @yanwenli #reine #name', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `USER`
--

CREATE TABLE `USER` (
  `ID_USER` int(11) NOT NULL,
  `USERNAME` varchar(128) NOT NULL,
  `NAME` varchar(128) NOT NULL,
  `DATE_INSCRI` datetime NOT NULL,
  `EMAIL` varchar(128) DEFAULT NULL,
  `PASSWORD` varchar(128) NOT NULL,
  `AVATAR` longblob
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `USER`
--

INSERT INTO `USER` (`ID_USER`, `USERNAME`, `NAME`, `DATE_INSCRI`, `EMAIL`, `PASSWORD`, `AVATAR`) VALUES
(1, 'wangtianxue', 'Lydie', '2017-01-22 17:16:19', 'wtxlz@163.com', '202cb962ac59075b964b07152d234b70', 0x696d616765732f77616e677469616e7875652e6a7067),
(2, 'yanwenli', 'Reine', '2017-01-22 17:17:26', 'yanwenli@gmail.com', '289dff07669d7a23de0ef88d2f7129e7', 0x696d616765732f79616e77656e6c692e6a7067),
(3, 'huangtianqi', 'Serenus', '2017-01-22 17:18:57', 'huangtianqi@gmail.com', 'd81f9c1be2e08964bf9f24b15f0e4900', 0x696d616765732f6875616e677469616e71692e706e67),
(4, 'qujunhong', 'likeReine001', '2017-01-22 17:32:12', 'qujunhong@gmail.com', '250cf8b51c773f3f8dc8b4be867a9a02', 0x696d616765732f71756a756e686f6e672e6a7067);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `FOLLOW`
--
ALTER TABLE `FOLLOW`
  ADD PRIMARY KEY (`ID_USER_HOST`,`ID_USER_FOLLOWER`),
  ADD KEY `FK_FOLLOW_USER1` (`ID_USER_FOLLOWER`);

--
-- Indexes for table `LIKES`
--
ALTER TABLE `LIKES`
  ADD PRIMARY KEY (`ID_TWEET`,`ID_USER`),
  ADD KEY `FK_LIKES_USER` (`ID_USER`);

--
-- Indexes for table `MAKE_TAG`
--
ALTER TABLE `MAKE_TAG`
  ADD PRIMARY KEY (`ID_TWEET`,`ID_TAG`),
  ADD KEY `FK_MAKE_TAG_TAG` (`ID_TAG`);

--
-- Indexes for table `MENTION`
--
ALTER TABLE `MENTION`
  ADD PRIMARY KEY (`ID_USER`,`ID_TWEET`),
  ADD KEY `FK_MENTION_TWEET` (`ID_TWEET`);

--
-- Indexes for table `TAG`
--
ALTER TABLE `TAG`
  ADD PRIMARY KEY (`ID_TAG`);

--
-- Indexes for table `TWEET`
--
ALTER TABLE `TWEET`
  ADD PRIMARY KEY (`ID_TWEET`),
  ADD KEY `FK_TWEET_TWEET` (`ID_TWEET_RESPONDED`),
  ADD KEY `FK_TWEET_USER` (`ID_USER`);

--
-- Indexes for table `USER`
--
ALTER TABLE `USER`
  ADD PRIMARY KEY (`ID_USER`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `TAG`
--
ALTER TABLE `TAG`
  MODIFY `ID_TAG` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `TWEET`
--
ALTER TABLE `TWEET`
  MODIFY `ID_TWEET` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `USER`
--
ALTER TABLE `USER`
  MODIFY `ID_USER` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `FOLLOW`
--
ALTER TABLE `FOLLOW`
  ADD CONSTRAINT `FOLLOW_ibfk_1` FOREIGN KEY (`ID_USER_HOST`) REFERENCES `USER` (`ID_USER`),
  ADD CONSTRAINT `FOLLOW_ibfk_2` FOREIGN KEY (`ID_USER_FOLLOWER`) REFERENCES `USER` (`ID_USER`);

--
-- Constraints for table `LIKES`
--
ALTER TABLE `LIKES`
  ADD CONSTRAINT `LIKES_ibfk_1` FOREIGN KEY (`ID_TWEET`) REFERENCES `TWEET` (`ID_TWEET`),
  ADD CONSTRAINT `LIKES_ibfk_2` FOREIGN KEY (`ID_USER`) REFERENCES `USER` (`ID_USER`);

--
-- Constraints for table `MAKE_TAG`
--
ALTER TABLE `MAKE_TAG`
  ADD CONSTRAINT `MAKE_TAG_ibfk_1` FOREIGN KEY (`ID_TWEET`) REFERENCES `TWEET` (`ID_TWEET`),
  ADD CONSTRAINT `MAKE_TAG_ibfk_2` FOREIGN KEY (`ID_TAG`) REFERENCES `TAG` (`ID_TAG`);

--
-- Constraints for table `MENTION`
--
ALTER TABLE `MENTION`
  ADD CONSTRAINT `MENTION_ibfk_1` FOREIGN KEY (`ID_USER`) REFERENCES `USER` (`ID_USER`),
  ADD CONSTRAINT `MENTION_ibfk_2` FOREIGN KEY (`ID_TWEET`) REFERENCES `TWEET` (`ID_TWEET`);

--
-- Constraints for table `TWEET`
--
ALTER TABLE `TWEET`
  ADD CONSTRAINT `TWEET_ibfk_1` FOREIGN KEY (`ID_TWEET_RESPONDED`) REFERENCES `TWEET` (`ID_TWEET`),
  ADD CONSTRAINT `TWEET_ibfk_2` FOREIGN KEY (`ID_USER`) REFERENCES `USER` (`ID_USER`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
