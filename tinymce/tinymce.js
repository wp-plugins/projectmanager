function addAttributes () {
	document.getElementById('list_projects').setAttribute("onChange", "ProjectManagerAjaxShowCategoryFormList('list_projects_category_form', getSelectedValue('list_projects'))", 1);
	document.getElementById('gallery_projects').setAttribute("onChange", "ProjectManagerAjaxShowCategoryFormList('gallery_projects_category_form', getSelectedValue('gallery_projects'))", 1);
}
function getSelectedValue( el_id ) {
 	return document.getElementById(el_id).value;
}

function ProjectManagerAjaxShowCategoryFormList( el_id, projectId ) {
	var ajax = new sack(ProjectManagerAjaxL10n.requestUrl);
	ajax.execute = 1;
	ajax.method = 'POST';
	ajax.setVar( "action", "projectmanager_show_category_selection" );
	ajax.setVar( "el_id", el_id );
	ajax.setVar( "project_id", projectId );
	ajax.onError = function() { alert('Ajax error on saving group'); };
	ajax.onCompletion = function() { return true; };
	ajax.runAJAX();

	return true;
}


function init() {
	tinyMCEPopup.resizeToInnerSize();
}


function getCheckedValue(radioObj) {
	if(!radioObj)
		return "";
	var radioLength = radioObj.length;
	if(radioLength == undefined)
		if(radioObj.checked)
			return radioObj.value;
		else
			return "";
	for(var i = 0; i < radioLength; i++) {
		if(radioObj[i].checked) {
			return radioObj[i].value;
		}
	}
	return "";
}

function ProjectManagerInsertLink() {
	
	var tagtext;
	
	var list = document.getElementById('list_panel');
	var gallery = document.getElementById('gallery_panel');
	var categories = document.getElementById('categories_panel');
	var searchform = document.getElementById('search_panel');
	
	// who is active ?
	if (list.className.indexOf('current') != -1) {
		var projectId = document.getElementById('list_projects').value;
		var showtype = getCheckedValue(document.getElementsByName('list_showtype'));
		var grpid = document.getElementById('list_projects_category_form').value;
		if ( grpid == 'undefined' ) {
			grpid = '';
		}
		
		if ( grpid == -1 )
			var grpid = '';
			
		if (projectId != 0)
			tagtext = "[dataset_list=" + projectId + "," + grpid + "," + showtype + "]";
		else
			tinyMCEPopup.close();
	}
	
	if (gallery.className.indexOf('current') != -1) {
		var projectId = document.getElementById('gallery_projects').value;
		var numCols = document.getElementById('num_cols').value;
		var grpid = document.getElementById('gallery_projects_category_form').value;
		
		if ( grpid == -1 ) {
			var grpid = '';
		} else if ( grpid == 'undefined' ) {
			grpid = '';
		}
			
		if (projectId != 0)
			tagtext = "[dataset_gallery=" + projectId + "," + numCols + "," + grpid + "]";
		else
			tinyMCEPopup.close();
	}
	
	/*if (categories.className.indexOf('current') != -1) {
		var projectId = document.getElementById('categories_projects').value;
		var showtype = getCheckedValue(document.getElementsByName('categories_showtype'));
		var pos = document.getElementById('align_categories').value;
		
		if (projectId != 0)
			tagtext = "[prjctmngr_category_selection=" + projectId + "," + showtype + "," + pos + "]";	
		else
			tinyMCEPopup.close();
	}*/
	
	if (searchform.className.indexOf('current') != -1) {
		var projectId = document.getElementById('search_projects').value;
		var pos = document.getElementById('align_search').value;
		var display = document.getElementById('display_search').value;
		if (projectId != 0)
			tagtext = "[prjctmngr_search_form=" + projectId + "," + pos + "," + display + "]";
		else
			tinyMCEPopup.close();
	}
	
	if(window.tinyMCE) {
		window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tagtext);
		//Peforms a clean up of the current editor HTML. 
		//tinyMCEPopup.editor.execCommand('mceCleanup');
		//Repaints the editor. Sometimes the browser has graphic glitches. 
		tinyMCEPopup.editor.execCommand('mceRepaint');
		tinyMCEPopup.close();
	}
	return;
}