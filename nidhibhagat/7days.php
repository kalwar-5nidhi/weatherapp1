<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Data</title>
    <link rel="stylesheet" href="7days.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
<div class="header-wrapper">
    <div class="icon cloudy-image"> 
        <img src="images/cloudy-day.png" width="50px">
    </div>
    <div class="para">
        <b><span>7 Days Historical Weather Data</span></b>
    </div>
</div>

<div class="content-wrapper">
    <section class="main">
        <div class="container">
            <?php
            // Start the session to access the weather data sent from connection.php
            session_start();
            // Check if the weather data is stored in the session
            if (isset($_SESSION['weatherData'])) {
                // Retrieve the weather data
                $weatherData = $_SESSION['weatherData'];

                // Now, you can use $weatherData as needed
                $searchedCity = $weatherData['city'];
            } else {
                // Data not found in the session
                echo "<h1>No weather data available.</h1>";
                // You can provide a message or take appropriate action here.
            }
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            
            // Database connection settings
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

            // Get today's date

            $today = date('Y-m-d');
            $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
            
            // Modify your SQL query based on $searchedCity to fetch data for the past 7 days
            $sql = "SELECT MAX(id) AS id, city, MAX(date_on) AS date_on, MAX(temperature) AS temperature, MAX(description) AS description, MAX(pressure) AS pressure, MAX(humidity) AS humidity, MAX(wind_speed) AS wind_speed
                    FROM temperature_history
                    WHERE city = '$searchedCity' AND date_on BETWEEN '$sevenDaysAgo' AND '$today'
                    GROUP BY date_on, city
                    ORDER BY date_on DESC
                    LIMIT 0,7";
            

            $result = $conn->query($sql);

            // Check if there are any rows returned from the query
            if ($result->num_rows > 0) {
                $index = 0;
                while ($row = $result->fetch_assoc()) {
                    $index = $index + 1;

                    // Display weather data for each day
                    echo "<div class='boxes'>";
                    echo "<div class='box$index'>";
                    echo "<div class='day'>";
                    echo "<h2 class='city date1'>" . $row['city'] . "</h2>";
                    echo "<h3 class='date1'>" . $row['date_on'] . "</h3>";
                    echo "</div>";

                    // Display temperature and different weather icons based on weather condition
                    echo "<div class='main-data'>";
                    // Icon URLs can be stored in an associative array for cleaner code
                    $weatherIcons = [
                        "overcast clouds" => "images/clouds.png",
                        "haze" => "images/rain.png",
                        "moderate rain" => "images/rain.png",
                        "light rain" => "images/rain.png",
                        "Moderate rain" => "images/rain.png",
                        "clear sky" => "images/clear.png",
                        "intensity rain" => "images/rain.png",
                        "few clouds" => "images/mist.png",
                        "broken clouds" => "images/broken.png",

                    ];

                    $weatherDescription = $row['description'];

                    // Check if the weather description exists in the array, and display the corresponding icon
                    if (isset($weatherIcons[$weatherDescription])) {
                        $iconUrl = $weatherIcons[$weatherDescription];
                        echo "<img src='$iconUrl' alt='$weatherDescription' class='weather-icon1'>";
                    } else {
                        // If the weather description doesn't match any in the array, display a default icon or text
                        echo "No icon available"; // You can customize this as needed
                    }

                    echo "<h3 class='temp1'>" . $row['temperature'] . "°C</h3>";
                    echo "<p class='desc1'>" . $row['description'] . "</p>";
                    echo "</div>";
                    echo "<div class='data'>";
                    echo "<div class='icondata1'>";
                    echo "<i class='fas fa-tachometer-alt'></i>";
                    echo "<p class='pressure1'>" . $row['pressure'] . "hpa</p>";
                    echo "</div>";
                    echo "<div class='icondata2'>";
                    echo "<i class='fas fa-tint'></i>";
                    echo "<p class='humidity1'>" . $row['humidity'] . "%</p>";
                    echo "</div>";
                    echo "<div class='icondata3'>";
                    echo "<i class='fas fa-wind'></i>";
                    echo "<p class='wind1'>" . $row['wind_speed'] . "km/h</p>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p>No weather data available for the last 7 days.</p>";
            }

            // Close the database connection
            mysqli_close($conn);
            ?>
        </div>
        <div class="button">
            <div class="btn">
                <a class="view-more" href="index.html" style="color: aliceblue;"> Back</a>
            </div>
        </div>
    </section>
</div>
</body>
</html>
