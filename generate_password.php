<?php
$password = 'fe123';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Password Hash: " . $hash . "\n";