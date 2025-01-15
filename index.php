<?php
/*
==============================
===== FORTIGATE SENTINEL =====
==============================
Author: Lukáš Tesař; tlukas.eu
Version: 1.4
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

// Function for get client IP
function getIP() 
{
    return $_SERVER['HTTP_X_FORWARDED_FOR']
        ?? $_SERVER['REMOTE_ADDR']
        ?? $_SERVER['HTTP_CLIENT_IP']
        ?? '';
}

// Configure options (IPv4 socket)
$options  = array("http" => array("user_agent" => "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.157 Safari/537.36"), "socket" => array("bindto" => "0:0",),);
$context  = stream_context_create($options);
$response = file_get_contents("https://view.sentinel.turris.cz/greylist-data/greylist-latest.csv", false, $context);

// Save Sentinel data locally
file_put_contents("./greylist-latest.csv", $response);

// Get data from Sentinel
$sourceData = fopen("./greylist-latest.csv", "r");

// Read data
for ($i = 0; $row = fgetcsv($sourceData); $i++) 
{
    // Each IP on new line (skip first two lines)
    if ($i > 1)
      echo $row[0] . "\n";
}

fclose($sourceData);

logAccess(getIP() , $_SERVER['REQUEST_URI']);
?>
