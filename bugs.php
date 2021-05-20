<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$imagen_tmp = date("Ymd")."_".session_id();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Panel de Control</title>
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<link href="css/menu.css" rel="stylesheet" type="text/css" />
<!--[if lte IE 6]><link rel="stylesheet" href="css/menu_ie6.css" /><![endif]-->
<script src="js/jquery.js" type="text/javascript" language="javascript1.2"></script>
<script type="text/javascript" src="js/menu.js"></script>
<link href="./uploadify/uploadify.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="./uploadify/swfobject.js"></script>
<script type="text/javascript" src="./uploadify/jquery.uploadify.v2.1.4.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
  $('#file_upload').uploadify({
    'uploader'  : './uploadify/uploadify.swf',
    'script'    : './uploadify/uploadify.php',
    'cancelImg' : './uploadify/cancel.png',
    'folder'    : './uploads',
    'expressInstall' : './uploadify/expressInstall.swf',
	'buttonText' : 'SELECCIONA',
    'fileExt'   : '*.jpg',
    'fileDesc'  : 'Archivos JPG',
	
    'scriptData'  : {'identificador':'<?=$imagen_tmp;?>','tipo':'jpg'},
	'sizeLimit'   : 1024000,
	'auto'      : true,
    'onSelect' : function(event,queueID,fileObj){
					var ext = fileObj.name;
					ext = ext.substr(ext.length-4); //gets last 4 chars (extension type)
					document.getElementById('message').innerHTML="";
					document.forma.imagen_seleccionada.value = "";
					switch (ext) {
						case "jpeg":
						case ".jpg":
						case ".JPG":
						// filetype ok
						break;
						default:
						// alert("Data type invalid");
						document.getElementById('message').innerHTML="Error en tipo de imagen. Solo JPG!";
						//$jQuery('#file_upload').uploadifyCancel(queueID);
						break;
					}
	},

	
    'onAllComplete' : function(event,data) {
	   document.getElementById('message').innerHTML="Archivo seleccionado!";
	   document.forma.imagen_seleccionada.value = "1";
    },
	
	'onError'     : function (event,ID,fileObj,errorObj) {
      document.getElementById('message').innerHTML="Error: "+errorObj.type;
    }
	
  });

});

</script>


<script language="JavaScript">
  function valida() {
   o = document.forma;
   if (o.tipo.value=='') {
   	 alert("Indica el tipo de evento a reportar");
	 o.tipo.focus();
	 return;
   }
   if (o.asunto.value=='') {
   	 alert("Indica el asunto");
	 o.asunto.focus();
	 return;
   }
   if (o.descripcion.value=='') {
   	 alert("Describe la falla o recomendación");
	 o.descripcion.focus();
	 return;
   }

   document.forma.action='graba_bug.php';
   document.forma.submit();
  }
  function descarta() {
   document.forma.action='principal.php';
   document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
	<? $tit='Registro de Bug o Recomendación'; include('top.php'); ?>
	<?
        include('../conexion.php');
		
        $resultado= mysql_query("SELECT * FROM datos_pedido",$conexion);
        $row = mysql_fetch_array($resultado);

        $resultado= mysql_query("SELECT * FROM mail ",$conexion);
        $rowM = mysql_fetch_array($resultado);

        $resultado= mysql_query("SELECT * FROM config WHERE reg = 1",$conexion);
        $rowCFG = mysql_fetch_array($resultado);
        
        
      ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
       <input name="imagen_seleccionada" id="imagen_seleccionada" type="hidden" value="<?=$imagen_seleccionada;?>" />
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">
          <tr>
            <td colspan="2"><strong>DATOS DEL EVENTO O FALLA A REGISTRAR</strong></td>
            <td width="49%">
            <div align="right">
            <a href="lista_bugs.php"><img src="images/foto.png" alt="Ver anteriores" width="14" height="15" align="absmiddle" /> Ver reportes anteriores            </a>            </div></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td width="15%"><div align="right"><strong>Aplicaci&oacute;n:</strong></div></td>
            <td colspan="2">CMS            </td>
          </tr>
          <tr>
            <td width="15%"><div align="right"><strong>M&oacute;dulo:</strong></div></td>
            <td colspan="2"><?=$_GET['mod'];?>
            <input name="modulo" type="hidden" id="modulo" value="<?=$_GET['mod'];?>" />

            </td>
          </tr>
          <tr>
            <td><div align="right"><strong>Tienda:</strong></div></td>
            <td colspan="2">N/A            </td>
          </tr>
          <tr>
            <td><div align="right"><strong>Tipo de reporte:</strong></div></td>
            <td colspan="2"><select name="tipo" class="campo" id="tipo">
            	 <option value="" selected="selected">Selecciona..</option> 
              <? $resultadoT = mysql_query("SELECT * FROM bug_tipo ORDER BY nombre",$conexion);
			      while ($rowT = mysql_fetch_array($resultadoT)) {
				     echo '<option value="'.$rowT['clave'].'"';
					 if ($rowT['clave']==$tipo) echo 'selected';
					 echo '>'.$rowT['nombre'].'</option>';
				  }
			    ?>
                </select></td>
          </tr>
          <tr>
            <td><div align="right"><strong>Asunto:</strong></div></td>
            <td colspan="2"><input name="asunto" type="text" class="campo" id="asunto" size="60" maxlength="50" /></td>
          </tr>
          <tr>
            <td><div align="right"><strong>Descripci&oacute;n:</strong></div></td>
            <td colspan="2"><textarea name="descripcion" cols="70" rows="5" class="campo" id="descripcion"></textarea></td>
          </tr>
          <tr>
            <td><div align="right"><strong>Imagen:</strong></div></td>
            <td colspan="2"><span class="fLeft pr10">
              <input id="file_upload" name="file_upload" type="file" />
            </span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="2"><div id="message"></div></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="2"><input name="grabar" type="button" class="boton" onclick="valida();" value="GRABAR" />
                <input name="desc" type="button" class="boton" onclick="descarta();" value="DESCARTAR" id="desc" /></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="2">&nbsp;</td>
          </tr>
        </table>
      </form>    
    </div>
</div>
</body>
</html>
