var availableDates = ["9-3-2012","14-3-2012","15-3-2012"];

function unavailable(date) {
  dmy = date.getDate() + "-" + (date.getMonth()+1) + "-" + date.getFullYear();
  if ($.inArray(dmy, availableDates) == -1) {
    return [false,"","Unavailable"];
  } else {
    return [true, ""];
  }
}

$(function() {
	$( "#calendar-search div.content" ).datepicker({ beforeShowDay: unavailable, dayNamesMin: ['S', 'M', 'T', 'W', 'T', 'F', 'S'], firstDay: 1 });
});
