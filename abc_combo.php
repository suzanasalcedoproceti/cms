<?
//if (!include('ctrl_acceso.php')) return; 

include("funciones_ajax.php");
include_once('funciones.php');
include("../conexion.php");

$modulo=2;
if (!op($modulo))  {
	$aviso = 'Usuario sin permiso para acceder a este módulo';
	$aviso_link = 'principal.php';
	include('mensaje_sistema.php');
	return;
}

$combo = $_GET['combo'];
if (!$combo) $combo = $_POST['combo'];

$nuevo_combo = $_GET['nuevo'];
if ($nuevo_combo) { // de get, primera vez
	$_SESSION['ss_combo'] = array();
}
// recordarlo, para saber si pedir imagen
if (!isset($nuevo_combo)) $nuevo_combo = $_POST['nuevo_combo'];

// primera vez en edición, cargar datos.. despues ya no porque se recarga la página
$editar = $_GET['editar'];
if ($editar && $combo) {

	$_SESSION['ss_combo'] = array();
	$resultadoC = mysql_query("SELECT * FROM combo WHERE clave = $combo");
	$rowC = mysql_fetch_array($resultadoC);
	$nombre = $rowC['nombre'];
	$descripcion = $rowC['descripcion'];
	$activo = $rowC['activo'];
	// cargar detalle
	$resultadoDC = mysql_query("SELECT combo_detalle.*, producto.nombre AS nombre_producto 
								  FROM combo_detalle 
								  LEFT JOIN producto ON combo_detalle.producto = producto.clave
								 WHERE combo = $combo ORDER by orden");
	while ($rowDC = mysql_fetch_array($resultadoDC)) {

		end($_SESSION['ss_combo']);
		$nuevoElemento = key($_SESSION['ss_combo'])+1;
	
		$_SESSION['ss_combo'][$nuevoElemento]['orden'] = $rowDC['orden'];
		$_SESSION['ss_combo'][$nuevoElemento]['producto'] = $rowDC['producto'];
		$_SESSION['ss_combo'][$nuevoElemento]['nombre'] = $rowDC['nombre_producto'];
		$_SESSION['ss_combo'][$nuevoElemento]['modelo'] = $rowDC['modelo'];
		$_SESSION['ss_combo'][$nuevoElemento]['lista_precios'] = $rowDC['lista_precios'];
	
	}
	
} else {
	$nombre = $_POST['nombre'];
	$descripcion = $_POST['descripcion'];
	$activo = $_POST['activo'];
}

$imagen_seleccionada = $_POST['imagen_seleccionada'];


//include("../libprod.php");

$imagen_tmp = date("Ymd")."_".session_id();

///////////////

/// ajax autocompletar //////
require ('xajax/xajax_core/xajax.inc.php');
$xajax = new xajax(); 
$xajax->register(XAJAX_FUNCTION, 'autocomplete'); 
//$xajax->register(XAJAX_FUNCTION, 'cambia_orden'); 
$xajax->processRequest(); 
$xajax->configure('javascript URI','xajax/'); 
// las funciones van incluidas en funciones_ajax, arriba. Si van pegadas, van aqui..

/////////////////////////////
// Sesión combo
/////////////////////////////
if (!isset($_SESSION['ss_combo'])) {
	$_SESSION['ss_combo'] = array();
}


/////////////////////////////
//  ACCIONES 
//////////////////////////////

if ($_POST['accion'] == 'agrega_producto') {
	$clave_producto = $_POST['producto'];
	$lista_precios = $_POST['lista_precios'];
	$nombre_producto = $_POST['nombre_producto'];
	$modelo = $_POST['modelo'];
	$orden = $_POST['orden']+0;

	end($_SESSION['ss_combo']);
	$nuevoElemento = key($_SESSION['ss_combo'])+1;

	$_SESSION['ss_combo'][$nuevoElemento]['producto'] = $clave_producto;
	$_SESSION['ss_combo'][$nuevoElemento]['nombre'] = $nombre_producto;
	$_SESSION['ss_combo'][$nuevoElemento]['modelo'] = $modelo;
	$_SESSION['ss_combo'][$nuevoElemento]['lista_precios'] = $lista_precios;
	$_SESSION['ss_combo'][$nuevoElemento]['orden'] = $orden;
	
}

if ($_POST['accion'] == 'elimina_producto') {
    unset($_SESSION['ss_combo'][$_POST['iproducto']]);
}
if ($_POST['accion'] == 'cambia_orden') {
    $_SESSION['ss_combo'][$_POST['iproducto']]['orden'] = (int) $_POST['orden']+0;
}

usort($_SESSION['ss_combo'], "comparar_orden"); 


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Panel de Control</title>
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<link href="css/menu.css" rel="stylesheet" type="text/css" />
<link href="css/ui_autocomplete.css" rel="stylesheet" type="text/css" />
<!--[if lte IE 6]><link rel="stylesheet" href="css/menu_ie6.css" /><![endif]-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript" src="js/menu.js"></script>
<link href="js/src/shadowbox.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/src/shadowbox.js"></script>
<script src="js/ui.js" type="text/javascript" language="javascript1.2"></script>
<link href="./uploadify/uploadify.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="./uploadify/swfobject.js"></script>
<script type="text/javascript" src="./uploadify/jquery.uploadify.v2.1.4.min.js"></script>
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
      document.forma.imagen_seleccionada.value = "1";      
                              }
  });
}
</script>
<script type="text/javascript">
	Shadowbox.init({
		language: 'es',
		players:  ['img', 'html', 'iframe', 'qt', 'wmp', 'swf', 'flv']
	});
