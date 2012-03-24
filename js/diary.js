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
var fullhash = 'null';

function processHash() {
	var hashparts = fullhash.substring(1).split('/');
	for(var i = 0; i < hashparts.length; i++) {
		if(hashparts[i].substring(0, 4) == 'org-') {
			selOrg = hashparts[i];
		}
		else if(hashparts[i].substring(0, 5) == 'site-') {
			selPlace = hashparts[i];
		}
		else if(hashparts[i].length == 10 && hashparts[i][2] == '-' && hashparts[i][5] == '-') {
			selDate = hashparts[i];
		}
	}
	setHash();

	if(selOrg != '' || selPlace != '' || selDate != '') {
		switchToEventpage();
	}

	filter();
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

function filter() {
	$('div.day').show();
	$('div.event').hide();
	$('div.event').each(function(index, value) {
		if($(this).hasClass(selOrg) && $(this).hasClass(selPlace) && ($(this).hasClass('featured') || $('body').hasClass('full')))
		{
			$(this).show();
		}
	});
	availableDates = [];
	$('div.day').each(function(index, value) {
		if($(this).children().filter(':visible').size() == 1)
		{
			$(this).hide();
			if(!$('body').hasClass('full'))
			{
				availableDates.push(this.id);
			}
		}
		else
		{
			availableDates.push(this.id);
		}
	});
	$( "#calendar-search div.content" ).datepicker('refresh');

	$('#org li a').each(function(index, value) {
		$(value).removeClass('selected');
	});

	if(selOrg == '') {
		$('a#link-all-org').addClass('selected');
		$('#org li').each(function(index, value) {
			$(value).show();
		});
	} else {
		$('a#link-'+selOrg).addClass('selected');
		$('#org li').each(function(index, value) {
			$(value).hide();
		});
		$($('a#link-'+selOrg)[0].parentNode).show();
		$($('a#link-all-org')[0].parentNode).show();
	}

	$('#place li a').each(function(index, value) {
		$(value).removeClass('selected');
	});
	if(selPlace == '') {
		$('a#link-all-place').addClass('selected');
		$('#place li').each(function(index, value) {
			$(value).show();
		});
	} else {
		$('a#link-'+selPlace).addClass('selected');
		$('#place li').each(function(index, value) {
			$(value).hide();
		});
		$($('a#link-'+selPlace)[0].parentNode).show();
		$($('a#link-all-place')[0].parentNode).show();
	}
	if(selDate == '') {
	} else {
		var id = selDate.replace('-0', '-');
		if(id[0] == '0') id = id.substring(1);
		$('div#main div.content').scrollTop($('div#main div.content').scrollTop() + $('#'+id).position().top - 50);
	}
}

function toggleEvent(e) {
	var eventDiv;
	if(e.target.localName == 'a' || e.target.localName == 'img') {
		eventDiv = e.target.parentNode.parentNode;
	} else {
		eventDiv = e.target.parentNode;
	}
	if(eventDiv.children[1].children[0].innerText == 'Read more') {
		eventDiv.children[1].children[0].innerText = 'Read less';
		$(eventDiv.children[2]).show();
	} else {
		eventDiv.children[1].children[0].innerText = 'Read more';
		$(eventDiv.children[2]).hide();
	}
	return false;
}

$(function() {
	$( "#calendar-search div.content" ).datepicker({ beforeShowDay: unavailable, dayNamesMin: ['S', 'M', 'T', 'W', 'T', 'F', 'S'], firstDay: 1, onSelect: function(dateText, inst) { setDate(dateText); } });
	$( "ul#org a" ).click(function(e) {
		e.preventDefault();
		var realID = e.target.id.substring(5);
		if(realID == 'all-org') {
			setOrg('');
		} else {
			setOrg(realID);
		}
	});
	$( "ul#place a" ).click(function(e) {
		e.preventDefault();
		var realID = e.target.id.substring(5);
		if(realID == 'all-place') {
			setPlace('');
		} else {
			setPlace(realID);
		}
	});
	$( "#alldates" ).click(function(e) {
		e.preventDefault();
		setDate('');
	});
	$( ".expand-link" ).click(toggleEvent);
	$( ".event h3" ).click(toggleEvent);
	$( ".event h3 img" ).click(toggleEvent);
	$( ".view-events" ).click(function(e) {
		if($('body').hasClass('full')) {
			switchToHomepage();
			selOrg = '';
			selPlace = '';
			selDate = '';
			location.hash = '';
		} else {
			switchToEventpage();
		}
	});
});

function switchToHomepage() {
	$('body').removeClass('full');
	$('#view-all')[0].innerHTML = "View all events<img style='height:49px' src='img/chevron_large_right.png' />";
	filter();
}

function switchToEventpage() {
	$('body').addClass('full');
	$('#view-all')[0].innerHTML = "Back to homepage<img style='height:49px' src='img/chevron_large_left.png' />";
	filter();
}

$(window).hashchange( function(e){
	if(location.hash != fullhash)
	{
		fullhash = location.hash;
		processHash();
	}
});

$(window).hashchange();
