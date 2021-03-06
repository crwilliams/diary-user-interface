<?
function renderPage($title, $description, $header, $content, $footer)
{
	$i = rand(1, 4);
?><!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!-- Consider adding a manifest.appcache: h5bp.com/d/Offline -->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">

  <title><?php echo $title ?></title>
  <meta name="description" content="<?php echo $description ?>">

  <!-- Mobile viewport optimized: h5bp.com/viewport -->
  <meta name="viewport" content="width=device-width">

  <link rel="stylesheet" href="css/style.css">

  <!-- More ideas for your <head> here: h5bp.com/d/head-Tips -->

  <!-- All JavaScript at the bottom, except this Modernizr build.
       Modernizr enables HTML5 elements & feature detects for optimal performance.
       Create your own custom Modernizr build: www.modernizr.com/download/ -->
  <script src="js/libs/modernizr-2.5.3.min.js"></script>
</head>
<body class="rand<?php echo $i ?>">
  <!-- Prompt IE 6 users to install Chrome Frame. Remove this if you support IE 6.
       chromium.org/developers/how-tos/chrome-frame-getting-started -->
  <!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->
  <header>
<?php echo $header ?>
  </header>
  <div role="main">
	<img id='bg' src='img/background<?php echo $i ?>.jpg' alt='background image' />
	<img id='altbg' src='img/altbackground.jpg' alt='background image' />
<?php echo $content ?>
  </div>
  <footer>
<?php echo $footer ?>
  </footer>


  <!-- JavaScript at the bottom for fast page loading -->

  <!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="js/libs/jquery-1.7.1.min.js"><\/script>')</script>
  <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script>
  <script src="js/jquery.ba-hashchange.min.js"></script>

  <!-- scripts concatenated and minified via build script -->
  <script src="js/plugins.js"></script>
  <script src="js/script.js"></script>
  <script src="js/diary.js"></script>
  <!-- end scripts -->

  <!-- Asynchronous Google Analytics snippet. Change UA-XXXXX-X to be your site's ID.
       mathiasbynens.be/notes/async-analytics-snippet -->
  <script>
    var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];
    (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
    g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
    s.parentNode.insertBefore(g,s)}(document,'script'));
  </script>
</body>
</html>
<?
}

/**
 * Get the location of an event.
 *
 */
function getPlaces(&$places, $event)
{
	if( $event->has( "event:place" ) )
	{
		foreach( $event->all( "event:place" ) as $place )
		{
			$site = getSite($place);
			if($site != null)
			{
				$places[sid((string)$site)] = $site->label();
			}
		}
	}
}

/**
 * Get the site that a place belongs to.
 *
 */
function getSite($place)
{
	if($place->isType("http://www.w3.org/ns/org#Site"))
	{
		return $place;
	}
	else if($place->has("http://data.ordnancesurvey.co.uk/ontology/spatialrelations/within"))
	{
		return getSite($place->get("http://data.ordnancesurvey.co.uk/ontology/spatialrelations/within"));
	}
	else
	{
		return null;
	}
}

/**
 * Print a drop-down box to select from a set of values.
 *
 */
function getOptionTree($values, $id, $showAllString, $processOptions = null, $graph = null) {
	asort($values);
	$str = "";
	$str .= "<ul id='$id'>\n";
	$str .= "\t<li><a href='#' id='link-all-$id'>$showAllString</a></li>\n";
	if($processOptions == null) {
		foreach($values as $key => $name) {
			$str .= "\t<li><a href='#' id='link-$key'>".htmlspecialchars($name)."</a></li>\n";
		}
	} else {
		$str .= $processOptions($graph, $values);
	}
	$str .= "</ul>\n";
	return $str;
}

/**
 * Print a set of options representing the organisational structure.
 *
 */
function getOrganisationTreeOptions($graph, $values, $node = null, $depth = 0) {
	$str = "";
	if($node == null) {
		$orgtree = getOrganisationTree($graph->resource("http://id.southampton.ac.uk/"), array_keys($values));
		$str .= getOrganisationTreeOptions($graph, $values, $orgtree[sid('http://id.southampton.ac.uk/')]);
	}
	if(!isset($node['children'])) {
		return $str;
	}
	foreach($node['children'] as $key => $d) {
		$str .= "\t<li>";
		for($i = 0; $i < $depth; $i++) {
			$str .= "- ";
		}
		$str .= "<a href='#' id='link-$key'>".htmlspecialchars($d['name'])."</a></li>\n";
		$str .= getOrganisationTreeOptions($graph, $values, $d, $depth + 1);
	}
	return $str;
}

