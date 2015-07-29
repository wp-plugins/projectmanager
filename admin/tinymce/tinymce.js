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
	
	var project = document.getElementById('project_panel');
	var dataset = document.getElementById('dataset_panel');
	var search = document.getElementById('search_panel');
	var datasetform = document.getElementById('datasetform_panel');
	var num_datasets = document.getElementById('num_datasets_panel');
	var testimonials = document.getElementById('testimonials_panel');
	
	// who is active ?
	if (project.className.indexOf('current') != -1) {
		var projectId = document.getElementById('projects').value;
		var template = document.getElementById('project_template').value;
		var cat = document.getElementById('cat_id').value;
		var orderby = document.getElementById('orderby').value;
		var formfield_id = document.getElementById('formfield_id').value;
		var order = document.getElementById('order').value;
		var selections = getCheckedValue(document.getElementById('selections'));
		var limit = document.getElementById('limit').value;
		
		if (selections == "")
			selections = " selections=false";
		else
			selections = "";
		
		if (limit != "")
			limit = " results=" + limit;
		
		if ( orderby != '' ) {
			if ( orderby == 'formfields' && formfield_id != '' )
				orderby = " orderby=" + orderby + "_" + formfield_id;
			else
				orderby = " orderby=" + orderby;
		}
		if ( order != '' ) {	
			order = " order=" + order;
		}
		
		if ( cat <= 0 )
			cat = '';
		else
			cat = " cat_id=" + cat;
	
		if (projectId != 0)
			tagtext = "[project id=" + projectId + " template=" + template + cat + orderby + order + selections + limit +"]";
		else
			tinyMCEPopup.close();
	}
	
	if (dataset.className.indexOf('current') != -1) {
		var datasetId = document.getElementById('datasets').value;
		if (datasetId != 0)
			tagtext = "[dataset id=" + datasetId + "]";
		else
			tinyMCEPopup.close();
	}
	
	if (search.className.indexOf('current') != -1) {
		var projectId = document.getElementById('search_projects').value;
		var template = getCheckedValue(document.getElementsByName('search_display'));
		
		if (projectId != 0)
			tagtext = "[project_search project_id=" + projectId + " template=" + template + "]";
		else
			tinyMCEPopup.close();
	}

	if (datasetform.className.indexOf('current') != -1) {
		var projectId = document.getElementById('datasetform_projects').value;
		var template = document.getElementById('datasetform_templates').value;
		
		if (projectId != 0)
			tagtext = "[dataset_form project_id=" + projectId + " template=" + template + "]";
		else
			tinyMCEPopup.close();
	}

	if (num_datasets.className.indexOf('current') != -1) {
		var projectId = document.getElementById('num_datasets_projects').value;
		var text = document.getElementById('num_datasets_text').value;
		
		if (projectId != 0)
			tagtext = "[projectmanager_num_datasets project_id=" + projectId + " text='" + text + "']";
		else
			tinyMCEPopup.close();
	}
	
	if (testimonials.className.indexOf('current') != -1) {
		var projectId = document.getElementById('testimonials_projects').value;
		var number = document.getElementById('testimonials_number').value;
		var ncol = document.getElementById('testimonials_ncol').value;
		var comment_id = document.getElementById('testimonials_comment_id').value;
		var country_id = document.getElementById('testimonials_country_id').value;
		var city_id = document.getElementById('testimonials_city_id').value;
		var list_page_id = document.getElementById('testimonials_list_page_id').value;
		var sign_page_id = document.getElementById('testimonials_sign_page_id').value;
		var sign_page_id_text = document.getElementById('testimonials_sign_page_id_text').value;
		var template = document.getElementById('testimonials_template').value;
		var selections = getCheckedValue(document.getElementById('testimonials_selections'));
		
		if (number == "") number = 6;
		if (ncol == "") ncol = 3;
		
		if (sign_page_id_text != "")
			sign_page_id = sign_page_id_text;
		
		if (list_page_id != "")
			list_page_id = " list_page_id=" + list_page_id;
		
		if (sign_page_id != "")
			sign_page_id = " sign_page_id=" + sign_page_id;
		
		if (selections == "")
			selections = " selections=false";
		else
			selections = "";
		
		if (projectId != 0)
			tagtext = "[testimonials project_id=" + projectId + " template=" + template + " number=" + number + " comment=" + comment_id + " country=" + country_id + " city=" + city_id + " ncol=" + ncol + list_page_id + sign_page_id + selections + "]";
		else
			tinyMCEPopup.close();
	}
	
	if(window.tinyMCE) {
		/* get the TinyMCE version to account for API diffs */
		var tmce_ver=window.tinyMCE.majorVersion;
		
		if (tmce_ver>="4") {
			window.tinyMCE.execCommand('mceInsertContent', false, tagtext);
		} else {
			window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tagtext);
		}
		//Peforms a clean up of the current editor HTML. 
		//tinyMCEPopup.editor.execCommand('mceCleanup');
		//Repaints the editor. Sometimes the browser has graphic glitches. 
		tinyMCEPopup.editor.execCommand('mceRepaint');
		tinyMCEPopup.close();
	}
	return;
}
