<?php
require_once('inc/functions.php');

/* Configuration */

$pagetitle = 'Calendar';
$pagedescription = 'A Calendar Viewer';

$content = "";
$content .= "<div class='sidebox' id='calendar-search'><div class='header'>Search by date</div><div class='content'></div></div>";
$content .= "<div class='sidebox' id='department-search'><div class='header'>Search by department</div><div class='content'></div></div>";
$content .= "<div class='sidebox' id='location-search'><div class='header'>Search by location</div><div class='content'></div></div>";
$content .= "<div class='mainbox' id='main'><div class='header'>Events Calendar<a class='right'>View all events</a></div><div class='content'></div></div>";

$footerlinks[] = array("Link 1", "http://example.com");
$footerlinks[] = array("Link 2", "http://example.com");
$footerlinks[] = array("Link 3", "http://example.com");
$footerlinks[] = array("Link 4", "http://example.com");

/* End of configuration */

$footer = "<ul>";
foreach($footerlinks as $link) {
	$footer .= "<li><a href='".$link[1]."'>".$link[0]."</a></li>";
}
$footer .= "</ul>";

renderPage($pagetitle, $pagedescription, '', $content, $footer);
?>
