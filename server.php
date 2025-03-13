<?php
// Database connection details
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'countries';

// Connect to the MySQL database
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to get a random country name, latitude, and longitude from the database
function getRandomCountry() {
    global $conn;

    $stmt = $conn->prepare('SELECT name, latitude, longitude FROM countries ORDER BY RAND() LIMIT 1');

    $stmt->execute();

    $stmt->bind_result($countryName, $latitude, $longitude);

    if ($stmt->fetch()) {
        return array('name' => $countryName, 'latitude' => $latitude, 'longitude' => $longitude);
    } else {
        return null;
    }
}

// Function to get the coordinates for a specific country from the database
function getCountryCoordinates($country) {
    global $conn;

    // Prepare the query
    $stmt = $conn->prepare('SELECT latitude, longitude FROM countries WHERE name = ?');

    // Bind the parameter
    $stmt->bind_param('s', $country);

    // Execute the query
    $stmt->execute();

    // Bind the result variables
    $stmt->bind_result($latitude, $longitude);

    // Fetch the result
    if ($stmt->fetch()) {
        return array('latitude' => $latitude, 'longitude' => $longitude);
    } else {
        return null;
    }
}

// Get the country parameter from the AJAX request
$country = $_GET['country'];

// Check if the requested country is "random"
if ($country === 'random') {
    // Get a random country
    $randomCountry = getRandomCountry();

    // Return the random country as JSON
    header('Content-Type: application/json');
    echo json_encode($randomCountry);
} else {
    // Get the coordinates for the requested country
    $coordinates = getCountryCoordinates($country);

    // Return the coordinates as JSON
    header('Content-Type: application/json');
    echo json_encode($coordinates);
}

$conn->close();
?>
