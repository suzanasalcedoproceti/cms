<?
  
  if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
  include('../conexion.php');

	$modulo=9;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}

 

$estado=$_POST['estado'];
if (empty($estado)) $estado=$_GET['estado'];
$tipo_producto=$_POST['tipo_producto'];
if (empty($tipo_producto)) $tipo_producto=$_GET['tipo_producto'];
$tipo_entrega=$_POST['tipo_entrega'];
if (empty($tipo_entrega)) $tipo_entrega=$_GET['tipo_entrega'];


if (!empty($estado)) {
  $resultado= mysql_query("SELECT * FROM estado WHERE clave='$estado'",$conexion);
  $row = mysql_fetch_array($resultado);
  $estado=$row['clave'];
}

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
  
$resultadoPlanta = mysql_query("SELECT * FROM planta ORDER BY planta, loc",$conexion);
while($rowPlanta = mysql_fetch_assoc($resultadoPlanta)) {
  $plantas[] = $rowPlanta;
}

$resultadoEdoPlanta = mysql_query("SELECT * FROM estado_planta WHERE estado = '".$estado."' and tipo_producto= '".$tipo_producto."' and tipo_entrega= '".$tipo_entrega."' ORDER BY orden ",$conexion);
while($rowEdoPlanta = mysql_fetch_assoc($resultadoEdoPlanta)) {
  $edoPlantas[$rowEdoPlanta['tipo_producto']][$rowEdoPlanta['orden']] = $rowEdoPlanta;
}  
//echo "SELECT * FROM estado_planta WHERE estado = '".$estado."' and tipo_producto= '".$tipo_producto."' and tipo_entrega= '".$tipo_entrega."' ORDER BY orden ";
//print_r($edoPlantas);
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
   if (document.getElementById('planta_1').value=="") {
	   alert("Selecciona el Cedis 1");
	   return; 
   }

   document.forma.action='graba_estado.php';
   document.forma.submit();
  }
  function descarta() {
   document.forma.action='lista_estado.php';
   document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
	<? $tit='Administrar estados'; include('top.php'); ?>
	
<div class="main">
  <form action="" method="post" name="forma" id="forma">
    <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">
      <tr>
        <td>
          <div align="right">Estado:</div>
        </td>
        <td colspan="3">
          <?= $row['nombre']; ?>
        </td>
      </tr>
      <tr>
        <td>
          <div align="right">Tipo de Entrega:</div>
        </td>
        <td colspan="3">
          <?= $rowTE['nombre']; ?>
        </td>
      </tr>
      <tr>
        <td align="right">&nbsp;</td>
        <td width="20%" align="center" bgcolor="#999999"><strong><?= $tipo_producto;?></strong></td>
        <td width="32%" align="right">&nbsp;</td>
      </tr>

      <?php for($i = 1; $i <= 7; $i++):?>
      <tr>
        <td>
          <div align="right">Cedis <?php echo $i?>:</div>
        </td>
        <td align="center"><span class="row1">
              <?php 
                $clave_rm = '';
                if(isset($edoPlantas[$tipo_producto][$i])){  
                    $clave_rm = $edoPlantas[$tipo_producto][$i]['clave'];
                }

              ?>
              <input type="hidden" name="estado_planta_clave_[<?php echo $i?>]" id="estado_planta_clave" value="<?php echo $clave_rm?>">
              <select name="planta_[<?php echo $i?>]" class="campo" id="planta_<?php echo $i?>">
                <option value="">Selecciona planta...</option>
                <?php
                  foreach ($plantas as $k => $planta) {
                    $selected = '';
                    if(isset($edoPlantas[$tipo_producto][$i])){                     
                      $selected = ($edoPlantas[$tipo_producto][$i]['planta'] == $planta['clave']) ? 'selected' : '';
                    }
                    echo "<option value='".$planta['clave']."' ".$selected.">".$planta['planta']."/".$planta['loc']."</option>\n";
                  }
                ?>
              </select>
            </span></td>
        <td>&nbsp;</td>
      </tr>
      <?php endfor;?>
      <tr>
        <td>&nbsp;</td>
        <td colspan="3">
          <input name="grabar" type="button" class="boton" onclick="valida();" value="GRABAR" />
          <input name="desc" type="button" class="boton" onclick="descarta();" value="REGRESAR AL LISTADO" id="desc" />
          <input name="estado" type="hidden" id="estado" value="<?= $estado; ?>" /> </td>
          <input name="tipo_entrega" type="hidden" id="tipo_entrega" value="<?= $tipo_entrega; ?>" /> </td>
          <input name="tipo_producto" type="hidden" id="tipo_producto" value="<?= $tipo_producto; ?>" /> </td>
      </tr>
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
