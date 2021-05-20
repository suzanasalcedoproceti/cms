function substr( f_string, f_start, f_length ) {
    // Returns part of a string  
    // 
    // version: 810.1317
    // discuss at: http://phpjs.org/functions/substr
    // +     original by: Martijn Wieringa
    // +     bugfixed by: T.Wild
    // +      tweaked by: Onno Marsman
    // *       example 1: substr('abcdef', 0, -1);
    // *       returns 1: 'abcde'
    // *       example 2: substr(2, 0, -6);
    // *       returns 2: ''
    f_string += '';

    if(f_start < 0) {
        f_start += f_string.length;
    }

    if(f_length == undefined) {
        f_length = f_string.length;
    } else if(f_length < 0){
        f_length += f_string.length;
    } else {
        f_length += f_start;
    }

    if(f_length < f_start) {
        f_length = f_start;
    }

    return f_string.substring(f_start, f_length);
}

function isPwd(string) {
    if (string.search(/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/) != -1)
        return true;
    else
        return false;
}

function SetAllCheckBoxes(FormName, FieldName, CheckValue) {
	if(!document.forms[FormName])
		return;
	var objCheckBoxes = document.forms[FormName].elements[FieldName];
	if(!objCheckBoxes)
		return;
	var countCheckBoxes = objCheckBoxes.length;
	if(!countCheckBoxes)
		objCheckBoxes.checked = CheckValue;
	else
		// set the check value for all check boxes
		for(var i = 0; i < countCheckBoxes; i++) {
			objCheckBoxes[i].checked = CheckValue;
		}
}


function InvertAllCheckBoxes(FormName, FieldName) {
	if(!document.forms[FormName])
		return;
	var objCheckBoxes = document.forms[FormName].elements[FieldName];
	if(!objCheckBoxes)
		return;
	var countCheckBoxes = objCheckBoxes.length;
	if(!countCheckBoxes)
		objCheckBoxes.checked = !objCheckBoxes.checked;
	else
		// set the check value for all check boxes
		for(var i = 0; i < countCheckBoxes; i++)
			objCheckBoxes[i].checked = !objCheckBoxes[i].checked;

}

function hayChecados(FormName, FieldName) {
	if(!document.forms[FormName])
		return 0;
	var objCheckBoxes = document.forms[FormName].elements[FieldName];
	if(!objCheckBoxes)
		return 0;
	var countCheckBoxes = objCheckBoxes.length;
	if(!countCheckBoxes)
		if (objCheckBoxes.checked) return 1; else return 0;
	else {
		// set the check value for all check boxes
		
		var ich = 0;
		for(var i = 0; i < countCheckBoxes; i++) {
			if (objCheckBoxes[i].checked) ich++;
		}
		return ich;
	}
}

function validaRFC(rfc,homo) {
  var correcto = true;
  rfc = formatRFC(rfc);
  if (homo==1) {   // con homoclave (default)
	  if (rfc.search(/^[a-zA-Z]{3,4}(\d{6})((\D|\d){3})$/) == -1) {
		correcto = false;
	  } else {
		// revisar que la última sea número o letra
		len = rfc.length;
		ultima = rfc.substring(len-1,len);
		if (ultima.search(/^[0-9A]$/) == -1) {
			correcto = false;
		}
	  }
  } else {  // sin homoclave obligatoria (solo personas físicas)
	  if (rfc.search(/^[a-zA-Z]{3,4}(\d{6})((\D|\d){3})?$/) == -1) {
		correcto = false;
	  } 
  }

return correcto;	

}

function formatRFC(rfc) {
  var error = false;
  // solo letras y números
  rfc = rfc.replace(/[^a-zA-Z0-9]+/g,'');
  // convertir a may
  rfc = rfc.toUpperCase();
  // validar largo mínimo
  return rfc;	

}

function checklength(obj,max) {
	var txt;
	var n = obj.value.length;
	if (n>max) { 
		obj.value = obj.value.substring(0, max); 
		return false;
	}
}