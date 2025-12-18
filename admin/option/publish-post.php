<?php

require '../../src/php/function.php';

$id = $_POST['id'];

query("UPDATE post SET status='published', scheduled_at=NULL WHERE id=$id");

header("Location: ../pending.php");

exit;