/**
 * Get the organisation tree, rooted at the given node, filtered according to the filter.
 *
 */
function getOrganisationTree($node, $filter)
{
	$tree = array();
	foreach($node->all("http://www.w3.org/ns/org#hasSubOrganization") as $child)
	{
		$subtree = getOrganisationTree($child, $filter);
		if(count($subtree) > 0)
		{
			foreach($subtree as $k => $v)
			{
				$tree[sid((string)$node)]['children'][$k] = $v;
			}
		}
	}
	if(count($tree) > 0 || in_array(sid((string)$node), $filter))
	{
		@uasort($tree[sid((string)$node)]['children'], 'sortOrgTree');
		$tree[sid((string)$node)]['name'] = $node->label();
	}
	return $tree;
}

/**
 * Compare elements in the organisation tree.
 *
 */
function sortOrgTree($a, $b) {
	if($a['name'] == $b['name']) return 0;
	return ($a['name'] < $b['name']) ? -1 : 1;
}

/**
 * Get the organisers of an event.
 *
 */
function getOrganisers(&$organisers, $event)
{
	if( $event->has( "event:agent" ) )
	{
		foreach( $event->all( "event:agent" ) as $agent )
		{
			if(!$agent->isType("http://www.w3.org/ns/org#Organization"))
			{
				continue;
			}
			$organisers[sid((string)$agent)] = $agent->label();
			while($agent->has("-http://www.w3.org/ns/org#hasSubOrganization"))
			{
				$agent = $agent->get("-http://www.w3.org/ns/org#hasSubOrganization");
				$organisers[sid((string)$agent)] = $agent->label();
			}
		}
	}
}

function getGraph()
{
	require_once( "/var/wwwsites/tools/arc2/ARC2.php" );
	require_once( "/var/wwwsites/tools/Graphite/Graphite.php" );
	$graph = Graphite::thaw( "/home/diary/var/data.php" );
	
	$graph->cacheDir("/home/diary/diary.soton.ac.uk/cache");

	$graph->ns( "event", "http://purl.org/NET/c4dm/event.owl#" );
	$graph->ns( "tl", "http://purl.org/NET/c4dm/timeline.owl#" );
	return $graph;
}

function loadEvents()
{
	foreach(getGraph()->allOfType("event:Event") as $event)
	{
		$events[] = $event;
	}
	return $events;
}

function loadGraph(&$organisers, &$places, &$events)
{
	$graph = getGraph();

	foreach($graph->allOfType("event:Event") as $event)
	{
		if($event->has("event:time"))
		{
			foreach($event->all("event:time") as $time)
			{
				if($time->has("tl:at"))
				{
					$events[$time->getString("tl:at")]["0"][] = $time;
				}
				if($time->has("tl:start"))
				{
					$start = $time->getString("tl:start");
					$events[substr($time->getString("tl:start"), 0, 10)][substr($time->getString("tl:start"), 11, 5)][] = $time;
					if($time->has("tl:end"))
					{
						$end = $time->getString("tl:end");
					}
				}
			}
		}
	}

	ksort($events);

	foreach($events as $date => $dayevents)
	{
		if($date < date('Y-m-d'))
		{
			continue;
		}
		//echo "<div class='day'>\n";
		//echo "\t<h2>".date('l jS F Y', strtotime($date))."</h2>\n";
		ksort($dayevents);
		foreach($dayevents as $time => $timeevents)
		{
			foreach($timeevents as $eventtime)
			{
				//formatEvent($eventtime, $date);
				getOrganisers($organisers, $eventtime->get("-event:time"));
				getPlaces($places, $eventtime->get("-event:time"));
			}
		}
		//echo "</div>\n";
	}

	return $graph;
}

