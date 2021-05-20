<?
//if (!include('ctrl_acceso.php')) return; 

include("funciones_ajax.php");
include_once('funciones.php');
include("../conexion.php");

$modulo=23;
if (!op($modulo))  {
	$aviso = 'Usuario sin permiso para acceder a este módulo';
	$aviso_link = 'principal.php';
	include('mensaje_sistema.php');
	return;
}

$producto_wp = $_GET['producto_wp'];
if (!$producto_wp) $producto_wp = $_POST['producto_wp'];

// primera vez en edición, cargar datos.. despues ya no porque se recarga la página
$editar = $_GET['editar'];
$accion = $_POST['accion'];
if ($editar && $producto_wp) {
	$accion = 'agrega_producto';
}



///////////////

/// ajax autocompletar //////
require ('xajax/xajax_core/xajax.inc.php');
$xajax = new xajax(); 
$xajax->register(XAJAX_FUNCTION, 'autocomplete'); 
$xajax->register(XAJAX_FUNCTION, 'asocia'); 
$xajax->processRequest(); 
$xajax->configure('javascript URI','xajax/'); 
// las funciones van incluidas en funciones_ajax, arriba. Si van pegadas, van aqui..

function asocia($producto_wp,$producto_comp,$valor) {
	$objResponse = new xajaxResponse();
	$objResponse->setCharacterEncoding('ISO-8859-1');
	if ($valor == 'true') {
		$resultado = @mysql_query("INSERT comp_matriz (producto_wp, producto_comp) VALUES ($producto_wp, $producto_comp)");
	} else { 
		$resultado = @mysql_query("DELETE FROM comp_matriz WHERE producto_wp = $producto_wp AND producto_comp = $producto_comp");
	}
	return $objResponse;   
}

/////////////////////////////
//  ACCIONES 
//////////////////////////////
if ($accion == 'agrega_producto') {

//	$producto_wp = $_POST['producto'];
	
	// obtener datos del producto WP
	$query = "SELECT producto.nombre, modelo, producto.categoria, subcategoria, categoria.nombre AS nombre_categoria, subcategoria.nombre AS nombre_subcategoria
								FROM producto 
								LEFT JOIN categoria ON producto.categoria = categoria.clave
								LEFT JOIN subcategoria ON producto.subcategoria = subcategoria.clave
								WHERE producto.clave = $producto_wp";
	$resultadoP = mysql_query($query);
	$rowP = mysql_fetch_array($resultadoP);

	$sku = $rowP['modelo'];
	$descr_producto = $rowP['nombre'];
	$categoria = $rowP['categoria'];
	$subcategoria = $rowP['subcategoria'];
	$nombre_categoria = $rowP['nombre_categoria'];
	$nombre_subcategoria = $rowP['nombre_subcategoria'];

}

//include("_checa_vars.php");

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
<script src="js/jquery.js" type="text/javascript" language="javascript1.2"></script>
<script type="text/javascript" src="js/menu.js"></script>
<script type="text/javascript" src="js/jquery.js"></script>
<link href="js/src/shadowbox.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/src/shadowbox.js"></script>
<script src="js/ui.js" type="text/javascript" language="javascript1.2"></script>
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
  function seleccionar() {
   o = document.forma;
   if (o.producto.value=='') {
	alert ("Debes seleccionar un producto Whirlpool a registrar");
	return;
   }
   document.forma.producto_wp.value = document.forma.producto.value;
   document.forma.accion.value = 'agrega_producto';
   document.forma.action='abc_sku_comp.php';
   document.forma.submit();
  }

  function recarga(accion) {
   document.forma.accion.value=accion;
   document.forma.action='abc_sku_comp.php';
   document.forma.submit();
  }  	
  function descarta() {
   document.forma.action='lista_matriz_comp.php';
   document.forma.submit();
  }  	
</script>
</head>

