<?php
    if (!include('ctrl_acceso.php')) return;
	include("funciones_ajax.php");
	include('funciones.php');
	include("lib.php");
//ini_set ('error_reporting', E_ALL);
//ini_set ("display_errors","0" );

	$modulo=8;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
	/// ajax cambia_estado //////
	require ('xajax/xajax_core/xajax.inc.php');
	$xajax = new xajax(); 
	$xajax->register(XAJAX_FUNCTION, 'cambia_estado_proy'); 
	$xajax->processRequest(); 
	$xajax->configure('javascript URI','xajax/'); 
	// las funciones van incluidas en funciones_ajax, arriba. Si van pegadas, van aqui..

	include('../conexion.php');

	$direccion=$_GET['direccion']+0;
	if (!$direccion) $direccion = $_POST['direccion']+0;
	$cliente=$_GET['cliente']+0;
	if (!$cliente) $cliente= $_POST['cliente']+0;

	if ($direccion) {
		$resultado = mysql_query("SELECT * FROM direccion_envio WHERE clave = $direccion",$conexion);
		$row = mysql_fetch_array($resultado);

		$cliente=$row['cliente']+0;
		$estado = $row['estado'];
	} 
	
	$resultado= mysql_query("SELECT * FROM cliente WHERE clave=$cliente",$conexion);
	$rowC = mysql_fetch_array($resultado);
	$empresa=$rowC['empresa'];
	$resEMP= mysql_query("SELECT * FROM empresa WHERE clave='$empresa'",$conexion);
	$rowEMP= mysql_fetch_array($resEMP); 

	if ($rowEMP['empresa_proyectos']!=1) {
	   return;
	}
	// obtener datos de configuracion
	$resultadoCFG = mysql_query("SELECT * FROM config WHERE reg = 1",$conexion);
	$rowCFG = mysql_fetch_array($resultadoCFG);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Panel de Control</title>
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<link href="css/styles_dashboard.css" rel="stylesheet" type="text/css" />
<link href="css/menu.css" rel="stylesheet" type="text/css" />
<!--[if lte IE 6]><link rel="stylesheet" href="css/menu_ie6.css" /><![endif]-->
<script type="text/javascript" src="js/menu.js"></script>
<script type="text/javascript" src="js/lib.js"></script>
<script src="js/jquery.js" type="text/javascript" language="javascript1.2"></script>

<script language="JavaScript">
  function isEmail(string) {
    if (string.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) != -1)
        return true;
    else
        return false;
  }
function checklength(obj,max) {
	var txt;
	var n = obj.value.length;
	if (n>max) { 
		obj.value = obj.value.substring(0, max); 
		return false;
	}
}
  function valida() {
	o = document.forma;
	if (o.ship_to.value=='') {
		alert("Indica el valor de SHIP-TO");
		o.ship_to.focus();
		return false;
	}	
	if (o.cedis.value=='') {
		alert("Indica el valor de CEDIS");
		o.cedis.focus();
		return false;
	}	
	if (o.alias.value=='') {
		alert("Indica el alias de la dirección de envío");
		o.alias.focus();
		return false;
	}
	if (o.nombre.value=='') {
		alert("Indica el nombre del destinatario");
		o.nombre.focus();
		return false;
	}
	if (o.estado.value=='') {
		alert("Indica el estado de la dirección de envío");
		o.estado.focus();
		return false;
	}
	if (o.ciudad.value=='') {
		alert("Indica la ciudad de la dirección de envío");
		o.ciudad.focus();
		return false;
	}
	if (o.colonia.value=='') {
		alert("Indica la colonia");
		o.colonia.focus();
		return false;
	}
	if (o.calle.value=='') {
		alert("Indica la calle");
		o.calle.focus();
		return false;
	}
	if (o.exterior.value=='') {
		alert("Indica el número exterior");
		o.exterior.focus();
		return false;
	}
	if (o.observaciones.value=='') {
		alert("Indica la referencia (entre qué calles está la dirección)");
		o.observaciones.focus();
		return false;
	}
	if (o.cp.value=='') {
		alert("Indica el Código Postal");
		o.cp.focus();
		return false;
	}
	if (o.cp.value.length < 5 ) {	
		alert("Código Postal incompleto. Verifica");
		o.cp.focus();
		return false;
	}
	if (o.telefono_casa.value=='' && o.telefono_celular.value=='') {
		alert("Indica al menos un teléfono de contacto");
		o.telefono_casa.focus();
		return false;
	}
	if (o.contacto.value=='') {
		alert("Indica el nombre de la persona a contactar en esta dirección de envío");
		o.contacto.focus();
		return false;
	}	
	document.forma.action='graba_dir_cliente_proyectos.php';
    document.forma.submit();
  }
  
  function descarta() {
   document.forma.action='direcciones_cliente_proyectos.php';
   document.forma.submit();
  }
</script>
<?php 
 $xajax->printJavascript("xajax/"); 
?>
</head>

