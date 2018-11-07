-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 15, 2018 at 01:03 AM
-- Server version: 5.7.23-0ubuntu0.18.04.1
-- PHP Version: 7.2.10-0ubuntu0.18.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tyre`
--

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_code` varchar(255) DEFAULT NULL,
  `role_name` varchar(255) DEFAULT NULL,
  `role_status` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_code`, `role_name`, `role_status`) VALUES
(1, 'admin', 'Admin', 'active'),
(2, 'user', 'User', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `state`
--

CREATE TABLE `state` (
  `state_id` int(11) NOT NULL,
  `state_name` varchar(255) NOT NULL,
  `country_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `state`
--

INSERT INTO `state` (`state_id`, `state_name`, `country_id`) VALUES
(1, 'Andhra Pradesh', 1),
(2, 'Arunachal Pradesh', 1),
(3, 'Andaman and Nicobar Islands', 1),
(4, 'Assam', 1),
(5, 'Bihar', 1),
(6, 'Chandigarh', 1),
(7, 'Chhattisgarh', 1),
(8, 'Dadra and Nagar Haveli', 1),
(9, 'Daman and Diu', 1),
(10, 'Delhi', 1),
(11, 'Goa', 1),
(12, 'Gujarat', 1),
(13, 'Haryana', 1),
(14, 'Himachal Pradesh', 1),
(15, 'Jammu and Kashmir', 1),
(16, 'Jharkhand', 1),
(17, 'Karnataka', 1),
(18, 'Kerala', 1),
(19, 'Lakshadweep', 1),
(20, 'Madhya Pradesh', 1),
(21, 'Maharashtra', 1),
(22, 'Manipur', 1),
(23, 'Meghalaya', 1),
(24, 'Mizoram', 1),
(25, 'Nagaland', 1),
(26, 'Odisha', 1),
(27, 'Puducherry', 1),
(28, 'Punjab', 1),
(29, 'Rajasthan', 1),
(30, 'Sikkim', 1),
(31, 'Tamil Nadu', 1),
(32, 'Tripura', 1),
(33, 'Uttarakhand', 1),
(34, 'Uttar Pradesh', 1),
(35, 'West Bengal', 1),
(36, 'Telangana', 1),
(37, 'Jammu & Kashmir', 0),
(38, 'Orissa', 0),
(39, 'Pondicherry', 0),
(40, 'Uttaranchal', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tyre_details`
--

CREATE TABLE `tyre_details` (
  `tyre_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `tyre` varchar(255) DEFAULT NULL,
  `tyre_brand` varchar(255) DEFAULT NULL,
  `tyre_name` varchar(255) DEFAULT NULL,
  `tyre_size` varchar(255) DEFAULT NULL,
  `rim_size` varchar(255) DEFAULT NULL,
  `tyre_life_remaining` varchar(255) DEFAULT NULL,
  `date_of_parchase` date DEFAULT NULL,
  `tyre_side` varchar(255) DEFAULT NULL,
  `tyre_type` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tyre_details`
--

INSERT INTO `tyre_details` (`tyre_id`, `user_id`, `vehicle_id`, `tyre`, `tyre_brand`, `tyre_name`, `tyre_size`, `rim_size`, `tyre_life_remaining`, `date_of_parchase`, `tyre_side`, `tyre_type`) VALUES
(1, 2, 1, 'new', 'TVS', 'TVS Super', '44/60', 'R18', '10000', '2018-01-12', 'front', 'same'),
(2, 2, 2, 'new', 'TVS', 'TVS Super', '44/60', 'R18', '10000', '2018-01-12', 'front', 'same'),
(3, 2, 2, 'new', 'TVS', 'TVS Super', '44/60', 'R18', '10000', '2018-01-12', 'front', 'same'),
(4, 2, 2, 'new', 'TVS', 'TVS Super', '44/60', 'R18', '10000', '2018-01-12', 'front', 'same'),
(6, 2, 2, 'old', 'Consequat Consequatur Eos omnis voluptatem sed elit sint rem', 'Buffy Bullock', 'Temporibus veritatis suscipit ipsa facere dolore ut placeat nihil nostrum id nihil dolorum ex voluptatibus ipsum et veritatis non corrupti', 'Velit hic sit aliquam ratione anim laudantium in fugit rem facere incididunt non', 'Necessitatibus necessitatibus distinctio Excepteur perspiciatis ullam voluptatum molestias nostrud sint voluptas quasi autem harum duis impedit', '0000-00-00', 'back', 'different');

-- --------------------------------------------------------

--
-- Table structure for table `user_details`
--

CREATE TABLE `user_details` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL DEFAULT '2',
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` bigint(16) NOT NULL,
  `user_dob` date NOT NULL,
  `state` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `street_address` varchar(255) NOT NULL,
  `pincode` bigint(22) NOT NULL,
  `auth_token` varchar(255) NOT NULL,
  `user_status` varchar(255) NOT NULL DEFAULT 'inactive'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_details`
--

INSERT INTO `user_details` (`user_id`, `role_id`, `name`, `username`, `password`, `phone`, `user_dob`, `state`, `city`, `street_address`, `pincode`, `auth_token`, `user_status`) VALUES
(1, 2, 'John', 'user@tyre.com', '340257a7b31f401b2174e8ed51bf87385d8a6d16', 9994215369, '0000-00-00', '', '', '', 0, 'orqg9711', 'active'),
(2, 1, 'John Doe', 'admin@tyre.com', '340257a7b31f401b2174e8ed51bf87385d8a6d16', 9994215369, '1994-01-12', '31', 'Nellai', '55, South street.', 627856, 'q33r9ff4', 'active'),
(14, 2, 'Patience Hawkins', 'jivyk@mailinator.net', '340257a7b31f401b2174e8ed51bf87385d8a6d16', 79, '0000-00-00', '26', 'Consequatur Adipisci rem architecto tempore minus quia veritatis', 'Ullam cillum itaque eaque et corporis sed exercitation quod et ipsa', 93, '', 'active'),
(15, 2, 'Thanaseelan', 'thana@tire.com', '2f6410582233d5acc662061dc23996d4229debc7', 9994027557, '0000-00-00', '', '', '', 0, '', 'active'),
(16, 2, 'Thanaseelan', 'thana@tyre.com', '2f6410582233d5acc662061dc23996d4229debc7', 9994027557, '1994-01-12', '31', 'Nellai', '55, South street.', 627856, '00mm0zqz', 'active'),
(17, 2, 'Marah Lee', 'xyweceqin@mailinator.net', '2c8c73abd0cb7d08459c860547ac58c4a185d436', 65, '2018-01-31', '31', 'Omnis natus voluptas placeat architecto odio consequatur dolore assumenda veritatis earum', 'Facere officiis quis dolore aut dolor fuga Tenetur ad et impedit est', 65, '', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_details`
--

CREATE TABLE `vehicle_details` (
  `vehicle_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vehicle_no` varchar(255) NOT NULL,
  `vehicle_name` varchar(255) NOT NULL,
  `vehicle_brand` varchar(255) NOT NULL,
  `vehicle_model` varchar(255) NOT NULL,
  `vehicle_type` varchar(255) NOT NULL,
  `vehicle_version` varchar(255) NOT NULL,
  `year_of_purchase` varchar(255) NOT NULL,
  `km_done` varchar(255) NOT NULL,
  `avg_drive_per_week` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vehicle_details`
--

INSERT INTO `vehicle_details` (`vehicle_id`, `user_id`, `vehicle_no`, `vehicle_name`, `vehicle_brand`, `vehicle_model`, `vehicle_type`, `vehicle_version`, `year_of_purchase`, `km_done`, `avg_drive_per_week`) VALUES
(1, 1, 'TN76 AA 0B56', '', 'KTM', 'Maisto KTM RC 390 1/18', 'Bike', '', '', '', ''),
(2, 1, 'Fuga Obcaecati do ipsam aut amet nisi omnis explicabo Quo ut', 'Alfreda Sandoval', 'Rerum id autem quia beatae voluptatem', 'Aut inventore assumenda iste cum laudantium repudiandae obcaecati quia voluptatibus quam velit pariatur Irure itaque vel eum irure sed', 'Fuga Obcaecati do ipsam aut amet nisi omnis explicabo Quo ut', 'Officiis earum perspiciatis veritatis maxime anim nisi magna excepturi occaecat non illo non voluptatem', '', 'Impedit consequatur omnis reprehenderit suscipit amet quis excepteur eu', 'Aut eum omnis dicta laudantium Nam consequatur Consequat Non officia nemo amet cupidatat totam explicabo Veniam sunt qui quis voluptates'),
(3, 1, 'TN76 AA 0B45', '', 'KTM', 'Maisto KTM RC 390 1/18', 'Bike', '', '', '', ''),
(4, 1, 'TN72 AA 6754', '', 'TVS', '2012', 'Moter Cycle', '', '', '', ''),
(5, 1, 'TN76 AA 0B4098', 'KTM', 'KTM', 'Maisto KTM RC 390 1/18', 'Bike', '2.0', '2014', '25,000', '30,000'),
(6, 1, 'TN76 AA 0B4098', 'KTM', 'KTM', 'Maisto KTM RC 390 1/18', 'Bike', '2.0', '2014', '25,000', '30,000');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `state`
--
ALTER TABLE `state`
  ADD PRIMARY KEY (`state_id`);

--
-- Indexes for table `tyre_details`
--
ALTER TABLE `tyre_details`
  ADD PRIMARY KEY (`tyre_id`),
  ADD KEY `user_forign_tyre` (`user_id`);

--
-- Indexes for table `user_details`
--
ALTER TABLE `user_details`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `role_foreign_key_user` (`role_id`);

--
-- Indexes for table `vehicle_details`
--
ALTER TABLE `vehicle_details`
  ADD PRIMARY KEY (`vehicle_id`),
  ADD KEY `user_forign_vehicle` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `tyre_details`
--
ALTER TABLE `tyre_details`
  MODIFY `tyre_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `user_details`
--
ALTER TABLE `user_details`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `vehicle_details`
--
ALTER TABLE `vehicle_details`
  MODIFY `vehicle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `tyre_details`
--
ALTER TABLE `tyre_details`
  ADD CONSTRAINT `user_forign_tyre` FOREIGN KEY (`user_id`) REFERENCES `user_details` (`user_id`);

--
-- Constraints for table `user_details`
--
ALTER TABLE `user_details`
  ADD CONSTRAINT `role_foreign_key_user` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);

--
-- Constraints for table `vehicle_details`
--
ALTER TABLE `vehicle_details`
  ADD CONSTRAINT `user_forign_vehicle` FOREIGN KEY (`user_id`) REFERENCES `user_details` (`user_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
