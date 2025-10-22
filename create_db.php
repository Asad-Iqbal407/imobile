<?php
$servername = "localhost";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS imobile";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select database
$conn->select_db("imobile");

// Create tables
$tables = [
    "CREATE TABLE IF NOT EXISTS hero (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title TEXT,
        description TEXT,
        cta_text VARCHAR(255)
    )",
    "CREATE TABLE IF NOT EXISTS services (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255),
        description TEXT
    )",
    "CREATE TABLE IF NOT EXISTS features (
        id INT AUTO_INCREMENT PRIMARY KEY,
        icon VARCHAR(255),
        title VARCHAR(255),
        description TEXT
    )",
    "CREATE TABLE IF NOT EXISTS quote (
        id INT AUTO_INCREMENT PRIMARY KEY,
        text TEXT,
        author VARCHAR(255),
        position VARCHAR(255)
    )",
    "CREATE TABLE IF NOT EXISTS newsletter (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255),
        description TEXT
    )",
    "CREATE TABLE IF NOT EXISTS contact (
        id INT AUTO_INCREMENT PRIMARY KEY,
        type VARCHAR(50),
        title VARCHAR(255),
        value TEXT
    )",
    "CREATE TABLE IF NOT EXISTS footer (
        id INT AUTO_INCREMENT PRIMARY KEY,
        text TEXT
    )",
    "CREATE TABLE IF NOT EXISTS subscribers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) UNIQUE NOT NULL,
        subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )"
];

foreach ($tables as $table) {
    if ($conn->query($table) === TRUE) {
        echo "Table created successfully<br>";
    } else {
        echo "Error creating table: " . $conn->error . "<br>";
    }
}

$conn->close();
?>