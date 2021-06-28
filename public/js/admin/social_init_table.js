$(document).ready(function() {
	$('#dataTable').DataTable({
		"paging": true, // Toggle page system
		"info": true, // Toggle bottom left info about rows displayed
		"ordering": true, // Toggle rows ordering
		"bFilter": true, // Toggle search bar
		"order": [[ 0, "asc" ]], // Sort by the name column in asc to have them sort by alphabetical order
	});
});