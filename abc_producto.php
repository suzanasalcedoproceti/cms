<?php
// Control de Cambios
// Julio 21 2016 Bitmore
// Destacar productos para Proyectos

    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=4;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
	include('../conexion.php');
	include("funciones_ajax.php");

	/// ajax autocompletar //////
	require ('xajax/xajax_core/xajax.inc.php');
	$xajax = new xajax(); 
	$xajax->register(XAJAX_FUNCTION, 'autocomplete_rel'); 
	$xajax->processRequest(); 
	$xajax->configure('javascript URI','xajax/'); 
	// las funciones van incluidas en funciones_ajax, arriba. Si van pegadas, van aqui..


	$producto=$_POST['producto'];
	if (empty($producto)) $producto=$_GET['producto'];
	$autorizar=$_GET['autorizar'];
	$categoria = $_POST['categoria']+0;
	$subcategoria = $_POST['subcategoria']+0;
	
	// recordar filtros
	$texto = $_POST['texto'];
	$fcategoria = $_POST['fcategoria'];
	$fsubcategoria = $_POST['fsubcategoria'];
	$fmarca = $_POST['fmarca'];
	
	
	// obtener datos de producto para edición	
	if (!empty($producto)) {
	  $query = "SELECT producto.*, categoria.nombre AS nombre_categoria, subcategoria.nombre AS nombre_subcategoria 
				FROM producto 
				 LEFT JOIN categoria ON producto.categoria = categoria.clave
				 LEFT JOIN subcategoria ON producto.subcategoria = subcategoria.clave
				WHERE producto.clave='$producto'";
	  $resultado= mysql_query($query,$conexion);
	  $row = mysql_fetch_array($resultado);
	  if (!$categoria) $categoria = $row['categoria'];
	  if (!$subcategoria) $subcategoria = $row['subcategoria'];
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

	if ($_GET['borrar']=='borrapdf') {
		$nombrepdf='images/cms/productos/pdf/'.$_GET['producto']."_".$_GET['ipdf'].".pdf";
		@unlink($nombrepdf);
	}
	
	function get_nombre_lista($lista) {
	  include("../conexion.php");
	  $resLIS = mysql_query("SELECT nombre FROM lista_precios WHERE clave = '$lista'",$conexion);
	  $rowLIS = mysql_fetch_assoc($resLIS);
	  return $rowLIS['nombre'];	
	}

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

<link href="css/jquery-ui.css" rel="stylesheet">
<script src="js/jquery_1.10.js"></script>
<script src="js/jquery-ui.js"></script>

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
    'fileExt'   : '*.pdf',
    'fileDesc'  : 'Archivos PDF',
	
    'scriptData'  : {'identificador':'<?=$archivo_tmp;?>','tipo':'pdf'},
	'sizeLimit'   : 358400,
	'auto'      : true,
    'onSelect' : function(event,queueID,fileObj){
					var ext = fileObj.name;
					ext = ext.substr(ext.length-4); //gets last 4 chars (extension type)
					document.getElementById('message').innerHTML="";
					switch (ext) {
						case ".pdf":
						case ".PDF":
						// filetype ok
						break;
						default:
						// alert("Data type invalid");
						document.getElementById('message').innerHTML="Error en tipo de imagen. Solo PDF!";
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
    if (document.forma.modelo.value == "") {
     alert("Falta modelo.");
	 document.forma.modelo.focus();
     return;
     }
    if (document.forma.descripcion_stage.value == "") {
     alert("Falta descripción de stage.");
	 document.forma.descripcion_stage.focus();
     return;
     }
    if (document.forma.clasificacion.value == "") {
     alert("Indica la clasificacion.");
	 document.forma.clasificacion.focus();
     return;
     }

	<? if (!$es_accesorio) { ?>
	// combina claves de productos relacionados seleccionados en un string separado por comas
	var string_pr = '';
	for (var i=0; i < document.forma.lista_relacionados.length; i++) {
	  string_pr += ' '+document.forma.lista_relacionados.options[i].value+',';
	}
	document.forma.relacionados.value = string_pr;
    <? } else { ?>
	// combina claves de categorias seleccionadas en un string separado por comas
	var string_ca = '';
	for (var i=0; i < document.forma.lista_categorias.length; i++) {
	  string_ca += ' '+document.forma.lista_categorias.options[i].value+',';
	}
	document.forma.categorias.value = string_ca;
	<? } ?>
	// combina claves de marcas para sitios de marca seleccionados en un string separado por comas
	var string_ma = '';
	for (var i=0; i < document.forma.lista_marcas.length; i++) {
	  string_ma += document.forma.lista_marcas.options[i].value+',';
	}
	string_ma = string_ma.substring(0,string_ma.length - 1);
	document.forma.marcas_sitios_marca.value = string_ma;
	
	//if ((document.forma.pl_amecop.value!="" || document.forma.pl_comentarios.value!="" || document.forma.pl_aplica_para_TP.checked || document.forma.pl_exclusivo.checked ||document.forma.pl_aplica_para_TN.checked) && document.forma.pl_clave_cat.value=='')  {
	//	alert("Debes completar los datos de PriceList");
	//	return;
	//}

   document.forma.action='graba_producto.php';
   document.forma.submit();
  }
  function rechaza() {
   document.forma.action='borra_producto.php';
   document.forma.submit();
  }
  function descarta() {
   document.forma.action='lista_producto.php';
   document.forma.submit();
  }
  function borra_pdf(id,producto,i) {
	   continuar = window.confirm("Deseas eliminar el PDF #"+i);
	   if (!continuar) {
			 return;
	   }
	   document.forma.action='abc_producto.php?producto='+producto+'&borrar=borrapdf&ipdf='+i;
	   document.forma.submit();
   }
  
  function agregaRe(inForm,texto,valor) {
		var siguiente = inForm.lista_relacionados.options.length;
		var encontrado = false;
		for (var i=0; i < inForm.lista_relacionados.length; i++) {
			if (inForm.lista_relacionados.options[i].value == valor) {
				encontrado = true;
			}
		}
		if (!encontrado) {
			eval("inForm.lista_relacionados.options[siguiente]=" + "new Option(texto,valor,false,true)");
		}
  }
  function eliminaRe(inForm,indice) {
		var i = inForm.lista_relacionados.options.length;
		inForm.lista_relacionados.options[indice] = null;
  }
  function agregaCa(inForm,texto,valor) {
		var siguiente = inForm.lista_categorias.options.length;
		var encontrado = false;
		for (var i=0; i < inForm.lista_categorias.length; i++) {
			if (inForm.lista_categorias.options[i].value == valor) {
				encontrado = true;
			}
		}
		if (!encontrado) {
			eval("inForm.lista_categorias.options[siguiente]=" + "new Option(texto,valor,false,true)");
		}
  }
  function eliminaCa(inForm,indice) {
		var i = inForm.lista_categorias.options.length;
		inForm.lista_categorias.options[indice] = null;
  }
/*  function agregaMas(inForm,texto,valor) {
		var siguiente = inForm.lista_mas.options.length;
		var encontrado = false;
		for (var i=0; i < inForm.lista_mas.length; i++) {
			if (inForm.lista_mas.options[i].value == valor) {
				encontrado = true;
			}
		}
		if (!encontrado) {
			eval("inForm.lista_mas.options[siguiente]=" + "new Option(texto,valor,false,true)");
		}
  }
  function eliminaMas(inForm,indice) {
		var i = inForm.lista_mas.options.length;
		inForm.lista_mas.options[indice] = null;
  }
  */
  function agregaMa(inForm,texto,valor) {
		var siguiente = inForm.lista_marcas.options.length;
		var encontrado = false;
		for (var i=0; i < inForm.lista_marcas.length; i++) {
			if (inForm.lista_marcas.options[i].value == valor) {
				encontrado = true;
			}
		}
		if (!encontrado) {
			eval("inForm.lista_marcas.options[siguiente]=" + "new Option(texto,valor,false,true)");
		}
  }
  function eliminaMa(inForm,indice) {
		var i = inForm.lista_marcas.options.length;
		inForm.lista_marcas.options[indice] = null;
  }
  
  // VALG - 06.05.16 - BEGIN
jQuery.fn.filterByText = function(textbox) {
    return this.each(function() {
        var select = this;
        var options = [];
        $(select).find('option').each(function() {
            options.push({value: $(this).val(), text: $(this).text()});
        });
        $(select).data('options', options);

        $(textbox).bind('change keyup', function() {
            var options = $(select).empty().data('options');
            var search = $.trim($(this).val());
            var regex = new RegExp(search,"gi");

            $.each(options, function(i) {
                var option = options[i];
                if(option.text.match(regex) !== null) {
                    $(select).append(
                        $('<option>').text(option.text).val(option.value)
                    );
                }
            });
        });
    });
};

$(function() {
    $('#pro_relacionados').filterByText($('#filtro_prod'));
});

// VALG - 06.05.16 - END

</script>
<?php 
 $xajax->printJavascript("xajax/"); 
?>
</head>

<body>
<div id="container">
	<? $tit='Administrar Productos'; include('top.php'); ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma" enctype="multipart/form-data">
      <input type="hidden" name="MAX_FILE_SIZE" value="35000000" />
      	<input type="hidden" name="archivo_tmp" id="archivo_tmp" value="<?=$archivo_tmp;?>" />
      	<input type="hidden" name="texto" id="texto" value="<?=$texto;?>" />
      	<input type="hidden" name="fcategoria" id="fcategoria" value="<?=$fcategoria;?>" />
      	<input type="hidden" name="fsubcategoria" id="fsubcategoria" value="<?=$fsubcategoria;?>" />
      	<input type="hidden" name="fmarca" id="fmarca" value="<?=$fmarca;?>" />
        <table width="100%" border="0" align="center" cellpadding="2" cellspacing="0" class="texto">
          <tr class="tr-subt">
            <td>DATOS GENERALES</td>
            <td colspan="10">&nbsp;</td>
          </tr>
          <tr>
            <td><div align="right">Categor&iacute;a:</div></td>
            <td colspan="10">
			   <? if (0 && !empty($producto)) { echo '<strong>'.$row['nombre_categoria'].'</strong>'; ?>
               	<input type="hidden" name="categoria" id="categoria" value="<?=$categoria;?>" />
               <? } else { ?>
                <select name="categoria" class="campo" id="categoria" onchange="document.forma.subcategoria.value=''; document.forma.submit()">
                <option value="">Seleccionar categor&iacute;a...</option>
                <?  $resCAT= mysql_query("SELECT * FROM categoria ORDER BY orden, nombre",$conexion);
					  while ($rowCAT = mysql_fetch_array($resCAT)) { 
						echo '<option value="'.$rowCAT['clave'].'"';
						if ($rowCAT['clave']==$categoria) echo ' selected';
						echo '>'.$rowCAT['nombre'].'</option>';
					  }
				  ?>
            </select><? } ?></td>
          </tr>
          <tr>
            <td><div align="right">Subcategor&iacute;a:</div></td>
            <td colspan="10">
			   <? if (0 && !empty($producto)) { if ($row['nombre_subcategoria']) echo '<strong>'.$row['nombre_subcategoria'].'</strong>'; else echo 'N/A';?>
               	<input type="hidden" name="subcategoria" id="subcategoria" value="<?=$subcategoria;?>" />
               <? } else { ?>            
               <select name="subcategoria" class="campo" id="subcategoria" onchange="document.forma.submit()">
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
            </select><? } ?></td>
          </tr>
          
          <? if ($mostrar) { ?>
          <tr>
            <td><div align="right">Marca:</div></td>
            <td colspan="10">
             <select name="marca" id="marca">
               <? $resultadoMAR = mysql_query("SELECT * FROM marca ORDER BY orden",$conexion);
			      while ($rowMAR = mysql_fetch_array($resultadoMAR)) {
				     echo '<option value="'.$rowMAR['clave'].'"';
					 if ($rowMAR['clave']==$row['marca']) echo 'selected';
					 echo '>'.$rowMAR['nombre'].'</option>';
				  }
			    ?>
             </select></td>
          </tr>
          <tr>
            <td><div align="right">Nombre:</div></td>
            <td colspan="10"><input name="nombre" type="text" class="campo" id="nombre" value="<?= $row['nombre']; ?>" size="100" maxlength="100" />
            (Lavadora carga frontal 12 Kg)</td>
          </tr>
          <tr>
            <td><div align="right">Modelo:</div></td>
            <td colspan="10"><input name="modelo" type="text" class="campo" id="modelo" value="<?= $row['modelo']; ?>" size="50" maxlength="50" <? if ($row['clave']) echo 'readonly';?> /></td>
          </tr>
          <tr>
            <td><div align="right">Color:</div></td>
            <td colspan="10"><input name="color" type="text" class="campo" id="color" value="<?= $row['color']; ?>" size="50" maxlength="50" /></td>
          </tr>
          <tr>
            <td><div align="right"><strong>Clasificaci&oacute;n:</strong></div></td>
            <td colspan="10">
            <select name="clasificacion" id="clasificacion">
              <option value="low" <? if ($row['clasificacion']=='low') echo 'selected';?>>Low Ticket</option>
              <option value="ltl" <? if ($row['clasificacion']=='ltl') echo 'selected';?>>LTL</option>
              <option value=""  <? if ($row['clasificacion']=='') echo 'selected';?>></option>
            </select> 
            descontinuado por RM/RS</td>
          </tr>
		  <tr>
			<td><div align="right">Sincronizar con Mercadolibre:</div></td>
			<td>
				<input name="sync_meli" type="checkbox" id="sync_meli" value="1" <? if($row['sync_meli']) echo 'checked';?> />
			</td>
		  </tr>
          <? if ($row['subcategoria']==130 || $subcategoria == 130) { // garantia ?> 
          <tr>
            <td height="30" align="right"><strong>Es garant&iacute;a:</strong></td>
            <td height="30" colspan="10" align="left" valign="middle">
              <select name="es_garantia" id="es_garantia">
                <option value="1" <? if ($row['es_garantia']==1) echo 'selected';?>>Por 1 a&ntilde;o</option>
                <option value="2" <? if ($row['es_garantia']==2) echo 'selected';?>>Por 2 a&ntilde;os</option>
                <option value="3" <? if ($row['es_garantia']==3) echo 'selected';?>>Por 3 a&ntilde;os</option>
                <option value="4" <? if ($row['es_garantia']==4) echo 'selected';?>>Por 4 a&ntilde;os</option>
              </select> 
              <!--
              Tipo:
              <select name="fuera_de_garantia" id="fuera_de_garantia">
                <option value="0" <? if ($row['fuera_de_garantia']==0) echo 'selected';?>>Dentro de Garantía</option>
                <option value="1" <? if ($row['fuera_de_garantia']==1) echo 'selected';?>>Fuera de Garantía</option>
              </select> 
			  -->
            </td>
          </tr>
          <? } // garantias ?>

          <tr>
            <td valign="top">&nbsp;</td>
            <td colspan="10">&nbsp;</td>
          </tr>
          <tr>
            <td valign="top"><div align="right">Descripci&oacute;n (stage):</div></td>
            <td colspan="10"><textarea name="descripcion_stage" cols="117" rows="3" class="campo" id="descripcion_stage"><?= $row['descripcion_stage']; ?></textarea></td>
          </tr>
          <tr>
            <td valign="top"><div align="right">Descripci&oacute;n larga:</div></td>
            <td colspan="10"><textarea name="descripcion_larga" cols="117" rows="3" class="campo" id="descripcion_larga"><?= $row['descripcion_larga']; ?></textarea></td>
          </tr>
          <tr>
            <td><div align="right">Compartir Facebook:</div></td>
            <td colspan="10"><input name="link_compartir" type="text" class="campo" id="link_compartir" value="<?= $row['link_compartir']; ?>" size="50" maxlength="50" /> 
              URL directa a ficha de producto para compartir en Facebook</td>
          </tr>
          <tr>
            <td><div align="right">Youtube:</div></td>
            <td colspan="10"><input name="video" type="text" class="campo" id="video" value="<?= $row['video']; ?>" size="30" maxlength="20" />
              ID de video Youtube</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="10">&nbsp;</td>
          </tr>
          <tr  class="tr-subt">
            <td colspan="11">PRECIOS</td>
          </tr>
          <tr>
            <td align="center"><div align="right"><strong><?=get_nombre_lista('lista');?></strong></div></td>
            <td align="center"><strong><?=get_nombre_lista('web');?></strong></td>
            <td align="center"><strong><?=get_nombre_lista('w1');?></strong></td>
            <td align="center"><strong><?=get_nombre_lista('w2');?></strong></td>
            <td align="center"><strong><?=get_nombre_lista('w3');?></strong></td>
            <td align="center"><strong><?=get_nombre_lista('w4');?></strong></td>
            <td align="center"><strong><?=get_nombre_lista('w5');?></strong></td>
            <td align="center"><strong><?=get_nombre_lista('w6');?></strong></td>
            <td align="center"><strong><?=get_nombre_lista('w7');?></strong></td>
            <td align="center"><strong><?=get_nombre_lista('w8');?></strong></td>
            <td align="center"><strong><?=get_nombre_lista('w9');?></strong></td>
          </tr>
          <tr>
            <td align="center"><div align="right">
              <input name="precio_lista" type="text" class="campo numerico" id="precio_lista" value="<?= $row['precio_lista']; ?>" size="10" maxlength="10" onblur="this.value = this.value.replace(/[^0-9.]+/g,'');"/>
            </div></td>
            <td align="center"><input name="precio_web" type="text" class="campo numerico" id="precio_web" value="<?= $row['precio_web']; ?>" size="10" maxlength="10" onblur="this.value = this.value.replace(/[^0-9.]+/g,'');" /></td>
            <td align="center"><input name="precio_w1" type="text" class="campo numerico" id="precio_w1" value="<?= $row['precio_w1']; ?>" size="10" maxlength="10" onblur="this.value = this.value.replace(/[^0-9.]+/g,'');" /></td>
            <td align="center"><input name="precio_w2" type="text" class="campo numerico" id="precio_w2" value="<?= $row['precio_w2']; ?>" size="10" maxlength="10" onblur="this.value = this.value.replace(/[^0-9.]+/g,'');" /></td>
            <td align="center"><input name="precio_w3" type="text" class="campo numerico" id="precio_w3" value="<?= $row['precio_w3']; ?>" size="10" maxlength="10" onblur="this.value = this.value.replace(/[^0-9.]+/g,'');" /></td>
            <td align="center"><input name="precio_w4" type="text" class="campo numerico" id="precio_w4" value="<?= $row['precio_w4']; ?>" size="10" maxlength="10" onblur="this.value = this.value.replace(/[^0-9.]+/g,'');" /></td>
            <td align="center"><input name="precio_w5" type="text" class="campo numerico" id="precio_w5" value="<?= $row['precio_w5']; ?>" size="10" maxlength="10" onblur="this.value = this.value.replace(/[^0-9.]+/g,'');" /></td>
            <td align="center"><input name="precio_w6" type="text" class="campo numerico" id="precio_w6" value="<?= $row['precio_w6']; ?>" size="10" maxlength="10" onblur="this.value = this.value.replace(/[^0-9.]+/g,'');" /></td>
            <td align="center"><input name="precio_w7" type="text" class="campo numerico" id="precio_w7" value="<?= $row['precio_w7']; ?>" size="10" maxlength="10" onblur="this.value = this.value.replace(/[^0-9.]+/g,'');" /></td>
            <td align="center"><input name="precio_w8" type="text" class="campo numerico" id="precio_w8" value="<?= $row['precio_w8']; ?>" size="10" maxlength="10" onblur="this.value = this.value.replace(/[^0-9.]+/g,'');" /></td>
            <td align="center"><input name="precio_w9" type="text" class="campo numerico" id="precio_w9" value="<?= $row['precio_w9']; ?>" size="10" maxlength="10" onblur="this.value = this.value.replace(/[^0-9.]+/g,'');" /></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td align="center"><strong><?=get_nombre_lista('x0');?></strong></td>
            <td align="center"><strong><?=get_nombre_lista('x1');?></strong></td>
            <td align="center"><strong><?=get_nombre_lista('x2');?></strong></td>
            <td align="center"><strong><?=get_nombre_lista('x3');?></strong></td>
            <td align="center"><strong><?=get_nombre_lista('x4');?></strong></td>
            <td align="center"><strong><?=get_nombre_lista('x5');?></strong></td>
            <td align="center"><strong><?=get_nombre_lista('x6');?></strong></td>
            <td align="center"><strong><?=get_nombre_lista('x7');?></strong></td>
            <td align="center"><strong><?=get_nombre_lista('x8');?></strong></td>
            <td align="center"><strong><?=get_nombre_lista('x9');?></strong></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td align="center"><input name="precio_x0" type="text" class="campo numerico" id="precio_x0" value="<?= $row['precio_x0']; ?>" size="10" maxlength="10" onblur="this.value = this.value.replace(/[^0-9.]+/g,'');" /></td>
            <td align="center"><input name="precio_x1" type="text" class="campo numerico" id="precio_x1" value="<?= $row['precio_x1']; ?>" size="10" maxlength="10" onblur="this.value = this.value.replace(/[^0-9.]+/g,'');" /></td>
            <td align="center"><input name="precio_x2" type="text" class="campo numerico" id="precio_x2" value="<?= $row['precio_x2']; ?>" size="10" maxlength="10" onblur="this.value = this.value.replace(/[^0-9.]+/g,'');" /></td>
            <td align="center"><input name="precio_x3" type="text" class="campo numerico" id="precio_x3" value="<?= $row['precio_x3']; ?>" size="10" maxlength="10" onblur="this.value = this.value.replace(/[^0-9.]+/g,'');" /></td>
            <td align="center"><input name="precio_x4" type="text" class="campo numerico" id="precio_x4" value="<?= $row['precio_x4']; ?>" size="10" maxlength="10" onblur="this.value = this.value.replace(/[^0-9.]+/g,'');" /></td>
            <td align="center"><input name="precio_x5" type="text" class="campo numerico" id="precio_x5" value="<?= $row['precio_x5']; ?>" size="10" maxlength="10" onblur="this.value = this.value.replace(/[^0-9.]+/g,'');" /></td>
            <td align="center"><input name="precio_x6" type="text" class="campo numerico" id="precio_x6" value="<?= $row['precio_x6']; ?>" size="10" maxlength="10" onblur="this.value = this.value.replace(/[^0-9.]+/g,'');" /></td>
            <td align="center"><input name="precio_x7" type="text" class="campo numerico" id="precio_x7" value="<?= $row['precio_x7']; ?>" size="10" maxlength="10" onblur="this.value = this.value.replace(/[^0-9.]+/g,'');" /></td>
            <td align="center"><input name="precio_x8" type="text" class="campo numerico" id="precio_x8" value="<?= $row['precio_x8']; ?>" size="10" maxlength="10" onblur="this.value = this.value.replace(/[^0-9.]+/g,'');" /></td>
            <td align="center"><input name="precio_x9" type="text" class="campo numerico" id="precio_x9" value="<?= $row['precio_x9']; ?>" size="10" maxlength="10" onblur="this.value = this.value.replace(/[^0-9.]+/g,'');" /></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
			<td align="center"><strong><?=get_nombre_lista('s1');?></strong></td>
            <td align="center"><strong><?=get_nombre_lista('s2');?></strong></td>
            <td align="center"><strong><?=get_nombre_lista('s3');?></strong></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td align="center"><input name="precio_s1" type="text" class="campo numerico" id="precio_s1" value="<?= $row['precio_s1']; ?>" size="10" maxlength="10" onblur="this.value = this.value.replace(/[^0-9.]+/g,'');"/></td>
            <td align="center"><input name="precio_s2" type="text" class="campo numerico" id="precio_s2" value="<?= $row['precio_s2']; ?>" size="10" maxlength="10" onblur="this.value = this.value.replace(/[^0-9.]+/g,'');"/></td>
            <td align="center"><input name="precio_s3" type="text" class="campo numerico" id="precio_s3" value="<?= $row['precio_s3']; ?>" size="10" maxlength="10" onblur="this.value = this.value.replace(/[^0-9.]+/g,'');"/></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td align="right"><strong>PROYECTOS:</strong></td>
            <td align="center"><strong>
              <?=get_nombre_lista('LH');?>
            </strong></td>
            <td align="center"><strong>
              <?=get_nombre_lista('LG');?>
            </strong></td>
            <td align="center"><strong>
              <?=get_nombre_lista('LF');?>
            </strong></td>
            <td align="center"><strong>
              <?=get_nombre_lista('T2');?>
            </strong></td>
            <td align="center"><strong>
              <?=get_nombre_lista('T5');?>
            </strong></td>
            <td align="center">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td align="right">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td align="center"><input name="precio_LH" type="text" class="campo numerico" id="precio_LH" value="<?= $row['precio_LH']; ?>" size="10" maxlength="10" onblur="this.value = this.value.replace(/[^0-9.]+/g,'');" readonly="readonly"/></td>
            <td align="center"><input name="precio_LG" type="text" class="campo numerico" id="precio_LG" value="<?= $row['precio_LG']; ?>" size="10" maxlength="10" onblur="this.value = this.value.replace(/[^0-9.]+/g,'');" readonly="readonly"/></td>
            <td align="center"><input name="precio_LF" type="text" class="campo numerico" id="precio_LF" value="<?= $row['precio_LF']; ?>" size="10" maxlength="10" onblur="this.value = this.value.replace(/[^0-9.]+/g,'');" readonly="readonly"/></td>
            <td align="center"><input name="precio_T2" type="text" class="campo numerico" id="precio_T2" value="<?= $row['precio_T2']; ?>" size="10" maxlength="10" onblur="this.value = this.value.replace(/[^0-9.]+/g,'');" readonly="readonly"/></td>
            <td align="center"><input name="precio_T5" type="text" class="campo numerico" id="precio_T5" value="<?= $row['precio_T5']; ?>" size="10" maxlength="10" onblur="this.value = this.value.replace(/[^0-9.]+/g,'');" readonly="readonly"/></td>
            <td align="center">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td align="right"><strong>Notas del precio MAP:</strong></td>
            <td colspan="10"><input name="notas_precio" type="text" class="campo" id="notas_precio" value="<?= $row['notas_precio']; ?>" size="40" maxlength="30" /> 
              Estas anotaciones aparecer&aacute;n junto al precio de Lista (en las fichas de TW).</td>
          </tr>
          <tr>
            <td align="right">&nbsp;</td>
            <td colspan="10">&nbsp;</td>
          </tr>
          <tr  class="tr-subt">
            <td colspan="11">EXISTENCIAS</td>
          </tr>
          <? if ($row['subcategoria']!=130) { // garantia ?> 
          <tr>
            <td align="right">&nbsp;</td>
            <td colspan="10">Estatus de inventario: <strong>
			   <?
			   	 // obtener estatus de inventario 				 
			     $estatus_inv = mysql_query("SELECT estatus FROM existencia WHERE producto = '{$row['modelo']}' LIMIT 1",$conexion);
				 $estatus_inv = mysql_fetch_assoc($estatus_inv);
				 $estatus_inv = $estatus_inv['estatus'];
				 if ($estatus_inv) echo $estatus_inv; else echo 'No encontrado';
				?>
                
                </strong>
            </td>
          </tr>
          <tr>
            <td align="right">&nbsp;</td>
            <td colspan="10"><table width="35%" border="0" cellpadding="00">
              <tr>
                <td bgcolor="#F4F4F2"><div align="center"><strong>CEDIS</strong></div></td>
                <td bgcolor="#F4F4F2"><div align="center"><strong>STORE LOC</strong></div></td>
                <td bgcolor="#F4F4F2"><div align="center"><strong>EXISTENCIA</strong></div></td>
              </tr>
              <? 
			     $modelo = $row['modelo'];
			     $resultadoEX = mysql_query("SELECT * FROM existencia WHERE producto = '$modelo' ORDER BY cedis, loc",$conexion);
				 while ($rowEX = mysql_fetch_array($resultadoEX)) {
					 if ($rowEX['cedis']!='RM08' && $rowEX['loc']!=1) continue;  // SL > 1 solo para RM08 se muestra
					 if ($rowEX['cedis']=='RM04') continue;
			  ?>
              <tr bgcolor="#FFFFFF">
                <td align="center"><?=$rowEX['cedis'];?></td>
                <td align="center"><?=$rowEX['loc'];?></td>
                <td align="center"><?=$rowEX['existencia'];?></td>
              </tr>
              <? } ?>
            </table></td>
          </tr>
          <? } ?>
          <tr>
            <td align="right">&nbsp;</td>
            <td colspan="10">&nbsp;</td>
          </tr>
          <tr  class="tr-subt">
            <td colspan="11">CONFIGURACIONES</td>
          </tr>
          <!--tr>
            <td align="right" nowrap="nowrap"><strong>Exclusivo RM12:</strong></td>
            <td colspan="10"><input name="exclusivo_rm12" type="checkbox" id="exclusivo_rm12" value="1" <? if($row['exclusivo_rm12']) echo 'checked';?> />
              (Seleccionar para evitar que en POS se seleccione cualquier otro CEDIS)</td>
          </tr-->
          <tr>
            <td align="right"><strong>Destacar en TW</strong>:</td>
            <td colspan="10">
            <label><input name="es_promocion" type="checkbox" id="es_promocion" value="1" <? if($row['es_promocion']) echo 'checked';?> /> Promoci&oacute;n de la semana </label>&nbsp;&nbsp;&nbsp;
            <label><input name="es_nuevo" type="checkbox" id="es_nuevo" value="1" <? if($row['es_nuevo']) echo 'checked';?> /> Lo m&aacute;s nuevo</label>&nbsp;&nbsp;&nbsp;
            <label>            Promoci&oacute;n Especial: 
            <select name="es_promocion_especial" class="campo" id="es_promocion_especial">
              <option value="0">No</option>
              <?  $resPE= mysql_query("SELECT * FROM promo_producto ORDER BY nombre",$conexion);
				  while ($rowPE = mysql_fetch_array($resPE)) { 
						echo '<option value="'.$rowPE['clave'].'"';
						if ($rowPE['clave']==$row['es_promocion_especial']) echo ' selected';
						echo '>'.$rowPE['nombre'].'</option>';
					  }
				  ?>
            </select>
            </label></td>
          </tr>
          <tr>
            <td align="right"><strong>Destacar en MAS</strong>:</td>
            <td colspan="10"><label>
              <input name="es_promocion_mas" type="checkbox" id="es_promocion_mas" value="1" <? if($row['es_promocion_mas']) echo 'checked';?> />
              Promoci&oacute;n de la semana </label>
              &nbsp;&nbsp;&nbsp;
              <label>
                <input name="es_nuevo_mas" type="checkbox" id="es_nuevo_mas" value="1" <? if($row['es_nuevo_mas']) echo 'checked';?> />
                Lo m&aacute;s nuevo</label>
              &nbsp;&nbsp;</td>
          </tr>
          <?php /* INICIO : Aregado 21 Julio 2016 - Bitmore - Destacar productos en Sitio de Proyectos */ ?>
          <tr>
            <td align="right"><strong>Destacar en Proyectos</strong>:</td>
            <td colspan="10"><label>
              <input name="es_promocion_proyectos" type="checkbox" id="es_promocion_proyectos" value="1" <? if($row['es_promocion_proyectos']) echo 'checked';?> />
              Promoci&oacute;n de la semana </label>
              &nbsp;&nbsp;&nbsp;
              <label>
                <input name="es_nuevo_proyectos" type="checkbox" id="es_nuevo_proyectos" value="1" <? if($row['es_nuevo_proyectos']) echo 'checked';?> />
                Lo m&aacute;s nuevo</label>
              &nbsp;&nbsp;</td>
          </tr>
          <?php /* FIN : Aregado 21 Julio 2016 - Bitmore - Destacar productos en Sitio de Proyectos */ ?>
          <tr>
            <td align="right"><strong>Genera Puntos:</strong></td>
            <td colspan="10" align="left">
              <label><input type="radio" name="genera_puntos" id="genera_puntos1" value="1" <? if ($row['genera_puntos']==1) echo 'checked';?> />
              S&iacute;</label>&nbsp;&nbsp;
              <label><input type="radio" name="genera_puntos" id="genera_puntos0" value="0" <? if ($row['genera_puntos']==0) echo 'checked';?> />No</label>              </td>
          </tr>
          <tr>
            <td align="right"><strong>Solo para Sitios de Marca:</strong></td>
            <td colspan="10" align="left"><label>
            <input type="radio" name="solo_para_marcas" id="solo_para_marcas1" value="1" <? if ($row['solo_para_marcas']==1) echo 'checked';?> />
S&iacute;</label>
             &nbsp;&nbsp;
             <label><input type="radio" name="solo_para_marcas" id="solo_para_marcas0" value="0" <? if ($row['solo_para_marcas']==0) echo 'checked';?> />No</label>
<label>  (Si es solo para sitios de marca, no se actualizan los precios en la importaci&oacute;n; el precio debe estar en cero para que no aparezcan en sitios de marca</label></td>
          </tr>
          <tr>
            <td align="right"><strong>Solo para TW:</strong></td>
            <td colspan="10" align="left"><label><input type="radio" name="solo_para_web" id="solo_para_web" value="1" <? if ($row['solo_para_web']==1) echo 'checked';?> />S&iacute;</label>
              &nbsp;&nbsp;
              <label><input type="radio" name="solo_para_web" id="solo_para_web" value="0" <? if ($row['solo_para_web']==0) echo 'checked';?> />No</label>			</td>
          </tr>
          <tr>
            <td align="right"><strong>Solo para POS:</strong></td>
            <td colspan="10" align="left"><label><input type="radio" name="solo_para_pos" id="solo_para_pos" value="1" <? if ($row['solo_para_pos']==1) echo 'checked';?> />S&iacute;</label>
              &nbsp;&nbsp;
              <label><input type="radio" name="solo_para_pos" id="solo_para_pos" value="0" <? if ($row['solo_para_pos']==0) echo 'checked';?> />No</label>			</td>
          </tr>
          <tr>
            <td align="right"><strong>Mostrar en MAS</strong></td>
            <td colspan="10" align="left"> <input name="mostrar_en_mas" type="checkbox" id="mostrar_en_mas" value="1" <? if($row['mostrar_en_mas']) echo 'checked';?> />
           </td>
          </tr>
          <tr>
            <td align="right"><strong>Es KAid Mayor?</strong></td>
            <td colspan="10" align="left"><label>
              <input type="radio" name="kad_mayor" id="kad_mayor" value="1" <? if ($row['kad_mayor']==1) echo 'checked';?> />S&iacute;</label>
              &nbsp;&nbsp;
              <label><input type="radio" name="kad_mayor" id="kad_mayor" value="0" <? if ($row['kad_mayor']==0) echo 'checked';?> />No</label> &nbsp;&nbsp;(Sirve para limitar venta de productos mayores KAD, solo para empleados Whirlpool)</td>
          </tr>
          <tr>
            <td align="right" valign="top">&nbsp;</td>
            <td colspan="10" align="left">&nbsp;</td>
          </tr>
          <tr class="tr-subt">
            <td height="30" colspan="11">SITIOS DE MARCA. 
            Este producto estará disponible para ser agregado en los sitios de marca seleccionados:</td>
          </tr>
          <tr>
            <td align="right" valign="top">&nbsp;</td>
            <td colspan="10" align="left"><table border="0" cellpadding="0" cellspacing="0" class="texto">
              <tr>
                <td><strong>Marcas  disponibles:</strong><br />
                  (dbl clic para seleccionar)</td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td><strong>Marcas  seleccionados:</strong><br />
                  (dbl clic para eliminar de la lista)</td>
              </tr>
              <tr>
                <td>
                  <select name="mar_relacionadas" size="6" class="campo" id="mar_relacionadas" ondblclick="agregaMa(document.forma,this.options[this.selectedIndex].text,this.value);" style="min-width:200px">
                    <?    
					      $CR = chr(10);
						  $cat = '';
						  $query = "SELECT * FROM marca WHERE clave IN (SELECT marca FROM tienda_marca) ORDER BY orden, nombre";
					      $resMAR= mysql_query($query,$conexion);
                          while ($rowMAR = mysql_fetch_array($resMAR)) { 
			                echo $CR.'<option value="'.$rowMAR['clave'].'">'.$rowMAR['nombre'].'</option>';
                          }
                      ?>
                  </select>
                </td>
                <td>&nbsp;</td>
                <td><select name="lista_marcas" size="6" class="campo" id="lista_marcas" ondblclick="eliminaMa(document.forma,this.selectedIndex);" style="min-width:200px;">
                  <?  $op=explode(',',trim($row['marcas_sitios_marca']));
						foreach ($op AS $cvemar) {
						  $cvemar+=0;
						  if (!$cvemar) continue;
						  $resMAR= mysql_query("SELECT * FROM marca WHERE clave=$cvemar",$conexion);
						  $rowMAR = mysql_fetch_array($resMAR);
						  echo $CR.'<option value="'.$cvemar.'">'.$rowMAR['nombre'].'</option>';
						}
					?>
                </select></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="10">&nbsp;</td>
          </tr>
          <tr class="tr-subt">
            <td height="30" colspan="11" >CAMPOS ADICIONALES</td>
          </tr>
<?   
			  // campos administrables (considerar grupos de características)
			  // query para considerar grupo..
			  $query = "SELECT campo.*, grupo_caracteristica.nombre AS nombre_grupo FROM campo 
			  				LEFT JOIN grupo_caracteristica ON campo.grupo = grupo_caracteristica.clave 
						   WHERE categoria = $categoria AND subcategoria = $subcategoria 
						   ORDER BY grupo_caracteristica.orden, campo.orden";
			  // query sin grupos
			  $query = "SELECT * FROM campo WHERE categoria = $categoria AND subcategoria = $subcategoria ORDER BY orden";
						   
			  $resultadoCA = mysql_query($query,$conexion);
			  $grupo='x';
			  while ($rowCA = mysql_fetch_array($resultadoCA)) {
				// habilitar esto para activar grupos
				/*
			  	if ($rowCA['grupo']!=$grupo) {  
					$grupo = $rowCA['grupo'];
			
			?>
                
		  <tr>
            <td>&nbsp;</td>
            <td align="left"><strong><?=$rowCA['nombre_grupo'];?></strong></td>
          </tr>                
				          
		 <?
		 		}
				// fin grupos de características
				*/
				$ic = substr($rowCA['campo_tabla'],6,3);
				$nombrecampo = "campo_".$ic;			  
			  ?>
          <tr>
            <td align="right" valign="top"><?= $rowCA['nombre_campo'];?></td>
            <td colspan="10" align="left"> 
			<? if ($rowCA['tipo'] == 'texto') { 
				$xval = limpia_comillas($row[$nombrecampo]);
			?>
               <input name="campo_<?=$ic;?>" type="text" class="campo" id="campo_<?=$ic;?>" value="<?= $xval; ?>" size="70" maxlength="100">
		   <? }
               if ($rowCA['tipo'] == 'menu') { 
                    $arr_valores = explode(chr(10),$rowCA['valores']);
            ?>
               <select name="campo_<?=$ic;?>" size="1" class="campo" id="campo_<?=$ic;?>">
             <? for ($icv = 0; $icv < count($arr_valores); $icv++) {  
//					$valor = mysql_real_escape_string($arr_valores[$icv]);
					$valor = trim($arr_valores[$icv]);

			 ?>
                <option value="<?=$valor;?>" <? if (trim($row[$nombrecampo]) == trim($valor)) echo ' selected ';?>><?=$arr_valores[$icv];?></option>
             <? } ?>
               </select>						   
            <? } ?></td>
          </tr>
          <?  } // while rowCA  ?>
          <tr>
            <td>&nbsp;</td>
            <td colspan="10">&nbsp;</td>
          </tr>
          <tr class="tr-subt">
            <td height="30" colspan="11">T&Eacute;RMINOS DE GARANT&Iacute;A</td>
          </tr>
          <tr>
            <td valign="top"><div align="right">T&eacute;rminos de la garant&iacute;a:</div></td>
            <td colspan="10"><textarea name="terminos_garantia" cols="117" rows="3" class="campo" id="terminos_garantia"><?= $row['terminos_garantia']; ?></textarea></td>
          </tr>
          <tr>
            <td><div align="right">Garant&iacute;a extendida:</div></td>
            <td colspan="10"><input name="garantia_extendida" type="text" class="campo" id="garantia_extendida" value="<?= $row['garantia_extendida']; ?>" size="100" maxlength="100" /></td>
          </tr>
          <tr>
            <td valign="top"><div align="right">Otras caracter&iacute;sticas:</div></td>
            <td colspan="10"><textarea name="otras_caracteristicas" cols="117" rows="3" class="campo" id="otras_caracteristicas"><?= $row['otras_caracteristicas']; ?></textarea></td>
          </tr>

          <tr>
            <td align="right" valign="top">&nbsp;</td>
            <td colspan="10" align="left">
            <input type="hidden" name="relacionados" id="relacionados" />
            <input type="hidden" name="categorias" id="categorias" />
            <input type="hidden" name="marcas_sitios_marca" id="marcas_sitios_marca" />
            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="10">&nbsp;</td>
          </tr>
         
          <? if (!$es_accesorio) { ?>
          <tr class="tr-subt">
            <td colspan="11">PRODUCTOS RELACIONADOS
            <? if ($row['subcategoria']==130 || $subcategoria==130) { // garantia ?>
            A LOS QUE APLICA ESTA GARANT&Iacute;A             <? } ?> </td>
          </tr>
          <tr>
            <td height="30" align="right" valign="top">&nbsp;</td>
            <td height="30" colspan="10" align="left"><table border="0" cellpadding="0" cellspacing="0" class="texto">
              <tr>
                <td><strong>Productos  disponibles:</strong><br />
                  (dbl clic para seleccionar) 
				  <!-- VALG - 06.05.16 - BEGIN -->
				  <br>Buscar: <input type="text" name="filtro_prod" id="filtro_prod" >
				  <!-- VALG - 06.05.16 - END -->	</td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td><strong>Productos  seleccionados:</strong><br />
                  (dbl clic para eliminar de la lista)</td>
              </tr>
              <tr>
                <td><span class="rotulo">
                  <select name="pro_relacionados" size="10" class="campo" id="pro_relacionados" ondblclick="agregaRe(document.forma,this.options[this.selectedIndex].text,this.value);">
                    <?    
						  $filtro_producto = "WHERE NOT categoria.accesorios AND NOT categoria.garantias ";
						  if ($producto) $filtro_producto .= " AND producto.clave != '$producto'";

					      $CR = chr(10);
						  $cat = '';
						  $query = "SELECT producto.clave, producto.descripcion_larga, producto.nombre, modelo, producto.categoria, categoria.nombre AS nombre_categoria 
						  			FROM producto 
									LEFT JOIN categoria ON producto.categoria = categoria.clave
									$filtro_producto
								   ORDER BY categoria.orden, producto.subcategoria, producto.descripcion_larga";
								   
					      $resPRO= mysql_query($query,$conexion);
                          while ($rowPRO = mysql_fetch_array($resPRO)) { 
						  	if ($cat != $rowPRO['categoria']) {
							  echo '<optgroup label="'.$rowPRO['nombre_categoria'].'"></optgroup>';
							  $cat = $rowPRO['categoria'];
							}
			                echo $CR.'<option value="'.$rowPRO['clave'].'" title="'.$rowPRO['nombre'].'">'.$rowPRO['nombre']." [ ".$rowPRO['modelo']." ]".'</option>';
                          }
                      ?>
                  </select>
                </span></td>
                <td>&nbsp;</td>
                <td><select name="lista_relacionados" size="10" class="campo" id="lista_relacionados" ondblclick="eliminaRe(document.forma,this.selectedIndex);" style="min-width:200px;">
                    <?  $op = $row['relacionados']." 0";
                    	$query = "SELECT * FROM producto WHERE clave IN ($op) ORDER BY producto.nombre";
                    	$resPRO= mysql_query($query,$conexion);
                    	echo $query;



						while ($rowPRO = mysql_fetch_array($resPRO)) {
						  $clavepro = $rowPRO['clave'];						 
						  echo $CR.'<option value="'.$clavepro.'" title="'.$rowPRO['nombre'].'">'.$rowPRO['nombre']." [ ".$rowPRO['modelo']." ] ".'</option>';
						}
					?>
                </select></td>
              </tr>
            </table></td>
          </tr>
          <? } else { // !es_accesorio
		  		/*
		  
		   ?>
          <tr>
            <td height="30" colspan="11" align="left"><strong>Productos relacionados MAS</strong></td>
          </tr>
          <tr>
            <td height="30" align="right" valign="top">&nbsp;</td>
            <td height="30" colspan="10" align="left"><table border="0" cellpadding="0" cellspacing="0" class="texto">
              <tr>
                <td><strong>Productos  disponibles:</strong><br />
                  (dbl clic para seleccionar)</td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td><strong>Productos  seleccionados:</strong><br />
                  (dbl clic para eliminar de la lista)</td>
              </tr>
              <tr>
                <td><select name="productos_mas" size="10" class="campo" id="prductos_mas" ondblclick="agregaMas(document.forma,this.options[this.selectedIndex].text,this.value);" style="min-width:400px">
                  <?    
						  $filtro_producto = "WHERE NOT categoria.accesorios AND NOT categoria.garantias AND producto.clave != '$producto' ";

					      $CR = chr(10);
						  $cat = '';
						  $query = "SELECT producto.clave, producto.descripcion_larga, producto.nombre, modelo, producto.categoria, categoria.nombre AS nombre_categoria 
						  			FROM producto 
									LEFT JOIN categoria ON producto.categoria = categoria.clave
									$filtro_producto
								   ORDER BY categoria.orden, categoria, producto.subcategoria, producto.nombre";
								   
					      $resPRO= mysql_query($query,$conexion);
                          while ($rowPRO = mysql_fetch_array($resPRO)) { 
						  	if ($cat != $rowPRO['categoria']) {
							  echo '<optgroup label="'.$rowPRO['nombre_categoria'].'"></optgroup>';
							  $cat = $rowPRO['categoria'];
							}
			                echo $CR.'<option value="'.$rowPRO['clave'].'" title="'.$rowPRO['nombre'].'">'.$rowPRO['nombre']." [ ".$rowPRO['modelo']." ]".'</option>';
                          }
                      ?>
                </select></td>
                <td>&nbsp;</td>
                <td><select name="lista_mas" size="10" class="campo" id="lista_mas" ondblclick="eliminaMas(document.forma,this.selectedIndex);" style="min-width:400px;">
                  <?  $op=explode(',',$row['relacionados_mas']);
						for ($i=0; $i<=count($op)-2; $i++) {
						  $clavepro=trim($op[$i]);
						  $resPRO= mysql_query("SELECT * FROM producto WHERE clave=$clavepro",$conexion);
						  $rowPRO = mysql_fetch_array($resPRO);
						  echo $CR.'<option value="'.$clavepro.'" title="'.$rowPRO['nombre'].'">'.$rowPRO['nombre']." [ ".$rowPRO['modelo']." ] ".'</option>';
						}
					?>
                </select></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td align="right">Buscar:</td>
            <td colspan="10"><input name="pro_mas" type="text" id="prod_mas" onkeyup="xajax_autocomplete_rel(this.value,'prod_mas')" style="width:380px"/></td>
          </tr>
		  <? */ ?>
          <tr>
            <td colspan="11"><div id="debug">&nbsp;</div></td>
          </tr>
          <tr class="tr-subt">
            <td colspan="11">CATEGOR&Iacute;AS DONDE SE OFRECE ESTE ACCESORIO</td>
          </tr>
          <tr>
            <td height="30" align="right" valign="top">&nbsp;</td>
            <td height="30" colspan="10" align="left"><table border="0" cellpadding="0" cellspacing="0" class="texto">
                <tr>
                  <td><strong>Categor&iacute;as disponibles:</strong><br />
                    (dbl clic para seleccionar)</td>
                  <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                  <td><strong>Categor&iacute;as  seleccionados:</strong><br />
                    (dbl clic para eliminar de la lista)</td>
                </tr>
                <tr>
                  <td><span class="rotulo">
                    <select name="cat_relacionados" size="10" class="campo" id="cat_relacionados" ondblclick="agregaCa(document.forma,this.options[this.selectedIndex].text,this.value);">
                      <?    
					      $CR = chr(10);
						  $query = "SELECT clave, nombre
						  			FROM categoria
									WHERE NOT accesorios
								   ORDER BY orden, nombre";
								   
					      $resCAT = mysql_query($query,$conexion);
                          while ($rowCAT = mysql_fetch_array($resCAT)) { 
			                echo $CR.'<option value="'.$rowCAT['clave'].'" title="'.$rowCAT['nombre'].'">'.$rowCAT['nombre'].'</option>';
                          }
                      ?>
                    </select>
                  </span></td>
                  <td>&nbsp;</td>
                  <td>
                  <select name="lista_categorias" size="10" class="campo" id="lista_categorias" ondblclick="eliminaCa(document.forma,this.selectedIndex);" style="min-width:200px;">
                      <?  $op=explode(',',$row['categorias_accesorios']);
						for ($i=0; $i<=count($op)-2; $i++) {
						  $clavecat=trim($op[$i]);
						  $resCAT= mysql_query("SELECT clave, nombre FROM categoria WHERE clave=$clavecat",$conexion);
						  $rowCAT = mysql_fetch_array($resCAT);
						  echo $CR.'<option value="'.$clavecat.'">'.$rowCAT['nombre'].'</option>';
						}
					?>
                  </select></td>
                </tr>
            </table></td>
          </tr>
          <? } // else accesorios ?>
          <tr>
            <td height="30" align="right" valign="top">&nbsp;</td>
            <td height="30" colspan="10" align="left">&nbsp;</td>
          </tr>
          <tr class="tr-subt">
            <td height="30" colspan="11">PDF FICHA GENERAL</td>
          </tr>
          <tr>
            <td height="30" align="right" valign="top">Ficha General (PDF):<br />
            max 2,000 KB</td>
          <td height="30" colspan="10" align="left"><input id="file_upload" name="file_upload" type="file" />
                <div id="message"></div></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="10"><? if (file_exists("images/cms/productos/".$producto.".jpg")) { ?>
                <div style="float:left;"><img src="images/cms/productos/<?=$producto;?>.jpg?<?=$ht;?>" /></div>
                <? } ?>            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="10">&nbsp;</td>
          </tr>
          <tr class="tr-subt">
            <td colspan="11">PDFs FICHAS PARA SITIOS DE MARCA</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="10"><table width="100%" border="0" cellspacing="1" cellpadding="3">
              <tr>
                <td width="3%" align="right"><strong>#</strong></td>
                <td width="29%"><strong>Seleccionar</strong></td>
                <td colspan="2"><strong>T&iacute;tulo</strong></td>
                <td colspan="2"><strong>Resumen</strong></td>
                <td width="9%" align="center"><strong>Eliminar</strong></td>
                <td width="9%" align="center"><strong>Ver</strong></td>
              </tr>
              <? for ($i = 1; $i<=5; $i++) { 
                        $titulo = "pdf_".$i."_titulo";
						$resumen = "pdf_".$i."_resumen";
						$arc_pdf = "images/cms/productos/pdf/".$producto."_".$i.".pdf";
              ?>
              <tr>
                <td align="right" valign="top"><?= $i; ?></td>
                <td valign="top"><input name="documento<?= $i; ?>" type="file" class="campo" id="documento<?= $i; ?>" size="5" /></td>
                <td colspan="2" valign="top" ><input name="pdf_<?=$i;?>_titulo" type="text" class="campo" id="pdf_<?=$i;?>_titulo" 
                      value="<?=$row[$titulo];?>" size="40" maxlength="50" /></td>
                <td colspan="2" valign="top" ><textarea name="pdf_<?=$i;?>_resumen" cols="50" rows="2" class="campo" id="pdf_<?=$i;?>_resumen"><?=$row[$resumen];?></textarea></td>
                <td align="center" valign="top"><? if(file_exists($arc_pdf)) { ?>
                    <a href="javascript:borra_pdf('<?=$id;?>','<?=$producto;?>','<?=$i;?>')"><img src="images/borrar.png" alt="Eliminar la imagen" width="14" height="15" border="0" /></a>
                    <? } else echo '&nbsp;';?>                </td>
                <td align="center" valign="top"><? if(file_exists($arc_pdf)) { ?>
                    <a href="<?=$arc_pdf;?>?ht=<?=$ht;?>" rel="shadowbox" target="_blank"> <img src="images/pdf.gif" name="img" border="0" id="img" /> </a>
                    <? } // if file_exists ?>                </td>
              </tr>
              <? } // for i ?>
            </table></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="10">&nbsp;</td>
          </tr>
          <tr class="tr-subt">
            <td colspan="11">PRICELIST</td>
          </tr>
          <tr>
            <td><div align="right">AMECOP:</div></td>
            <td colspan="10"><input name="pl_amecop" type="text" class="campo" id="pl_amecop" value="<?=$row['pl_amecop'];?>" size="20" maxlength="20" /></td>
          </tr>
          <tr>
            <td><div align="right">Comentarios PL:</div></td>
            <td colspan="10"><textarea name="pl_comentarios" cols="117" rows="3" class="campo" id="pl_comentarios"><?= $row['pl_comentarios']; ?></textarea></td>
          </tr>
          <tr>
            <td><div align="right">Exclusivo PriceList:</div></td>
            <td colspan="10"><input name="pl_exclusivo" type="checkbox" id="pl_exclusivo" value="1" <? if ($row['pl_exclusivo']) echo 'checked';?> />(si se selecciona, el producto no se mostrar&aacute; en Web ni POS)</td>
          </tr>
          <tr>
            <td><div align="right">Aplica para:</div></td>
            <td colspan="10" align="left"><label><input type="radio" name="pl_aplica_para" id="pl_aplica_para_TP" value="TP" <? if ($row['pl_aplica_para']=='TP') echo 'checked';?> /> Trade Partner</label>
			&nbsp;&nbsp;
			<label><input type="radio" name="pl_aplica_para" id="pl_aplica_para_TN" value="TN" <? if ($row['pl_aplica_para']=='TN') echo 'checked';?> /> Tipo de Negocio</label>
            &nbsp;&nbsp;
			<label><input type="radio" name="pl_aplica_para" id="pl_aplica_para_TN" value="" <? if ($row['pl_aplica_para']=='') echo 'checked';?> /> N/A</label></td>
          </tr>
          <tr>
            <td><div align="right">Industria:</div></td>
            <td colspan="10">
             <select name="pl_clave_cat" class="campo" id="pl_clave_cat">
              <option value="">Seleccionar Trade Partner o Tipo de Negocio (Industria)...</option>
              <?  $resIND= mysql_query("SELECT * FROM pl_customerindustry ORDER BY Type, Name",$conexion);
					  while ($rowIND = mysql_fetch_array($resIND)) { 
						echo '<option value="'.$rowIND['CustomerIndustryID'].'"';
						if ($rowIND['CustomerIndustryID']==$row['pl_clave_cat']) echo ' selected';
						echo '>'.$rowIND['Name'].'</option>';
					  }
				  ?>
            </select></td>
          </tr>

          <? } // mostrar ?>
          <? 
			if (!$autorizar) { ?>
          <tr>
            <td>&nbsp;</td>
            <td colspan="10">&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="10">
                <? if ($mostrar) { ?><input name="grabar" type="button" class="boton" onclick="valida();" value="GRABAR" /><? } ?>
                <input name="desc" type="button" class="boton" onclick="descarta();" value="REGRESAR AL LISTADO" id="desc" />
            <input name="producto" type="hidden" id="producto" value="<?= $producto; ?>" />            </td>
          </tr>
          <? } else { ?>
          <tr>
            <td>&nbsp;</td>
            <td colspan="10">
                <? if ($mostrar) { ?>
                <input name="grabar" type="button" class="boton" onclick="valida();" value="AUTORIZAR" />
	            <input name="rechazar" type="button" class="boton" onclick="rechaza();" value="RECHAZAR" />
                <input name="producto" type="hidden" id="producto" value="<?= $producto; ?>" /> 
                <input name="autorizar" type="hidden" id="autorizar" value="<?= $autorizar; ?>" />
                <? } ?>            </td>
          </tr>
          <? } ?>
          <tr>
            <td>&nbsp;</td>
            <td colspan="10">&nbsp;</td>
          </tr>
        </table>
      </form>    
    </div>
</div>
</body>
</html>
