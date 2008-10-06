$.tablesorter.addParser({
	id: 'datetime',
	is: function(s) {
		return false
	},
	format: function(s) {
		s = s.replace(/(..)-(..)-(....) (..):(..):(..)/, '$2/$1/$3 $4:$5:$6');
		return $.tablesorter.formatFloat(new Date(s).getTime());
	},
	type: "numertic"
});

function load(f) {
	if (f == 'forum') {
		$('#topics').tablesorter({
			sortList: [[3,1]],
			headers: {
				3: {
					sorter: 'datetime'
				}
			},
			widgets: ['zebra']
		});

		$('#topics').tablesorterPager({
			container: $('#pager'),
			size: 10
		});
	}
}
