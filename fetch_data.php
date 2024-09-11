<?php 
include "config.php"; 

// Connect to the database
$db = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname); 

if (!$db) { 
    die("Connection failed: " . mysqli_connect_error()); 
} 

// Check the total number of records
$query = "SELECT COUNT(*) as totalRecords FROM YourDataBase"; 
$result = mysqli_query($db, $query); 

if ($result) {
    $row = mysqli_fetch_assoc($result); 
    $totalRecords = $row['totalRecords']; 
} else {
    die("Query failed: " . mysqli_error($db));
}

// If records exceed 100, delete the oldest ones
if ($totalRecords >= 100) { 
    $deleteQuery = "DELETE FROM YourDatabase ORDER BY PvmAika LIMIT 20";  // Delete 20 oldest records
    if (!mysqli_query($db, $deleteQuery)) {
        die("Deletion failed: " . mysqli_error($db));
    }
}

// Fetch the latest 20 records from the database
$query = "SELECT PvmAika, Pulse as `Pulssi` FROM YourDataBase ORDER BY PvmAika DESC LIMIT 20"; 
$result = mysqli_query($db, $query); 

if ($result) {
    $data = array(); 
    while ($row = mysqli_fetch_assoc($result)) { 
        $data[] = array($row["PvmAika"], (int)$row["Pulssi"]); 
    }
    echo json_encode(array_reverse($data));  // Reverse to get the oldest data first
} else {
    die("Data fetch failed: " . mysqli_error($db));
}

// Close the database connection
mysqli_close($db); 
?>
