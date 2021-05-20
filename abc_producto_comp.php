<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=23;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
	include('../conexion.php');

	$producto=$_POST['producto'];
	if (empty($producto)) $producto=$_GET['producto'];
	$categoria = $_POST['categoria']+0;
	$subcategoria = $_POST['subcategoria']+0;
	$marca = $_POST['marca']+0;
	
	// recordar filtros
	$texto = $_POST['texto'];
	$fcategoria = $_POST['fcategoria'];
	$fsubcategoria = $_POST['fsubcategoria'];
	$fmarca = $_POST['fmarca'];
	
	
	// obtener datos de producto para edición	
	if (!empty($producto)) {
	  $query = "SELECT comp_producto.*, categoria.nombre AS nombre_categoria, subcategoria.nombre AS nombre_subcategoria 
				FROM comp_producto 
				 LEFT JOIN categoria ON comp_producto.categoria = categoria.clave
				 LEFT JOIN subcategoria ON comp_producto.subcategoria = subcategoria.clave
				WHERE comp_producto.clave='$producto'";
	  $resultado= mysql_query($query,$conexion);

	  $row = mysql_fetch_array($resultado);
	  if (!$categoria) $categoria = $row['categoria'];
	  if (!$subcategoria) $subcategoria = $row['subcategoria'];
	  if (!$marca) $marca = $row['marca'];
	} 



	// detectar si es categoria de accesorios
	if ($categoria) {
		$resultadoCAT = mysql_query("SELECT accesorios FROM categoria WHERE clave = $categoria",$conexion);
		$rowCAT = mysql_fetch_array($resultadoCAT);
		if ($rowCAT['accesorios']) 
			$es_accesorio = true;
		else 
			$es_accesorio = false;
	} else $es_accesorio = false;

	$mostrar = 0;
	if ($categoria) {
		if ($subcategoria) $mostrar = 1;
		else {
			$resultado = mysql_query("SELECT 1 FROM subcategoria WHERE categoria = $categoria LIMIT 1",$conexion);
			$enc = mysql_num_rows($resultado);
			if ($enc <= 0) $mostrar = 1; 
		}
	}
		
	// crear nombre temporal de imagen
	$ht = date("U");
	srand((double)microtime()*1000000);
	$token='';
	for ($i=1; $i<=4; $i++) {
	   $token=$token.chr(rand(97,122));
	} 
	$archivo_tmp = date("Ymd")."_".$token."_".$ht;



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
    'script'    : './uploadify/uploadify.php',
    'cancelImg' : './uploadify/cancel.png',
    'folder'    : './uploads',
    'expressInstall' : './uploadify/expressInstall.swf',
	'buttonText' : 'SELECCIONA',
    'fileExt'   : '*.jpg',
    'fileDesc'  : 'Archivos JPG',
	
    'scriptData'  : {'identificador':'<?=$archivo_tmp;?>','tipo':'jpg'},
	'sizeLimit'   : 358400,
	'auto'      : true,
    'onSelect' : function(event,queueID,fileObj){
					var ext = fileObj.name;
					ext = ext.substr(ext.length-4); //gets last 4 chars (extension type)
					document.getElementById('message').innerHTML="";
					switch (ext) {
						case ".jpg":
						case ".JPG":
						// filetype ok
						break;
						default:
						// alert("Data type invalid");
						document.getElementById('message').innerHTML="Error en tipo de imagen. Solo jpg!";
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

</script>

<script language="JavaScript">
  function valida() {
    if (document.forma.marca.value == "") {
     alert("Falta marca.");
	 document.forma.marca.focus();
     return;
     }
    if (document.forma.categoria.value == "") {
     alert("Falta categoría.");
	 document.forma.categoria.focus();
     return;
     }
    if (document.forma.subcategoria.value == "") {
     alert("Falta subcategoría.");
	 document.forma.subcategoria.focus();
     return;
     }
    if (document.forma.nombre.value == "") {
     alert("Falta nombre.");
	 document.forma.nombre.focus();
     return;
     }
    if (document.forma.sku.value == "") {
     alert("Falta SKU.");
	 document.forma.sku.focus();
     return;
     }

   document.forma.action='graba_producto_comp.php';
   document.forma.submit();
  }
  function descarta() {
   document.forma.action='lista_producto_comp.php';
   document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
	<? $tit='Administrar Productos de Competencia'; include('top.php'); ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma" enctype="multipart/form-data">
      	<input type="hidden" name="archivo_tmp" id="archivo_tmp" value="<?=$archivo_tmp;?>" />
      	<input type="hidden" name="texto" id="texto" value="<?=$texto;?>" />
      	<input type="hidden" name="fcategoria" id="fcategoria" value="<?=$fcategoria;?>" />
      	<input type="hidden" name="fsubcategoria" id="fsubcategoria" value="<?=$fsubcategoria;?>" />
      	<input type="hidden" name="fmarca" id="fmarca" value="<?=$fmarca;?>" />
        <table width="100%" border="0" align="center" cellpadding="2" cellspacing="0" class="texto">
          <tr>
            <td><div align="right">Marca:</div></td>
            <td><select name="marca" id="marca" class="campo">
            	<option value="">Selecciona...</option>
                <? $resultadoMAR = mysql_query("SELECT * FROM comp_marca ORDER BY nombre",$conexion);
			      while ($rowMAR = mysql_fetch_array($resultadoMAR)) {
				     echo '<option value="'.$rowMAR['clave'].'"';
					 if ($rowMAR['clave']==$marca) echo 'selected';
					 echo '>'.$rowMAR['nombre'].'</option>';
				  }
			    ?>
            </select></td>
          </tr>
          <tr>
            <td><div align="right">Categor&iacute;a:</div></td>
            <td><select name="categoria" class="campo" id="categoria" onchange="document.forma.subcategoria.value=''; document.forma.submit()">
                <option value="">Seleccionar categor&iacute;a...</option>
                <?  $resCAT= mysql_query("SELECT * FROM categoria ORDER BY orden, nombre",$conexion);
					  while ($rowCAT = mysql_fetch_array($resCAT)) { 
						echo '<option value="'.$rowCAT['clave'].'"';
						if ($rowCAT['clave']==$categoria) echo ' selected';
						echo '>'.$rowCAT['nombre'].'</option>';
					  }
				  ?>
            </select></td>
          </tr>
          <tr>
            <td><div align="right">Subcategor&iacute;a:</div></td>
            <td><select name="subcategoria" class="campo" id="subcategoria" onchange="document.forma.submit()">
                <? // detectar si tiene subcategorias 
				   $resSCAT = mysql_query ("SELECT clave FROM subcategoria WHERE categoria = $categoria",$conexion);
				   $totSCAT = mysql_num_rows($resSCAT);
				   if ($totSCAT > 0 || !$categoria) {
				?>
                <option value="">Seleccionar subcategor&iacute;a...</option>
                <?  
				      $query = "SELECT * FROM subcategoria WHERE categoria = $categoria ORDER BY orden, nombre";
					  $resSCAT = mysql_query($query,$conexion);
					  while ($rowSCAT = mysql_fetch_array($resSCAT)) { 
						echo '<option value="'.$rowSCAT['clave'].'"';
						if ($rowSCAT['clave']==$subcategoria) echo ' selected';
						echo '>'.$rowSCAT['nombre'].'</option>';
					  }
				   } else { // categoria sin subcategorias ?>
                <option value="0" selected="selected">Sin subcategor&iacute;as</option>
                <? } ?>
            </select></td>
          </tr>
          
          <? if ($mostrar) { ?>
          <tr>
            <td><div align="right">Nombre:</div></td>
            <td><input name="nombre" type="text" class="campo" id="nombre" value="<?= $row['nombre']; ?>" size="100" maxlength="100" /></td>
          </tr>
          <tr>
            <td><div align="right">SKU:</div></td>
            <td><input name="sku" type="text" class="campo" id="sku" value="<?= $row['sku']; ?>" size="50" maxlength="50" /></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <!--tr>
            <td align="right" nowrap="nowrap"><strong>Exclusivo RM12:</strong></td>
            <td colspan="10"><input name="exclusivo_rm12" type="checkbox" id="exclusivo_rm12" value="1" <? if($row['exclusivo_rm12']) echo 'checked';?> />
              (Seleccionar para evitar que en POS se seleccione cualquier otro CEDIS)</td>
          </tr-->
          <tr>
            <td align="right">Menor 3 meses:</td>
            <td>
            <input name="menor_3m" type="checkbox" id="menor_3m" value="1" <? if($row['menor_3m']) echo 'checked';?> />            </td>
          </tr>
          <tr>
            <td align="right">Precio Real:</td>
            <td align="left"><input name="precio_real" type="text" class="campo" id="precio_real" value="<?= $row['precio_real']; ?>" size="20" maxlength="12" onblur="this.value = this.value.replace(/[^0-9.]+/g,'');" /></td>
          </tr>
          <tr>
            <td align="right" valign="top">Precio Promoci&oacute;n:</td>
            <td align="left"><input name="precio_promocion" type="text" class="campo" id="precio_promocion" value="<?= $row['precio_promocion']; ?>" size="20" maxlength="12" onblur="this.value = this.value.replace(/[^0-9.]+/g,'');"/></td>
          </tr>
          <tr>
            <td align="right" valign="top">&nbsp;</td>
            <td align="left"></td>
          </tr>
          <?   
			  // campos administrables
			  $resultadoCA = mysql_query("SELECT * FROM campo WHERE categoria = $categoria AND subcategoria = $subcategoria ORDER BY orden",$conexion);
			  while ($rowCA = mysql_fetch_array($resultadoCA)) {
				$ic = substr($rowCA['campo_tabla'],6,3);
				$nombrecampo = "campo_".$ic;			  
			  ?>
          <tr>
            <td align="right" valign="top"><?= $rowCA['nombre_campo'];?></td>
            <td align="left">
			<? if ($rowCA['tipo'] == 'texto') { ?>
               <input name="campo_<?=$ic;?>" type="text" class="campo" id="campo_<?=$ic;?>" value="<?= $row[$nombrecampo]; ?>" size="70" maxlength="100">
		   <? }
               if ($rowCA['tipo'] == 'menu') { 
                    $arr_valores = split(chr(10),$rowCA['valores']);
            ?>
               <select name="campo_<?=$ic;?>" size="1" class="campo" id="campo_<?=$ic;?>">
             <? for ($icv = 0; $icv < count($arr_valores); $icv++) {  
					$valor = mysql_real_escape_string($arr_valores[$icv]);

			 ?>
                <option value=<?=$valor;?> <? if (trim(mysql_real_escape_string($row[$nombrecampo])) == trim($valor)) echo ' selected ';?>><?=$arr_valores[$icv];?></option>
             <? } ?>
               </select>						   
            <? } ?></td>
          </tr>
          <?  } // while rowCA  ?>
          <? } // mostrar ?>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>

          <tr>
            <td><div align="right">
              <p>Foto:<br />
                Formato<strong> jpg</strong> de<br /> 
                <strong>260 x 260</strong> pixeles</p>
              </div></td>
            <td><input id="file_upload" name="file_upload" type="file" />
            <div id="message"></div></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>
                <? if ($mostrar) { ?><input name="grabar" type="button" class="boton" onclick="valida();" value="GRABAR" /><? } ?>
                <input name="desc" type="button" class="boton" onclick="descarta();" value="REGRESAR AL LISTADO" id="desc" />
            <input name="producto" type="hidden" id="producto" value="<?= $producto; ?>" />            </td>
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
