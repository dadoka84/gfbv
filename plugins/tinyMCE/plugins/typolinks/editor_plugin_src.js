/**
 * $Id: editor_plugin_src.js 201 2007-02-12 15:56:56Z leo $
 *
 * The plugin is based on the "typolightlinks" plugin created by
 * Andreas Gross (http://www.meta-level.de).
 *
 * @author Leo Feyer
 * @copyright Leo Feyer, 2007
 */

/* Import plugin specific language pack */
tinyMCE.importPluginLanguagePack('typolinks');

var TinyMCE_TypolinksPlugin = {
	_isDisabled : false,
	_selection : null,

	getInfo : function() {
		return {
			longname : 'TYPOlight links plugin',
			author : 'Leo Feyer',
			authorurl : 'http://www.typolight.org',
			infourl : 'http://www.typolight.org',
			version : '1.0'
		};
	},

	getControlHTML : function(cn) {
		switch (cn) {
			case "typolinks":
				return tinyMCE.getButtonHTML(cn, 'lang_typolinks_desc', '{$pluginurl}/images/link.gif', 'mceTypolinks', true);
		}

		return '';
	},

	execCommand : function(editor_id, element, command, user_interface, value) {
		switch (command) {
			case "mceTypolinks":
				var inst = tinyMCE.getInstanceById(editor_id);
				var doc = inst.getDoc();
				var selectedText = "";

				if (tinyMCE.isMSIE) {
					var rng = doc.selection.createRange();
					selectedText = rng.text;
				} else
					selectedText = inst.getSel().toString();

				if (!tinyMCE.linkElement) {
					if ((tinyMCE.selectedElement.nodeName.toLowerCase() != "img") && (selectedText.length <= 0))
						return true;
				}

				var href = "", target = "", title = "", onclick = "", action = "insert", style_class = "";

				if (tinyMCE.selectedElement.nodeName.toLowerCase() == "a")
					tinyMCE.linkElement = tinyMCE.selectedElement;

				// Is anchor not a link
				if (tinyMCE.linkElement != null && tinyMCE.getAttrib(tinyMCE.linkElement, 'href') == "")
					tinyMCE.linkElement = null;

				if (tinyMCE.linkElement) {
					href = tinyMCE.getAttrib(tinyMCE.linkElement, 'href');
					target = tinyMCE.getAttrib(tinyMCE.linkElement, 'target');
					title = tinyMCE.getAttrib(tinyMCE.linkElement, 'title');
					onclick = tinyMCE.getAttrib(tinyMCE.linkElement, 'onclick');
					style_class = tinyMCE.getAttrib(tinyMCE.linkElement, 'class');

					// Try old onclick to if copy/pasted content
					if (onclick == "")
						onclick = tinyMCE.getAttrib(tinyMCE.linkElement, 'onclick');

					onclick = tinyMCE.cleanupEventStr(onclick);

					href = eval(tinyMCE.settings['urlconverter_callback'] + "(href, tinyMCE.linkElement, true);");

					// Use mce_href if defined
					mceRealHref = tinyMCE.getAttrib(tinyMCE.linkElement, 'mce_href');
					if (mceRealHref != "") {
						href = mceRealHref;

						if (tinyMCE.getParam('convert_urls'))
							href = eval(tinyMCE.settings['urlconverter_callback'] + "(href, tinyMCE.linkElement, true);");
					}

					action = "update";
				}

				var template = new Array();

				template['file'] = '../../plugins/typolinks/typolinks.php';
				template['width'] = 360;
				template['height'] = 230;

				// Language specific width and height addons
				template['width'] += tinyMCE.getLang('lang_insert_link_delta_width', 0);
				template['height'] += tinyMCE.getLang('lang_insert_link_delta_height', 0);

				if (inst.settings['insertlink_callback']) {
					var returnVal = eval(inst.settings['insertlink_callback'] + "(href, target, title, onclick, action, style_class);");
					if (returnVal && returnVal['href'])
						TinyMCE_AdvancedTheme._insertLink(returnVal['href'], returnVal['target'], returnVal['title'], returnVal['onclick'], returnVal['style_class']);
				} else {
					tinyMCE.openWindow(template, {href : href, target : target, title : title, onclick : onclick, action : action, className : style_class, inline : "yes"});
				}

				return true;
		}

		return false;
	},

	handleNodeChange : function(editor_id, node, undo_index, undo_levels, visual_aid, any_selection) {
		do
		{
			if (node.nodeName == "A" && tinyMCE.getAttrib(node, 'href') != "")
			{
				tinyMCE.switchClass(editor_id + '_typolinks', 'mceButtonSelected');
				return true;
			}
		} while ((node = node.parentNode));

		if (any_selection)
		{
			tinyMCE.switchClass(editor_id + '_typolinks', 'mceButtonNormal');
			return true;
		}
		
		tinyMCE.switchClass(editor_id + '_typolinks', 'mceButtonDisabled');
		return true;
	}
};

// Register plugin
tinyMCE.addPlugin("typolinks", TinyMCE_TypolinksPlugin);
