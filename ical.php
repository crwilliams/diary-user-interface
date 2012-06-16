<?php
require_once('inc/functions.php');

$events = loadEvents();

renderICal($events);
?>
