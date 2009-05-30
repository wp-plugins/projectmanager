ProjectManager.addFormField = function() {
  time = new Date();
  new_element_number = time.getTime();
  new_element_id = "form_id_"+new_element_number;
  
  new_element_contents = "";
  new_element_contents = "<td>&#160;</td>";
  new_element_contents += "<td><input type='text' name='new_form_name["+new_element_number+"]' value='' /></td>\n\r";
  new_element_contents += "<td id='form_field_options_box"+new_element_number+"'><select onChange='ProjectManager.toggleOptions("+new_element_number+", this.value, \"" + ProjectManagerAjaxL10n.Save + "\", \"" + ProjectManagerAjaxL10n.Cancel + "\", \"" + ProjectManagerAjaxL10n.Options + "\", \"\");' name='new_form_type["+new_element_number+"]' size='1'>"+PRJCTMNGR_HTML_FORM_FIELD_TYPES+"</select></td>\n\r";
  new_element_contents += "<td><input type='checkbox' name='new_show_on_startpage["+new_element_number+"]' value='1' /></td>\n\r";
  new_element_contents += "<td><input type='checkbox' name='new_show_in_profile["+new_element_number+"]' value='1' checked='checked' /></td>\n\r";
  new_element_contents += "<td><input type='text' size='2' name='new_form_order["+new_element_number+"]' value='' /></td>\n\r";
  new_element_contents += "<td><input type='checkbox' name='new_order_by["+new_element_number+"]' value='1' /></td>\n\r";
  new_element_contents += "<td  style='text-align: center; width: 12px; vertical-align: middle;'><a class='image_link' href='#' onclick='return ProjectManager.removeNewFormField(\""+new_element_id+"\");'><img src='../wp-content/plugins/projectmanager/admin/icons/trash.gif' alt='" + ProjectManagerAjaxL10n.Delete + "' title='" + ProjectManagerAjaxL10n.Delete + "' /></a></td>\n\r";
  
  new_element = document.createElement('tr');
  new_element.id = new_element_id;
   
  document.getElementById("projectmanager_form_fields").appendChild(new_element);
  document.getElementById(new_element_id).innerHTML = new_element_contents;
  return false;
}

ProjectManager.removeNewFormField = function(id) {
  element_count = document.getElementById("projectmanager_form_fields").childNodes.length;
  if(element_count > 1) {
    target_element = document.getElementById(id);
    document.getElementById("projectmanager_form_fields").removeChild(target_element);
  }
  return false;
}

ProjectManager.removeFormField = function(id,form_id) {
  element_count = document.getElementById("projectmanager_form_fields").childNodes.length;
  if(element_count > 1) {
    target_element = document.getElementById(id);
    document.getElementById("projectmanager_form_fields").removeChild(target_element);
  }
  return false;
}


ProjectManager.toggleOptions = function(form_id, value, button_save, button_cancel, title, textarea_value) {
	textarea_value = textarea_value.split(',').join("\n");
	new_element_contents = "";
	new_element_contents += "<div id='form_field_options_div" + form_id + "' style='width: 450px; height: 350px; overflow: auto; display: none;'><div class='projectmanager_thickbox'>";
	new_element_contents += "<form><textarea cols='40' rows='10' id='form_field_options" + form_id + "'>" + textarea_value + "</textarea><div style='text-align:center; margin-top: 1em;'><input type='button' value='" + button_save + "' class='button-secondary' onclick='ProjectManager.ajaxSaveFormFieldOptions(" + form_id + ");return false;' />&#160;<input type='button' value='" + button_cancel + "' class='button' onclick='tb_remove();' /></div></form></div></div>";

	new_element_contents += "<span>&#160;<a href='#TB_inline&width=450&height=350&inlineId=form_field_options_div" + form_id + "' id='options_link" + form_id + "' class='thickbox' title='" + title + "' style='display: inline;'>" + title + "</a></span>";
	
	new_element = document.createElement('div');
	new_element_id = 'form_field_options_container'+form_id;
	new_element.id = new_element_id;
	new_element.style.display = 'inline';
	
	// Check if selected form type is selection, checkbox, or radio
	if ( value == 'select' || value == 'checkbox' || value == 'radio' ) {
		if ( ! document.getElementById(new_element_id) ) {
			document.getElementById("form_field_options_box"+form_id).appendChild(new_element);
			jQuery("div#form_field_options_container"+form_id).html(new_element_contents).fadeIn('fast');
		}
		ProjectManager.reInit();
	} else {
		jQuery("div#form_field_options_container" + form_id).fadeOut('fast');
		if (target_element = document.getElementById(new_element_id)) {;
			document.getElementById("form_field_options_box"+form_id).removeChild(target_element);
		}
	}
}

