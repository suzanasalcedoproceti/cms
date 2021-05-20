<?
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Content-type: application/vnd.ms-excel");  
	header("Content-Disposition: attachment; filename=WP Pedidos.xls");  

    if (!include('ctrl_acceso.php')) return;
	include('../conexion.php');
	include('funciones.php');
	include('lib.php');
	$modulo=13;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
	$empresa = $_POST['empresa'];
	$cliente = $_POST['cliente'];
	$estatus = $_POST['estatus'];
	$origen = $_POST['origen'];
	$folio = $_POST['folio'];
	$forma_pago = $_POST['forma_pago'];
	$puntos = $_POST['puntos'];
	$puntos_flex = $_POST['puntos_flex'];
	$puntos_pep = $_POST['puntos_pep'];
	$fecha = $_POST['fecha'];
	if (!$fecha) $fecha = "01/".date("m/Y")." - ".date("d/m/Y");
	
	if (!isset($estatus)) $estatus = 'x';
	if (!isset($origen)) $origen = 'x';
	 $condicion = "WHERE 1 ";

	 if (!empty($fecha)) {
		$fecha_desde = convierte_fecha(substr($fecha,0,10));
		$fecha_hasta = convierte_fecha(substr($fecha,13,10));
		$condicion .= " AND pedido.fecha BETWEEN '$fecha_desde' AND '$fecha_hasta' ";
	 }
	 if ($cliente>0)
		$condicion .= " AND pedido.cliente=$cliente";

	 if ($empresa>0)
		$condicion .= " AND pedido.empresa=$empresa";

	 if ($estatus != 'x')
		$condicion .= " AND pedido.estatus = '$estatus' ";

	 if ($origen == 'web') 
		$condicion .= " AND pedido.origen = 'web' AND mobile = 0 ";
	 
	 if ($origen == 'pos') 
		$condicion .= " AND pedido.origen = 'pos' ";
	 
	 if ($origen == 'mobile') 
		$condicion .= " AND pedido.origen = 'web' AND mobile = 1 ";

	 if ($origen == 'mas') 
		$condicion .= " AND pedido.empresa = 404";

	 if ($origen == 'proy') 
		$condicion .= " AND pedido.tipo_venta = 'PR'";

		
	 if ($forma_pago=='tdc') $condicion .= " AND pedido.fdp_tdc > 0 ";
	 if ($forma_pago=='tdd') $condicion .= " AND pedido.fdp_tdd > 0  ";
	 if ($forma_pago=='cheque') $condicion .= " AND pedido.fdp_cheque > 0  ";
	 if ($forma_pago=='cep') $condicion .= " AND pedido.fdp_cep > 0  ";
	 if ($forma_pago=='credito') $condicion .= " AND pedido.fdp_credito > 0  ";
	 if ($forma_pago=='dep') $condicion .= " AND pedido.fdp_deposito > 0  ";

	 if ($puntos=='1') $condicion .= " AND pedido.fdp_puntos > 0 ";
	 if ($puntos_flex=='1') $condicion .= " AND pedido.fdp_puntos_flex > 0 ";
	 if ($puntos_pep=='1') $condicion .= " AND pedido.fdp_puntos_pep > 0 ";

	 if ($folio)  
		$condicion .= " AND CONCAT(REPLACE(pedido.fecha,'-',''),pedido.folio,'_L') LIKE '%$folio%' ";
		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Panel de Control</title>
<style type="text/css">
<!--
body {
	background-color: #FFFFFF;
}
-->
</style>
</head>

