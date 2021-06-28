$('input[type="checkbox"]').on("change", function () {
	const comment = $(this);

	if (comment.attr("id").match(/[0-9]+Comment/)) {
		const commentId = comment.attr("id").replace("Comment", "");
		const newStatus = comment.is(':checked');
		const newLabel = newStatus ? comment.attr("data-true-label") : comment.attr("data-false-label");

		// Update label text
		comment.parent("label").children("span.label").html(newLabel);

		// Request data update
		$.ajax({
			url: "/admin/comment/" + commentId + "/" + (newStatus ? "put-online" : "put-offline"), 
			success: function(data) {}, 
			error: function() {}, 
		});
	}
});