<body>
<div id="container">
	<? $tit='Administrar Direcciones de envío de clientes de Proyectos'; include('top.php'); ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="1" class="texto">
          <tr>
            <td><strong>DOMICILIO DE ENTREGA</strong></td>
            <td>*Datos obligatorios</td>
          </tr>
          <tr>
            <td align="right" >*Ship-To:</td>
            <td><input name="ship_to" type="text" id="ship_to" size="40" maxlength="35" value="<?=$row['ship_to'];?>"/> 
              (Para txt Order Entry)</td>
          </tr>
          <tr>
            <td align="right">*CEDIS:</td>
            <td><input name="cedis" type="text" id="cedis" size="40" maxlength="35" value="<?=$row['cedis'];?>"/></td>
          </tr>
          <tr>
            <td ><div align="right">*Alias:</div></td>
            <td><input name="alias" type="text" id="alias" size="40" maxlength="35" value="<?=$row['alias'];?>"/> 
              (Ejemplo: Sucursal Norte)</td>
          </tr>
          <tr>
            <td class="mid"><div align="right">*Nombre de quien recibe:</div></td>
            <td><input name="nombre" type="text" id="nombre" size="50" maxlength="50" value="<?=$row['nombre'];?>"/></td>
          </tr>
          <tr>
            <td >&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td ><div align="right">*Estado:</div></td>
            <td><select name="estado" class="campo" id="estado" onchange="xajax_cambia_estado_proy(this.value);">
              <option value="">Selecciona el estado</option>
              <? $resultadoEDO = mysql_query("SELECT DISTINCT ciudad.estado, estado.nombre 
				  									FROM ciudad 
													LEFT JOIN estado ON ciudad.estado = estado.clave 
												WHERE 1 ORDER BY ciudad.estado",$conexion);
					   while ($rowEDO = mysql_fetch_array($resultadoEDO)) {
			      ?>
              <option value="<?=$rowEDO['estado'];?>" <? if ($rowEDO['estado']==$row['estado']) echo 'selected';?>>
                <?=$rowEDO['nombre'];?>
              </option>
              <? } ?>
            </select></td>
          </tr>
          <tr>
            <td align="right">*Ciudad:</td>
            <td><div id="div_ciudad">
             <select name="ciudad" class="campo" id="ciudad" >
              <option value="">Selecciona la ciudad</option>
              <? $resultadoCD = mysql_query("SELECT * FROM ciudad WHERE estado = '$estado' ORDER BY nombre ",$conexion);
					   while ($rowCD = mysql_fetch_array($resultadoCD)) {
			      ?>
              <option value="<?=$rowCD['clave'];?>" <? if ($rowCD['clave']==$row['ciudad']) echo 'selected';?>><?=$rowCD['nombre'];?></option>
              <? } ?>
            </select></div></td>
          </tr>
          <tr>
            <td ><div align="right">*Calle:</div></td>
            <td><input name="calle" type="text" id="calle" size="60" maxlength="50" value="<?=$row['calle'];?>"/>
              *Exterior:
              <input name="exterior" type="text" id="exterior" size="10" maxlength="10" value="<?=$row['exterior'];?>"/>
              Interior:
  <input name="interior" type="text" id="interior" size="10" maxlength="10" value="<?=$row['interior'];?>"/></td>
          </tr>
          <tr>
            <td ><div align="right">*Colonia:</div></td>
            <td><input name="colonia" type="text" id="colonia" size="70" maxlength="50" value="<?=$row['colonia'];?>"/></td>
          </tr>
          <tr>
            <td ><div align="right">*C.P.:</div></td>
            <td><input name="cp" type="text" id="cp" size="7" maxlength="5" value="<?=$row['cp'];?>"/></td>
          </tr>
          <tr>
            <td align="right">Referencias (entre calles):</td>
            <td><textarea name="observaciones" cols="60" rows="3" class="campo" id="observaciones" onkeydown="javascript:checklength(this,300)"><?=$row['observaciones'];?></textarea></td>
          </tr>
          <tr>
            <td ><div align="right">*Tel&eacute;fono 1:</div></td>
            <td><input name="telefono_casa" type="text" id="telefono_casa" size="25" maxlength="20" value="<?=$row['telefono_casa'];?>"/></td>
          </tr>
          <tr>
            <td ><div align="right">*Tel&eacute;fono 2:</div></td>
            <td><input name="telefono_oficina" type="text" id="telefono_oficina" size="25" maxlength="20" value="<?=$row['telefono_oficina'];?>"/></td>
          </tr>
          <tr>
            <td ><div align="right">Tel&eacute;fono celular:</div></td>
            <td><input name="telefono_celular" type="text" id="telefono_celular" size="25" maxlength="20" value="<?=$row['telefono_celular'];?>"/></td>
          </tr>
          <tr>
            <td align="right" >*Persona a contactar:</td>
            <td><input name="contacto" type="text" id="contacto" size="60" maxlength="50" value="<?=$row['contacto'];?>"/></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input name="grabar" type="button" class="boton" onclick="valida();" value="GRABAR" />
              <input name="desc" type="button" class="boton" onclick="descarta();" value="REGRESAR AL LISTADO" id="desc" />
            <input name="cliente" type="hidden" id="cliente" value="<?= $cliente; ?>" /></td>
            <input name="direccion" type="hidden" id="direccion" value="<?= $direccion; ?>" /></td>
            <input name="viene_de" type="hidden" id="viene_de" value="<?= $viene_de; ?>" />
            <td></td></td>
          </tr>
        </table>
      </form>    
    </div>
</div>
</body>
</html>
