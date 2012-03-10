function unavailable(date) {
  dmy = date.getDate() + "-" + (date.getMonth()+1) + "-" + date.getFullYear();
  if ($.inArray(dmy, availableDates) == -1) {
    return [false,"","No events listed for " + dmy];
  } else {
    return [true, ""];
  }
}

var selDate = '';
var selOrg = '';
var selPlace = '';
var fullhash = '';

function processHash() {
	var hashparts = fullhash.substring(1).split('/');
	for(var i = 0; i < hashparts.length; i++) {
		console.log(hashparts[i]);
		if(hashparts[i].substring(0, 4) == 'org-') {
			selOrg = hashparts[i];
			console.log('org: ' + selOrg);
		}
		else if(hashparts[i].substring(0, 5) == 'site-') {
			selPlace = hashparts[i];
			console.log('place: ' + selPlace);
		}
		else if(hashparts[i].length == 10 && hashparts[i][2] == '-' && hashparts[i][5] == '-') {
			selDate = hashparts[i];
			console.log('date: ' + selDate);
		}
	}
	setHash();
	
}

function setDate(str) {
	if(str == '')
		selDate = '';
	else
		selDate = str.substring(3,5) + '-' + str.substring(0,2) + '-' + str.substring(6,10);
	setHash();
}

function setOrg(str) {
	selOrg = str;
	setHash();
}

function setPlace(str) {
	selPlace = str;
	setHash();
}

function setHash() {
	var str = "";
	if(selOrg != '')
		str += '/' + selOrg;
	if(selPlace != '')
		str += '/' + selPlace;
	if(selDate != '')
		str += '/' + selDate;
	window.location.hash = str.substring(1);
}

$(function() {
	$( "#calendar-search div.content" ).datepicker({ beforeShowDay: unavailable, dayNamesMin: ['S', 'M', 'T', 'W', 'T', 'F', 'S'], firstDay: 1, onSelect: function(dateText, inst) { setDate(dateText); } });
	$( "ul#org a" ).click(function(e) {
		setOrg(e.target.id);
		return false;
	});
	$( "ul#place a" ).click(function(e) {
		setPlace(e.target.id);
		return false;
	});
	$( "#alldates" ).click(function(e) {
		setDate('');
		return false;
	});
});

$(window).hashchange( function(){
	if(location.hash != fullhash)
	{
		fullhash = location.hash;
		processHash();
	}
});

$(window).hashchange();