</script>

<?php 
 $xajax->printJavascript("xajax/"); 
?>

<script language="JavaScript">
  function valida_agregar() {
   o = document.forma;
   if (o.producto.value=='') {
	alert ("Debes seleccionar un producto a agregar");
	return;
   }
   if (o.lista_precios.value=='') {
	alert ("Debes seleccionar la lista de precios para el producto");
	return;
   }
   document.forma.accion.value = 'agrega_producto';
   document.forma.action='abc_combo.php';
   document.forma.submit();
  }
  function valida() {
   o = document.forma;
   if (o.nombre.value=='') {
	alert ("Debes capturar el nombre del combo");
	return;
   }
   if (o.descripcion.value=='') {
	alert ("Debes capturar la descripción del combo");
	return;
   }
   if (o.clasificacion.value=='') {
	alert ("Debes indicar la clasificación");
	return;
   }
   <? if ($nuevo_combo) { ?>
   if (!o.imagen_seleccionada.value==1) {
	alert ("Debes seleccionar una imagen");
	return;
   }
   <? } ?>
   if (o.tot_prods.value==0) {
	alert ("Debes agregar al menos un producto al combo");
	return;
   }
   document.forma.action='graba_combo.php';
   document.forma.submit();
  }

  function recarga(accion) {
   document.forma.accion.value=accion;
   document.forma.action='abc_combo.php';
   document.forma.submit();
  }  	
  function elimina_producto(producto,nombre) {
   continuar = window.confirm("Deseas eliminar el producto?");
   if (!continuar) {
		 return;
   }
   document.forma.iproducto.value = producto;
   document.forma.accion.value = "elimina_producto";
   document.forma.action='abc_combo.php';
   document.forma.submit();
  }
  function cambia_orden(producto,orden) {
   document.forma.iproducto.value = producto;
   document.forma.accion.value = "cambia_orden";
   document.forma.orden.value = orden;
   document.forma.action='abc_combo.php';
   document.forma.submit();
  }
  function descarta() {
   document.forma.action='lista_combo.php';
   document.forma.submit();
  }  	
</script>
</head>

