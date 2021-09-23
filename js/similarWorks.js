$('#similarWorksList').on('mouseover','img',function(e) {
	var selectedTags = {};
	$.each($(e.currentTarget).parents('li').data('tag-id-list').split(','),
		function(i, val) {
			selectedTags[val]=true; });
	$('#similarWorksTags li').each( function(i) {
		var $this = $(this); 
		if (selectedTags[$this.data('tag-id')]) { 
			$this.removeClass('hidden'); 
		} else { 
			$this.addClass('hidden');
		} 
	});
});
$('#similarWorksList').on('mouseout','img',function(e) {
	$('#similarWorksTags li').each( function() { 
		$(this).removeClass('hidden'); 
	});
});
