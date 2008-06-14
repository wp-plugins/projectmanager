var ProjectManager = new Object();

ProjectManager.addFormField = function() {
  time = new Date();
  new_element_number = time.getTime();
  new_element_id = "form_id_"+new_element_number;
  
  new_element_contents = "";
  new_element_contents += "<td><input type='text' name='new_form_name["+new_element_number+"]' value='' /></td>\n\r";
  new_element_contents += "<td><select name='new_form_type["+new_element_number+"]' size='1'>"+PRJCTMNGR_HTML_FORM_FIELD_TYPES+"</select></td>\n\r"; 
  new_element_contents += "<td><input type='checkbox' name='new_show_on_startpage["+new_element_number+"]' value='1' /><td>\n\r";
  new_element_contents += "<td><input type='text' size='3' name='new_form_order["+new_element_number+"]' value='' /></td>\n\r";
  new_element_contents += "<td  style='text-align: center; width: 12px; vertical-align: middle;'><a class='image_link' href='#' onclick='return ProjectManager.removeNewFormField(\""+new_element_id+"\");'><img src='../wp-content/plugins/projectmanager/images/trash.gif' alt='Delete' title='' /></a></td>\n\r";
  
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

ProjectManager.checkAll = function(form) {
   for (i = 0, n = form.elements.length; i < n; i++) {
      if(form.elements[i].type == "checkbox" && !(form.elements[i].getAttribute('onclick',2))) {
         if(form.elements[i].checked == true)
            form.elements[i].checked = false;
         else
            form.elements[i].checked = true;
      }
   }
}


/*
tinyMCE.init({
	mode : "textareas",
	editor_selector: "projectmanager_mceEditor",
	theme : "advanced",
	//plugins : "safari, inlinepopups, autosave, spellchecker, paste, wordpress, media,template,contextmenu",
	plugins : "inlinepopups, autosave, spellchecker, paste, wordpress",
	//plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordpress",
	theme_advanced_buttons1 : "bold,italic,underline,|,bullist,numlist,blockquote,|,justifyleft,justifycenter,justifyright,justifyfull,|,link,unlink,image,pastetext,pasteword,wp_adv",
	theme_advanced_buttons2 : "formatselect,cut,copy,|,outdent,indent,|,undo,redo,|,cleanup,help,code,forecolor,backcolor",
	//theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
	theme_advanced_buttons3 : "",
	theme_advanced_buttons4 : "",
	//theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
	relative_urls : false,
	language: "de",
	//paste_remove_spans: true,
	//paste_remove_styles: true,
	//extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
	//template_external_list_url : "example_template_list.js"
});
*/