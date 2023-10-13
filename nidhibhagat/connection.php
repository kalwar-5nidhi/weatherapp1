<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "website";

    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $database);

    // Check connection
    if (!$conn) {
        die('Connection failed: ' . mysqli_connect_error());
    }
    echo "Connected successfully";

    $jsonData = file_get_contents("php://input");

    // Convert JSON data to PHP array
    $weatherData = json_decode($jsonData, true);

    // Send weather data to the session
    session_start(); // Start the session
    $_SESSION['weatherData'] = $weatherData;

    // accessing  the data
    $city = $weatherData['city'];
    $date_on = $weatherData['date_on'];
    $temperature = $weatherData['temperature'];
    $pressure = $weatherData['pressure'];
    $humidity = $weatherData['humidity'];
    $wind_speed = $weatherData['wind_speed'];
    $weather = $weatherData['weather'];
    $description = $weatherData['description'];

    $formatted_date = explode("T", $date_on)[0];
    $lower_wind = $wind_speed - 0.01;
    $higher_wind = $wind_speed + 0.01;

    $sql = "SELECT * FROM  temperature_history where city ='$city' and temperature =$temperature and pressure = $pressure  and date_on = '$formatted_date' AND wind_speed >= $lower_wind AND wind_speed <= $higher_wind";
    echo $sql;
    $result = $conn->query($sql);

    // Check if there are any rows returned from the query
    if (!$result->num_rows > 0) {
        // SQL query to insert data
        $sql = "INSERT INTO temperature_history (city, date_on, temperature, pressure, humidity, wind_speed, weather, description)
        VALUES ('$city', '$date_on', $temperature, $pressure, $humidity, $wind_speed, '$weather', '$description')";

        if ($conn->query($sql) === TRUE) {
            echo "Data inserted successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