function getEventListings($events)
{
	$str = "";
	foreach($events as $date => $dayevents)
	{
		if($date < date('Y-m-d'))
		{
			continue;
		}
		$str .= "<div class='day' id='".date('j-n-Y', strtotime($date))."'>\n";
		$str .= "\t<h2>".date('l jS F Y', strtotime($date))."</h2>\n";
		ksort($dayevents);
		$str .= getEventListingsDay($dayevents, $date);
		$str .= "</div>\n";
	}
	return $str;
}

function getEventListingsDay($dayevents, $date)
{
	$str = "";
	$first = true;
	foreach($dayevents as $time => $timeevents)
	{
		foreach($timeevents as $eventtime)
		{
			$str .= formatEvent($eventtime, $date, $first);
			$str .= getOrganisers($organisers, $eventtime->get("-event:time"));
			$str .= getPlaces($places, $eventtime->get("-event:time"));
			$first = false;
		}
	}
	return $str;
}

$eventcounter = 0;
$eventdate = null;
/**
 * Format a single event.
 *
 */
function formatEvent($time, $date, $firstInList=false)
{
	$str = "";
	$event = $time->get("-event:time");
	$organisers = array();
	getOrganisers($organisers, $event);
	$places = array();
	getPlaces($places, $event);

	$sid = sid($time);

	global $eventcounter;
	global $eventdate;
	$eventcounter++;
	if($eventcounter <= 10 || $eventdate == $date) { $featured = "featured "; } else { $featured = ""; }
	if($eventcounter <= 10) { $eventdate = $date; }
	$starts = "";
	if( $time->has( "tl:start" ) && substr($time->getString("tl:start"), 0, 10) == $date )
	{
		$starts = " <span class='date'>(".formatTime($time->getString( "tl:start" ), $date).")</span>";
	}
	$str .= "<div class='event $featured".($firstInList?"first-event ":"").implode(" ", array_keys($organisers))." ".implode(" ", array_keys($places))."' itemscope itemtype='http://data-vocabulary.org/Event'>\n";
	$str .= "\t<h3 itemprop='summary'><img class='small-chevron' src='img/chevron_small_right.png' alt='' />".htmlspecialchars($event->label())."$starts</h3><div class='event-links'><a href='#' class='expand-link'>Read more</a>";
	if( $event->has( "foaf:homepage" ) )
	{
		$str .= " | <a href='".htmlspecialchars($event->get( "foaf:homepage" ))."' itemprop='url' target='_blank'>View event</a>";
	}
	$str .= "</div>\n";
	$str .= "\t<div class='event-more' id='".$sid."'>\n";
	$str .= "\t\t<div class='event-info'>\n";
	if( $time->has( "tl:start" ) && substr($time->getString("tl:start"), 0, 10) == $date )
	{
		$str .= "\t\t\t<div>";
		$str .= formatTime($time->getString( "tl:start" ), $date);
		if( $time->has( "tl:end" ) )
		{
			$str .= " - ".formatTime($time->getString( "tl:end" ), $date);
		}
		$str .= "</div>\n";
		$str .= "<span style='display:none' itemprop='startDate'>".$time->getString( "tl:start" )."</span>";
		if( $time->has( "tl:end" ) )
		{
			$str .= "<span style='display:none' itemprop='endDate'>".$time->getString( "tl:end" )."</span>";
		}
	}
	$str .= getEventPlaces($event, "Place");
	$str .= getEventPlaces($event, "Additional Place Info");
	$organisers = getEventAgents($event, "Organiser");
	if(count($organisers) > 0)
	{
		sort($organisers);
		$str .= "\t\t\t<div class='organisers'>Organised by: ";
		foreach($organisers as $organiser)
		{
			$str .= $organiser." ";
		}
		$str .= "</div>\n";
	}
	$str .= "\t\t</div>\n";
	//$str .= "\t<div style='clear:left'></div>\n";
	$speakers = getEventAgents($event, "Speaker");
	if(count($speakers) > 0)
	{
		$str .= "\t\t<div class='speakers'>Speaker".((count($speakers) > 1) ? "s" : "").": ";
		foreach($speakers as $speaker)
		{
			$str .= $speaker." ";
		}
		$str .= "</div>\n";
	}
	if( $event->has( "dct:description" ) )
	{
		$str .= "\t\t<div class='description' itemprop='description'>".$event->getString( "dct:description" )."</div>\n";
	}
	$str .= "\t\t<div style='clear:both'></div>\n";
	$str .= "\t</div>\n";
	$str .= "</div>\n";
	return $str;
}

