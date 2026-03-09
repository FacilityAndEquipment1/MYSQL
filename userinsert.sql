INSERT INTO users (name, email, password, role) VALUES
('rems', 'rems@example.com', 'password123', 'user'),
('ry', 'ry@example.com', 'password123', 'user'),
('jay', 'jay@example.com', 'password123', 'user'),
('Admin', 'adminTest@example.com', 'password123', 'admin');

SELECT * FROM users WHERE user_id = 3;