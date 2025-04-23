CREATE DATABASE seeder;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    description VARCHAR(255),
    category VARCHAR(100),
    amount DECIMAL(10, 2),
    type ENUM('income', 'expense')
);


CREATE TABLE portfolio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    symbol VARCHAR(10) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    purchased_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users(username, password) VALUES
('test1234', 'test1234');

INSERT INTO transactions (description, category, amount, type) VALUES
('Paycheck', NULL, 1500.00, 'income'),
('Groceries', 'Food', 200.00, 'expense'),
('Gas', 'Transportation', 60.00, 'expense');

INSERT INTO portfolio (user_id, symbol, quantity, price) VALUES
(1, 'AAPL', 2, 198.87),
(1, 'NVDA', 1, 290.25);