<body>
<div id="container">
	<? $tit='Configurar Combo'; $sec_ped = 'sel'; include('top.php'); ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
       <input name="accion" id="accion" type="hidden" value="" />
       <input name="iproducto" id="iproducto" type="hidden" value="" />
       <input name="combo" id="combo" type="hidden" value="<?=$combo;?>" />
       <input name="nuevo_combo" id="nuevo_combo" type="hidden" value="<?=$nuevo_combo;?>" />
       <input name="imagen_seleccionada" id="imagen_seleccionada" type="hidden" value="<?=$imagen_seleccionada;?>" />
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">
          <tr>
            <td width="21%"><div align="right">Nombre:</div></td>
            <td colspan="4"><input name="nombre" type="text" class="campo" id="nombre" value="<?= $nombre; ?>" size="100" maxlength="100" /></td>
          </tr>
          <tr>
            <td valign="top"><div align="right">Descripci&oacute;n:</div></td>
            <td colspan="4"><textarea name="descripcion" cols="97" rows="4" class="campo" id="descripcion"><?=$descripcion;?></textarea></td>
          </tr>
          <tr>
            <td><div align="right">Clasificaci&oacute;n:</div></td>
            <td colspan="4"><select name="clasificacion" id="clasificacion">
              <option value="low" <? if ($rowC['clasificacion']=='low') echo 'selected';?>>Low Ticket</option>
              <option value="ltl" <? if ($rowC['clasificacion']=='ltl') echo 'selected';?>>LTL</option>
              <option value=""  <? if ($rowC['clasificacion']=='') echo 'selected';?>></option>
            </select>              (para criterio de entregas nacionales)</td>
          </tr>
          <tr>
            <td><div align="right">Activo:</div></td>
            <td colspan="4"><input name="activo" type="checkbox" id="activo" value="1" <? if ($activo) echo 'checked';?> /></td>
          </tr>
          <tr>
            <td height="30" align="right" valign="top">Imagen (JPG):<br />
              max 1MB (
              <strong>800</strong><strong> </strong>  px de ancho max)</td>
            <td height="30" colspan="4" align="left">
                <div class="fLeft pr10"><input id="file_upload" name="file_upload" type="file" /></div>
                <div class="fLeft">
                  <? if (file_exists("images/cms/combos/".$combo.".jpg")) { ?>
                   <a href="images/cms/combos/<?=$combo;?>.jpg?<?=$ht;?>" rel="shadowbox"><img src="images/cms/combos/<?=$combo;?>t.jpg?<?=$ht;?>" height="45" /></a>
                  <? } ?>
				</div>
            <div id="message"></div></td>
          </tr>
          <tr>
            <td><strong>Productos del Combo</strong></td>
            <td width="28%">&nbsp;</td>
            <td colspan="3" valign="top">&nbsp;</td>
          </tr>
          <tr>
            <td bgcolor="#999999"><strong>Producto</strong></td>
            <td bgcolor="#999999"><strong>Nombre del producto</strong></td>
            <td width="4%" bgcolor="#999999"><strong>Orden</strong></td>
            <td width="22%" bgcolor="#999999"><strong>Lista de Precios</strong></td>
            <td width="25%" bgcolor="#999999"><input type="hidden" name="producto" id="producto"  width="50"/>
              <input type="hidden" name="modelo" id="modelo"  width="50"/>
              <input type="hidden" name="productox" id="productox"  width="50"/>            </td>
          </tr>
          
          <tr>
            <td valign="top"><div class="busqueda">
              <input type="text" id="busqueda" onfocus="document.forma.productox.value = this.value;" 
                onkeypress="if (event.keyCode==13) {  document.forma.productox.value = this.value; document.forma.submit(); }" onkeyup="xajax_autocomplete(this.value);"
                autocomplete="off" />
            </div></td>
          <td><input name="nombre_producto" type="text" id="nombre_producto" size="45" maxlength="100" readonly="readonly"/></td>
           <td><input name="orden" type="text" class="campo" id="orden" size="3" maxlength="3"/></td>
           <td><select name="lista_precios" class="campo" id="lista_precios">
             <option value="" selected="selected">Selecciona Lista de Precios...</option>
              <?php
					$resLP = mysql_query("SELECT * FROM lista_precios",$conexion);
					while ($rowLP = mysql_fetch_assoc($resLP)) {
						echo '<option value="'.$rowLP['clave'].'"';
						echo '>'.$rowLP['nombre'].'</option>';
					} // while
			  ?>
           </select></td>
           <td><input name="bagreg" type="button" class="boton_agregar" onclick="valida_agregar();" value="Agregar" id="bagreg" /></td>
          </tr>
          <tr>
            <td colspan="5" >
             <table width="100%" border="0" cellpadding="2" cellspacing="2">
              <tr>
                <td colspan="6">&nbsp;</td>
               </tr>
              <tr bgcolor="AAAAAA">
                <th><div align="center">Orden</div></th>
                <th>Modelo</th>
                <th>Nombre</th>
                <th align="center">Lista de Precios</th>
                <th><div align="right">Precio*</div></th>
                <th>&nbsp;</th>
              </tr>
			  <?
			  		$total = 0;
                    foreach($_SESSION['ss_combo'] as $i_prod => $item_prod) {
						
						//obtener precio 
						$xxlista = 'precio_'.$item_prod['lista_precios'];
						$xproducto = $item_prod['producto'];
						$query = "SELECT $xxlista AS precio FROM producto WHERE clave = $xproducto";
						$resultadoPROD = mysql_query($query,$conexion);
						$rowPROD = mysql_fetch_array($resultadoPROD);
						$total += $rowPROD['precio']*1.16;

					

              ?>
              <tr bgcolor="#FFFFFF" >
                <td align="center">
                <input type="text" class="campo" value="<?=$item_prod['orden'];?>" size="3" maxlength="3" onchange="javascript:cambia_orden(<?=$i_prod;?>,this.value);"  />                </td>
                <td><?=$item_prod['modelo'];?></td>
                <td><?= $item_prod['nombre'];?></td>
                <td align="center"><?= $item_prod['lista_precios'];?></td>
                <td align="right" ><?= number_format($rowPROD['precio'],2);?></td>
                <td align="center" ><a href="javascript:elimina_producto(<?= $i_prod; ?>);"><img src="images/borrar.png" alt="Elimina producto del pedido" title="Quitar producto" width="14" height="15" border="0" align="top" /> </a> </td>
              </tr>

            
          <? } // foreach ?>
              <tr >
                <td colspan="4">* Precio no se graba; es s&oacute;lo informativo. Se calcula al mostrarse en la tienda.</td>
                <td align="right" ><strong><?=number_format($total,2);?></strong></td>
                <td >&nbsp;</td>
              </tr>
			</table>            </td>
          </tr>		  

          <tr>
            <td colspan="5">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="5"><input name="bgra" type="button" class="boton" onclick="valida();" value="Grabar Combo" id="bgra" />
            
            <input name="desc" type="button" class="boton" onclick="descarta();" value="Regresar al listado" id="desc" />
            <input name="tot_prods" type="hidden" id="tot_prods" value="<?=count($_SESSION['ss_combo']);?>" /></td>
          </tr>
        </table>
      </form>    
    </div>
</div>
</body>
</html>
