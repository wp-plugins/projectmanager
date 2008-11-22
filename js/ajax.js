ProjectManager.ajaxSaveGroup = function( dataset_id ) {
	tb_remove();
	var group = document.getElementById('grp_id' + dataset_id).value;
	window.setTimeout("ProjectManager.groupSpanFadeOut(" + dataset_id + "," + group + ")", 50);
}
ProjectManager.groupSpanFadeOut = function( dataset_id, group ) {
	jQuery("span#prjctmngr_group" + dataset_id).fadeIn('fast', function() {
		var ajax = new sack(ProjectManagerAjaxL10n.requestUrl);
		ajax.execute = 1;
		ajax.method = 'POST';
		ajax.setVar( "action", "projectmanager_save_group" );
		ajax.setVar( "dataset_id", dataset_id );
		ajax.setVar( "group", group );
		ajax.onError = function() { alert('Ajax error on saving group'); };
		ajax.onCompletion = function() { ProjectManager.reInit(); };
		ajax.runAJAX();
	});
	//jQuery("span#prjctmngr_group" + dataset_id).html( loading );
	return true;
}

ProjectManager.ajaxSaveDataField = function( dataset_id, formfield_id, formfield_type ) {
	tb_remove();
	if ( formfield_type == 4 ) {
		var day = document.getElementById('form_field_' + formfield_id + '_' + dataset_id + '_day').value;
		var month = document.getElementById('form_field_' + formfield_id + '_' + dataset_id + '_month').value;
		var year = document.getElementById('form_field_' + formfield_id + '_' + dataset_id + '_year').value;
		var newvalue = year+"-"+month+"-"+day;
	} else {
		var newvalue = document.getElementById('form_field_' + formfield_id + '_' + dataset_id).value.split('\n').join('\\n');
	}
	window.setTimeout("ProjectManager.dataFieldSpanFadeOut('" +  dataset_id  +  "','"  + formfield_id + "','" + newvalue + "','" + formfield_type + "')", 50);
}
ProjectManager.dataFieldSpanFadeOut = function( dataset_id, formfield_id, newvalue, formfield_type ) {
	var newvalue = newvalue.split('\n').join('\\n');
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


ProjectManager.reInit = function () {
	tb_init('a.thickbox, area.thickbox, input.thickbox');
}