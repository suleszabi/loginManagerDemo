START TRANSACTION;

CREATE TABLE `user` (
  `id` int(2) NOT NULL,
  `username` varchar(12) COLLATE utf8_bin NOT NULL,
  `email` varchar(40) COLLATE utf8_bin NOT NULL,
  `password_hash` varchar(128) COLLATE utf8_bin NOT NULL,
  `password_salt` varchar(10) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `user`
  MODIFY `id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;
