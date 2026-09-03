<?php
// Run once at: yoursite.com/admin/make-hash.php  — DELETE after use!
$password = 'WhhP8229*';
echo password_hash($password, PASSWORD_BCRYPT);
?>