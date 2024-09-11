<?php 
include "config.php";  // Config file to store database credentials

$api_key = $pulse = $BPM = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    $api_key = input($_POST["api_key"]);  
    
    // Check if the API key is valid
    if ($api_key == $api_key_value) { 
        $pulse = input($_POST["pulse"]);  
        $BPM = input($_POST["BPM"]);      

        // Database connection
        $conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname); 

        // Check if connection was successful
        if ($conn->connect_error) { 
            die("Ei yhteyttä: " . $conn->connect_error);  // Return error if unable to connect
        } 

        // SQL query using prepared statements to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO YourDataBase (pulse, BPM) VALUES (?, ?)"); 
        $stmt->bind_param("ss", $pulse, $BPM);  // Bind parameters
        
        // Execute the query and check if successful
        if ($stmt->execute()) { 
            echo "Uuden tiedon tallennus onnistui.";  // Data successfully inserted
        } else { 
            echo "Virhe: " . $conn->error;  // Output SQL errors
        }

        // Close the statement and connection
        $stmt->close(); 
        $conn->close(); 
    } else { 
        echo "Väärä API Key.";  // Invalid API key
    } 
} else { 
    echo "Ei dataa.";  // No POST data received
} 

// Function to sanitize inputs 
function input($data) { 
    $data = trim($data); 
    $data = stripslashes($data); 
    $data = htmlspecialchars($data); 
    return $data; 
} 
?>
