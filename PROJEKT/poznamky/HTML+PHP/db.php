<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "autoskola";

$conn = mysqli_connect($servername, $username, $password);
if (!$conn) {
    die("Připojení selhalo: " . mysqli_connect_error());
}

mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
mysqli_select_db($conn, $dbname);

mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'user') NOT NULL DEFAULT 'user'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        jmeno VARCHAR(100) NOT NULL,
        prijmeni VARCHAR(100) NOT NULL,
        birthdate DATE NOT NULL,
        tel VARCHAR(20) NOT NULL,
        email VARCHAR(150) NOT NULL,
        prospel TINYINT(1) NOT NULL DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS instructors (
        id INT AUTO_INCREMENT PRIMARY KEY,
        jmeno VARCHAR(100) NOT NULL,
        prijmeni VARCHAR(100) NOT NULL,
        tel VARCHAR(20) NOT NULL,
        email VARCHAR(150) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS vozidla (
        id INT AUTO_INCREMENT PRIMARY KEY,
        znacka VARCHAR(100) NOT NULL,
        model VARCHAR(100) NOT NULL,
        spz VARCHAR(20) NOT NULL,
        rok_vyroby YEAR NOT NULL,
        barva VARCHAR(50) NOT NULL,
        palivo VARCHAR(50) NOT NULL,
        prevodovka VARCHAR(50) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS jizdy (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        instructor_id INT NOT NULL,
        vozidlo_id INT NOT NULL,
        datum DATE NOT NULL,
        cas TIME NOT NULL,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
        FOREIGN KEY (instructor_id) REFERENCES instructors(id) ON DELETE CASCADE,
        FOREIGN KEY (vozidlo_id) REFERENCES vozidla(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

$check = mysqli_query($conn, "SELECT id FROM users WHERE username = 'adam'");
if (mysqli_num_rows($check) === 0) {
    $hash = password_hash("admin", PASSWORD_DEFAULT);
    mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('adam', '$hash', 'admin')");
}

$check = mysqli_query($conn, "SELECT id FROM users WHERE username = 'uzivatel'");
if (mysqli_num_rows($check) === 0) {
    $hash = password_hash("user", PASSWORD_DEFAULT);
    mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('uzivatel', '$hash', 'user')");
}
?>