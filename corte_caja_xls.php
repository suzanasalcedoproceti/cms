<?php
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Content-type: application/vnd.ms-excel");  
header("Content-Disposition: attachment; filename=WP Corte de Caja.xls"); 

	date_default_timezone_set("America/Mexico_City");

    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	include('lib.php');
	$modulo=13;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}

	include('../conexion.php');
	
	$tienda = $_POST['tienda']+0;
	$fecha = $_POST['fecha'];
	$folio = $_POST['folio'];
	$folio_oc = $_POST['folio_oc'];
	$folio_tc = $_POST['folio_tc'];
	$folio_td = $_POST['folio_td'];
	$tipo = $_POST['tipo'];
	$buscar = $_POST['buscar']+0;
	$ord = $_POST['ord'];
	$empresa = $_POST['empresa'];
	$vendedor = $_POST['vendedor'];

	
    $condicion = " WHERE pedido.origen = 'pos' ";

	if (!empty($fecha)) {
		$fecha_desde = convierte_fecha(substr($fecha,0,10));
		$fecha_hasta = convierte_fecha(substr($fecha,13,10));
		$condicion .= " AND pedido.fecha BETWEEN '$fecha_desde' AND '$fecha_hasta' ";
	 }

	 if ($tipo != '')
		$condicion .= " AND pedido.tipo_pedido = '$tipo' ";

	 if ($empresa) 
		$condicion .= " AND pedido.empresa = $empresa ";

	 if ($tienda > 0) 
		$condicion .= " AND pedido.tienda = $tienda";
	
	 if ($vendedor)
		$condicion .= " AND pedido.vendedor = $vendedor ";

	 if ($folio) 
		$condicion .= " AND pedido.folio = $folio ";
								
	 if ($folio_oc) 
		$condicion .= " AND pedido.fdp_credito_nomina_folio LIKE '%".trim($folio_oc)."%' ";

	 if ($folio_tc) 
		$condicion .= " AND pedido.fdp_tdc_folio LIKE '%".trim($folio_tc)."%' ";

	 if ($folio_td) 
		$condicion .= " AND pedido.fdp_tdd_folio LIKE '%".trim($folio_td)."%' ";
		
	//  $condicion .= " AND pedido.estatus = 1 ";

$html = '	
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Punto de Venta</title>
<style type="text/css">
<!--
body {
	background-color: #FFFFFF;
	font-family:Verdana, Arial, Helvetica, sans-serif;
	font-size:12px;
	
}
@page { margin: 20px 20px; }
body { margin: 0px 20px; }
-->
</style>
</head>

