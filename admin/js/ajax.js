ProjectManager.saveOrder = function(order) {
	var ajax = new sack(ProjectManagerAjaxL10n.requestUrl);
	ajax.execute = 1;
	ajax.method = 'POST';
	ajax.setVar( "action", "projectmanager_save_dataset_order" );
	ajax.setVar( "order", order );
	ajax.onError = function() { alert('Ajax error on saving dataset order'); };
	ajax.onCompletion = function() { return true; };
	ajax.runAJAX();
}

ProjectManager.ajaxSaveDatasetName = function( dataset_id ) {
	tb_remove();
	var dataset_name = document.getElementById('dataset_name' + dataset_id).value;
	window.setTimeout("ProjectManager.datasetnameSpanFadeOut(" + dataset_id + ",'" + dataset_name + "')", 50);
}

ProjectManager.datasetnameSpanFadeOut = function( dataset_id, dataset_name ) {
	jQuery("span#dataset_name_text" + dataset_id).fadeIn('fast', function() {
		var ajax = new sack(ProjectManagerAjaxL10n.requestUrl);
		ajax.execute = 1;
		ajax.method = 'POST';
		ajax.setVar( "action", "projectmanager_save_name" );
		ajax.setVar( "dataset_id", dataset_id );
		ajax.setVar( "new_name", dataset_name );
		ajax.onError = function() { alert('Ajax error on saving group'); };
		ajax.onCompletion = function() { ProjectManager.reInit(); };
		ajax.runAJAX();
	});
	//jQuery("span#dataset_name" + dataset_id).html( loading );
	return true;
}

ProjectManager.ajaxSaveCategories = function( dataset_id ) {
	tb_remove();
	var n = jQuery("#groupchoose" + dataset_id + " #categorychecklist" + dataset_id + " input:checked").length;
	//var cats = '';
	var cats = new Array();
	for(var a=0;a<n;a++){
		cats += jQuery("#groupchoose" + dataset_id + " #categorychecklist" + dataset_id + " input:checked")[a].value + ",";
	}
	window.setTimeout("ProjectManager.categorySpanFadeOut(" + dataset_id + ",'" + cats + "')", 50);
}
ProjectManager.categorySpanFadeOut = function( dataset_id, cats ) {
	jQuery("span#dataset_category_text" + dataset_id).fadeIn('fast', function() {
		var ajax = new sack(ProjectManagerAjaxL10n.requestUrl);
		ajax.execute = 1;
		ajax.method = 'POST';
		ajax.setVar( "action", "projectmanager_save_categories" );
		ajax.setVar( "dataset_id", dataset_id );
		ajax.setVar( "cat_ids", cats );
		ajax.onError = function() { alert('Ajax error on saving group'); };
		ajax.onCompletion = function() { ProjectManager.reInit(); };
		ajax.runAJAX();
	});
	//jQuery("span#dataset_group" + dataset_id).html( loading );
	return true;
}

ProjectManager.ajaxSaveDataField = function( dataset_id, formfield_id, formfield_type ) {
	tb_remove();
	if ( formfield_type == 4 ) {
		var day = document.getElementById('form_field_' + formfield_id + '_' + dataset_id + '_day').value;
		var month = document.getElementById('form_field_' + formfield_id + '_' + dataset_id + '_month').value;
		var year = document.getElementById('form_field_' + formfield_id + '_' + dataset_id + '_year').value;
		var newvalue = year+"-"+month+"-"+day;
	} else if ( formfield_type == 7 ) {
		var values = ProjectManager.getSelectedCheckboxValue(document.getElementsByName("form_field_"+formfield_id+"_"+dataset_id));
		var newvalue = '';
		for(var a=0;a<values.length;a++){
			newvalue += values[a] + ",";
		}
	} else if ( formfield_type == 8 ) {
		var newvalue = ProjectManager.getSelectedRadioValue(document.getElementsByName("form_field_"+formfield_id+"_"+dataset_id));
	} else {
		var newvalue = document.getElementById('form_field_' + formfield_id + '_' + dataset_id).value.split('\n').join('\\n');
	}
	window.setTimeout("ProjectManager.dataFieldSpanFadeOut(" +  dataset_id  +  ","  + formfield_id + ",'" + newvalue + "'," + formfield_type + ")", 50);
}
ProjectManager.dataFieldSpanFadeOut = function( dataset_id, formfield_id, newvalue, formfield_type ) {
	jQuery("span#datafield" + formfield_id + "_" + dataset_id).fadeIn('fast', function() {
		var ajax = new sack(ProjectManagerAjaxL10n.requestUrl);
		ajax.execute = 1;
		ajax.method = 'POST';
		ajax.setVar( "action", "projectmanager_save_form_field_data" );
		ajax.setVar( "dataset_id", dataset_id );
		ajax.setVar( "formfield_id", formfield_id );
		ajax.setVar( "formfield_type", formfield_type );
		ajax.setVar( "new_value", newvalue );
		ajax.onError = function() { alert('Ajax error on saving group'); };
		ajax.onCompletion = function() { ProjectManager.reInit(); };
		ajax.runAJAX();
});
	//jQuery("span#datafield" + formfield_id + "_" + dataset_id).html( loading );
	return true;
}

ProjectManager.ajaxSaveFormFieldOptions = function ( form_id ) {
	tb_remove();
	jQuery("a#options_link" + form_id).fadeIn('fast', function() {
		form_field_options = document.getElementById('form_field_options' + form_id).value;
		var ajax = new sack(ProjectManagerAjaxL10n.requestUrl);
		ajax.execute = 1;
		ajax.method = 'POST';
		ajax.setVar( "action", "projectmanager_save_form_field_options" );
		ajax.setVar( "form_id", form_id );
		ajax.setVar( "options", form_field_options );
		ajax.onError = function() { alert('Ajax error on saving group'); };
		ajax.onCompletion = function() { ProjectManager.reInit(); };
		ajax.runAJAX();
	});
	return true;
}


ProjectManager.reInit = function () {
	tb_init('a.thickbox, area.thickbox, input.thickbox');
}
