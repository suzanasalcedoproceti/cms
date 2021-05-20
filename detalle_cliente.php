<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	include('lib.php');
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
</head>

<body class="body_popup">
	<?
        include("../conexion.php");
		
		$cliente = $_GET['cliente'];
        
		$resCLI= mysql_query("SELECT *, CONCAT(cliente.nombre,' ',cliente.apellido_paterno,' ',cliente.apellido_materno) AS nombre FROM cliente WHERE clave='$cliente'",$conexion);
		$rowCLI= mysql_fetch_array($resCLI);

		$empresa=$rowCLI['empresa'];
		$resEMP= mysql_query("SELECT * FROM empresa WHERE clave='$empresa'",$conexion);
		$rowEMP= mysql_fetch_array($resEMP); 
		
		$resultadoINVS = mysql_query("SELECT COUNT(*) AS total_invitados FROM cliente WHERE invitado AND invitado_por = $cliente",$conexion);
		$rowINVS = mysql_fetch_array($resultadoINVS);
		$total_invitados = $rowINVS['total_invitados'];		
        
      ?>
	<table width="500" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">
      <tr>
        <td align="right">&nbsp;</td>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td width="34%" align="right"><strong>Clave:</strong></td>
        <td colspan="2"><div align="left">
            <?= $rowCLI['clave']; ?>
        </div></td>
      </tr>
      <tr>
        <td align="right"><strong>Nombre:</strong></td>
        <td colspan="2"><div align="left"><?= $rowCLI['nombre']; ?></div></td>
      </tr>
      <tr>
        <td align="right" valign="top"><strong>Correo electr&oacute;nico:</strong></td>
        <td colspan="2" valign="top"><div align="left"><?= $rowCLI['email']; ?>
        </div></td>
      </tr>
      <tr>
        <td align="right" valign="top"><strong>Empresa:</strong></td>
        <td colspan="2"><div align="left"><?= $rowEMP['nombre']; ?></div></td>
      </tr>
      <tr>
        <td align="right" valign="top"><strong>Num. empleado:</strong></td>
        <td colspan="2"><div align="left">
          <?= $rowCLI['numero_empleado']; ?>
        </div></td>
      </tr>
      <tr>
        <td align="right" valign="top"><strong>Fecha de registro:</strong></td>
        <td colspan="2"><div align="left">
          <?= date('d/m/Y H:i:s',strtotime($rowCLI['fecha'])); ?>
        </div></td>
      </tr>
          <? if ($rowEMP['empresa_whirlpool']) { ?>
          <tr>
            <td align="right" valign="top"><strong>Puntos:</strong></td>
            <td colspan="2"><div align="left">
              <?= $rowCLI['puntos']; ?>
            </div></td>
          </tr>
          <tr>
            <td align="right" valign="top"><strong>Puntos Flex:</strong></td>
            <td colspan="2"><div align="left">
              <?= $rowCLI['puntos_flex']; ?>
            </div></td>
          </tr>
          <tr>
            <td align="right" valign="top"><strong>Puntos PEP:</strong></td>
            <td colspan="2"><div align="left">
              <?= $rowCLI['puntos_pep']; ?>
            </div></td>
          </tr>
          <tr>
            <td align="right" valign="top"><strong>KAD Mayores comprados:</strong></td>
            <td colspan="2"><div align="left">
              <?= $rowCLI['kad_comprados']; ?>
            </div></td>
          </tr>
          <? } ?>


      <? if ($rowCLI['invitado']) { 
	  		$cliente_inv = $rowCLI['invitado_por'];
	  		$resultadoINV = mysql_query("SELECT nombre FROM cliente WHERE clave = $cliente_inv",$conexion);
			$rowINV = mysql_fetch_array($resultadoINV);
	  ?>
      <tr>
        <td align="right" valign="top"><strong>Invitado por:</strong></td>
        <td colspan="2"><div align="left">
          <?= $rowINV['nombre']; ?>
        </div></td>
      </tr>
      <tr>
        <td align="right" valign="top"><strong>Vigencia de Invitaci&oacute;n:</strong></td>
        <td colspan="2"><div align="left">
          <?= fecha($rowCLI['vigencia_invitacion']); ?>
        </div></td>
      </tr>
      <? } ?>
      <tr>
        <td colspan="3" >&nbsp;</td>
      </tr>
      <tr>
        <td colspan="3" ><div align="center"><strong>DATOS DE FACTURACI&Oacute;N</strong></div></td>
      </tr>
      <tr>
        <td><div align="right"><strong>Calle y n&uacute;mero:</strong></div></td>
        <td colspan="2" align="left"><div align="left">
          <?=$rowCLI['fact_calle'];?>
        </div></td>
      </tr>
      <tr>
        <td><div align="right"><strong>Colonia:</strong></div></td>
        <td colspan="2" align="left"><div align="left">
          <?=$rowCLI['fact_colonia'];?>
        </div></td>
      </tr>
      <tr>
        <td><div align="right"><strong>Ciudad:</strong></div></td>
        <td colspan="2" align="left" ><div align="left">
          <?=$rowCLI['fact_ciudad'];?>
        </div></td>
      </tr>
      <tr>
        <td><div align="right"><strong>Estado:</strong></div></td>
        <td colspan="2" align="left" ><div align="left">
          <?= $rowCLI['fact_estado'];?>        
        </div></td>
      </tr>
      <tr>
        <td><div align="right"><strong>C.P.:</strong></div></td>
        <td colspan="2" align="left"><div align="left">
          <?=$rowCLI['fact_cp'];?>
        </div></td>
      </tr>
      <tr>
        <td><div align="right"><strong>Tel&eacute;fono:</strong></div></td>
        <td colspan="2" align="left"><div align="left">
          <?=$rowCLI['fact_telefono'];?>
        </div></td>
      </tr>
      <tr>
        <td><div align="right"><strong>Raz&oacute;n Social:</strong></div></td>
        <td colspan="2" align="left" ><div align="left">
          <?=$rowCLI['razon_social'];?>
        </div></td>
      </tr>
      <tr>
        <td><div align="right"><strong>R.F.C.:</strong></div></td>
        <td colspan="2" align="left"><div align="left">
          <?=$rowCLI['rfc'];?>
        </div></td>
      </tr>
	  <? if ($total_invitados) { ?>
      <tr>
        <td colspan="3" >&nbsp;</td>
      </tr>
      <tr>
        <td colspan="3" ><div align="center"><strong>Familiares y Amigos Invitados</strong></div></td>
      </tr>
      <tr>
        <td><div align="left"><strong>Nombre</strong></div></td>
        <td align="left"><div align="left"><strong>Correo</strong></div></td>
        <td align="left"><strong>Vigencia</strong></td>
      </tr>
      <? 
			$resultadoINVS = mysql_query("SELECT nombre, email, vigencia_invitacion FROM cliente WHERE invitado AND invitado_por = $cliente",$conexion);
			while ($rowINVS = mysql_fetch_array($resultadoINVS)) { 
	  ?>
      <tr>
        <td><div align="left"><?=$rowINVS['nombre'];?></div></td>
        <td align="left"><div align="left"><?=$rowINVS['email'];?></div></td>
        <td align="left"><?=fecha($rowINVS['vigencia_invitacion']);?></td>
      </tr>
      <? }  // while
	  }  // if total invs ?>
    </table>
</body>
</html>
