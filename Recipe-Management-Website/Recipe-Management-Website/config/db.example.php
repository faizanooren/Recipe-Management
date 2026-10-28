<?php
// Copy this file to config/db.php and update the values for your setup.
// (config/db.php is ignored by git so your real credentials stay private.)
$servername = "localhost";
$username   = "root";   // XAMPP default
$password   = "";       // XAMPP default (empty)
$dbname     = "recipe_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
