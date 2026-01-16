<?php
return array (
  'db' => 
  array (
    'host' => '127.0.0.1',
    'port' => '3306',
    'dbname' => 'junk_tracker',
    'user' => 'root',
    'pass' => 'root',
    'charset' => 'utf8mb4',
  ),
  'app' => 
  array (
        'base_url' => 'http://localhost/junktracker/public',
        'env' => 'local',
        'debug' => true,
        'admin_role_min' => 90,
  ),
);
