// API key and URL for OpenWeatherMap API
const apikey = "70b4d31df8c73da1b55d37dadcb50b80";
const apiUrl = "https://api.openweathermap.org/data/2.5/weather?units=metric";

// Get DOM elements
const searchBox = document.querySelector(".search-container input"); // Search input
const searchBtn = document.querySelector(".search-container button"); // Search button
const weatherIcon = document.querySelector(".icons-image"); // Weather icon
const adviceMessage = document.querySelector(".advice-message"); // Advice message element

// Function to handle city search
function handleCitySearch() {
  const searchedCity = searchBox.value.trim();
  if (searchedCity) {
    console.log("Data is being accessed for city search.")
    checkWeather(searchedCity); // Call weather check function

    // Store the searched city in local storage
    localStorage.setItem("lastSearchedCity", searchedCity);
  } else {
    alert("Please provide a city name to get the weather information.");
  }
}

// Attach click event listener to search button
searchBtn.addEventListener("click", handleCitySearch);

// Event listener for Enter key press in the search input field
searchBox.addEventListener("keypress", function (event) {
  if (event.key === "Enter") {
    event.preventDefault();
    handleCitySearch();
  }
});

// Function to fetch and display weather data
    async function checkWeather(city) {
        try {
          // Attempt to retrieve cached data from local storage
          const cachedWeatherData = localStorage.getItem(city);
          if (cachedWeatherData) {
            console.log("Data is being accessed from local storage");
            // Check if the cached data is already an object
            const data = typeof cachedWeatherData === "string" ? JSON.parse(cachedWeatherData) : cachedWeatherData;
            // If cached data is available, display it
            displayWeatherData(data);
            return;
          }
      
          // The rest of your code remains unchanged...
      

    // Fetch weather data from the API
    const response = await fetch(`${apiUrl}&q=${city}&appid=${apikey}`);
    const data = await response.json();

    if (data.cod === "404") {
      console.log("Data is being accessed from the API due to a 404 response.")
      alert("The city you entered is not available. Please try again.");
      return;
    }

    // Update weather details on the page
    console.log("Data is being accessed from the API. ")
    displayWeatherData(data);

    // Store weather data in local storage for future use
    localStorage.setItem(city, JSON.stringify(data));

    // Rest of the code to send data to PHP script if needed
  } catch (error) {
    console.log("Error fetching weather data:", error);
  }
}



// Function to display weather data
function displayWeatherData(data) {
  // Update weather details on the page
  document.querySelector(".city-name").textContent = data.name;
  document.querySelector(".main-temp").textContent =
    Math.round(data.main.temp) + "°C";
  document.querySelector(".pressure-value").textContent = data.main.pressure + "hPa";
  document.querySelector(".humidity-value").textContent = data.main.humidity + "%";
  document.querySelector(".wind-value").textContent = data.wind.speed + " km/h";

  // Get and display weather condition
  const weatherDescription = data.weather[0].description;
  const cityConditionElement = document.querySelector(".city-condition");
  cityConditionElement.textContent =
    weatherDescription.charAt(0).toUpperCase() +
    weatherDescription.slice(1);

  // Update weather icon based on weather condition
  if (data.weather[0].main == "Clouds") {
    weatherIcon.src = "images/clouds.png";
  } else if (data.weather[0].main == "Rain") {
    weatherIcon.src = "images/rain.png";
  } else if (data.weather[0].main == "Clear") {
    weatherIcon.src = "images/clear.png";
  } else if (data.weather[0].main == "Drizzle") {
    weatherIcon.src = "images/drizzle.png";
  } else if (data.weather[0].main == "Mist") {
    weatherIcon.src = "images/mist.png";
  }

  // Format and display current date
  const currentDate = new Date();
  const options = {
    weekday: "long",
    year: "numeric",
    month: "long",
    day: "numeric",
  };
  const formattedDate = currentDate.toLocaleDateString("en-US", options);
  document.querySelector(".date-value").textContent = formattedDate;

  // Update advice message based on weather conditions
  adviceMessage.textContent = ""; // Reset advice message
  const weatherConditions = [
    { condition: "Rain", advice: "Weather Message : It's raining, take an umbrella with you" },
    { condition: "Clear", advice: "Weather Message : The weather is clear. Enjoy the sunny day!" },
    { condition: "Clouds", advice: "Weather Message : It might rain. Be careful" },
    { condition: "Drizzle", advice: "Weather Message : Light rain is falling. Take a jacket with you" },
    { condition: "Mist", advice: "Weather Message : Watch out for misty conditions" },
  ];

  const currentWeatherCondition = data.weather[0].main;
  const advice = weatherConditions.find(condition => condition.condition === currentWeatherCondition);
  if (advice) {
    adviceMessage.textContent = advice.advice;
  }

  // Prepare weather data for sending to PHP script
  const weatherData = {
    city: data.name,
    date_on: currentDate,
    temperature: Math.round(data.main.temp),
    pressure: data.main.pressure,
    humidity: data.main.humidity,
    wind_speed: data.wind.speed,
    weather: data.weather[0].main,
    description: data.weather[0].description,
  };
  console.log(weatherData);
  
  // Send weather data to the PHP script using AJAX
  fetch("connection.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(weatherData),
  })
    .then((response) => response.text())
    .then((data) => {
      console.log(data);
      // Store the weather data in a session variable
      sessionStorage.setItem("weatherData", JSON.stringify(weatherData));
    })
    .catch((error) => console.error("Error sending data to PHP:", error));
}

// Show weather information for the default city (Havant) on page load
window.addEventListener("DOMContentLoaded", () => {
  if (!navigator.onLine) {
    alert("You are currently offline. Please check your internet connection.");
    return;
  }
  checkWeather("Kirklees");
});