<?php
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script Filename: " . $_SERVER['SCRIPT_FILENAME'] . "<br>";
echo "Current Dir: " . __DIR__ . "<br>";
echo "<br>";
echo "style.css exists: " . (file_exists(__DIR__ . '/style.css') ? 'YES' : 'NO') . "<br>";
echo "admin-sidebar.css exists: " . (file_exists(__DIR__ . '/admin-sidebar.css') ? 'YES' : 'NO') . "<br>";
