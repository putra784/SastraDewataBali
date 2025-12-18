<?php
session_start();
require '../../src/php/function.php';

$id = $_POST['id'];

query("DELETE FROM post WHERE id=$id");

header("Location: ../uploaded-post.php");

exit;