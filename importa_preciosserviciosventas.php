<?
    if (!include('ctrl_acceso.php')) return;
  include('funciones.php');
  $modulo=9;
  if (!op($modulo))  {
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

<script language="JavaScript">
  function valida() {
   document.forma.action='graba_importa_preciosserviciosventas.php';
   document.forma.submit();
  }
  function descarta() {
   document.forma.action='lista_preciosservicios.php';
   document.forma.submit();
  }
</script>

<link href="./uploadify/uploadify.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="./uploadify/swfobject.js"></script>
<script type="text/javascript" src="./uploadify/jquery.uploadify.v2.1.4.min.js"></script>
<script type="text/javascript">
/*$(document).ready(function() {
  $('#file_upload').uploadify({
    'uploader'  : './uploadify/uploadify.swf',
    'script'    : './uploadify/uploadify_oc.php',
    'cancelImg' : './uploadify/cancel.png',
    'folder'    : './imp_oc',
    'expressInstall' : './uploadify/expressInstall.swf',
  'buttonText' : 'SELECCIONA',
    'fileExt'   : '*.csv',
    'fileDesc'  : 'Archivos CSV',
  
  'sizeLimit'   : 1358400,
  'auto'      : true,
    'onSelect' : function(event,queueID,fileObj){
          var ext = fileObj.name;
          ext = ext.substr(ext.length-4); //gets last 4 chars (extension type)
          document.getElementById('message').innerHTML="";
          switch (ext) {
            case ".csv":
            case ".CSV":
            // filetype ok
            break;
            default:
            // alert("Data type invalid");
            document.getElementById('message').innerHTML="Error en tipo de archivo. Solo csv!";
            //$jQuery('#file_upload').uploadifyCancel(queueID);
            break;
          }
  },

  
    'onAllComplete' : function(event,data) {
     document.getElementById('validar').innerHTML='<input name="grabar" type="button" class="boton" onclick="valida();" value="SUBIR Y PROCESAR" />';
     //document.getElementById('validar').innerHTML='El archivo se subio de manera correcta.<br>Los puntos quedaran actualizados dentro de los proximos 10 minutos.';
    
    },
  
  'onError'     : function (event,ID,fileObj,errorObj) {
      document.getElementById('message').innerHTML="Error: "+errorObj.type;
    }
  
  });

});*/


$(document).ready(function() {
    $("#file_upload").change(function () {
        var fileExtension = ['csv'];
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
          $("#file_upload").val("");
          alert("Error en tipo de archivo. Solo : "+fileExtension.join(', '));
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
  frmData.append('identificador','archivo_preciosserviciosventas');
  frmData.append('tipo','csv');
  frmData.append('fileext','*.csv');
  frmData.append('fileDesc','Archivos CSV');
  frmData.append('folder','/admin/imp_oc/');


  //formData.append("Filedata",file.files[0]);
  

  $.ajax({
      url: "uploadify/uploadify.php",
      type: "POST",
      data: frmData,
      mimeType:"multipart/form-data",
      processData: false,  // tell jQuery not to process the data
      contentType: false,   // tell jQuery not to set contentType
      success: function(data){   
      console.log(data); 
      document.getElementById('validar').innerHTML='<input name="grabar" type="button" class="boton" onclick="valida();" value="SUBIR Y PROCESAR" />';       
                              }
  });
}
</script>

</head>

<body>
<div id="container">
  <? $tit='Importar Determinta Planta'; include('top.php'); ?>
  <div class="main">
      <form action="" method="post" name="forma" id="forma" enctype="multipart/form-data">
       <input type="hidden" name="MAX_FILE_SIZE" value="2000000" />
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">

          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><div align="right">Selecciona archivo:</div></td>
            <td><input id="file_upload" name="file_upload" type="file" />
            <div id="message"></div>
            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><div id="validar" style="width:200px; float:left"></div>
              <input name="descartar" type="button" class="boton" onclick="descarta();" value="REGRESAR" />
            </td>
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