<body>
        
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="3">
  <tr class="texto" bgcolor="#F4F4F2">
    <td nowrap="nowrap"><b>Folio</b></td>
    <td nowrap="nowrap"><b>Fecha</b></td>
    <td nowrap="nowrap"><div align="center"><strong>Origen</strong></div></td>
    <td nowrap="nowrap"><div align="center"><strong>Tienda</strong></div></td>
    <td nowrap="nowrap"><b>Empresa </b></td>
    <td><strong>Cliente</strong></td>
    <td><strong>Entrega</strong></td>
    <td align="center">EFVO</td>
    <td align="center">TDC</td>
    <td align="center">TDD</td>
    <td align="center">CHE</td>
    <td align="center">CEP</td>
    <td align="center">ODC</td>
    <td align="center"><label title="Cr&eacute;dito Empresas Proyectos">CRE</label></td>
    <td align="center">DEP</td>
    <td align="center">PTS</td>
    <td align="center">FLEX</td>
    <td align="center">PEP</td>
    <td><div align="center">msi</div></td>
    <td><div align="center"><strong>Total</strong></div></td>
    <td><div align="center"><strong>Estatus</strong></div></td>
    <td><div align="center"><strong>Usuario Cancelacion</strong></div></td>
    <td><div align="center"><strong>Motivo Cancelacion</strong></div></td>
    <td><div align="center"><strong>Fecha Cancelacion</strong></div></td>
    <td><div align="right">Detalle:</div></td>
    <td><strong>Modelo</strong></td>
    <td><strong>Cantidad</strong></td>
    <td><strong>Precio</strong></td>
    <td><strong>Subtotal</strong></td>
    <td><strong>Entrega</strong></td>
    <td><strong>Cedis</strong></td>
    <td><strong>Loc</strong></td>
  </tr>
  <?

     $query = "SELECT pedido.*, empresa.nombre AS nombre_empresa, cliente.nombre AS nombre_cliente, tienda.nombre AS nombre_tienda, usuario_tienda.nombre AS usuario_cancelacion_nombre
                FROM pedido 
                LEFT JOIN empresa ON pedido.empresa = empresa.clave
                LEFT JOIN cliente ON pedido.cliente = cliente.clave
                LEFT JOIN tienda  ON pedido.tienda = tienda.clave
                LEFT JOIN usuario_tienda ON usuario_tienda.clave = pedido.usuario_cancelacion
                       $condicion ORDER BY folio DESC";

     $resultado= mysql_query($query,$conexion);
     while ($row = mysql_fetch_array($resultado)){ 
        if ($row['pagado_cms']) $pagado_cms = '*'; else $pagado_cms = '';
		
		$pedido = $row['folio'];		
		$query = "SELECT * FROM detalle_pedido 
					 WHERE pedido = $pedido ORDER BY partida";
		$resultadoDP = mysql_query($query,$conexion);
		$encDP = mysql_num_rows($resultadoDP);
		$det_modelos = ''; $det_cantidad = ''; $det_stotal = '';
		$det_precio = ''; $det_fentr = ''; $det_cedis = ''; $det_loc = '';
		$i = 0;
		while ($rowDP = mysql_fetch_array($resultadoDP)) {
			$i++;
			if ($i < $encDP) $sep = '|'; else $sep = '';
			$det_modelos .= $rowDP['modelo'].$sep; 
			$det_cantidad .= $rowDP['cantidad'].$sep;
			$det_precio .= $rowDP['precio_empleado'].$sep;
			$det_stotal   .= $rowDP['subtotal'].$sep;
			$det_fentr   .= fecha($rowDP['fecha_entrega']).$sep;
			$det_cedis   .= $rowDP['cedis'].$sep;
			$det_loc	 .= $rowDP['loc'].$sep;
		}

  ?>
  <tr class="texto" valign="top" bgcolor="<? if ($color) echo '#FFFFFF'; else echo '#F4F4F2'; ?>">
    <td bgcolor="#FFFFFF"><?= substr(str_replace('-','',$row['fecha']).$row['folio'].'_L',2,50); ?></td>
    <td bgcolor="#FFFFFF"><?= fecha($row['fecha']); ?></td>
    <td bgcolor="#FFFFFF"><div align="center">
      <? echo strtoupper($row['origen']);
         if ($row['mobile']) echo ' mobile';
       ?>
    </div></td>
    <td bgcolor="#FFFFFF"><?= ($row['nombre_tienda']) ? $row['nombre_tienda'] : 'Tienda en Línea';?></td>
    <td bgcolor="#FFFFFF"><?= $row['nombre_empresa']; ?></td>
    <td bgcolor="#FFFFFF"><?= $row['nombre_cliente']; ?></td>
    <td nowrap="nowrap" bgcolor="#FFFFFF"><?=$row['envio_ciudad_nombre'].", ".substr($row['envio_estado'],0,3);?></td>
    <td align="right" bgcolor="#FFFFFF"><?=nocero($row['fdp_efectivo']);?></td>
    <td align="right" bgcolor="#FFFFFF"><? echo nocero($row['fdp_tdc']); ?></td>
    <td align="right" bgcolor="#FFFFFF"><?=nocero($row['fdp_tdd']);?></td>
    <td align="right" bgcolor="#FFFFFF"><?=nocero($row['fdp_cheque']);?></td>
    <td align="right" bgcolor="#FFFFFF"><?=nocero($row['fdp_cep']);?></td>
    <td align="right" nowrap="nowrap" bgcolor="#FFFFFF"><?=nocero($row['fdp_credito_nomina']);?></td>
    <td align="right" nowrap="nowrap" bgcolor="#FFFFFF"><?=nocero($row['fdp_credito']);?></td>
    <td align="right" nowrap="nowrap" bgcolor="#FFFFFF"><?=nocero($row['fdp_deposito']);?></td>
    <td align="right" nowrap="nowrap" bgcolor="#FFFFFF"><?=nocero($row['fdp_puntos']);?></td>
    <td align="right" nowrap="nowrap" bgcolor="#FFFFFF"><?=nocero($row['fdp_puntos_flex']);?></td>
    <td align="right" nowrap="nowrap" bgcolor="#FFFFFF"><?=nocero($row['fdp_puntos_pep']);?></td>
    <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><? if ($row['pago_msi']>0) echo $row['pago_msi']; else echo '&nbsp;'; ?></td>
    <td align="right" nowrap="nowrap" bgcolor="#FFFFFF"><?= number_format($row['total'],2); ?></td>
    <td align="center" nowrap="nowrap" bgcolor="#FFFFFF">
    <? switch ($row['estatus']) {
        case '0' : echo 'Pendiente'; break;
        case '1' : echo 'Pagado'.$pagado_cms; break;
        case '2' : echo 'Rechazado'; break;
        case '9' : echo 'Cancelado'; break;
       }
     ?>	</td>
    <td nowrap="nowrap" bgcolor="#FFFFFF"><?= $row['usuario_cancelacion_nombre']; ?></td>
    <td nowrap="nowrap" bgcolor="#FFFFFF"><?= $row['motivo_cancelacion']; ?></td>
    <td nowrap="nowrap" bgcolor="#FFFFFF"><?= date_format(date_create($row['fecha_cancelacion']),'Y-m-d'); ?></td>
    <td align="center" nowrap="nowrap" bgcolor="#FFFFFF">&nbsp;</td>
    <td nowrap="nowrap" bgcolor="#FFFFFF"><?= $det_modelos; ?></td>    
    <td nowrap="nowrap" bgcolor="#FFFFFF"><?= $det_cantidad; ?></td>
    <td nowrap="nowrap" bgcolor="#FFFFFF"><?= $det_precio; ?></td>
    <td nowrap="nowrap" bgcolor="#FFFFFF"><?= $det_stotal; ?></td>
    <td nowrap="nowrap" bgcolor="#FFFFFF"><?= $det_fentr; ?></td>
    <td nowrap="nowrap" bgcolor="#FFFFFF"><?= $det_cedis; ?></td>
    <td nowrap="nowrap" bgcolor="#FFFFFF"><?= $det_loc; ?></td>
  </tr>
  <?
         } // WHILE
         mysql_close();
      ?>
</table>
</body>
</html>
