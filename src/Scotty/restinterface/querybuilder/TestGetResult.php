<?php
//The only purpose of this file is to check, if mysqlnd is used as a driver.
$mysqli = new mysqli("philippjenni.ch", "philippjenni_tt", "PUT RIGHT PASSWORD HERE", "philippjenni_testonai_testonai");

if ($mysqli->connect_error) {
    die("$mysqli->connect_errno: $mysqli->connect_error");
}

$query = "SELECT * FROM user where additionalInfos = ?";

$stmt = $mysqli->stmt_init();
if (! $stmt->prepare($query)) {
    print "Failed to prepare statement\n";
} else {
    $value = "Luzern";
    $stmt->bind_param("s", $value);
    
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        foreach ($row as $key => $r) {
            print "$key: $r ";
        }
        print "\n<br>";
    }
}

$stmt->close();
$mysqli->close();
?>