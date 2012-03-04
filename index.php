<?php
require_once('inc/functions.php');

/* Configuration */

$pagetitle = 'Calendar';
$pagedescription = 'A Calendar Viewer';

$content = "";

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
