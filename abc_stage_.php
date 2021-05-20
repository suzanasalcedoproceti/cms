<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	include("lib.php");
	$modulo=11;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
	include('../conexion.php');

	$stage=$_POST['stage'];
	if (empty($stage)) $stage=$_GET['stage'];
	$autorizar=$_GET['autorizar'];

	if (!empty($stage)) {
	  $resultado= mysql_query("SELECT * FROM stage WHERE clave='$stage' LIMIT 1",$conexion);
	  $row = mysql_fetch_array($resultado);
    //print_r($row);
	  if (fecha($row['inicio_vigencia']) && fecha($row['fin_vigencia'])) 
		  $fechas = fechamy2mx($row['inicio_vigencia']).' - '.fechamy2mx($row['fin_vigencia']); 
	} else {
		$fechas = '';
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
<script src="https://code.jquery.com/jquery-migrate-1.1.1.js"></script>
<link href="css/jquery.datepick.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.datepick.js" type="text/javascript" language="javascript1.2"></script>
<script src="js/jquery.datepick-es.js" type="text/javascript" language="javascript1.2"></script>
<link href="./uploadify/uploadify.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="./uploadify/swfobject.js"></script>
<script type="text/javascript" src="./uploadify/jquery.uploadify.v2.1.4.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
  $('#fechas').datepick({showOn: 'both', buttonImageOnly: true, buttonImage: 'images/calendario.gif', rangeSelect: true, numberOfMonths: 2, minDate: '01/01/2011', maxDate: '+1m' } );
});
</script>

<script type="text/javascript">
/*
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
	'sizeLimit'   : 358400,
	'auto'      : true,
    'onSelect' : function(event,queueID,fileObj){
					var ext = fileObj.name;
					ext = ext.substr(ext.length-4); //gets last 4 chars (extension type)
					document.getElementById('message').innerHTML="";
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
	  // document.getElementById('message').innerHTML="Archivo subido!";
    },
	
	'onError'     : function (event,ID,fileObj,errorObj) {
      document.getElementById('message').innerHTML="Error: "+errorObj.type;
    }
	
  });

});
*/

