<?php
// Control de Cambios
// 3 Oct 2016:B+  Actualizar código de payment terms (plazo) en Dashboard, y agregar filtro por folio de Orden de Compra
	include('ajax_ini_dashboard.php'); 
    if (!include('ctrl_acceso.php')) return;
   	include('funciones.php');

	$modulo=24;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}

	include('../conexion.php');
	include('lib.php');

	$buscando = $_POST['buscando']; 	
	$estatus_pedido = $_POST['estatus_pedido'];
	$estatus_entrega = $_POST['estatus_entrega'];
	$tienda = $_POST['tienda']; 	
	$fechas = $_POST['fechas']; 	
	$fecha_fac = $_POST['fecha_fac'];


	if (!$fechas && !$fecha_fac) {
		$fechas = '01/'.date("m/Y").' - '.date("d/m/Y");
	}
	$desde = convierte_fecha(substr($_POST['fechas'],0,10));
	$hasta = convierte_fecha(substr($_POST['fechas'],13,10));
	$desde_fac = convierte_fecha(substr($_POST['fecha_fac'],0,10));
	$hasta_fac = convierte_fecha(substr($_POST['fecha_fac'],13,10));

	$texto = $_POST['texto'];
	$folio_odc = $_POST['folio_odc'];

	$tipo_pedido = $_POST['tipo_pedido'];
	$empresa = $_POST['empresa'];
	$po_number = $_POST['po_number'];
	$semaforo = $_POST['semaforo'];

    $accion = $_POST['accion'];
    $ver = $_POST['ver'];
    $numpag = $_POST['numpag'];
    $ord = $_POST['ord'];
    $estado = $_POST['estado'];
    if (empty($ver)) $ver='100';
    if (empty($numpag)) $numpag='1';


	$filtros=0;
	$criterios='';

	// llenar array con filtros posibles
	include("dashboard_filtros.php");

	if (!empty($fechas) || !empty($fecha_fac)) {
		$filtros++;
		if ($fechas) 
			$criterios.='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Fecha pedido: <strong>'.$fechas.'</strong><br />';
		else
			$criterios.='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Fecha factura: <strong>'.$fecha_fac.'</strong><br />';
	}

	if ($filtros==0) $buscando=0;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
<title>Panel de Control</title>
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<link href="css/menu.css" rel="stylesheet" type="text/css" />
<!--[if lte IE 6]><link rel="stylesheet" href="css/menu_ie6.css" /><![endif]-->
<link href="css/styles_dashboard.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/menu.js"></script>
<script src="js/jquery.js" type="text/javascript" language="javascript1.2"></script>
<script src="js/functions.js" type="text/javascript" language="javascript1.2"></script>
<link href="css/jquery.datepick.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.datepick.js" type="text/javascript" language="javascript1.2"></script>
<script src="js/jquery.datepick-es.js" type="text/javascript" language="javascript1.2"></script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>

<script src="js/jquery.fixedheadertable.min.js"></script>

<script type="text/javascript" src="js/engine_dashboard.js"></script>

<style type="text/css">
<!--
-->
</style>
<?php $xajax->printJavascript("xajax/"); ?>
</head>

<script type="text/javascript">
  function ir(form, Pag) {
    form.numpag.value = Pag;
	form.buscando.value = 1;
	form.target = '_self';
    form.action='dashboardd.php';
    form.submit();
  }
  function actualiza(id) {
   continuar = window.confirm("Deseas actualizar el registro del dashboard?");
   if (!continuar) {
	 return;
   }
	document.forma_dashboard.buscando.value = 1;
	document.forma_dashboard.accion.value = 'actualiza';
	document.forma_dashboard.id.value = id;
	document.forma_dashboard.target = '_self';
    document.forma_dashboard.action='dashboardd.php';
    document.forma_dashboard.submit();
  }
  function exportar() {
  	document.forma_dashboard.target = '_blank';
    document.forma_dashboard.action='dashboard_csv.php';
    document.forma_dashboard.submit();
	document.forma_dashboard.target = '_self';
  }
  
  function SetAllCheckBoxes(FormName, FieldName, CheckValue) {
	if(!document.forms[FormName])
		return;
	var objCheckBoxes = document.forms[FormName].elements[FieldName];
	if(!objCheckBoxes)
		return;
	var countCheckBoxes = objCheckBoxes.length;
	if(!countCheckBoxes)
		objCheckBoxes.checked = CheckValue;
	else
		// set the check value for all check boxes
		for(var i = 0; i < countCheckBoxes; i++) {
			objCheckBoxes[i].checked = CheckValue;
		}
  }    
</script>
<body>
<div id="container"  style="width:1300px">
	<? $tit='Dashboard'; include('top.php'); ?>
