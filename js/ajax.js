var ProjectManager = new Object();

ProjectManager.ajaxSaveGroup = function( dataset_id ) {
	tb_remove();
	var group = document.getElementById('grp_id' + dataset_id).value;
	window.setTimeout("ProjectManager.groupSpanFadeOut(" + dataset_id + ", " + group + ")", 50);
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
	return true;
	//jQuery("span#prjctmngr_group" + dataset_id ).html( loading );
}

ProjectManager.reInit = function () {
	tb_init('a.thickbox, area.thickbox, input.thickbox');
}