<body>
 <table width="100%" border="0" align="center" cellpadding="2" cellspacing="3">
		 <tr class="texto">
	         <td colspan="2" align="center"></td>
	         <td colspan="16" align="center" valign="middle">Reporte de Corte de Caja</td>
	         <td colspan="2" align="right"></td>
		  </tr>
          <tr class="texto" bgcolor="#e5e5e2">
            <td><b>Fecha</b></td>
            <td><strong>Empresa</strong></td>
            <td><strong>Vendedor</strong></td>
            <td><strong>Pedido POS</strong></td>
            <td><strong>Estatus</strong></td>
            <td><strong>OC</strong></td>
            <td><strong>Folio OC</strong></td>
            <td><strong>Tarjeta de D&eacute;bito</strong></td>
            <td align="center"><strong>Folio TD</strong></td>
            <td align="center"><strong>Tarjeta de Crédito</strong></td>
            <td align="center"><strong>Folio TC</strong></td>
            <td align="center"><strong>Cheque</strong></td>
            <td align="center"><strong>Folio Cheque</strong></td>
            <td align="center"><strong>Dep&oacute;sito Directo</strong></td>
            <td align="center"><strong>Efectivo</strong></td>
            <td align="center"><strong>Puntos</strong></td>
            <td align="center"><strong>Puntos Flex</strong></td>
            <td align="center"><strong>Puntos PEP</strong></td>
            <td align="center"><strong>Total</strong></td>
          </tr>';

			 $query = "SELECT pedido.*, empresa.nombre AS nombre_empresa, usuario_tienda.login AS nombre_vendedor FROM pedido
			 			LEFT JOIN empresa ON pedido.empresa = empresa.clave 
						LEFT JOIN usuario_tienda ON pedido.vendedor = usuario_tienda.clave
						$condicion $orden";
             $resultado= mysql_query($query,$conexion);
			 $odd = false;
			 $gran_total = 0;
             while ($row = mysql_fetch_array($resultado)){ 

				$total = $row['fdp_credito_nomina'] + $row['fdp_tdd']+ $row['fdp_tdc'] + $row['fdp_cheque'] + $row['fdp_deposito'] + 
							 $row['fdp_efectivo'] + $row['fdp_puntos'] + $row['fdp_puntos_flex'] +$row['fdp_puntos_pep'];
				
             	if ($row['estatus']==1 && ($row['tipo_pedido']=='V' || $row['tipo_pedido']=='S')) {
					$pedido = $row['folio'];
					$total_credito_nomina += $row['fdp_credito_nomina'];
					$total_tdd += $row['fdp_tdd'];
					$total_tdc += $row['fdp_tdc'];
					$total_cheque += $row['fdp_cheque'];
					$total_deposito += $row['fdp_deposito'];
					$total_efectivo += $row['fdp_efectivo'];
					$total_puntos += $row['fdp_puntos'];
					$total_puntos_flex += $row['fdp_puntos_flex'];
					$total_puntos_pep += $row['fdp_puntos_pep'];
					$gran_total += $total;
				}

          $html .= '
			<tr class="texto" valign="top" ';
			if ($odd) $html .= ' bgcolor="#F9F9F9"'; else $html .= ' bgcolor="#FFFFFF"';
		   $html .= '>
            <td >'.fechamy2mx($row['fecha']).'</td>
            <td >'.$row['nombre_empresa'].'</td>
            <td >'.$row['nombre_vendedor'].'</td>';
			
			$html .= '
            <td ><div align="center">'.$row['folio'];
			
			switch ($row['tipo_pedido']) {
				case 'V' : break;
				case 'S' : $html .= '-S'; break;
				case 'R' : $html .= '-R'; break;
			}

			
			$html .= '</div></td>
            <td align="left" valign="top" nowrap="nowrap">';
			
			switch ($row['estatus']) {
			
				case '0' : $html .= 'Pendiente'; break;
				case '1' : $html .=  'Pagado'.$pagado_cms; break;
				case '2' : $html .=  'Rechazado'; break;
				case '3' : $html .=  'Revisión TDD'; break;
				case '4' : $html .=  'Revisión CEP'; break;
				case '9' : $html .=  'Cancelado'; break;
			   }
			$html .= '
			 </td>
            <td align="right">'.nocero(number_format($row['fdp_credito_nomina'],2)).'</td>
            <td>'.$row['fdp_credito_nomina_folio'].'</td>
            <td align="right">'.nocero(number_format($row['fdp_tdd'],2)).'</td>
            <td>'.$row['fdp_tdd_folio'].'</td>
            <td align="right">'.nocero(number_format($row['fdp_tdc'],2)).'</td>
            <td>'.$row['fdp_tdc_folio'].'</td>
            <td align="right">'.nocero(number_format($row['fdp_cheque'],2)).'</td>
            <td>'.$row['fdp_cheque_folio'].'</td>
            <td align="right">'.nocero(number_format($row['fdp_deposito'],2)).'</td>
            <td align="right">'.nocero(number_format($row['fdp_efectivo'],2)).'</td>
            <td align="right">'.nocero(number_format($row['fdp_puntos'],2)).'</td>
            <td align="right">'.nocero(number_format($row['fdp_puntos_flex'],2)).'</td>
            <td align="right">'.nocero(number_format($row['fdp_puntos_pep'],2)).'</td>
            <td align="right">'.number_format($total,2).'</td>
          </tr>';

 		  } // while
			 
		  $html .= '

		  <tr class="texto" valign="top" bgcolor="#e5e5e2">
            <td colspan="5" align="left"><strong>Totales</strong></td>
            <td align="right">'.nocero(number_format($total_credito_nomina,2)).'</td>
            <td></td>
            <td align="right">'.nocero(number_format($total_tdd,2)).'</td>
            <td>&nbsp;</td>
            <td align="right">'.nocero(number_format($total_tdc,2)).'</td>
            <td>&nbsp;</td>
            <td align="right">'.nocero(number_format($total_cheque,2)).'</td>
            <td>&nbsp;</td>
            <td align="right">'.nocero(number_format($total_deposito,2)).'</td>
            <td align="right">'.nocero(number_format($total_efectivo,2)).'</td>
            <td align="right">'.nocero(number_format($total_puntos,2)).'</td>
            <td align="right">'.nocero(number_format($total_puntos_flex,2)).'</td>
            <td align="right">'.nocero(number_format($total_puntos_pep,2)).'</td>
            <td align="right">'.number_format($gran_total,2).'</td>
          </tr>


        </table>

</body>
</html>';

echo $html; 

//return;


?>