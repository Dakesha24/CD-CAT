<?php
$password = 'password';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Password Hash: " . $hash . "\n";