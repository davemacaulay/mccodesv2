INSERT INTO `users` (
    `username`, `login_name`, `userpass`, `level`, `money`, `crystals`, `donatordays`, `user_level`, `energy`,
    `maxenergy`, `will`, `maxwill`, `brave`, `maxbrave`, `hp`, `maxhp`, `location`, `gender`, `signedup`, `email`,
    `bankmoney`, `lastip`, `lastip_signup`, `pass_salt`, `display_pic`, `staffnotes`, `voted`, `user_notepad`
) VALUES (
    'admin', 'admin', '862b159a2262556d8c077fbf4353e32a', 1, 100, 0, 0, 2, 12, 12, 100, 100, 5, 5, 100, 100, 1, 'Male',
    '1719158844', 'admin@localhost.com', -1, '127.0.0.1', '127.0.0.1', 'a7487647', '', '', '', ''
);

INSERT INTO `userstats` VALUES (LAST_INSERT_ID(), 10, 10, 10, 10, 10);

INSERT INTO `settings`
    (conf_name, conf_value, data_type)
VALUES
    ('game_name', 'MCCodes v2', 'string'),
    ('game_owner', 'Owner Name', 'string'),
    ('paypal', NULL, 'string'),
    ('game_description', 'Game made on the MCCodes engine', 'string');