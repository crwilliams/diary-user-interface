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
	<img id='bg' src='img/background<?php echo $i ?>.jpg' />
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
				$places[md5((string)$site)] = $site->label();
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
	$str .= "<select id='$id' onchange='showCats()'>\n";
	$str .= "\t<option value='event'>($showAllString)</option>\n";
	if($processOptions == null) {
		foreach($values as $key => $name) {
			$str .= "\t<option value='$key'>$name</option>\n";
		}
	} else {
		$str .= $processOptions($graph, $values);
	}
	$str .= "</select>\n";
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
		$str .= getOrganisationTreeOptions($graph, $values, $orgtree[md5('http://id.southampton.ac.uk/')]);
	}
	if(!isset($node['children'])) {
		return $str;
	}
	foreach($node['children'] as $key => $d) {
		$str .= "\t<option value='$key'>";
		for($i = 0; $i < $depth; $i++) {
			$str .= "- ";
		}
		$str .= $d['name']."</option>\n";
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
				$tree[md5((string)$node)]['children'][$k] = $v;
			}
		}
	}
	if(count($tree) > 0 || in_array(md5((string)$node), $filter))
	{
		@uasort($tree[md5((string)$node)]['children'], 'sortOrgTree');
		$tree[md5((string)$node)]['name'] = $node->label();
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
			$organisers[md5((string)$agent)] = $agent->label();
			while($agent->has("-http://www.w3.org/ns/org#hasSubOrganization"))
			{
				$agent = $agent->get("-http://www.w3.org/ns/org#hasSubOrganization");
				$organisers[md5((string)$agent)] = $agent->label();
			}
		}
	}
}

function loadGraph(&$organisers, &$places)
{
	require_once( "/var/wwwsites/phplib/arc/ARC2.php" );
	require_once( "/var/wwwsites/phplib/Graphite.php" );
	$graph = Graphite::thaw( "/home/diary/var/data.php" );
	
	$graph->cacheDir("/home/diary/diary.soton.ac.uk/cache");

	$graph->ns( "event", "http://purl.org/NET/c4dm/event.owl#" );
	$graph->ns( "tl", "http://purl.org/NET/c4dm/timeline.owl#" );

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
			continue;
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
?>
