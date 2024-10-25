<?php
require '../models/User.php';
session_start();

$user = new User($_SESSION['user_id']);

echo $user->profile_image_url;
// return $_SESSION['profile_photo'];
