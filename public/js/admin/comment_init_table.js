$(document).ready(function() {
	$('#dataTable').DataTable({
		"paging": true, // Toggle page system
		"info": true, // Toggle bottom left info about rows displayed
		"ordering": true, // Toggle rows ordering
		"bFilter": true, // Toggle search bar
		"order": [[ 2, "desc" ]], // Sort by the createdAt column in desc to have the newest in first
	});
});