function renderICal($events)
{
	echo "BEGIN:VCALENDAR\n";
	echo "VERSION:2.0\n";
	echo "PRODID:-//hacksw/handcal//NONSGML v1.0//EN\n";
	echo "X-WR-CALNAME:University of Southampton Events\n";
	echo "X-WR-CALDESC:University of Southampton Events\n";
	foreach($events as $event)
	{
		if($event->has("event:time"))
		{
			foreach($event->all("event:time") as $time)
			{
				echo formatEventICal($event, $time);
			}
		}
	}
	echo "END:VCALENDAR";
}

function fold($str)
{
	$str = str_replace(array("\r", "\n"), "", $str);
	$str = preg_replace("/[^a-zA-Z0-9:;\/=_. -]/", "", $str);
	if(strlen($str) < 72)
	{
		return $str."\n";
	}
	else
	{
		return substr($str, 0, 72)."\n ".fold(substr($str, 72));
		
	}
}

function formatDateICal($date)
{
	return substr(str_replace(array(":", "-", " "), array("", "", "T"), $date), 0, 15);
}

/**
 * Format a single event, in iCal format
 *
 */
function formatEventICal($event, $time)
{
	/*
	$firststart = "Z";
	if($event->has("event:time"))
	{
		foreach($event->all("event:time") as $time)
		{
			if($time->has("tl:at"))
			{
				//$eventstr .= fold("RDATE;VALUE=DATE;TZID=Europe/London:".formatDateICal($time->getString("tl:at")));
				$date = formatDateICal($time->getString("tl:at"))."/P1D";
				$eventstr .= fold("RDATE;VALUE=PERIOD;TZID=Europe/London:".$date);
				$firststart = min($firststart, $date);
			}
			if($time->has("tl:start"))
			{
				if($time->has("tl:end"))
				{
					$date = formatDateICal($time->getString("tl:start"))."/".formatDateICal($time->getString("tl:end"));
					$eventstr .= fold("RDATE;VALUE=PERIOD;TZID=Europe/London:".$date);
					$firststart = min($firststart, $date);
				}
				else
				{
					//$eventstr .= fold("RDATE;VALUE=DATE-TIME;TZID=Europe/London:".formatDateICal($time->getString("tl:start")));
					$date = formatDateICal($time->getString("tl:start"))."/PT1H";
					$eventstr .= fold("RDATE;VALUE=PERIOD;TZID=Europe/London:".$date);
					$firststart = min($firststart, $date);
				}
			}
		}
	}
	else
	{
		return "";
	}
	*/

	if($time->has("tl:at"))
	{
		$date = formatDateICal($time->getString("tl:at"))."/P1D";
	}
	if($time->has("tl:start"))
	{
		if($time->has("tl:end"))
		{
			$date = formatDateICal($time->getString("tl:start"))."/".formatDateICal($time->getString("tl:end"));
			if(strlen($date) == 31 && substr($date, 9, 6) == "000000" && substr($date, 25, 6) == "000000")
			{
				$date = substr($date, 0, 8)."/".substr($date, 16, 8);
			}
		}
		else
		{
			$date = formatDateICal($time->getString("tl:start"))."/PT1H";
		}
	}

	$str  = "BEGIN:VEVENT\n";
	$str .= fold("UID:".(string)($time));
	$str .= fold("SUMMARY:".$event->label());
	if( $event->has( "dct:description" ) )
	{
		$str .= fold("DESCRIPTION:".$event->getString( "dct:description" ));
	}
	//$str .= "GEO:".$event->label()."\n";
	if( $event->has( "event:place" ) )
	{
		foreach( $event->all( "event:place" ) as $place )
		{
			//echo $place->dump();
			$str .= fold("LOCATION:".$place->label());
		}
	}
	if( $event->has( "foaf:homepage" ) )
	{
		$str .= fold("URL:".$event->get( "foaf:homepage" ));
	}
	//$str .= "ORGANIZER:".$event->label()."\n";
	$date = explode("/", $date);
	$str .= fold("DTSTART;TZID=Europe/London:".$date[0]);
	if($date[1][0] == 'P')
	{
		$str .= fold("DURATION:".$date[1]);
	}
	else
	{
		if(strlen($date[1]) == 8)
		{
			$t = new DateTime(substr($date[1], 0, 4).'/'.substr($date[1], 4, 2).'/'.substr($date[1], 6, 2));
			$t->add('P1D');
			$date[1] = $t->format('Ymd');
			echo "____";
		}
		$str .= fold("DTEND;TZID=Europe/London:".$date[1]);
	}
	//$str .= $eventstr;
	$str .= "END:VEVENT\n";
	return $str;
}

