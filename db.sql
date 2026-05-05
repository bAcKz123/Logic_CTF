-- Simple lab schema and seed data
CREATE DATABASE IF NOT EXISTS simple_lab;
USE simple_lab;

DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS products;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    coins INT NOT NULL DEFAULT 5
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(50) NOT NULL,
    price INT NOT NULL
);

INSERT INTO users (username, coins) VALUES
('player1', 5);

INSERT INTO products (code, name, price) VALUES
('shirt', 'Shirt', 2),
('hat', 'Hat', 3),
('flag', 'Flag', 10);
