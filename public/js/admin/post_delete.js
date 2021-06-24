$('a[data-target="#deleteModal"]').on("click", function () {
	const slug = $(this).attr("data-slug");

	$("a#deleteModalInjectSlug").attr("href", $("a#deleteModalInjectSlug").attr("data-href-model").replace("*slug*", slug));
});