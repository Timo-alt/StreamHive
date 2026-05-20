<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "streamhive";

try {
    // Hier maken we de ECHTE PDO-verbinding aan die je formulieren nodig hebben
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Dit zorgt ervoor dat PDO direct foutmeldingen geeft als een query mislukt
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Als de verbinding met de database mislukt, zie je hier de reden
    die("Database verbinding mislukt: " . $e->getMessage());
}
?>