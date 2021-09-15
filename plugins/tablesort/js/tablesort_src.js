/* Mootools table sorting script by Leo Feyer, Copyright 2007 (LGPL) */


/**
 * Current index
 */
var SORT_INDEX;


/**
 * Class TableSort
 *
 * Provide methods to sort tables using the mootools framework
 * keeping the TYPOlight class names intact.
 */
var TableSort = new Class(
{

	/**
	 * Initialize the object
	 * @param integer
	 * @param object
	 * @return boolean
	 */
	initialize: function(id, options)
	{
		var table = $(id);

		// Check whether table exists
		if (table == null)
		{
			return false;
		}

		// Check whether table has rows
		if (!table.rows || table.rows.length < 1 || !table.tHead || table.tHead.rows.length < 1)
		{
			return false;
		}

		var cook = null;
		var vars = Cookie.get('TS_' + id.toUpperCase());

		if (vars !== false)
		{
			var cook = vars.split('|');
		}

		var lastRow = table.tHead.rows[table.tHead.rows.length-1];

		// Add sorting links
		for (var i=0; i<lastRow.cells.length; i++)
		{
			if (lastRow.cells[i].className.indexOf('unsortable') != -1)
			{
				continue;
			}

			var el = lastRow.cells[i];
			var txt = el.innerHTML;
			var a = new Element('a').addClass('pointer').appendText(txt);

			el.innerHTML = '';
			a.addEvent('click', function(i, el) { this.resort(i, el) }.pass([i, el], this));
			a.injectInside(el);

			// Sort the table if there is a cookie
			if (cook !== null && cook[0] == i)
			{
				el.addClass((cook[1] == 'desc') ? 'asc' : 'desc');
				this.resort(cook[0], el);
			}
		}
	},


	/**
	 * Resort the table
	 * @param integer
	 * @param object
	 * @return boolean
	 */
	resort: function (index, el)
	{
		var col = $(el);

		// Check whether column exists
		if (col == null)
		{
			return false;
		}

		var th = col.getParent();
		var table = th.getParent().getParent();

		// Check whether table exists and there is more than one row
		if (table == null || table.tBodies[0].rows.length < 2)
		{
			return false;
		}

		SORT_INDEX = index;

		var i = 0;
		var val = '';

		// Skip emtpy cells and get value
		while (val == '' && table.tBodies[0].rows[i])
		{
			val = table.tBodies[0].rows[i].cells[index].innerHTML.replace(/<[^>]+>/i).clean();
			i++;
		}

		var tbody = new Array();

		for (var i=0; i<table.tBodies[0].rows.length; i++)
		{
			tbody[i] = table.tBodies[0].rows[i];
		}

		// Date
		if (val.match(/^\d{1,4}[\/\. -]\d{1,2}[\/\. -]\d{1,4}$/))
		{
			tbody.sort(this.sortDate);
		}

		// Currency
		else if (val.match(/^[£$€Û¢´]/) || val.match(/^-?[\d\.,]+[£$€]$/))
		{
			tbody.sort(this.sortNumeric);
		}

		// Numbers
		else if (val.match(/^-?[\d\.,]+(E[-+][\d]+)?$/) || val.match(/^-?[\d\.,]+%?$/))
		{
			tbody.sort(this.sortNumeric);
		}

		// Default
		else
		{
			tbody.sort(this.sortCaseInsensitive);
		}

		// Sort ascending
		if (el.className.indexOf('asc') == -1)
		{
			var cs = th.getChildren();

			for (var i=0; i<cs.length; i++)
			{
				cs[i].removeClass('asc');
				cs[i].removeClass('desc');
			}

			el.addClass('asc');
			Cookie.set('TS_' + table.id.toUpperCase(), index + '|asc', { path: '/'});
		}

		// Sort descending
		else
		{
			var cs = th.getChildren();

			for (var i=0; i<cs.length; i++)
			{
				cs[i].removeClass('asc');
				cs[i].removeClass('desc');
			}

			el.addClass('desc');
			Cookie.set('TS_' + table.id.toUpperCase(), index + '|desc', { path: '/'});

			tbody.reverse();
		}

		// Update table
		for (i=0; i<tbody.length; i++)
		{
			var cls = tbody[i].className;
			cls = cls.replace(/row_\d+/, '').replace(/odd|even|row_first|row_last/g, '').clean();

			// Row number
			cls += ' row_' + i;

			// First row
			if (i == 0)
			{
				cls += ' row_first';
			}

			// Last row
			if (i >= (tbody.length-1))
			{
				cls += ' row_last';
			}

			// Odd/even
			cls += (i%2 == 0) ? ' odd' : ' even';

			// Apply tr class
			tbody[i].className = cls.trim();

			for (j=0; j<tbody[i].cells.length; j++)
			{
				var cls = tbody[i].cells[j].className;
				cls = cls.replace(/col_\d+/, '').replace(/odd|even|col_first|col_last/g, '').clean();

				// Col number
				cls += ' col_' + j;

				// First col
				if (j == 0)
				{
					cls += ' col_first';
				}

				// Last col
				if (j >= (tbody[i].cells.length-1))
				{
					cls += ' col_last';
				}

				// Apply td class
				tbody[i].cells[j].className = cls.trim();
			}

			table.tBodies[0].appendChild(tbody[i]);
		}
	},


	/**
	 * Compare two dates
	 * @param string
	 * @param string
	 * @return integer
	 */
	sortDate: function(a, b)
	{
		aa = a.cells[SORT_INDEX].innerHTML.replace(/<[^>]+>/i).clean();
		bb = b.cells[SORT_INDEX].innerHTML.replace(/<[^>]+>/i).clean();

		var aaChunks = aa.replace(/[\/\.-]/g, ' ').split(' ');
		var bbChunks = bb.replace(/[\/\.-]/g, ' ').split(' ');

		// DD-MM-YYYY
		if (aa.match(/^\d{1,2}[\/\. -]\d{1,2}[\/\. -]\d{2,4}$/))
		{
			var aaTstamp = ((aaChunks[2].length == 4) ? aaChunks[2] : '19' + aaChunks[2]) + ((aaChunks[1].length == 2) ? aaChunks[1] : '0' + aaChunks[1]) + ((aaChunks[0].length == 2) ? aaChunks[0] : '0' + aaChunks[0]);
			var bbTstamp = ((bbChunks[2].length == 4) ? bbChunks[2] : '19' + bbChunks[2]) + ((bbChunks[1].length == 2) ? bbChunks[1] : '0' + bbChunks[1]) + ((bbChunks[0].length == 2) ? bbChunks[0] : '0' + bbChunks[0]);
		}

		// YYYY-MM-DD
		if (aa.match(/^\d{2,4}[\/\. -]\d{1,2}[\/\. -]\d{1,2}$/))
		{
			var aaTstamp = ((aaChunks[0].length == 4) ? aaChunks[0] : '19' + aaChunks[0]) + ((aaChunks[1].length == 2) ? aaChunks[1] : '0' + aaChunks[1]) + ((aaChunks[2].length == 2) ? aaChunks[2] : '0' + aaChunks[2]);
			var bbTstamp = ((bbChunks[0].length == 4) ? bbChunks[0] : '19' + bbChunks[0]) + ((bbChunks[1].length == 2) ? bbChunks[1] : '0' + bbChunks[1]) + ((bbChunks[2].length == 2) ? bbChunks[2] : '0' + bbChunks[2]);
		}

		if (aaTstamp == bbTstamp)
		{
			return 0;
		}

		if (aaTstamp < bbTstamp)
		{
			return -1;
		}

		return 1;
	},


	/**
	 * Compare two numbers
	 * @param string
	 * @param string
	 * @return integer
	 */
	sortNumeric: function(a, b)
	{
		aa = a.cells[SORT_INDEX].innerHTML.replace(/<[^>]+>/i).replace(/[^0-9\.]/g, '').clean();
		bb = b.cells[SORT_INDEX].innerHTML.replace(/<[^>]+>/i).replace(/[^0-9\.]/g, '').clean();

		aa = parseFloat(aa);
		aa = (isNaN(aa) ? 0 : aa);

		bb = parseFloat(bb);
		bb = (isNaN(bb) ? 0 : bb);

		return aa - bb;
	},


	/**
	 * Compare two strings
	 * @param string
	 * @param string
	 * @return integer
	 */
	sortCaseInsensitive: function(a, b)
	{
		aa = a.cells[SORT_INDEX].innerHTML.replace(/<[^>]+>/i).clean().toLowerCase();
		bb = b.cells[SORT_INDEX].innerHTML.replace(/<[^>]+>/i).clean().toLowerCase();

		if (aa == bb)
		{
			return 0;
		}

		if (aa < bb)
		{
			return -1;
		}

		return 1;
	}
});
