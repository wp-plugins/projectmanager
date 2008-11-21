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

