<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=25;
	if (!(op($modulo)||op(32)))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Panel de Control</title>
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<link href="css/menu.css" rel="stylesheet" type="text/css" />
<!--[if lte IE 6]><link rel="stylesheet" href="css/menu_ie6.css" /><![endif]-->
<script type="text/javascript" src="js/menu.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js" type="text/javascript"></script>
<!--<script src="js/jquery.js" type="text/javascript" language="javascript1.2"></script>-->

<script language="JavaScript">
  function valida() {
   document.forma.action='graba_importa_dash.php';
   document.forma.submit();
  }
  function descarta() {
   document.forma.action='principal.php';
   document.forma.submit();
  }
</script>

<link href="./uploadify/uploadify.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="./uploadify/swfobject.js"></script>
<script type="text/javascript" src="./uploadify/jquery.uploadify.v2.1.4.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
  $('#file_upload').uploadify({
    'uploader'  : './uploadify/uploadify.swf',
    'script'    : './uploadify/uploadify_dashboard.php',
    'cancelImg' : './uploadify/cancel.png',
    'folder'    : './imp_dashboard',
    'expressInstall' : './uploadify/expressInstall.swf',
	'buttonText' : 'SELECCIONA',
    'fileExt'   : '*.txt',
    'fileDesc'  : 'Archivos TXT',
	
	'sizeLimit'   : 4075200,
	'auto'      : true,
    'onSelect' : function(event,queueID,fileObj){
					var ext = fileObj.name;
					ext = ext.substr(ext.length-4); //gets last 4 chars (extension type)
					document.getElementById('message').innerHTML="";
					switch (ext) {
						case ".txt":
						case ".TXT":
						// filetype ok
						break;
						default:
						// alert("Data type invalid");
						document.getElementById('message').innerHTML="Error en tipo de archivo. Solo txt!";
						//$jQuery('#file_upload').uploadifyCancel(queueID);
						break;
					}
	},

	
    'onAllComplete' : function(event,data) {
//	   document.getElementById('validar').innerHTML='<input name="grabar" type="button" class="boton" onclick="valida();" value="SUBIR Y PROCESAR" />';
	   document.getElementById('validar').innerHTML='El archivo se subio de manera correcta.<br>Los datos del dashboard quedaran actualizados dentro de los proximos 10 minutos.';
	  
    },
	
	'onError'     : function (event,ID,fileObj,errorObj) {
      document.getElementById('message').innerHTML="Error: "+errorObj.type;
    }
	
  });

});
function upload_txt() {

  var archivo = $('#uploadfileAM').val();
  var reg=1;
  var file = document.getElementById("uploadfileAM");  
  if(file.value.length==0){
                          alert("No ha cargado Archivo ");
                          reg=0;
                          return 0;                         
                        } 
  var arch = file.value;
  var arch2 = arch.indexOf(".");
  var ext = arch.substring(arch2+1).toLowerCase();    
  if (ext != "txt" && ext != "TXT") {alert("Archivo no valido, por favor intenta de nuevo");
                                      reg=0;
                                      return 0;
                                                   }    
  var formData = new FormData();
  formData.append("uploadfileAM",file.files[0]);
  
  if(reg==1){
  $.ajax({
      url: "uploadify/uploadify_dashboard_service.php",
      type: "POST",
      data: formData,
	  mimeType:"multipart/form-data",
      processData: false,  // tell jQuery not to process the data
      contentType: false,   // tell jQuery not to set contentType
      success: function(data){
     document.getElementById('validar').innerHTML='<input name="grabar" type="button" class="boton" onclick="valida();" value="PROCESAR" />';
                              return 1;              
                              }
  });
  }
}
</script>
</head>

<body>
<div id="container">
	<? $tit='Importar datos de SAP para Dashboard'; include('top.php'); ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma" enctype="multipart/form-data">
       <input type="hidden" name="MAX_FILE_SIZE" value="2000000" />
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">

          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
		  <?php if(op(32)){ ?>
		  <tr>
            <td><div align="right">Selecciona archivo:</div></td>
            <td><input id="uploadfileAM" name="uploadfileAM" type="file" />
            <div id="message"></div>
            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input name="subir" type="button" class="boton" onclick="upload_txt();" value="CARGAR ARCHIVO" /></td>
          </tr>

		  <?php } else { ?>
          <tr>
            <td><div align="right">Selecciona archivo:</div></td>
            <td><input id="file_upload" name="file_upload" type="file" />
              <div id="message"></div>            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>Solo txt delimitado por<strong> | </strong>(pipe) </td>
          </tr>
		  <?php } ?>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><div id="validar" style="width:400px; float:left"></div>
            <br />
	            <input name="descartar" type="button" class="boton" onclick="descarta();" value="REGRESAR" />            </td>
          </tr>
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