<form id="forma_dashboard" name="forma_dashboard" method="post" action="">
     <input type="hidden" name="accion" id="accion" />
     <input type="hidden" name="id" id="id" />
     <input type="hidden" name="buscando" id="buscando" />
	<div class="main" >
    	<div class="filtros">
        	<h1>Filtros</h1>
            <? if ($filtros==0) echo '<div class="nofiltros">En este momento no hay filtros</div>
			                          <a href="javascript:void();" id="link_filtro">Agregar un filtro</a>';
			   else {
			   		echo '<div class="detalle">'.$criterios.'</div>
			              <a href="javascript:void();" id="link_filtro">Cambia filtros </a>';
						  
					echo  '<input name="Button" type="button" class="boton" onclick="javascript:ir(document.forma_dashboard,1)" value="Buscar" style="float:right; margin-right:20px"/>';
			  }
			  if ($buscando) {
					echo '<button style="float:right; margin-right:3px" onclick="javascript:exportar();" type="button">Exportar XLS</button>';
					echo '<a href="javascript:void();" id="link_graficas">Ver gráficas</a>';
			  } else echo '<input type="hidden" name="numpag" id="numpag" />';
			 
			?>
									
      </div>
      <div id="seleccionar_filtros">
      	<h1>Seleccionar criterios de filtrado: <a href="javascript:void();" id="cerrar_filtro" class="cerrar_fil">Cerrar</a></h1>
          <div id="grupo_filtros">
     	    <table width="95%" border="0" cellspacing="0" cellpadding="3">
              <tr>
                <td><div align="right">Fecha Pedido:</div></td>
                <td><input name="fechas" type="text" class="fLeft" id="fechas" value="<?=$fechas;?>" readonly="readonly" size="25" /></td>
              </tr>
              <tr>
                <td><div align="right">Fecha Factura:</div></td>
                <td><input name="fecha_fac" type="text" class="fLeft" id="fecha_fac" value="<?=$fecha_fac;?>" readonly="readonly" size="25" /></td>
              </tr>
              <tr>
                <td><div align="right">Texto:</div></td>
                <td><input name="texto" type="text" class="fLeft" id="texto" value="<?=$texto;?>" size="25" /><div style="padding:3px 0 0 10px">&nbsp; Folio SAP, Folio POS, Guia</div></td>
              </tr>
              <tr>
                <td><div align="right">Folio OC:</div></td>
                <td><input name="folio_odc" type="text" class="fLeft" id="folio_odc" value="<?=$folio_odc;?>" size="15" /></td>
              </tr>
              <tr>
                <td><div align="right">Tipo de Pedido: </div></td>
                <td>
                <div class="filtro" style="height:60px">
				  <label class="blanks"><input type="checkbox" id="x" name="xtp" value="1" onclick="SetAllCheckBoxes('forma_dashboard', 'tipo_pedido', !this.checked);" />Blanks</label> 
                    <?
						$filtro_tipo_pedido = '';
						for ($iel = 1; $iel<=count($arr_tipo_pedido); $iel++) { 
						  echo '<label><input type="checkbox" id="tipo_pedido" name="tipo_pedido_'.$arr_tipo_pedido[$iel]['clave'].'" value="1" ';
						  if ($_POST['tipo_pedido_'.$arr_tipo_pedido[$iel]['clave']]==1) {
						  	echo 'checked="checked" ';
							$filtro_tipo_pedido .= "'".$arr_tipo_pedido[$iel]['clave']."', ";
						  }
						  echo '/>'.$arr_tipo_pedido[$iel]['nombre'].'</label>';
						}
						$filtro_tipo_pedido = substr($filtro_tipo_pedido,0,-2);
					?>
                </div>
                  </td>
              </tr>

              <tr>
                <td><div align="right">Empresa: </div></td>
                <td>
                  <div class="filtro"> 
                     <label class="blanks"><input type="checkbox" id="x" name="xemp" value="1" onclick="SetAllCheckBoxes('forma_dashboard', 'empresa', !this.checked);" />Blanks</label>
                    <?
						$filtro_empresa = '';
						for ($iel = 1; $iel<=count($arr_empresa); $iel++) {
						  echo '<label><input type="checkbox" id="empresa" name="empresa_'.$arr_empresa[$iel]['clave'].'" value="1" ';
						  if ($_POST['empresa_'.$arr_empresa[$iel]['clave']]==1) {
						  	echo 'checked="checked" ';
							$filtro_empresa .= "'".$arr_empresa[$iel]['clave']."', ";
						  }
						  echo '/>'.$arr_empresa[$iel]['nombre'].'</label>';
						}
						$filtro_empresa = substr($filtro_empresa,0,-2);
					  ?>
                  </div>
               </td>
              </tr>
              <!--tr>
                <td><div align="right">PO Number: </div></td>
                <td>
                  <div class="filtro"> 
                     <label class="blanks"><input type="checkbox" id="x" name="xpo" value="1" onclick="SetAllCheckBoxes('forma_dashboard', 'po_number', !this.checked);" />Blanks</label>
                    <?
						$resPO = mysql_query("SELECT DISTINCT po_number FROM dashboard WHERE po_number != '' ORDER BY po_number",$conexion);
						while ($rowPO = mysql_fetch_array($resPO)) {
						  echo '<label><input type="checkbox" id="po_number" name="po_number_'.$rowPO['po_number'].'" value="1" ';
						  if ($_POST['po_number_'.$rowPO['po_number']]==1) echo 'checked="checked" ';
						  echo '/>'.$rowPO['po_number'].'</label>';
						}
					  ?>
                  </div>
                
                </td>
              </tr-->
              <tr>
                <td><div align="right">Tienda: </div></td>
                <td>
                  <div class="filtro"> 
                     <label class="blanks"><input type="checkbox" id="x" name="xem" value="1" onclick="SetAllCheckBoxes('forma_dashboard', 'tienda', !this.checked);" />Blanks</label>
                    <?  
						$filtro_tienda = '';
						for ($iel = 1; $iel<=count($arr_tienda); $iel++) {
						  echo '<label><input type="checkbox" id="tienda" name="tienda_'.$arr_tienda[$iel]['clave'].'" value="1" ';
						  if ($_POST['tienda_'.$arr_tienda[$iel]['clave']]==1) {
						  	echo 'checked="checked" ';
							$filtro_tienda .= "'".$arr_tienda[$iel]['clave']."', ";
						  }
						  echo '/>'.$arr_tienda[$iel]['nombre'].'</label>';
						}	
						$filtro_tienda = substr($filtro_tienda,0,-2);		
					  ?>
                  </div>
                
                </td>
              </tr>              

              <tr>
                <td><div align="right">Vendedor: </div></td>
                <td>
                  <div class="filtro"> 
                     <label class="blanks"><input type="checkbox" id="x" name="xven" value="1" onclick="SetAllCheckBoxes('forma_dashboard', 'vendedor', !this.checked);" />Blanks</label>
                    <?
						$filtro_vendedor = '';
						for ($iel = 1; $iel<=count($arr_vendedor); $iel++) {
						  echo '<label><input type="checkbox" id="vendedor" name="vendedor_'.$arr_vendedor[$iel]['clave'].'" value="1" ';
						  if ($_POST['vendedor_'.$arr_vendedor[$iel]['clave']]==1) {
						  	echo 'checked="checked" ';
							$filtro_vendedor .= "'".$arr_vendedor[$iel]['clave']."', ";
						  }
						  echo '/>'.$arr_vendedor[$iel]['nombre'].'</label>';
						}
						$filtro_vendedor = substr($filtro_vendedor,0,-2);
					  ?>
                  </div>
                
                </td>
              </tr> 
              <tr>
                <td><div align="right">Tipo de Cliente: </div></td>
                <td>
                <div class="filtro">
				  <label class="blanks"><input type="checkbox" id="x" name="xtc" value="1" onclick="SetAllCheckBoxes('forma_dashboard', 'tipo_cliente', !this.checked);" />Blanks</label> 
                    <?
						$filtro_tipo_cliente = '';
						for ($iel = 1; $iel<=count($arr_tipo_cliente); $iel++) { 
						  echo '<label><input type="checkbox" id="tipo_cliente" name="tipo_cliente_'.$arr_tipo_cliente[$iel]['clave'].'" value="1" ';
						  if ($_POST['tipo_cliente_'.$arr_tipo_cliente[$iel]['clave']]==1) {
						  	echo 'checked="checked" ';
							$filtro_tipo_cliente .= "'".$arr_tipo_cliente[$iel]['clave']."', ";
						  }
						  echo '/>'.$arr_tipo_cliente[$iel]['nombre'].'</label>';
						}
						$filtro_tipo_cliente = substr($filtro_tipo_cliente,0,-2);
					?>                  
                </div>
                  </td>
              </tr>              

              <tr>
                <td><div align="right">Proveedor Log: </div></td>
                <td>
                  <div class="filtro"> 
                     <label class="blanks"><input type="checkbox" id="x" name="xpo" value="1" onclick="SetAllCheckBoxes('forma_dashboard', 'proveedor', !this.checked);" />Blanks</label>
                    <?
						$filtro_proveedor = '';
						for ($iel = 1; $iel<=count($arr_proveedor); $iel++) { 
						  echo '<label><input type="checkbox" id="proveedor" name="proveedor_'.$arr_proveedor[$iel]['clave'].'" value="1" ';
						  if ($_POST['proveedor_'.$arr_proveedor[$iel]['clave']]==1) {
						  	echo 'checked="checked" ';
							$filtro_proveedor .= "'".$arr_proveedor[$iel]['nombre']."', ";
						  }
						  echo '/>'.$arr_proveedor[$iel]['nombre'].'</label>';
						}
						$filtro_proveedor = substr($filtro_proveedor,0,-2);
					  ?>
                  </div>
                
                </td>
              </tr>

              <tr>
                <td><div align="right">Estatus Pedido:</div></td>
                <td>
                <div class="filtro" style="height:60px">
				  <label class="blanks"><input type="checkbox" id="x" name="xtc" value="1" onclick="SetAllCheckBoxes('forma_dashboard', 'estatus_pedido', !this.checked);" />Blanks</label> 
                    <?
						$filtro_estatus_pedido = ' AND ('; // no mover espacios 
						for ($iel = 1; $iel<=count($arr_estatus_pedido); $iel++) { 
						  echo '<label><input type="checkbox" id="estatus_pedido" name="estatus_pedido_'.$arr_estatus_pedido[$iel]['clave'].'" value="1" ';
						  if ($_POST['estatus_pedido_'.$arr_estatus_pedido[$iel]['clave']]==1) {
						  	echo 'checked="checked" ';
							if ($arr_estatus_pedido[$iel]['clave'] == 'Completo')   $filtro_estatus_pedido .= " avance_pedido = 1 OR "; // no mover espacios
							if ($arr_estatus_pedido[$iel]['clave'] == 'Incompleto') $filtro_estatus_pedido .= " avance_pedido < 1 OR ";
							if ($arr_estatus_pedido[$iel]['clave'] == 'Cancelado')  $filtro_estatus_pedido .= " estatus_material = 'Cancelado' OR ";
						  }
						  echo '/>'.$arr_estatus_pedido[$iel]['nombre'].'</label>';
						}
						$filtro_estatus_pedido = substr($filtro_estatus_pedido,0,-4);
						$filtro_estatus_pedido .= ' )'; // no mover espacios
					?>
                </div>                
				</td>
              </tr>
              
              <tr>
                <td><div align="right">Estatus Entrega: </div></td>
                <td>
                <div class="filtro">
				  <label class="blanks"><input type="checkbox" id="x" name="xee" value="1" onclick="SetAllCheckBoxes('forma_dashboard', 'estatus_entrega', !this.checked);" />Blanks</label> 
                    <?
						$filtro_estatus_entrega = '';
						for ($iel = 1; $iel<=count($arr_estatus_entrega); $iel++) { 
						  echo '<label><input type="checkbox" id="estatus_entrega" name="estatus_entrega_'.$arr_estatus_entrega[$iel]['clave'].'" value="1" ';
						  if ($_POST['estatus_entrega_'.$arr_estatus_entrega[$iel]['clave']]==1) {
						  	echo 'checked="checked" ';
							$filtro_estatus_entrega .= "'".$arr_estatus_entrega[$iel]['clave']."', ";
						  }
						  echo '/>'.$arr_estatus_entrega[$iel]['nombre'].'</label>';
						}
						$filtro_estatus_entrega = substr($filtro_estatus_entrega,0,-2);
					  ?>

                </div>
                  </td>
              </tr>              

              <tr>
                <td><div align="right">Semáforo:</div></td>
                <td>
                <div class="filtro" style="height:60px">
				  <label class="blanks"><input type="checkbox" id="x" name="xtc" value="1" onclick="SetAllCheckBoxes('forma_dashboard', 'semaforo', !this.checked);" />Blanks</label> 
                    <?
						$filtro_semaforo = '';
						for ($iel = 1; $iel<=count($arr_semaforo); $iel++) { 
						  echo '<label><input type="checkbox" id="semaforo" name="semaforo_'.$arr_semaforo[$iel]['clave'].'" value="1" ';
						  if ($_POST['semaforo_'.$arr_semaforo[$iel]['clave']]==1) {
						  	echo 'checked="checked" ';
							$filtro_semaforo .= "'".$arr_semaforo[$iel]['clave']."', ";
						  }
						  echo '/>'.$arr_semaforo[$iel]['nombre'].'</label>';
						}
						$filtro_semaforo = substr($filtro_semaforo,0,-2);
					?>
                </div>                
				</td>
              </tr>

              </table>
          </div>
          <div align="center">
          <br />
           
           <input type="submit" name="button" id="button" value="Aplicar filtros" class="boton" />
         </div>
   	  </div>
    
      <? if ($buscando) { 
	  
	  		   $condicion = " WHERE 1  ";

			   if ($fechas) {
			   		$condicion .= " AND fecha_pedido BETWEEN '$desde' AND '$hasta' ";
			   }
			   if ($fecha_fac) {
			   		$condicion .= " AND fecha_factura BETWEEN '$desde_fac' AND '$hasta_fac' ";
			   }
			   if ($texto) {
			   		$condicion .= " AND (folio_pos LIKE '%$texto%' OR folio_sap LIKE '%$texto%' OR guia LIKE '%$texto%') ";
			   }
			   if ($folio_odc) {
			   		$condicion .= " AND folio_odc = '$folio_odc'";
			   }
			   if ($filtro_tipo_pedido) {
			   		$condicion .= " AND tipo_pedido IN ( ".$filtro_tipo_pedido." ) ";
			   }
			   if ($filtro_empresa) {
			   		$condicion .= " AND empresa IN ( ".$filtro_empresa." ) ";
			   }
			   if ($filtro_tienda) {
			   		$condicion .= " AND tienda IN ( ".$filtro_tienda." ) ";
			   }
			   if ($filtro_vendedor) {
			   		$condicion .= " AND vendedor IN ( ".$filtro_vendedor." ) ";
			   }
			   if ($filtro_tipo_cliente) {
			   		$condicion .= " AND tipo_cliente IN ( ".$filtro_tipo_cliente." ) ";
			   }
			   if ($filtro_proveedor) {
			   		$condicion .= " AND proveedor IN ( ".$filtro_proveedor." ) ";
			   }
			   /*if ($po_number != 'x' && $po_number != '') {
			   		$condicion .= " AND po_number = '$po_number' ";
			   }*/
			   if ($filtro_estatus_pedido != ' A )') { // ver condicion en filtro
			   		$condicion .= $filtro_estatus_pedido;
			   }
			   if ($filtro_estatus_entrega) {
			   		$condicion .= " AND estatus_entrega IN ( ".$filtro_estatus_entrega." ) ";
			   }
			   if ($filtro_semaforo) {
			   		$condicion .= " AND semaforo IN ( ".$filtro_semaforo." ) ";
			   }
			   
			   
			   // obtener datos para gráficas
//				$condicion = 'WHERE folio_sap = 162568277 ';

//			   $_SESSION['ss_condicion_dashboard'] = " WHERE 1 AND id > 100"; //$condicion;
			   $_SESSION['ss_condicion_dashboard'] = $condicion;
			   

			   $resultadotot= mysql_query("SELECT 1 FROM dashboard $condicion",$conexion);
			   $totres = mysql_num_rows ($resultadotot);
			   $totpags = ceil($totres/$ver);
			   if ($totres==0)
				  $numpag = 0;
		
		
	  ?>
		  <textarea style="width:5px; height:5px; background-color:#CCCCCC; <? if ($_SESSION['usr_valido']!=7) echo 'visibility:hidden';?>" >
          <?
		  	// se movió a esta parte para imprimir el log en el textarea..
			if ($accion == 'actualiza') {
				$id = $_POST['id'];
				require_once("lib_dashboard.php");
				actualiza_reg_dash($id,1); // parámetros: id del dashboard, verbose
				actualiza_reg_dash_logistica($id,1); // parámetros: id del dashboard, verbose
				
				// obtener folio_pedido, para calcular porcentaje
				$query = "SELECT folio_pedido FROM dashboard WHERE id = $id";
				$resultadoAVG = mysql_query($query);
				$rowAVG = mysql_fetch_array($resultadoAVG);

				if ($rowAVG['folio_pedido']>0)
					actualiza_avg_avance($rowAVG['folio_pedido'],1); // parámetros: folio pedido POS, verbose
		
		
			}
		  
		  ?>
          </textarea>

		<? include("dashboard_grafica.php"); ?>
		      

            <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
              
              <tr>
                <td bgcolor="#BBBBBB">Total de registros en la lista: <strong><?=$totres;?></strong></td>
                <td align="right" bgcolor="#BBBBBB">
                    <?


                     $regini = ($numpag * $ver) - $ver;
					 if ($regini<0) $regini=0;

                     // poner flechitas anterior, primero, &uacute;ltimo, etc.
                     if ($numpag<=1) {
                         echo '<img src="images/primera_off.gif" width="16" height="11" align="absmiddle">&nbsp;';
                         echo '<img src="images/anterior_off.gif" width="13" height="11" align="absmiddle">';
                     } else {
                         echo '<a href="javascript:ir(document.forma_dashboard,1);"><img src="images/primera_on.gif" border="0" width="16" height="11" align="absmiddle" alt="Primera p&aacute;gina"></a>&nbsp;';
                         echo '<a href="javascript:ir(document.forma_dashboard,'.($numpag-1).');"><img src="images/anterior_on.gif" border="0" width="13" height="11" align="absmiddle" alt="P&aacute;gina anterior"></a>';
                     }
                     echo '&nbsp;&nbsp;<span class="texto">';
                     echo "P&aacute;gina ";
					 echo '<input type="text" name="pagina" value="'.$numpag.'" size="2" onchange="javascript:ir(document.forma_dashboard,this.value);" style="text-align:center"/>';
					 echo " de ".$totpags;
                     echo "&nbsp;</span>&nbsp;";
                     if ($numpag>=$totpags) {
                         echo '<img src="images/siguiente_off.gif" width="13" height="11" align="absmiddle">&nbsp;';
                         echo '<img src="images/ultima_off.gif" width="16" height="11" align="absmiddle">';
                     } else {
                         echo '<a href="javascript:ir(document.forma_dashboard,'.($numpag+1).');"><img src="images/siguiente_on.gif" border="0" width="13" height="11" align="absmiddle" alt="Siguiente p&aacute;gina"></a>&nbsp;';
                         echo '<a href="javascript:ir(document.forma_dashboard,'.$totpags.');"><img src="images/ultima_on.gif" border="0" width="16" height="11" align="absmiddle" alt="&Uacute;ltima p&aacute;gina"></a>';
                     }
              ?> </td>
              </tr>
            </table>
            <input name="ord" type="hidden" id="ord" value="<?= $ord; ?>" />
            <input name="numpag" type="hidden" id="numpag" value="<?= $numpag; ?>" />
            
            <div class="container">
    		<div class="height600">
              <table width="100%" border="0" align="center" cellpadding="2" cellspacing="1" class="fancyTable" id="res">
                <thead>
                  <tr>
                    <th nowrap="nowrap">Folio POS</th>
                    <th nowrap="nowrap">Fecha<br />
                      Pedido</th>
                    <th nowrap="nowrap">Tipo</th>
                    <th nowrap="nowrap">Tienda</th>
                    <th nowrap="nowrap">Vendedor</th>
                    <th nowrap="nowrap">PO Number</th>
                    <th nowrap="nowrap">Cliente</th>
                    <th nowrap="nowrap">Tipo<br />
                      Cliente</th>
                    <th align="center">#Empleado</th>
                    <th align="center">Empresa</th>
                    <th align="center">#Empresa</th>
                    <th nowrap="nowrap">Material</th>
                    <th align="center">Marca</th>
                    <th align="center">Categor&iacute;a</th>
                    <th align="center">Subcategor&iacute;a</th>
                    <th align="center">Vol Rebate</th>
                    <th align="center">Lista<br />
                      Precios</th>
                    <th align="center">Precio<br />
                      Unitario</th>
                    <th align="center">% Descuento</th>
                    <th align="center">Descuento</th>
                    <th align="center">Raz&oacute;n Descuento</th>
                    <th align="center">Total Unitario</th>
                    <th align="center">IVA</th>
                    <th align="center">Total</th>
                    <th align="center">Puntos Generados</th>
                    <!--th align="center">Forma Pago</th-->
                    <th align="center">Efectivo</th>
                    <th align="center">D&eacute;bito</th>
                    <th align="center">Cr&eacute;dito</th>
                    <th align="center">ODC</th>
                    <th align="center">Cheque</th>
                    <th align="center">CEP</th>
                    <th align="center">Dep<br />
                      Directo</th>
                    <th align="center">Puntos WP</th>
                    <th align="center">Puntos<br />
                      Flex</th>
                    <th align="center">Puntos<br />
                      PEP</th>
                    <th align="center">GiftCard</th>
                    <th align="center">Sustituto</th>
                    <th align="center">Refacturaci&oacute;n</th>
                    <th align="center">Folio<br />
                      ODC</th>
                    <th align="center">Plazo</th>
                    <th align="center">SKU<br />
                      P&oacute;liza</th>
                    <th align="center">Folio Garant&iacute;a</th>
                    <th align="center">Entrega</th>
                    <th nowrap="nowrap">Hay Costo<br />
                      Entrega</th>
                    <!--th nowrap="nowrap">Total</th-->
                    <th nowrap="nowrap">Estatus <br />
                      Pago</th>
                    <th nowrap="nowrap">Fecha Pago</th>
                    <th nowrap="nowrap">Confirm&oacute;<br />
                      Pago</th>
                    <th nowrap="nowrap">Compromiso<br />
                      Entrega</th>
                    <th nowrap="nowrap">Pedido SAP</th>
                    <th nowrap="nowrap">Fecha <br />
                      Pedido SAP</th>
                    <th nowrap="nowrap">Tipo <br />
                      Pedido</th>
                    <th nowrap="nowrap">Unidades</th>
                    <th nowrap="nowrap">Delivery</th>
                    <th nowrap="nowrap">Fecha<br />
                      Delivery</th>
                    <th nowrap="nowrap">Unidades<br />
                      Delivery</th>
                    <th nowrap="nowrap">Shipment</th>
                    <th nowrap="nowrap">Factura</th>
                    <th nowrap="nowrap">Fecha Factura</th>
                    <th>Delivery Block</th>
                    <th nowrap="nowrap">Cancelaci&oacute;n</th>
                    <th nowrap="nowrap">Cr&eacute;dito</th>
                    <th>Estatus General <br />SAP</th>
                    <th>Fecha Cambio <br />
                      Estatus</th>
                    <th nowrap="nowrap">Planta</th>
                    <th nowrap="nowrap">Storage</th>
                    <th nowrap="nowrap">Sales Org</th>
                    <th nowrap="nowrap">Canal</th>
                    <th nowrap="nowrap">Division</th>
                    <th nowrap="nowrap">Proveedor SAP</th>
                    <th nowrap="nowrap">Proveedor <br />Logística</th>
                    <th nowrap="nowrap">No.Gu&iacute;a</th>
                    <th nowrap="nowrap">Fecha Entrega<br />
                      Promesa</th>
                    <th nowrap="nowrap">Fecha Entrega<br />
                      Final</th>
                    <th nowrap="nowrap">Estatus<br />
                      Entrega</th>
                    <th nowrap="nowrap">Comentarios</th>
                    <th nowrap="nowrap">Estatus<br />
                      Material</th>
                    <th nowrap="nowrap">Estatus<br />
                      Pedido</th>
                    <th nowrap="nowrap">Sem&aacute;foro</th>
                    <th nowrap="nowrap">Periodo<br />
                      Pag-Entr</th>
                    <th nowrap="nowrap">Periodo<br />
                      Fac-Entr</th>
                    <th nowrap="nowrap">Periodo<br />
                      Compr-Entr</th>
                    <th nowrap="nowrap">No Return</th>
                    <th>Folio<br />
                      Devoluci&oacute;n</th>
                    <th nowrap="nowrap">Folio<br />
                      Inconformidad</th>
                    <th nowrap="nowrap">Tipo<br />
                      Inconformidad</th>
                    <th nowrap="nowrap">Fecha<br />
                      Inconformidad</th>
                    <th nowrap="nowrap">Estatus<br />
                      Inconformidad</th>
                    <th nowrap="nowrap">Feedback</th>
                    <th nowrap="nowrap">Acci&oacute;n</th>
                  </tr>
                </thead>
                <tbody>
                  <? 
			  
			  	 $query = "SELECT *
							 FROM dashboard 
							 $condicion
				 			 ORDER BY dashboard.folio_pedido LIMIT $regini,$ver";
			 echo $query;
			  	 $resultado = mysql_query($query);
			     while ($row = mysql_fetch_array($resultado)) {

			  ?>
                  <tr>
                    <td><?=$row['folio_fpedido'];?></td>
                    <td nowrap="nowrap"><?=fecha_dash($row['fecha_pedido'],'novacio');?></td>
                    <td><? switch ($row['tipo_pedido']) {
						case 'V' : echo 'Venta'; break;
						case 'S' : echo 'Sustituci&oacute;n'; break;
						case 'R' : echo 'Refacturaci&oacute;n'; break;
					   }
					?>                    </td>
                    <td><?=$row['nombre_tienda'];?></td>
                    <td><div align="left">
                        <?=$row['nombre_vendedor'];?>
                    </div></td>
                    <td><?=$row['po_number'];?></td>
                    <td><div align="left">
                        <?=$row['nombre_cliente'];?>
                    </div></td>
                    <td><? switch ($row['tipo_cliente']) {
							case 'E' : echo 'Empleado'; break;
							case 'I' : echo 'Invitado'; break;
							case 'C' : echo 'Corporate'; break;
							case 'A' : echo 'Mercado Abierto'; break;
					   }
				   ?>                    </td>
                    <td><?=$row['numero_empleado'];?></td>
                    <td><?=$row['nombre_empresa'];?></td>
                    <td><?=$row['numero_empresa'];?></td>
                    <td><?=$row['material'];?></td>
                    <td><?=$row['nombre_marca'];?></td>
                    <td><div align="left">
                        <?=$row['nombre_categoria'];?>
                    </div></td>
                    <td><div align="left">
                        <?=$row['nombre_subcategoria'];?>
                    </div></td>
                    <td><?=$row['vol_reb'];?></td>
                    <td><?=$row['lista_precios'];?></td>
                    <td><?=$row['precio_unitario'];?></td>
                    <td><?=nocero($row['pct_descuento']);?>
                        <? if ($row['pct_descuento']>0) echo ' %';?></td>
                    <td><?=nocero($row['descuento']);?></td>
                    <td><?=$row['motivo_descuento'];?></td>
                    <td><?=$row['total_unitario'];?></td>
                    <td><?=$row['iva'];?></td>
                    <td><?=$row['total'];?></td>
                    <td><?=$row['puntos_generados'];?></td>
                    <!--td><?=$row['forma_pago'];?></td-->
                    <td><?=nocero($row['fdp_efectivo']);?></td>
                    <td><?=nocero($row['fdp_tdd']);?></td>
                    <td><?=nocero($row['fdp_tdc']);?></td>
                    <td><?=nocero($row['fdp_odc']);?></td>
                    <td><?=nocero($row['fdp_cheque']);?></td>
                    <td><?=nocero($row['fdp_cep']);?></td>
                    <td><?=nocero($row['fdp_dep']);?></td>
                    <td><?=nocero($row['fdp_puntos']);?></td>
                    <td><?=nocero($row['fdp_puntos_flex']);?></td>
                    <td><?=nocero($row['fdp_puntos_pep']);?></td>
                    <td><?=nocero($row['fdp_gc']);?></td>
                    <td><?=nocero($row['fdp_sustitucion']);?></td>
                    <td><?=nocero($row['fdp_refacturacion']);?></td>
                    <td><?=$row['folio_odc'];?></td>
                    <td><?=$row['plazo_odc'];?></td>
                    <td><?=$row['sku_garantia'];?></td>
                    <td><?=$row['folio_garantia'];?></td>
                    <td><?=$row['entrega'];?></td>
                    <td><? if ($row['costo_entrega']>0) echo 'SI';?></td>
                    <!--td><?=$row['total'];?></td-->
                    <td><?=$row['estatus_pago'];?></td>
                    <td><?=fecha_dash($row['fecha_pago'],'novacio');?></td>
                    <td><?=$row['confirmo_pago'];?></td>
                    <td><?=fecha_dash($row['compromiso_entrega'],'novacio');?></td>
                    <td><?=$row['folio_sap'];?></td>
                    <td><?=$row['order_date'];?></td>
                    <td><?=$row['saty'];?></td>
                    <td><?=$row['cantidad_pedido'];?></td>
                    <td><?=$row['delivery'];?></td>
                    <td><?=$row['delivery_date'];?></td>
                    <td><?=$row['cantidad_delivery'];?></td>
                    <td><?=$row['shipment'];?></td>
                    <td><?=$row['billing_doc'];?></td>
                    <td><?=$row['billing_date'];?></td>
                    <td><?=$row['delivery_block'];?></td>
                    <td><?=nocero($row['reason_rejection']);?></td>
                    <td><?=$row['credit_status'];?></td>
                    <td><?=$row['overall_status'];?></td>
                    <td><?=fecha_dash($row['fecha_cambio_estatus'],'novacio');?></td>
                    <td><?=$row['planta'];?></td>
                    <td><?=$row['store_loc'];?></td>
                    <td><?=$row['organizacion'];?></td>
                    <td><?=$row['canal'];?></td>
                    <td><?=$row['division'];?></td>
                    <td><?=$row['proveedor'];?></td>
                    <td><?=$row['proveedor_logistica'];?></td>
                    <td><?=$row['guia'];?></td>
                    <td><?=fecha_dash($row['fecha_entrega_promesa'],'novacio');?></td>
                    <td><?=fecha_dash($row['fecha_entrega_final'],'novacio');?></td>
                    <td><?=$row['estatus_entrega'];?></td>
                    <td><?=$row['adicionales'];?></td>
                    <td><?=$row['estatus_material'];?></td>
                    <td><? 
						if ($row['avance_pedido']==1) echo '100%';
						elseif ($row['avance_pedido']==0) echo '0%'; 
						else  echo number_format($row['avance_pedido']*100,2).'%';?></td>
								<td <? switch ($row['semaforo']) {
						 case 'amarillo' : echo ' style="background-color:#FFFF99" '; break;
						 case 'rojo' 	 : echo ' style="background-color:#FF0033" '; break;
						 case 'verde'	 : echo ' style="background-color:#66CC33" '; break;
						}
					 ?>></td>
                    <td><?=nocero($row['periodo_ped_entr']);?></td>
                    <td><?=nocero($row['periodo_fac_entr']);?></td>
                    <td><?=nocero($row['periodo_com_entr']);?></td>
                    <td><?=nocero($row['no_return']);?></td>
                    <td><?=$row['folio_devolucion'];?></td>
                    <td><?=nocero($row['folio_inconformidad']);?></td>
                    <td><?=$row['tipo_inconformidad'];?></td>
                    <td><?=fecha_dash($row['fecha_inconformidad'],'novacio');?></td>
                    <td><?=$row['estatus_inconformidad'];?></td>
                    <td><?=$row['feedback'];?></td>
                    <td align="center"><a href="javascript:actualiza(<?=$row['id'];?>);" title="Actualizar pedido" alt="Actualizar pedido"><img src="images/icons/gear_wheel.png" /></a></td>
                  </tr>
                  <? } ?>
                </tbody>
              </table>
            </div>
            </div>
      	  <? } ?>
    
    
  </div>
    </form>
</div>

<script type="text/javascript">

      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {

        data1 = google.visualization.arrayToDataTable([
          ['Tienda', 	'Garzad',	  'Maltod', 'Garcai'],
          ['Apodaca',    60, 10, 19],
          ['Padre Mier', 12, 18, 22],
          ['Ramos Arizpe', 12, 7, 31],
          ['Celaya',	  14, 20, 16],
          ['Guadalajara', 13, 13, 17],
          ['Corp Mty', 	  14, 20, 35],
          ['Corp GDL', 	  17, 12, 22],
        ]);
						
        options1 = {
          title: 'Ventas por Tienda / Vendedor - Pedidos',
        };


        chart = new google.visualization.ColumnChart(document.getElementById('gra1'));
//        chart.draw(data1, options1);
      }

	  
</script>



<div id="debug"></div>
</body>
</html>