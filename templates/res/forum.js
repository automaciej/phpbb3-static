$.tablesorter.addParser({
	id: 'typedatetime',
	is: function(s) {
		return false
	},
	format: function(s, table, cell, cellIndex) {
		topic_type = $(cell).attr('ttype');
		return "" + topic_type + s;
	},
	type: "text"
});

function load(f) {
	if (f == 'forum') {
		$('#topics').tablesorter({
			sortList: [[3,1]],
			headers: {
				3: {
					sorter: 'typedatetime'
				}
			},
			widgets: ['zebra']
		});

		$('#topics').tablesorterPager({
			container: $('#pager'),
			size: 20000
		});
	}
}