/**
 * Format a time.
 *
 */
function formatTime($time, $date) {
	if(substr($time, 0, 10) != $date)
	{
		return substr($time, 0, 10)." ".substr($time, 11, 5);
	}
	return substr($time, 11, 5);
}

/**
 * Get all places associated with an event.
 *
 */
function getEventPlaces($event, $filter=null)
{
	$str = "";
	if( $event->has( "event:place" ) )
	{
		foreach( $event->all( "event:place" ) as $place )
		{
			if($place->isType("http://vocab.deri.ie/rooms#Room") || $place->isType("http://vocab.deri.ie/rooms#Building") || $place->isType("http://www.w3.org/ns/org#Site"))
				$type = "Place";
			else
				$type = "Additional Place Info";
			if(!is_null($filter) && $filter != $type)
				continue;
			$typel = $type.": ";
			if($type == "Place")
			{
				$typel = "at ";
			}
			elseif($type == "Additional Place Info")
			{
				$style = "";
			}
			if($place->label() == '[NULL]')
			{
				$str .= "\t\t<div>$typel".$place->link()."</div>\n";
			}
			else
			{
				$str .= "\t\t<div>$typel".getPlaceLabel($place)."</div>\n";
			}
		}
	}
	return $str;
}

function getPlaceLabel($place)
{
	$str = "";
	// Try to get a rdfs:label which is not simply the building/room number.
	foreach($place->all("rdfs:label") as $label)
	{
		if(!preg_match('/^[0-9]+[A-Z] \/ [0-9]+$/', $label))
		{
			if(substr($place, 0, 34) == 'http://id.southampton.ac.uk/event/')
				$str = htmlspecialchars($label);
			else
				$str = "<a href='".htmlspecialchars($place)."' target='_blank'>" . htmlspecialchars($label) . "</a>";
		}
	}
	// If that fails, use any label.
	if($str == "")
	{
		if(substr($place, 0, 34) == 'http://id.southampton.ac.uk/event/')
			$str = htmlspecialchars($place->label());
		else
			$str = "<a href='".htmlspecialchars($place)."' target='_blank'>" . htmlspecialchars($place->label()) . "</a>";
	}
	if($place->has("http://data.ordnancesurvey.co.uk/ontology/spatialrelations/within"))
	{
		$within = $place->get("http://data.ordnancesurvey.co.uk/ontology/spatialrelations/within");
		$str .= ",<br/>".getPlaceLabel($within);
	}
	return $str;
}

/**
 * Get the agents related to an event.
 *
 */
function getEventAgents($event, $filter=null)
{
	$agents = array();
	if( ! $event->has( "event:agent" ) ) { return $agents; }

	foreach( $event->all( "event:agent" ) as $agent )
	{
		if($agent->isType("http://www.w3.org/ns/org#Organization"))
		{
			$type = "Organiser";
		}
		else
		{
			$type = "Speaker";
		}

		if(!is_null($filter) && $filter != $type) { continue; }

		if( !$agent->hasLabel() )
		{
			$agents[] = $agent->link();
		}
		else
		{
			if($agent->has("foaf:homepage"))
			{
				$agents[] = "<a href='".htmlspecialchars($agent->get("foaf:homepage"))."' target='_blank'>".htmlspecialchars($agent->label())."</a>";
			}
			else
			{
				$agents[] = htmlspecialchars($agent->label());
			}
		}
	}
	return $agents;
}

function sid($str)
{
	return strtolower(preg_replace('/[^A-Za-z0-9]/', '-', str_replace('http://id.southampton.ac.uk/', '', $str)));
}

?>
