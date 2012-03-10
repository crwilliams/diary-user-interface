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

$eventdates = array();
foreach(array_keys($events) as $d)
{
	$eventdates[] = '"'.(int)substr($d, 8, 2).'-'.(int)substr($d, 5, 2).'-'.(int)substr($d, 0, 4).'"';
}

$content = "";
$content .= "<script>var availableDates = [".implode(',', $eventdates)."]</script>";
$content .= "<div class='box sidebox' id='calendar-search'><div class='header'>Search by date</div><div class='content'></div></div>";
$content .= "<div class='box sidebox' id='department-search'><div class='header'>Search by department</div><div class='content'>".getOptionTree($organisers, 'org', 'All departments', 'getOrganisationTreeOptions', $graph)."</div></div>";
$content .= "<div class='box sidebox' id='location-search'><div class='header'>Search by location</div><div class='content'>".getOptionTree($places, 'place', 'All locations')."</div></div>";
$content .= "<div class='box mainbox' id='main'><div class='header'><h1>Events Calendar</h1><a class='right'>View all events</a></div><div class='content'><h2>Upcoming Events</h2>".getEventListings($events)."</div></div>";

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