<body>
<div id="container">
	<? $tit='Agregar productos a matriz'; $sec_ped = 'sel'; include('top.php'); ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
       <input name="accion" id="accion" type="hidden" value="" />
       <input name="producto_wp" id="producto_wp" type="hidden" value="<?=$producto_wp;?>" />
        <table width="100%" border="0" align="center" cellpadding="2" cellspacing="0" class="texto">
          <tr>
            <td><div align="right"><strong>Selecciona el producto WP:</strong></div></td>
            <td><span class="busqueda">
              <input type="text" id="busqueda" onfocus="document.forma.productox.value = this.value;" 
                onkeypress="if (event.keyCode==13) {  document.forma.productox.value = this.value; document.forma.submit(); }" onkeyup="xajax_autocomplete(this.value);"
                autocomplete="off" style="width:160px;"/>
            </span></td>
            <td><input name="bagreg" type="button" class="boton_agregar" onclick="seleccionar();" value="Seleccionar" id="bagreg" /></td>
            <td><input type="hidden" name="producto" id="producto"  width="50"/>
              <input type="hidden" name="modelo" id="modelo"  width="50"/>
              <input type="hidden" name="productox" id="productox"  width="50"/></td>
              <input type="hidden" name="nombre_producto" id="nombre_producto"  width="50"/>
          </tr>
          <tr>
            <td >&nbsp;</td>
            <td >&nbsp;</td>
            <td >&nbsp;</td>
            <td >&nbsp;</td>
          </tr>
          <tr>
            <td width="31%" bgcolor="#999999"><strong>Nombre del producto</strong></td>
            <td width="21%" bgcolor="#999999"><strong>SKU</strong></td>
            <td width="24%" bgcolor="#999999">Categor&iacute;a</td>
            <td width="24%" bgcolor="#999999">Subcategor&iacute;a</td>
          </tr>
          
          <tr>
            <td><input name="descr_producto" type="text" id="descr_producto" size="50" maxlength="100" readonly="readonly" value="<?=$descr_producto;?>"/></td>
           <td><input name="sku" type="text" id="sku" size="20" maxlength="50" readonly="readonly" value="<?=$sku;?>"/></td>
           <td><input name="categoria" type="text" id="categoria" size="35" maxlength="100" readonly="readonly" value="<?=$nombre_categoria;?>"/></td>
           <td><input name="subcategoria" type="text" id="subcategoria" size="35" maxlength="100" readonly="readonly" value="<?=$nombre_subcategoria;?>"/></td>
          </tr>
          <tr>
            <td >&nbsp;</td>
            <td >&nbsp;</td>
            <td >&nbsp;</td>
            <td >&nbsp;</td>
          </tr>
          <tr>
            <td colspan="4" >&nbsp;</td>
          </tr>
          <? if ($accion=='agrega_producto') { ?>
          <tr>
            <td colspan="4" >
            <table width="100%" border="0" cellpadding="2" cellspacing="2">
              <tr>
                <td colspan="4"><strong>Productos de la competencia</strong></td>
              </tr>
              <tr bgcolor="AAAAAA">
                <th><div align="center">SKU</div></th>
                <th>Marca</th>
                <th>Nombre</th>
                <th><div align="center">Asociar</div></th>
              </tr>
              <?
			  		$resultado = mysql_query("SELECT comp_producto.*, comp_marca.nombre AS nombre_marca 
												FROM comp_producto 
												LEFT JOIN comp_marca ON comp_producto.marca = comp_marca.clave
											   WHERE categoria = $categoria AND subcategoria = $subcategoria
											   ORDER BY marca");
					
                    while ($row = mysql_fetch_array($resultado)) {
						
						// detectar si ya está asociado
						$producto_comp = $row['clave'];
						$resultadoMAT = mysql_query("SELECT 1 FROM comp_matriz WHERE producto_wp = $producto_wp AND producto_comp = $producto_comp");
						$enc = mysql_num_rows($resultadoMAT);
						if ($enc>0) $asociado = 1; else $asociado = 0;

              ?>
              <tr bgcolor="#FFFFFF" >
                <td align="center"><?=$row['sku']; ?></td>
                <td><?=$row['nombre_marca'];?></td>
                <td><?= $row['nombre'];?></td>
                <td align="center" ><label>
                <input type="checkbox" name="act_<?=$producto_wp;?>_<?=$row['clave'];?>" id="act_<?=$producto_wp;?>_<?=$row['clave'];?>" onchange="xajax_asocia(<?=$producto_wp;?>,<?=$row['clave'];?>, this.checked);" <? if ($asociado) echo 'checked'; ?> />
                </label></td>
              </tr>
              <? } // while  ?>
            </table></td>
          </tr>	
          <? } // agrega producto?>	  

          <tr>
            <td colspan="4">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="4"><input name="desc" type="button" class="boton" onclick="descarta();" value="Regresar al listado" id="desc" /></td>
          </tr>
        </table>
      </form>    
    </div>
</div>
</body>
</html>
