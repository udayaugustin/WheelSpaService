/* Thanaseelan 06/10/2018 */
CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_code` varchar(255) DEFAULT NULL,
  `role_name` varchar(255) DEFAULT NULL,
  `role_status` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `roles` (`role_id`, `role_code`, `role_name`, `role_status`) VALUES
(1, 'admin', 'Admin', 'active'),
(2, 'user', 'User', 'active');


CREATE TABLE `user_details` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` bigint(16) NOT NULL,
  `auth_token` varchar(255) DEFAULT NULL,
  `user_status` varchar(255) NOT NULL DEFAULT 'inactive'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `user_details` (`user_id`, `role_id`, `name`, `username`, `password`, `phone`, `auth_token`, `user_status`) VALUES
(1, 2, 'John', 'john@tire.com', '4bf0051cddce04ead96c81424474c3a7f6f97fb6', 9994215369, 'orqg9711', 'active'),
(2, 1, 'John Deo', 'admin@tire.com', '25bd3b15794fb6d35006875f8249d0e3d37b944e', 9994215369, 'jsjvq7g7', 'active'),
(3, 2, 'Thanaseelan', 'thana@tire.com', '2f6410582233d5acc662061dc23996d4229debc7', 9994027557, NULL, 'inactive');


CREATE TABLE `vehicle_details` (
  `vehicle_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vehicle_no` varchar(255) DEFAULT NULL,
  `vehicle_brand` varchar(255) DEFAULT NULL,
  `vehicle_model` varchar(255) DEFAULT NULL,
  `vehicle_type` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `vehicle_details` (`vehicle_id`, `user_id`, `vehicle_no`, `vehicle_brand`, `vehicle_model`, `vehicle_type`) VALUES
(1, 1, 'TN76 AA 0B567', 'KTM', 'Maisto KTM RC 390 1/18', 'Bike'),
(2, 1, 'TN76 AA 0B04', 'KTM', 'Maisto KTM RC 390 1/18', 'Bike'),
(3, 1, 'TN76 AA 0B45', 'KTM', 'Maisto KTM RC 390 1/18', 'Bike');


ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

ALTER TABLE `user_details`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `role_foreign_key_user` (`role_id`);

ALTER TABLE `vehicle_details`
  ADD PRIMARY KEY (`vehicle_id`),
  ADD KEY `user_forign_vehicle` (`user_id`);

ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `user_details`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `vehicle_details`
  MODIFY `vehicle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `user_details`
  ADD CONSTRAINT `role_foreign_key_user` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);

ALTER TABLE `vehicle_details`
  ADD CONSTRAINT `user_forign_vehicle` FOREIGN KEY (`user_id`) REFERENCES `user_details` (`user_id`);
