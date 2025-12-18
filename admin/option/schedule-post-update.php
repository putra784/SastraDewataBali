<?php

require '../../src/php/function.php';

$id = $_POST['id'];
$schedule_time = $_POST['schedule_time'];

$sql = "UPDATE post SET scheduled_at = ?, status = 'scheduled' WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $schedule_time, $id);
$stmt->execute();

header("Location: ../schedule.php");
exit;