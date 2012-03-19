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

$organisers = array();
$places = array();
$events = array();
$graph = loadGraph($organisers, $places, $events);

$content = "";
$content .= "<script>var availableDates = []</script>";
$content .= "<div class='box sidebox' id='calendar-search'><div class='header'>Search by date</div><div class='content'><a href='#' id='alldates' class='view-events homepage-only'>View full calendar</a></div></div>";
$content .= "<div class='box sidebox' id='department-search'><div class='header'>Search by department</div><div class='content'>".getOptionTree($organisers, 'org', 'All departments', 'getOrganisationTreeOptions', $graph)."</div></div>";
$content .= "<div class='box sidebox' id='location-search'><div class='header'>Search by location</div><div class='content'>".getOptionTree($places, 'place', 'All locations')."</div></div>";
$content .= "<div class='box mainbox' id='main'><div class='header'><h1>Events Calendar</h1><a id='view-all' class='view-events right'>View all events<img style='height:100%' src='img/arrowr.png' /></a></div><div class='content'><b class='homepage-only'>Upcoming Events</b>".getEventListings($events)."<a href='#' class='view-events right homepage-only'>View all events</a></div></div>";

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
