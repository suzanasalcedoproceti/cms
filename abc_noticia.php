<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=17;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
	include('../conexion.php');

	$noticia=$_POST['noticia'];
	if (empty($noticia)) $noticia=$_GET['noticia'];

	if (!empty($noticia)) {
	  $resultado= mysql_query("SELECT * FROM noticia WHERE clave='$noticia'",$conexion);
	  $row = mysql_fetch_array($resultado);
	}

	
	// crear nombre temporal de imagen
	$ht = date("U");
	srand((double)microtime()*1000000);
	$token='';
	for ($i=1; $i<=4; $i++) {
	   $token=$token.chr(rand(97,122));
	} 
	$imagen_tmp = date("Ymd")."_".$token."_".$ht;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Panel de Control</title>
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<link href="css/menu.css" rel="stylesheet" type="text/css" />
<!--[if lte IE 6]><link rel="stylesheet" href="css/menu_ie6.css" /><![endif]-->
<script type="text/javascript" src="js/menu.js"></script>
<script src="js/jquery.js" type="text/javascript" language="javascript1.2"></script>

<link href="./uploadify/uploadify.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="./uploadify/swfobject.js"></script>
<script type="text/javascript" src="./uploadify/jquery.uploadify.v2.1.4.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
  $('#file_upload').uploadify({
    'uploader'  : './uploadify/uploadify.swf',
    'script'    : './uploadify/uploadify_noticias.php',
//	'checkScript' : './uploadify/check.php',
    'cancelImg' : './uploadify/cancel.png',
    'folder'    : './uploads',
    'expressInstall' : './uploadify/expressInstall.swf',
	'buttonText' : 'SELECCIONA',
    'fileExt'   : '*.jpg;*.docx;*.doc;*.xls;*.xlsx;*.ppt;*.pptx;*.pdf',
    'fileDesc'  : 'Documentos',
    'scriptData'  : {'identificador':'<?=$imagen_tmp;?>'},
	'sizeLimit'   : 2000000,
	'auto'      : true,
    'onSelect' : function(event,queueID,fileObj){
					var ext = fileObj.name;
					ext = ext.substr(ext.length-4); //gets last 4 chars (extension type)
					document.getElementById('message').innerHTML="";
					switch (ext) {
						case "jpeg":
						case ".jpg":
						case ".JPG":
						case ".XLS":
						case ".xls":
						case "XLSX":
						case "xlsx":
						case ".PPT":
						case ".ppt":
						case ".PPS":
						case ".pps":
						case "pptx":
						case "PPTX":
						case ".doc":
						case ".DOC":
						case "docx":
						case "DOCX":
						case ".pdf":
						case ".PDF":
						// filetype ok
						break;
						default:
						// alert("Data type invalid");
						document.getElementById('message').innerHTML="Error en tipo de archivo. Sólo JPG!";
						//$jQuery('#file_upload').uploadifyCancel(queueID);
						break;
					}
	},	
    'onComplete' : function(event,data,fileObj) {
		document.getElementById('message').innerHTML="Archivo a subir: <strong>"+fileObj.name+"</strong>";
	    document.forma.nombre_archivo.value = encodeURI(fileObj.name);
//		alert ( document.forma.nombre_archivo.value);
    },
	'onError'     : function (event,ID,fileObj,errorObj) {
      document.getElementById('message').innerHTML="Error: "+errorObj.type;
    },
    'onCancel' : function(event,data) {
	   document.getElementById('message').innerHTML="Upload cancelado.";
    },
	'onCheck'     : function(event,data,key) {
      document.forma.archivo.value='';
      $('#file_upload' + key).find('.percentage').text(' - Este archivo ya existe, no se subió...');
    }
  });
});
</script>


<script language="JavaScript">
 function valida() {
  if (document.forma.titulo.value == "") {
     alert("Falta Título de documento o noticia.");
	 document.forma.titulo.focus();
     return;
  }

   document.forma.action='graba_noticia.php';
   document.forma.submit();
 }
 function descarta() {
   document.forma.action='lista_noticia.php';
   document.forma.submit();
 }
</script>
</head>

<body>
<div id="container">
	<? $tit='Administrar noticias y documentos compartidos'; include('top.php'); ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma" enctype="multipart/form-data">
      	<input type="hidden" name="imagen_tmp" id="imagen_tmp" value="<?=$imagen_tmp;?>" />
      	<input type="hidden" name="nombre_archivo" id="nombre_archivo" value="" />
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><div align="right">T&iacute;tulo del documento o noticia:</div></td>
            <td><input name="titulo" type="text" class="campo" id="titulo" value="<?= $row['titulo']; ?>" size="100" maxlength="100" /></td>
          </tr>
          <tr>
            <td valign="top"><div align="right">Descripci&oacute;n:</div></td>
            <td><textarea name="descripcion" cols="100" rows="10" class="campo" id="descripcion"><?=$row['descripcion'];?></textarea></td>
          </tr>
          <tr>
            <td height="28" align="right" valign="top">&nbsp;</td>
            <td height="30" align="left">&nbsp;</td>
          </tr>
          <tr>
            <td height="28" align="right" valign="top" nowrap="nowrap">Documento<br />
            (jpg, doc, xls,ppt,pdf)</td>
          <td height="30" align="left"><input id="file_upload" name="file_upload" type="file" />
                <div id="message"></div>
                <div id="error"></div>
            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><? if (file_exists("images/cms/noticias/".$noticia.".jpg")) { ?>
                <div style="float:left;"><img src="images/cms/noticias/<?=$noticia;?>.jpg?<?=$ht;?>" /></div>
                <? } ?></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>
                <input name="grabar" type="button" class="boton" onclick="valida();" value="GRABAR" />
                <input name="desc" type="button" class="boton" onclick="descarta();" value="REGRESAR AL LISTADO" id="desc" />
            <input name="noticia" type="hidden" id="noticia" value="<?= $noticia; ?>" />            </td>
          </tr>
        </table>
      </form>    
    </div>
</div>
</body>
</html>
