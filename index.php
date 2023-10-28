<?php
/*
==============================
===== FORTIGATE SENTINEL =====
==============================
Author: Lukáš Tesař; tlukas.eu
Version: 1.2
Description: Script for parsing IP greylist by CZ.NIC to Fortigate format.

https://view.sentinel.turris.cz  
   
*/

// Function for log access
function logAccess($ip, $request)
{
    $date = date('d-m-Y H:i:s');
    $dateLog = date('Y-m-d');
    
    $log = $date . " - " . $ip . " [".$_SERVER['HTTP_USER_AGENT']."]" ." - " . $request . "\n";
    $logFile = "./logs/access-".$dateLog.".log";
    
    $file = fopen($logFile, 'a');
    fwrite($file, $log);
    fclose($file);
}

// Get data from Sentinel
$sourceData = fopen("https://view.sentinel.turris.cz/greylist-data/greylist-latest.csv", "r");

// Read data
for ($i = 0; $row = fgetcsv($sourceData); $i++) 
{
    // Each IP on new line (skip first two lines)
    if ($i > 1)
      echo $row[0] . "\n";
}

fclose($sourceData);

logAccess($_SERVER['REMOTE_ADDR'], $_SERVER['REQUEST_URI']);
?>
