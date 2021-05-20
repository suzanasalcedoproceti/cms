<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	include("lib.php");
	$modulo=8;
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
    if (document.forma.nombre.value == "") {
     alert("Falta categoría.");
	 document.forma.nombre.focus();
     return;
     }
 
   document.forma.action='graba_cliente.php';
   document.forma.submit();
  }
  function descarta() {
   document.forma.action='lista_cliente.php';
   document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
	<? $tit='Administrar Clientes'; include('top.php'); ?>
	<?
        include('../conexion.php');

		$cliente=$_GET['cliente'];
		if (empty($cliente)) $cliente=$_POST['cliente'];
        
        if (!empty($cliente)) {
          $resultado= mysql_query("SELECT * FROM cliente WHERE clave='$cliente'",$conexion);
          $row = mysql_fetch_array($resultado);
        }

		$empresa=$row['empresa'];
		$resEMP= mysql_query("SELECT * FROM empresa WHERE clave='$empresa'",$conexion);
		$rowEMP= mysql_fetch_array($resEMP); 
		
        if ($row['invitado']) { 
	  		$cliente_inv = $row['invitado_por'];
	  		$resultadoINV = mysql_query("SELECT nombre FROM cliente WHERE clave = $cliente_inv",$conexion);
			$rowINV = mysql_fetch_array($resultadoINV);
			$pe_disponibles_invitados = 0;
		} else {
			$resultadoINVS = mysql_query("SELECT COUNT(*) AS total_invitados, SUM(pe_disponibles) AS pe_disponibles FROM cliente WHERE invitado AND invitado_por = $cliente",$conexion);
			$rowINVS = mysql_fetch_array($resultadoINVS);
			$total_invitados = $rowINVS['total_invitados'];
			$pe_disponibles_invitados = $rowINVS['pe_disponibles'];
		}
		
		// precios especiales
	    // obtener datos de configuracion
	    $resultadoCFG = mysql_query("SELECT * FROM config WHERE reg = 1",$conexion);
	    $rowCFG = mysql_fetch_array($resultadoCFG);
	
	    $limite_precios_especiales = $rowCFG['limite_precios_especiales']+0;
		/*
		$ano = date("Y");
		$resultadoPRS = mysql_query("SELECT cantidad FROM precios_especiales WHERE ano = '$ano' AND cliente = $cliente");
		$rowPRS = mysql_fetch_array($resultadoPRS);
		$pe_disponibles = $limite_precios_especiales - $rowPRS['cantidad'];
		*/
		$pe_disponibles = $row['pe_disponibles']+0;
		if ($pe_disponibles <0) $pe_disponibles = 0;


      ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="1" class="texto">

          <tr>
            <td><strong>DATOS GENERALES</strong></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td align="right">Clave:</td>
            <td colspan="2"><div align="left">
                <?= $row['clave']; ?>
            </div></td>
          </tr>
          <tr>
            <td align="right">Nombre:</td>
            <td colspan="2"><div align="left">
              <input name="nombre" type="text" class="campo" id="nombre" value="<?= $row['nombre']; ?>" size="50" maxlength="100" />
            </div></td>
          </tr>
          <tr>
            <td align="right" valign="top">Apellido paterno:</td>
            <td colspan="2" valign="top"><input name="apellido_paterno" type="text" class="campo" id="apellido_paterno" value="<?= $row['apellido_paterno']; ?>" size="50" maxlength="50" /></td>
          </tr>
          <tr>
            <td align="right" valign="top">Apellido materno:</td>
            <td colspan="2" valign="top"><input name="apellido_materno" type="text" class="campo" id="apellido_materno" value="<?= $row['apellido_materno']; ?>" size="50" maxlength="50" /></td>
          </tr>
          <tr>
            <td align="right" valign="top">Correo electr&oacute;nico:</td>
            <td colspan="2" valign="top"><div align="left">
              <?= $row['email']; ?>
            </div></td>
          </tr>
          <tr>
            <td align="right" valign="top">Empresa:</td>
            <td colspan="2"><div align="left">
              <?= $rowEMP['nombre']; ?>
            </div></td>
          </tr>
          <tr>
            <td align="right" valign="top">Tipo:</td>
            <td colspan="2"><? switch ($row['tipo']) {
					case 'E' : echo 'Empleado'; break;
					case 'I' : echo 'Invitado'; break;
					case 'C' : echo 'Corporate'; break;
					case 'A' : echo 'Mercado Abierto'; break;
				 }
			 ?></td>
          </tr>
          <tr>
            <td align="right" valign="top">Num. empleado:</td>
            <td colspan="2"><input name="numero_empleado" type="text" class="campo" id="numero_empleado" value="<?= $row['numero_empleado']; ?>" size="12" maxlength="10" />
          <div align="left"></div></td>
          </tr>
          <? if ($rowEMP['empresa_whirlpool']) { ?>
          <tr>
            <td align="right">User ID:</td>
            <td colspan="2"><?= $row['user_id']; ?></td>
          </tr>
          <tr>
            <td align="right">Fecha Nacimiento:</td>
            <td colspan="2"><?=fechamy2mx($row['fecha_nacimiento']);?></td>
          </tr>
          <tr>
            <td align="right">Divisi&oacute;n de Personal:</td>
            <td colspan="2"><?= $row['division']; ?></td>
          </tr>
          <tr>
            <td align="right">&Aacute;rea de N&oacute;mina:</td>
            <td colspan="2"><?= $row['area_nomina']; ?></td>
          </tr>
          <tr>
            <td align="right">Falta (Justificada o Injustificada):</td>
            <td colspan="2"><?= $row['falta']; ?></td>
          </tr>
          <tr>
            <td align="right">Tope de deducci&oacute;n:</td>
            <td colspan="2"><?= number_format($row['tope_deduccion'],2); ?></td>
          </tr>
          <tr>
            <td align="right">Banda:</td>
            <td colspan="2"><?= $row['banda']; ?></td>
          </tr>
          <tr>
            <td align="right">Fecha de Ingreso:</td>
            <td colspan="2"><?= fecha($row['fecha_ingreso']); ?></td>
          </tr>
          <tr>
            <td align="right">Estatus:</td>
            <td colspan="2"><?= $row['estatus_sap']; ?></td>
          </tr>
          <tr>
            <td align="right" valign="top">&nbsp;</td>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td align="right" valign="top">Puntos:</td>
            <td colspan="2"><?= $row['puntos']; ?></td>
          </tr>
          <tr>
            <td align="right" valign="top">Puntos Flex:</td>
            <td colspan="2"><?= $row['puntos_flex']; ?></td>
          </tr>
          <tr>
            <td align="right" valign="top">Puntos PEP:</td>
            <td colspan="2"><?= $row['puntos_pep']; ?></td>
          </tr>
          <tr>
            <td align="right" valign="top">KAD Mayores comprados:</td>
            <td colspan="2"><?= $row['kad_comprados']; ?></td>
          </tr>
          <tr>
            <td align="right">Precios Especiales Disponibles:</td>
            <td colspan="7">
            <select name="pe_disponibles" id="pe_disponibles">
            <? for ($i=0; $i<=$limite_precios_especiales; $i++) { ?>
              <option value="<?=$i;?>" <? if ($i==$pe_disponibles) echo 'selected';?>><?=$i;?></option>
            <? } ?>
            </select>
            / <?= $limite_precios_especiales;?> (sus invitados tienen <strong><?=$pe_disponibles_invitados+0;?></strong> precios especiales disponibles)</td>
          </tr>
          <? } // empresa whirlpool?>
          <tr>
            <td align="right" valign="top">Fecha de registro:</td>
            <td colspan="2"><div align="left">
                <?= date('d/m/Y H:i:s',strtotime($row['fecha'])); ?>
            </div></td>
          </tr>
          <tr>
            <td align="right" valign="top">Tipo:</td>
            <td colspan="2"><div align="left">
            <? switch ($row['tipo']) {
					case 'E' : echo 'Empleado'; break;
					case 'I' : echo 'Invitado'; break;
					case 'C' : echo 'Corporate'; break;
					case 'A' : echo 'Mercado Abierto'; break;
				 }
			 ?>
            </div></td>
          </tr>
          <? if ($row['invitado']) { ?>
          <tr>
            <td align="right" valign="top">Invitado por:</td>
            <td colspan="2"><div align="left">
                <?= $rowINV['nombre']; ?>
            </div></td>
          </tr>
          <tr>
            <td align="right" valign="top">Vigencia de Invitaci&oacute;n:</td>
            <td colspan="2"><div align="left">
                <?= fecha($row['vigencia_invitacion']); ?>
            </div></td>
          </tr>
          <? } ?>
          <tr>
            <td colspan="3" ><strong>DATOS DE FACTURACI&Oacute;N</strong></td>
          </tr>
          <tr>
            <td><div align="right">Calle:</div></td>
            <td colspan="2" align="left"><div align="left">
                <?=$row['fact_calle'];?> # <?=$row['fact_exterior'];?> <?=$row['fact_interior'];?> 
            </div></td>
          </tr>
          <tr>
            <td><div align="right">Colonia:</div></td>
            <td colspan="2" align="left"><div align="left">
                <?=$row['fact_colonia'];?>
            </div></td>
          </tr>
          <tr>
            <td><div align="right">Ciudad:</div></td>
            <td colspan="2" align="left" ><div align="left">
                <?=$row['fact_ciudad'];?>
            </div></td>
          </tr>
          <tr>
            <td><div align="right">Estado:</div></td>
            <td colspan="2" align="left" ><div align="left">
                <?= $row['fact_estado'];?>
            </div></td>
          </tr>
          <tr>
            <td><div align="right">C.P.:</div></td>
            <td colspan="2" align="left"><div align="left">
                <?=$row['fact_cp'];?>
            </div></td>
          </tr>
          <tr>
            <td><div align="right">Tel&eacute;fono:</div></td>
            <td colspan="2" align="left"><div align="left">
                <?=$row['fact_telefono'];?>
            </div></td>
          </tr>
          <tr>
            <td><div align="right">Raz&oacute;n Social:</div></td>
            <td colspan="2" align="left" ><div align="left">
                <?=$row['razon_social'];?>
            </div></td>
          </tr>
          <tr>
            <td><div align="right">R.F.C.:</div></td>
            <td colspan="2" align="left"><div align="left">
                <?=$row['rfc'];?>
            </div></td>
          </tr>
          <? if (!$row['invitado'] && $rowEMP['invita_amigos']) {  ?>
          <tr>
            <td colspan="3" ><strong>Familiares y Amigos Invitados</strong></td>
          </tr>
          <tr>
            <td><div align="right">Invitaciones disponibles:</div></td>
            <td align="left"><div align="left">
              <input name="invitados_disponibles" type="text" class="campo" id="invitados_disponibles" value="<?= $row['invitados_disponibles']; ?>" size="5" maxlength="3" />
            </div></td>
            <td align="left">&nbsp;</td>
          </tr>
          <tr>
            <td bgcolor="#999999"><div align="left"><strong>Nombre</strong></div></td>
            <td align="left" bgcolor="#999999"><div align="left"><strong>Correo</strong></div></td>
            <td align="left" bgcolor="#999999"><strong>Vigencia</strong></td>
          </tr>
      <? 
			$resultadoINVS = mysql_query("SELECT nombre, email, vigencia_invitacion FROM cliente WHERE invitado AND invitado_por = $cliente",$conexion);
			while ($rowINVS = mysql_fetch_array($resultadoINVS)) { 
	  ?>
          <tr>
            <td><div align="left">
              <?=$rowINVS['nombre'];?>
            </div></td>
            <td align="left"><div align="left">
              <?=$rowINVS['email'];?>
            </div></td>
            <td align="left"><?=fecha($rowINVS['vigencia_invitacion']);?></td>
          </tr>
          <? } 
		   } ?>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
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
            <input name="cliente" type="hidden" id="cliente" value="<?= $cliente; ?>" />            </td>
            <td>&nbsp;</td>
          </tr>
        </table>
      </form>    
    </div>
</div>
</body>
</html>
