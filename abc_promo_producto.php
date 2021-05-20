<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=10;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este m�dulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
	include('../conexion.php');

	$promo=$_POST['promo'];
	if (empty($promo)) $promo=$_GET['promo'];
	$autorizar=$_GET['autorizar'];

	if (!empty($promo)) {
	  $resultado= mysql_query("SELECT * FROM promo_producto WHERE clave='$promo'",$conexion);
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js" type="text/javascript"></script>
<link href="./uploadify/uploadify.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="./uploadify/swfobject.js"></script>
<script type="text/javascript" src="./uploadify/jquery.uploadify.v2.1.4.min.js"></script>
<script type="text/javascript">
/*$(document).ready(function() {
  $('#file_upload').uploadify({
    'uploader'  : './uploadify/uploadify.swf',
    'script'    : './uploadify/uploadify.php',
    'cancelImg' : './uploadify/cancel.png',
    'folder'    : './uploads',
    'expressInstall' : './uploadify/expressInstall.swf',
	'buttonText' : 'SELECCIONA',
    'fileExt'   : '*.gif',
    'fileDesc'  : 'Archivos GIF',
	
    'scriptData'  : {'identificador':'<?=$imagen_tmp;?>','tipo':'gif'},
	'sizeLimit'   : 358400,
	'auto'      : true,
    'onSelect' : function(event,queueID,fileObj){
					var ext = fileObj.name;
					ext = ext.substr(ext.length-4); //gets last 4 chars (extension type)
					document.getElementById('message').innerHTML="";
					switch (ext) {
						case "gif":
						case ".gif":
						case ".GIF":
						// filetype ok
						break;
						default:
						// alert("Data type invalid");
						document.getElementById('message').innerHTML="Error en tipo de imagen. Solo GIF!";
						//$jQuery('#file_upload').uploadifyCancel(queueID);
						break;
					}
	},

	
    'onAllComplete' : function(event,data) {
	  // document.getElementById('message').innerHTML="Archivo subido!";
    },
	
	'onError'     : function (event,ID,fileObj,errorObj) {
      document.getElementById('message').innerHTML="Error: "+errorObj.type;
    }
	
  });

});*/

$(document).ready(function() {
    $("#file_upload").change(function () {
        var fileExtension = ['gif'];
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
          $("#file_upload").val("");
          alert("Error en tipo de imagen. Solo : "+fileExtension.join(', '));
        }
        else
        {
          upload_txt();
          
        }
    });
});

function upload_txt() {

  var archivo = $('#file_upload').val();
  var file = document.getElementById("file_upload");  
  
  var frmData = new FormData();
  frmData.append("Filedata",file.files[0]);
  frmData.append('identificador','<?php echo $imagen_tmp; ?>');
  frmData.append('tipo','gif');
  frmData.append('fileext','*.gif');
  frmData.append('fileDesc','Archivos GIF');
  frmData.append('folder','/admin/uploads');


  //formData.append("Filedata",file.files[0]);


  $.ajax({
      url: "uploadify/uploadify.php",
      type: "POST",
      data: frmData,
      mimeType:"multipart/form-data",
      processData: false,  // tell jQuery not to process the data
      contentType: false,   // tell jQuery not to set contentType
      success: function(data){    
      document.getElementById('message').innerHTML="Archivo subido!";        
                              }
  });
}
</script>

<script language="JavaScript">
  function valida() {
    if (document.forma.nombre.value == "") {
     alert("Falta nombre.");
	 document.forma.nombre.focus();
     return;
     }

   document.forma.action='graba_promo_producto.php';
   document.forma.submit();
  }
  function rechaza() {
   document.forma.action='borra_promo_producto.php';
   document.forma.submit();
  }
  function descarta() {
   document.forma.action='lista_promo_producto.php';
   document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
	<? $tit='Administrar Promos'; include('top.php'); ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma" enctype="multipart/form-data">
      	<input type="hidden" name="imagen_tmp" id="imagen_tmp" value="<?=$imagen_tmp;?>" />
        <input name="promo" type="hidden" id="promo" value="<?= $promo; ?>" />
        <input name="autorizar" type="hidden" id="autorizar" value="<?= $autorizar; ?>" />
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><div align="right">Nombre:</div></td>
            <td><input name="nombre" type="text" class="campo" id="nombre" value="<?= $row['nombre']; ?>" size="100" maxlength="100" /></td>
          </tr>
          <tr>
            <td height="28" align="right">Imagen (GIF):<br /></td>
          <td height="30" align="left"><input id="file_upload" name="file_upload" type="file" />
                <div id="message"></div>            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><? if (file_exists("images/cms/promo_productos/".$promo.".jpg")) { ?>
                <div style="float:left;"><img src="images/cms/promo_productos/<?=$promo;?>.jpg?<?=$ht;?>" /></div>
                <? } ?>            
            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>
                <input name="grabar" type="button" class="boton" onclick="valida();" value="GRABAR" />
                <input name="desc" type="button" class="boton" onclick="descarta();" value="REGRESAR AL LISTADO" id="desc" />
            </td>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table>
      </form>    
    </div>
</div>
</body>
</html>
