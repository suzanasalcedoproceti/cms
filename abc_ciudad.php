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

<script language="JavaScript">
  function valida() {
   if (document.forma.estado.value == "") {
     alert("Falta estado.");
	 document.forma.estado.focus();
     return;
   }
   if (document.forma.nombre.value == "") {
     alert("Falta nombre de la ciudad.");
	 document.forma.nombre.focus();
     return;
   }
   if (document.forma.trans_zone.value == "") {
     alert("Falta la clave del trans-zone.");
	 document.forma.trans_zone.focus();
     return;
   }
   document.forma.action='graba_ciudad.php';
   document.forma.submit();
  }
  function rechaza() {
   document.forma.action='borra_ciudad.php';
   document.forma.submit();
  }
  function descarta() {
   document.forma.action='lista_ciudad.php';
   document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
	<? $tit='Administrar Ciudades'; include('top.php'); ?>
	<?
        include('../conexion.php');

		$ciudad=$_POST['ciudad'];
		if (empty($ciudad)) $ciudad=$_GET['ciudad'];
        
        if (!empty($ciudad)) {
          $resultado= mysql_query("SELECT * FROM ciudad WHERE clave='$ciudad'",$conexion);
          $row = mysql_fetch_array($resultado);
		  $estado=$row['estado'];
        }
    $readonly = (empty($ciudad)) ? "" : "readonly";

$tipo_producto=$_POST['tipo_producto'];
if (empty($tipo_producto)) $tipo_producto=$_GET['tipo_producto'];
$tipo_entrega=$_POST['tipo_entrega'];
if (empty($tipo_entrega)) $tipo_entrega=$_GET['tipo_entrega'];

if (!empty($tipo_producto)) {
  $resultadoTP= mysql_query("SELECT * FROM tipo_producto WHERE clave='$tipo_producto'",$conexion);
  $rowTP = mysql_fetch_array($resultadoTP);
  $tipo_producto=$rowTP['clave'];
}

if (!empty($tipo_entrega)) {
  $resultadoTE= mysql_query("SELECT * FROM tipo_entrega WHERE clave='$tipo_entrega'",$conexion);
  $rowTE = mysql_fetch_array($resultadoTE);
  $tipo_entrega=$rowTE['clave'];
}     
      ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">

          <tr>
            <td width="24%">&nbsp;</td>
            <td colspan="3">&nbsp;</td>
          </tr>
          <tr>
            <td><div align="right">Ciudad:</div></td>
            <td colspan="3"><input name="nombre" type="text" class="campo" id="nombre" value="<?= $row['nombre']; ?>" size="115" maxlength="100" <?=$readonly;?> />            </td>
          </tr>
          <tr>
            <td><div align="right">Estado:</div></td>
            <td colspan="3"><span class="row1">
              <? if (empty($ciudad)){ ?>
              <select name="estado" class="campo" id="estado">
                <option value="" selected="selected">Selecciona estado...</option>
                <?
					$resultadoEDO = mysql_query("SELECT * FROM estado ORDER BY clave",$conexion);
					while ($rowEDO = mysql_fetch_array($resultadoEDO)) {
					  echo '<option value="'.$rowEDO['clave'].'"';
					  if ($rowEDO['clave']==$estado) echo 'selected';
				  	  echo '>'.$rowEDO['nombre'].'</option>';
				    }
			  ?>
              </select>
            <? } else { 
              $resultadoEDO = mysql_query("SELECT * FROM estado WHERE clave='$estado'",$conexion);
              $rowEDO = mysql_fetch_array($resultadoEDO);
              ?>
              <input type="hidden" name="estado" type="text" class="campo" id="estado" value="<?= $rowEDO['clave']; ?>">
              <input name="estado_nombre" type="text" class="campo" id="estado_nombre" value="<?= $rowEDO['nombre']; ?>" size="115" maxlength="100" <?=$readonly;?> />
            <? } ?>
            </span></td>
          </tr>
          <tr>
            <td><div align="right">Zona (Trans-zone):</div></td>
            <td colspan="3"><input name="trans_zone" type="text" class="campo" id="trans_zone" value="<?= $row['trans_zone']; ?>" size="12" maxlength="10" <?=$readonly;?>/>            </td>
          </tr>
          <tr>
            <td><div align="right">Zona (Purchase Order Number):</div></td>
            <td colspan="3"><input name="purch_no_c" type="text" class="campo" id="purch_no_c" value="<?= $row['purch_no_c']; ?>" size="30" maxlength="30" <?=$readonly;?>/>
(Ej. eCommerce Monterrey) Aplica solo para Web y Mobile. </td>
          </tr>
          <? if(!empty($ciudad)){ 
              $resultadoClave = mysql_query("SELECT * FROM ciudad_planta where ciudad=$ciudad and tipo_producto='$tipo_producto' and tipo_entrega='$tipo_entrega' LIMIT 1",$conexion);
              $rowCdPlanta = mysql_fetch_array($resultadoClave);
            ?>
          <tr>
            <td colspan="4">&nbsp;</td>
          </tr>          
          <tr>
            <td colspan="4" align="center">
              <table>
                <tr>
                  <td align="center" bgcolor="#999999"><strong>Tipo de Producto</strong></td>
                  <td align="center" bgcolor="#999999"><strong>Tipo de Entrega</strong></td>
                  <td align="center" bgcolor="#999999"><strong>Cobertura</strong></td>
                  <td align="center" bgcolor="#999999"><strong>Sku</strong></td>
                  <td align="center" bgcolor="#999999"><strong>Sucursal</strong></td>
                </tr>
                <tr>
                  <td align="right"><input type="text" readonly="readonly" name="clave_tipo_producto" value="<?=$rowTP['clave']; ?>"></td>
                  <td align="right"><input type="text" readonly="readonly" name="nombre_tipo_entrega" value="<?=$rowTE['nombre']; ?>"></td>
                  <td align="right"><select name="cobertura" class="campo" id="cobertura" style="width:100%;">
              <option value="1" <? if ($rowCdPlanta['cobertura']==0) echo 'selected ';?>>SI</option>
              <option value="0" <? if ($rowCdPlanta['cobertura']==0) echo 'selected ';?>>NO</option>
            </select></td>
                  <td align="right"><input name="sku" type="text" class="campo" id="sku" value="<?= $rowCdPlanta['sku']; ?>" size="25" maxlength="50" /></td>
                  <td align="center"><select name="sucursal" class="campo" id="sucursal">
              <option value="">Selecciona....</option>
              <? $resultadoAL = mysql_query("SELECT * FROM sucursal_ocurre ORDER BY nombre");
           while ($rowAL = mysql_fetch_array($resultadoAL)) { ?>
              <option value="<?=$rowAL['clave'];?>" <? if ($rowAL['clave']==$rowCdPlanta['sucursal']) echo 'selected';?>>
                <?=$rowAL['nombre'];?>
              </option>
              <? } ?>
            </select></td>
                </tr>
              </table>
            </td>
          </tr>
        <? } ?>
          <tr>
            <td colspan="4">&nbsp;</td>
          </tr>
          <!--tr>
            <td><div align="right">Disponible en Mobiles:</div></td>
            <td colspan="3"><input name="envio_sin_costo" type="checkbox" id="envio_sin_costo" value="1" <? if ($row['envio_sin_costo']) echo 'checked';?> />
            (temporalmente para mobiles no hay Entregas Nacionales, mostrar solo ciudades sin costo)</td>
          </tr-->
          <tr>
            <td>&nbsp;</td>
            <td colspan="3"><input name="grabar" type="button" class="boton" onclick="valida();" value="GRABAR" />
                <input name="desc" type="button" class="boton" onclick="descarta();" value="REGRESAR AL LISTADO" id="desc" />
            <input name="ciudad" type="hidden" id="ciudad" value="<?= $ciudad; ?>" />            </td>
          <input name="tipo_entrega" type="hidden" id="tipo_entrega" value="<?= $tipo_entrega; ?>" /> </td>
          <input name="tipo_producto" type="hidden" id="tipo_producto" value="<?= $tipo_producto; ?>" /> </td>          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="3">&nbsp;</td>
          </tr>
        </table>
      </form>    
    </div>
</div>
</body>
</html>
