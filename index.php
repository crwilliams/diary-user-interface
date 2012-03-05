<?php
require_once('inc/functions.php');

function mklist() {
	$content = "<ul>";
	for($i = 0; $i < 20; $i++)
	{
		$content .= "<li>$i</li>";
	}
	$content .= "</ul>";
	return $content;
}

/* Configuration */

$pagetitle = 'Calendar';
$pagedescription = 'A Calendar Viewer';

$content = "";
$content .= "<div class='box sidebox' id='calendar-search'><div class='header'>Search by date</div><div class='content'>".mklist()."</div></div>";
$content .= "<div class='box sidebox' id='department-search'><div class='header'>Search by department</div><div class='content'>".mklist()."</div></div>";
$content .= "<div class='box sidebox' id='location-search'><div class='header'>Search by location</div><div class='content'>".mklist()."</div></div>";
$content .= "<div class='box mainbox' id='main'><div class='header'>Events Calendar<a class='right'>View all events</a></div><div class='content'>".mklist()."</div></div>";

$footerlinks[] = array("Link 1", "http://example.com");
$footerlinks[] = array("Link 2", "http://example.com");
$footerlinks[] = array("Link 3", "http://example.com");
$footerlinks[] = array("Link 4", "http://example.com");

/* End of configuration */

$footer = "<ul>";
$i = 0;
foreach($footerlinks as $link) {
	$classstr = ($i == 0) ? " class='first'" : ""; 
	$footer .= "<li$classstr><a href='".$link[1]."'>".$link[0]."</a></li>";
	$i++;
}
$footer .= "</ul>";

renderPage($pagetitle, $pagedescription, '', $content, $footer);
?>
