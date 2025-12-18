<?php
require '../../src/php/function.php';

$id = $_POST['id'];
$schedule = $_POST['schedule_time'];

query("UPDATE post SET status='scheduled', scheduled_at='$schedule' WHERE id=$id");

header("Location: ../pending.php");
exit;