$(document).ready(function() {
    $("#file_upload").change(function () {
        var fileExtension = ['jpeg', 'jpg'];
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
  frmData.append('tipo','jpg');
  frmData.append('fileext','*.jpg');
  frmData.append('fileDesc','Archivos JPG');
  frmData.append('folder','uploads');


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
	if (document.forma.producto.value!="" && document.forma.combo.value!="") {
	 alert("No puedes elegir producto y combo a la vez para un stage");
	 return;
	}
	if ((document.forma.producto.value!="" || document.forma.combo.value!="" || document.forma.video.value!="") && document.forma.url.value!="") {
	 alert("No puedes asociar un Link cuando hay: producto, combo o video seleccionado");
	 return;
	}
	
	
	// combina claves de empresas omitidas seleccionados en un string separado por comas
	var string_pr = '';
	for (var i=0; i < document.forma.lista_omitidas.length; i++) {
	  string_pr += ' '+document.forma.lista_omitidas.options[i].value+',';
	}
	document.forma.omitidas.value = string_pr;

   document.forma.action='graba_stage.php';
   document.forma.submit();
  }
  function rechaza() {
   document.forma.action='borra_stage.php';
   document.forma.submit();
  }
  function descarta() {
   document.forma.action='lista_stage.php';
   document.forma.submit();
  }
  function agregaEm(inForm,texto,valor) {
		var siguiente = inForm.lista_omitidas.options.length;
		var encontrado = false;
		for (var i=0; i < inForm.lista_omitidas.length; i++) {
			if (inForm.lista_omitidas.options[i].value == valor) {
				encontrado = true;
			}
		}
		if (!encontrado) {
			eval("inForm.lista_omitidas.options[siguiente]=" + "new Option(texto,valor,false,true)");
		}
  }
  function eliminaEm(inForm,indice) {
		var i = inForm.lista_omitidas.options.length;
		inForm.lista_omitidas.options[indice] = null;
  }

</script>
</head>

<body>
<div id="container">
	<? $tit='Administrar Stages'; include('top.php'); ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma" enctype="multipart/form-data">
      	<input type="hidden" name="imagen_tmp" id="imagen_tmp" value="<?=$imagen_tmp;?>" />
        <input type="hidden" name="omitidas" id="omitidas" />
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><div align="right">Nombre:</div></td>
            <td><input name="nombre" type="text" class="campo" id="nombre" value="<?=htmlspecialchars($row['nombre']); ?>" size="100" maxlength="100" /></td>
          </tr>
          <tr>
            <td><div align="right">Producto:</div></td>
            <td>
              <select name="producto" class="campo" id="producto">
              		<option value="">Ningún producto...</option>
                <?    
					
						  $cat = '';
						  $query = "SELECT producto.clave, producto.nombre, modelo, producto.categoria, categoria.nombre AS nombre_categoria 
						  			FROM producto 
									LEFT JOIN categoria ON producto.categoria = categoria.clave
									WHERE NOT categoria.accesorios AND (producto.estatus=1 OR producto.estatus=2)
								   ORDER BY categoria.orden, producto.subcategoria, producto.nombre";
								   
					      $resPRO= mysql_query($query,$conexion);
                          while ($rowPRO = mysql_fetch_array($resPRO)) { 
						  	if ($cat != $rowPRO['categoria']) {
							  echo '<optgroup label="'.$rowPRO['nombre_categoria'].'"></optgroup>';
							  $cat = $rowPRO['categoria'];
							}
			                echo '<option value="'.$rowPRO['clave'].'" title="'.$rowPRO['nombre'].'"';
							if ($rowPRO['clave']==$row['producto']) echo ' selected ';
							echo '>'.$rowPRO['modelo'].'</option>';
                          }
                      ?>
                 </select></td>
          </tr>
          <tr>
            <td><div align="right">Combo:</div></td>
            <td><select name="combo" class="campo" id="combo">
                <option value="">Ning&uacute;n combo...</option>
                <?    
					
						  $cat = '';
						  $query = "SELECT * FROM combo WHERE activo ORDER BY nombre";
					      $resPRO= mysql_query($query,$conexion);
                          while ($rowPRO = mysql_fetch_array($resPRO)) { 
			                echo '<option value="'.$rowPRO['clave'].'" title="'.$rowPRO['nombre'].'"';
							if ($rowPRO['clave']==$row['combo']) echo ' selected ';
							echo '>'.$rowPRO['nombre'].'</option>';
                          }
                      ?>
            </select></td>
          </tr>
          <tr>
            <td><div align="right">ID video youtube:</div></td>
            <td><input name="video" type="text" class="campo" id="video" value="<?= $row['video']; ?>" size="30" maxlength="20" /></td>
          </tr>
          <tr>
            <td><div align="right">Link:</div></td>
            <td><input name="url" type="text" class="campo" id="url" value="<?= $row['url']; ?>" size="100" maxlength="100" /> 
              (Incluir protocolo: <strong>http://</strong> o <strong>https://</strong> )             </td>
          </tr>
          <tr>
            <td height="28" align="right" valign="top">&nbsp;</td>
            <td height="30" align="left"><input name="externa" type="checkbox" id="externa" value="1" <? if ($row['externa']) echo 'checked';?> />
              Abrir en una nueva ventana</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><div align="right">Empresa:</div></td>
            <td><select name="empresa" class="campo" id="empresa">
                <option value="">Genérico...</option>
            <?  include('../conexion.php');
			    $resEMP= mysql_query("SELECT * FROM empresa WHERE (estatus=1 OR estatus=2) ORDER BY nombre",$conexion);
				while ($rowEMP = mysql_fetch_array($resEMP)) { 
					echo '<option value="'.$rowEMP['clave'].'"';
					if ($rowEMP['clave'] == $row['empresa']) echo ' selected ';
					echo '>'.$rowEMP['nombre'].'</option>';
						  }
					  ?>
              </select>            </td>
          </tr>
          <tr>
            <td><div align="right">Solo para Mobile:</div></td>
            <td><input name="mobile" type="checkbox" id="mobile" value="1" <? if ($row['mobile']) echo 'checked';?> />
              La imagen debe ser de 520 x 230 pixeles m&aacute;ximo</td>
          </tr>
          <tr>
            <td><div align="right">Solo para Proyectos:</div></td>
            <td><p>
              <input name="solo_proyectos" type="checkbox" id="solo_proyectos" value="1" <? if ($row['solo_proyectos']) echo 'checked';?> />
              Mismo tama&ntilde;o que TAW</p></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td height="30" align="left">&nbsp;</td>
          </tr>
          <tr>
            <td><div align="right">Vigencia:</div></td>
            <td height="30" align="left">
            <input name="fechas" type="text" class="fLeft fechas" id="fechas" value="<?=$fechas;?>" readonly="readonly" />
            <div style="padding:4px 0 0 20px">&nbsp;&nbsp;&nbsp;Si se deja en blanco no hay vigencia</div></td>
          </tr>
          <tr>
            <td valign="top">&nbsp;</td>
            <td height="30" align="left">&nbsp;</td>
          </tr>
          <tr>
            <td valign="top"><div align="right">Empresas omitidas:</div></td>
            <td height="30" align="left"><table border="0" cellpadding="0" cellspacing="0" class="texto">
              <tr>
                <td colspan="3">(Solo cuando se elige empresa Gen&eacute;rica, para asignar TODAS excepto las de esta lista)</td>
              </tr>
              <tr>
                <td><strong>Empresas  disponibles:</strong><br />
                  (dbl clic para seleccionar)</td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td><strong>Empresas   seleccionados:</strong><br />
                  (dbl clic para eliminar de la lista)</td>
              </tr>
              <tr>
                <td>
                  <select name="emp_omitidas" size="10" class="campo" id="emp_omitidas" ondblclick="agregaEm(document.forma,this.options[this.selectedIndex].text,this.value);">
                    <?    
					      $CR = chr(10);
						  $cat = '';
						  $query = "SELECT clave, nombre FROM empresa
								   ORDER BY nombre";
								   
					      $resEMP = mysql_query($query,$conexion);
                          while ($rowEMP = mysql_fetch_array($resEMP)) { 
			                echo $CR.'<option value="'.$rowEMP['clave'].'" title="'.$rowEMP['nombre'].'">'.$rowEMP['nombre'].'</option>';
                          }
                      ?>
                  </select>                </td>
                <td>&nbsp;</td>
                <td><select name="lista_omitidas" size="10" class="campo" id="lista_omitidas" ondblclick="eliminaEm(document.forma,this.selectedIndex);" style="min-width:200px;">
                    <?  $op=explode(',',$row['empresas_omitidas']);
						for ($i=0; $i<=count($op)-2; $i++) {
						  $claveemp=trim($op[$i]);
						  $resEMP= mysql_query("SELECT * FROM empresa WHERE clave=$claveemp",$conexion);
						  $rowEMP = mysql_fetch_array($resEMP);
						  echo $CR.'<option value="'.$claveemp.'" title="'.$rowEMP['nombre'].'">'.$rowEMP['nombre'].'</option>';
						}
					?>
                </select></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td height="28" align="right" valign="top">&nbsp;</td>
            <td height="30" align="left"><strong>Nota:</strong> Si el stage tiene video pero no tiene producto, se ignorar&aacute; la imagen de stage y se pondr&aacute; una espec&iacute;fica para video.</td>
          </tr>
          <tr>
            <td height="28" align="right" valign="top">Imagen (JPG):<br />
            max 2,000 KB<br />
            <strong>1110</strong> x <strong>540</strong> pixeles</td>
          <td height="30" align="left"><input id="file_upload" name="file_upload" type="file" />
          <div id="message"></div>            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><? if (file_exists("images/cms/stages/".$stage.".jpg")) { ?>
                <div style="float:left;"><img src="images/cms/stages/<?=$stage;?>.jpg?<?=$ht;?>" width="600" /></div>
                <? } ?>            </td>
          </tr>
          <? 
			if (!$autorizar) { ?>
          <tr>
            <td>&nbsp;</td>
            <td>
                <input name="grabar" type="button" class="boton" onclick="valida();" value="GRABAR" />
                <input name="desc" type="button" class="boton" onclick="descarta();" value="REGRESAR AL LISTADO" id="desc" />
            <input name="stage" type="hidden" id="stage" value="<?= $stage; ?>" />            </td>
          </tr>
          <? } else { ?>
          <tr>
            <td>&nbsp;</td>
            <td>
                <input name="grabar" type="button" class="boton" onclick="valida();" value="AUTORIZAR" />
	            <input name="rechazar" type="button" class="boton" onclick="rechaza();" value="RECHAZAR" />
                <input name="stage" type="hidden" id="stage" value="<?= $stage; ?>" /> 
                <input name="autorizar" type="hidden" id="autorizar" value="<?= $autorizar; ?>" />             </td>
          </tr>
          <? } ?>
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
