CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `gasergy_balance` int(11) NOT NULL DEFAULT 0,
  `stripe_customer_id` varchar(255) DEFAULT NULL,
  `stripe_subscription_id` varchar(255) DEFAULT NULL,
  `subscription_gasergy` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
