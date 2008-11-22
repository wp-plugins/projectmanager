function addAttributes() {
	document.getElementById('list_projects').setAttribute("onChange", "displayListGroupSelection()", 1);
}

function init() {
	tinyMCEPopup.resizeToInnerSize();
}


function ProjectManagerGetCheckedValue(radioObj) {
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

function insertProjectManagerLink() {
	
	var tagtext;
	
	var list = document.getElementById('list_panel');
	var gallery = document.getElementById('gallery_panel');
	var groups = document.getElementById('groups_panel');
	var searchform = document.getElementById('search_panel');
	
	// who is active ?
	if (list.className.indexOf('current') != -1) {
		var projectId = document.getElementById('list_projects').value;
		var showtype = ProjectManagerGetCheckedValue(document.getElementsByName('list_showtype'));
		var grpid = document.getElementById('list_projects_group').value;
		
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
		var grpid = document.getElementById('gallery_projects_group').value;
		
		if ( grpid == -1 )
			var grpid = '';
			
		if (projectId != 0)
			tagtext = "[dataset_gallery=" + projectId + "," + numCols + "," + grpid + "]";
		else
			tinyMCEPopup.close();
	}
	
	if (groups.className.indexOf('current') != -1) {
		var projectId = document.getElementById('groups_projects').value;
		var showtype = ProjectManagerGetCheckedValue(document.getElementsByName('groups_showtype'));
		var pos = document.getElementById('align_groups').value;
		
		if (projectId != 0)
			tagtext = "[prjctmngr_group_selection=" + projectId + "," + showtype + "," + pos + "]";	
		else
			tinyMCEPopup.close();
	}
	
	if (searchform.className.indexOf('current') != -1) {
		var projectId = document.getElementById('search_projects').value;
		var pos = document.getElementById('align_search').value;
		
		if (projectId != 0)
			tagtext = "[prjctmngr_search_form=" + projectId + "," + pos + "]";
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