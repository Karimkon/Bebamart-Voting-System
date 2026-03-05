<?php
// TEMPORARY DIAGNOSTIC FILE - DELETE AFTER USE
$log = dirname(__DIR__) . '/storage/logs/laravel.log';
if (!file_exists($log)) { die('No log file found at: ' . $log); }
$lines = file($log);
$last500 = array_slice($lines, -500);
echo '<pre style="font-size:11px;background:#111;color:#0f0;padding:20px;overflow-x:auto;">';
echo htmlspecialchars(implode('', $last500));
echo '</pre>';
