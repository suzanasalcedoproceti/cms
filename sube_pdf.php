<?
// script para subir y redimensionar imagen...
if (!include('ctrl_acceso.php')) return;
include_once('funciones.php');
$modulo=4;
if (!op($modulo))  {
	return;
}
include('../conexion.php');


//if ($_FILES['imagen']['name']!="") {
if (1 || !empty($documento)) {

	$size=40720;  // tamaño máximo en Kb
	
	// Errores devueltos por $_FILES
	$errores = array();
	$errores[1]='*El archivo excede '.ini_get('upload_max_filesize').'.';  // excede el parámetro upload_max_filesize en php.ini
	$errores[2]='*El PDF excede '.$MAX_FILE_SIZE.'.';  // excede el parámetro MAX_FILE_SIZE en la form
	$errores[3]='*El documento se subió parcialmente.';
	$errores[4]='*No se subió el documento.';

	
	$subido=FALSE;
	$error='';
	$tipos_permitidos = array('application/octet','application/pdf');

	
	if ($_FILES[$doc_valor]['name']!="") {  // si hay imagen a subir
	  if ($_FILES[$doc_valor]['error']>0) { $error.=$errores[$_FILES[$doc_valor]['error']].'<br>'; }
	  else {
		if ($_FILES[$doc_valor]['size']>($size*1024)) { $error.='El documento excede '.$size.' Kb.<br>'; }
		if (!in_array($_FILES[$doc_valor]['type'],$tipos_permitidos)) { $error.='Sólo se pueden subir archivos PDF.<br>'; } 
	  }

echo $error;	  
	  if (!$error) {
		$nombredoc='images/cms/productos/pdf/'.$id_doc;
		if (file_exists($nombredoc)) unlink($nombredoc);
		
		if (is_uploaded_file($_FILES[$doc_valor]['tmp_name'])) {
		//	copy($_FILES[$doc_valor]['tmp_name'], $nombredoc);
			move_uploaded_file($_FILES[$doc_valor]['tmp_name'], $nombredoc);
			$subido=TRUE; 
		}
		else $error.='Problema al subir el documento.<br>';
	  }
	  
	} // si hay doc a subir
	
	  
} // if !empty documento
?>