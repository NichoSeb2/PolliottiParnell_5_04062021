$('a[data-target="#deleteModal"]').on("click", function () {
	const slug = $(this).attr("data-slug");

	const link = $(this).attr("data-href-model").replace("*slug*", slug);

	$("a#deleteModalInjectSlug").attr("href", link);
});