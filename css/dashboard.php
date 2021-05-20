<?
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
	if (!$fechas) {
		$fechas = '01/'.date("m/Y").' - '.date("d/m/Y");
	}
	$desde = convierte_fecha(substr($_POST['fechas'],0,10));
	$hasta = convierte_fecha(substr($_POST['fechas'],13,10));
	$fecha_fac = $_POST['fecha_fac'];
	$desde_fac = convierte_fecha(substr($_POST['fecha_fac'],0,10));
	$hasta_fac = convierte_fecha(substr($_POST['fecha_fac'],13,10));

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

	if ($accion == 'actualiza') {
		$id = $_POST['id'];
		require_once("lib_dashboard.php");
		actualiza_reg_dash($id,0);
	}


	$filtros=0;
	$criterios='';

	if (!empty($fechas)) {
		$filtros++;
		$criterios.='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Fecha pedido: <strong>'.$fechas.'</strong><br />';
	}
	if (!empty($fecha_fac)) {
		$filtros++;
		$criterios.='<a href="javascript:void();" id="clean_fecha_fac"><img src="images/borrar.png" width="14" height="15" align="absmiddle" alt="Quitar este filtro" title="Quitar este filtro"></a> Fecha factura: <strong>'.$fecha_fac.'</strong><br />';
	}
/*	
		//////// detectar si se filtra por estatus ////////////////
		$items_inactivos = 0; $items_activos = 0;
		$condicion_filtro = " AND busqueda_hotel.categoria_grupo IN (";
		foreach ($_SESSION['arr_fil']['categorias'] as $categoria) { 
			if ($categoria['activo']) {
				$condicion_filtro .= "'".$categoria['clave']."',";
				$items_activos ++;
			} else
				$items_inactivos ++;
		}
		$condicion_filtro = substr($condicion_filtro,0,-1).")";
		if ($items_inactivos>0 && $items_activos>0) $condicion.=$condicion_filtro;
		elseif ($items_activos==0) $condicion.=" AND 0";
		////////////////////////////////////////////////////////////
*/
//include("_checa_vars.php");	
	if ($tipo_pedido!='x' && $tipo_pedido !=  '') {
		$filtros++;
		$criterios.='<a href="javascript:void();" id="clean_tipo_pedido"><img src="images/borrar.png" width="14" height="15" align="absmiddle" alt="Quitar este filtro" title="Quitar este filtro"></a> Tipo de Pedido: <strong>';
		switch ($tipo_pedido) {
			case 'V' : $criterios .= 'Venta'; break;
			case 'S' : $criterios .= 'Sustitución'; break;
			case 'R' : $criterios .= 'Refacturación'; break;
	    }		
		$criterios.='</strong><br />';
	}
	if ($empresa!='x' && $empresa != '') {
		$filtros++;
		$empresa+=0;
		$criterios.='<a href="javascript:void();" id="clean_empresa"><img src="images/borrar.png" width="14" height="15" align="absmiddle" alt="Quitar este filtro" title="Quitar este filtro"></a> Empresa: <strong>';
		$resultadoEMP = mysql_query("SELECT nombre FROM empresa WHERE clave = $empresa");
		$rowEMP = mysql_fetch_array($resultadoEMP);
		$criterios .= $rowEMP['nombre'];
		$criterios.='</strong><br />';
	}

	if ($po_number!='x' && $po_number != '') {
		$filtros++;
		$criterios.='<a href="javascript:void();" id="clean_po_number"><img src="images/borrar.png" width="14" height="15" align="absmiddle" alt="Quitar este filtro" title="Quitar este filtro"></a> Tienda (PO number): <strong>'.$po_number.'</strong><br />';
	}
	
	if ($estatus_pedido!='x' && $estatus_pedido != '') {
		$filtros++;
		$criterios.='<a href="javascript:void();" id="clean_estatus_pedido"><img src="images/borrar.png" width="14" height="15" align="absmiddle" alt="Quitar este filtro" title="Quitar este filtro"></a> Estatus Pago: <strong>'.$estatus_pedido.'</strong><br />';
	}
	if ($estatus_entrega!='x' && $estatus_entrega!='') {
		$filtros++;
		$criterios.='<a href="javascript:void();" id="clean_estatus_entrega"><img src="images/borrar.png" width="14" height="15" align="absmiddle" alt="Quitar este filtro" title="Quitar este filtro"></a> Estatus Entrega: <strong>'.$estatus_entrega.'</strong><br />';
	}
	if ($semaforo!='x' && $semaforo!='') {
		$filtros++;
		$criterios.='<a href="javascript:void();" id="clean_semaforo"><img src="images/borrar.png" width="14" height="15" align="absmiddle" alt="Quitar este filtro" title="Quitar este filtro"></a> Semáforo: <strong>'.ucfirst($semaforo).'</strong><br />';
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

<script src="js/jquery.fixedheadertable.min.js"></script>

<script type="text/javascript" src="js/engine_dashboard.js"></script>

<style type="text/css">
<!--
-->
</style>
</head>

<script type="text/javascript">
  function ir(form, Pag) {
    form.numpag.value = Pag;
	form.buscando.value = 1;
	form.target = '_self';
    form.action='dashboard.php';
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
    document.forma_dashboard.action='dashboard.php';
    document.forma_dashboard.submit();
  }
  function exportar() {
  	document.forma_dashboard.target = '_blank';
    document.forma_dashboard.action='dashboard_xls.php';
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
	<div class="main" >
    	<div class="filtros">
        	<h1>Filtros</h1>
            <? if ($filtros==0) echo '<div class="nofiltros">En este momento no hay filtros</div>
			                          <a href="javascript:void();" id="link_filtro">Agregar un filtro</a>';
			   else {
			   		echo '<div class="detalle">'.$criterios.'</div>
			              <a href="javascript:void();" id="link_filtro">Modificar filtros</a>';
						  
					echo  '<input name="Button" type="button" class="boton" onclick="javascript:ir(document.forma_dashboard,1)" value="Buscar" style="float:right; margin-right:20px"/>';
			  }
			  if ($buscando) 
					echo '<button style="float:right; margin-right:20px" onclick="javascript:exportar();" type="button">Exportar XLS</button>';
			 
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
                <td><div align="right">Tipo de Venta: </div></td>
                <td>
                <div class="filtro" style="height:60px">
				  <label class="blanks"><input type="checkbox" id="x" name="xtp" value="1" onclick="SetAllCheckBoxes('forma_dashboard', 'tipo_pedido', !this.checked);" />Blanks</label> 
                  <label><input type="checkbox" id="tipo_pedido" name="tipo_pedido_V" value="1" <?php if ($_POST['tipo_pedido_V']==1) echo 'checked="checked" ';?>/>Venta</label>
                  <label><input type="checkbox" id="tipo_pedido" name="tipo_pedido_S" value="1" <?php if ($_POST['tipo_pedido_S']==1) echo 'checked="checked" ';?>/>Sustituto</label>
                  <label><input type="checkbox" id="tipo_pedido" name="tipo_pedido_R" value="1" <?php if ($_POST['tipo_pedido_R']==1) echo 'checked="checked" ';?>/>Refacturación</label>
                </div>
                  </td>
              </tr>

              <tr>
                <td><div align="right">Empresa: </div></td>
                <td>
                  <div class="filtro"> 
                     <label class="blanks"><input type="checkbox" id="x" name="xemp" value="1" onclick="SetAllCheckBoxes('forma_dashboard', 'empresa', !this.checked);" />Blanks</label>
                    <?
						$resEMP = mysql_query("SELECT clave, nombre FROM empresa ORDER BY nombre",$conexion);
						while ($rowEMP = mysql_fetch_array($resEMP)) {
						  echo '<label><input type="checkbox" id="empresa" name="empresa_'.$rowEMP['clave'].'" value="1" ';
						  if ($_POST['empresa_'.$rowEMP['clave']]==1) echo 'checked="checked" ';
						  echo '/>'.$rowEMP['nombre'].'</label>';
						}
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
						$resTIE = mysql_query("SELECT clave, nombre FROM tienda ORDER BY nombre",$conexion);
						while ($rowTIE = mysql_fetch_array($resTIE)) {
						  echo '<label><input type="checkbox" id="tienda" name="tienda_'.$rowTIE['clave'].'" value="1" ';
						  if ($_POST['tienda_'.$rowTIE['clave']]==1) echo 'checked="checked" ';
						  echo '/>'.$rowTIE['nombre'].'</label>';
						}
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
						$resVEN = mysql_query("SELECT usuario_tienda.clave, usuario_tienda.nombre, tienda.login 
												 FROM usuario_tienda 
												 LEFT JOIN tienda ON usuario_tienda.tienda = tienda.clave 
												ORDER BY tienda.login, usuario_tienda.nombre ",$conexion);
						while ($rowVEN = mysql_fetch_array($resVEN)) {
						  echo '<label><input type="checkbox" id="vendedor" name="vendedor_'.$rowVEN['clave'].'" value="1" ';
						  if ($_POST['vendedor_'.$rowVEN['clave']]==1) echo 'checked="checked" ';
						  echo '/>['.$rowVEN['login']."] ".$rowVEN['nombre'].'</label>';
						}
					  ?>
                  </div>
                
                </td>
              </tr> 
              <tr>
                <td><div align="right">Tipo de Cliente: </div></td>
                <td>
                <div class="filtro" style="height:60px">
				  <label class="blanks"><input type="checkbox" id="x" name="xtc" value="1" onclick="SetAllCheckBoxes('forma_dashboard', 'tipo_cliente', !this.checked);" />Blanks</label> 
                  <label><input type="checkbox" id="tipo_cliente" name="tipo_cliente_E" value="1" <?php if ($_POST['tipo_cliente_E']==1) echo 'checked="checked" ';?>/>Empleado</label>
                  <label><input type="checkbox" id="tipo_cliente" name="tipo_cliente_I" value="1" <?php if ($_POST['tipo_cliente_I']==1) echo 'checked="checked" ';?>/>Invitado</label>
                  <label><input type="checkbox" id="tipo_cliente" name="tipo_cliente_C" value="1" <?php if ($_POST['tipo_cliente_C']==1) echo 'checked="checked" ';?>/>Corporate</label>
                </div>
                  </td>
              </tr>              

              <tr>
                <td><div align="right">Proveedor Log: </div></td>
                <td>
                  <div class="filtro"> 
                     <label class="blanks"><input type="checkbox" id="x" name="xpo" value="1" onclick="SetAllCheckBoxes('forma_dashboard', 'proveedor', !this.checked);" />Blanks</label>
                    <?
						$resPRV = mysql_query("SELECT DISTINCT proveedor FROM dashboard WHERE proveedor != '' ORDER BY proveedor",$conexion);
						while ($rowPRV = mysql_fetch_array($resPRV)) {
						  $cod_proveedor = str_replace(' ','_',trim($rowPRV['proveedor']));
						  echo '<label><input type="checkbox" id="proveedor" name="proveedor_'.$cod_proveedor.'" value="1" ';
						  if ($_POST['proveedor_'.$cod_proveedor]==1) echo 'checked="checked" ';
						  echo '/>'.$rowPRV['proveedor'].'</label>';
						}
					  ?>
                  </div>
                
                </td>
              </tr>

              <tr>
                <td><div align="right">Estatus Pedido:</div></td>
                <td>
                <div class="filtro" style="height:60px">
				  <label class="blanks"><input type="checkbox" id="x" name="xtc" value="1" onclick="SetAllCheckBoxes('forma_dashboard', 'estatus_pedido', !this.checked);" />Blanks</label> 
                  <label><input type="checkbox" id="estatus_pedido" name="estatus_pedido_Completo" value="1" <?php if ($_POST['estatus_pedido_Completo']==1) echo 'checked="checked" ';?>/>Completo</label>
                  <label><input type="checkbox" id="estatus_pedido" name="estatus_pedido_Incompleto" value="1" <?php if ($_POST['estatus_pedido_Incompleto']==1) echo 'checked="checked" ';?>/>Incompleto</label>
                  <label><input type="checkbox" id="estatus_pedido" name="estatus_pedido_Cancelado" value="1" <?php if ($_POST['estatus_pedido_Cancelado']==1) echo 'checked="checked" ';?>/>Cancelado</label>
                </div>                
				</td>
              </tr>
              <tr>
                <td><div align="right">Estatus Entrega:</div></td>
                <td><select name="estatus_entrega" id="estatus_entrega">
                    <option value="" selected="selected">Cualquiera...</option>
                    <?
						$resPO = mysql_query("SELECT DISTINCT estatus_entrega FROM dashboard WHERE estatus_entrega != '' ORDER BY estatus_entrega",$conexion);
						while ($rowPO = mysql_fetch_array($resPO)) {
						  echo '<option value="'.$rowPO['estatus_entrega'].'"';
						  if ($rowPO['estatus_entrega']==$estatus_entrega) echo ' selected';
						  echo '>'.$rowPO['estatus_entrega'].'</option>';
						}
					  ?>
                  </select>   
               </td>
              </tr>
              <tr>
                <td><div align="right">Semáforo:</div></td>
                <td><select name="semaforo" id="semaforo">
                    <option value="x" <? if ($semaforo=='x') echo 'selected'; ?>>Cualquiera...</option>
					<option value="verde" <? if ($semaforo=='verde') echo 'selected'; ?>>Verde</option>
					<option value="amarillo" <? if ($semaforo=='amarillo') echo 'selected'; ?>>Amarillo</option>
					<option value="rojo" <? if ($semaforo=='rojo') echo 'selected'; ?>>Rojo</option>
                  </select>   
               </td>
              </tr>
              </table>
            </div>
            <div align="center">
            <br />
            <input type="hidden" name="buscando" id="buscando" />
            <input type="submit" name="button" id="button" value="Aplicar filtros" />
            </div>
       	 </div>
    
      <? if ($buscando) { 
	  
	  
	  		   $condicion = " WHERE 1  ";
			   
			   if ($tipo_pedido != 'x' && $tipo_pedido != '') {
			   		$condicion .= " AND tipo_pedido = '$tipo_pedido' ";
			   }
			   if ($empresa != 'x' && $empresa != '') {
			   		$condicion .= " AND empresa = $empresa ";
			   }
			   if ($po_number != 'x' && $po_number != '') {
			   		$condicion .= " AND po_number = '$po_number' ";
			   }
			   if ($estatus_pedido != 'x' && $estatus_pedido != '') {
			   		switch ($estatus_pedido) {
						case 'Completo' : $condicion .= " AND avance_pedido = 1 "; break;
						case 'Incompleto' : $condicion .= " AND avance_pedido < 1 "; break;
						case 'Cancelado' : $condicion .= " AND estatus_material = 'Cancelado' "; break;
					}
			   }
			   if ($estatus_entrega != 'x' && $estatus_entrega != '') {
			   		$condicion .= " AND estatus_entrega = '$estatus_entrega' ";
			   }
			   if ($semaforo != 'x' && $semaforo != '') {
			   		$condicion .= " AND semaforo = '$semaforo' ";
			   }
			   if ($fechas) {
			   		$condicion .= " AND fecha_pedido BETWEEN '$desde' AND '$hasta' ";
			   }
			   if ($fecha_fac) {
			   		$condicion .= " AND fecha_factura BETWEEN '$desde_fac' AND '$hasta_fac' ";
			   }
	  	
//				$condicion = 'WHERE folio_sap = 162568277 ';
			   $query = "SELECT *
						 FROM dashboard
						 $condicion";
//	  echo $query;
			   $resultadotot= mysql_query($query,$conexion);
			   $totres = mysql_num_rows ($resultadotot);
			   $totpags = ceil($totres/$ver);
			   if ($totres==0)
				  $numpag = 0;
				  
		  	   $gr1_ve = 0; $gr1_am = 0; $gr1_ro = 0; $gr1_ni = 0;
			   $gr2_co = 0; $gr2_in = 0; $gr2_ca = 0; $gr2_ni = 0;
			   $gr3_v = 0; $gr3_s = 0; $gr3_r = 0; $gr3_ni = 0;
			   while ($row = mysql_fetch_array($resultadotot)) {

				 	// totalizar para gráficas
					switch ($row['semaforo']) {
						 case 'amarillo' : $gr1_am++; break;
						 case 'rojo' : $gr1_ro++; break;
						 case 'verde' : $gr1_ve++; break;
						 case '' : $gr1_ni++; break;
					}
					
					switch ($row['estatus_material']) {
						 case 'Completo' : $gr2_co++; break;
						 case 'Incompleto' : $gr2_in++; break;
						 case 'Cancelado' : $gr2_ca++; break;
						 case '' : $gr2_ni++; break;
					}
					switch ($row['tipo_pedido']) {
						 case 'V' : $gr3_v++; break;
						 case 'S' : $gr3_s++; break;
						 case 'R' : $gr3_r++; break;
						 case '' : $gr3_ni++; break;
					}				  
				} // while totales gráficas
	  
//	  			echo " ve:".$gr1_ve." am:".$gr1_am." ro:".$gr1_ro." ni:".$gr1_ni." ot:".$gr1_ot;
	  
	  ?>

          <div id="graficas">
            	<ul class="tabs_graficas clear clearfix">
              		<li><a href="#grafica-1" class="grafica1">Gráfica1</a></li>
                    <li><a href="#grafica-2" class="grafica2">Gráfica2</a></li>
                    <li><a href="#grafica-3" class="grafica3">Gráfica3</a></li>
                </ul>
                <div id="grafica-1" class="grafica"><div id="gra1" style="height: 100px;"></div></div>
                <div id="grafica-2" class="grafica"><div id="gra2" style="height: 100px;"></div></div>
                <div id="grafica-3" class="grafica"><div id="gra3" style="height: 100px;"></div></div>
		  </div>      

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
                    <th align="center">SKU<br />
                      P&oacute;liza</th>
                    <th align="center">Folio Garant&iacute;a</th>
                    <th align="center">Entrega</th>
                    <th nowrap="nowrap">Costo<br />
                      Entrega</th>
                    <th nowrap="nowrap">Total</th>
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
                    <th>Estatus General <br />
                      SAP</th>
                    <th>Fecha Cambio <br />
                      Estatus</th>
                    <th nowrap="nowrap">Planta</th>
                    <th nowrap="nowrap">Storage</th>
                    <th nowrap="nowrap">Sales Org</th>
                    <th nowrap="nowrap">Canal</th>
                    <th nowrap="nowrap">Division</th>
                    <th nowrap="nowrap">Proveedor</th>
                    <th nowrap="nowrap">No.Gu&iacute;a</th>
                    <th nowrap="nowrap">Compromiso<br />
                      Entrega</th>
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
			  	 $resultado = mysql_query($query);
			     while ($row = mysql_fetch_array($resultado)) {

			  ?>
                  <tr>
                    <td><?=$row['folio_fpedido'];?></td>
                    <td nowrap="nowrap"><?=fechamy2mx($row['fecha_pedido'],'novacio');?></td>
                    <td><? switch ($row['tipo_pedido']) {
						case 'V' : echo 'Venta'; break;
						case 'S' : echo 'Sustituci&oacute;n'; break;
						case 'R' : echo 'Refacturaci&oacute;n'; break;
					   }
					?>
                    </td>
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
					   }
				   ?>
                    </td>
                    <td><?=$row['numero_empleado'];?></td>
                    <td><?=$row['nombre_empresa'];?></td>
                    <td><?=$row['numero_empresa'];?></td>
                    <td><?=$row['material'];?></td>
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
                    <td><?=$row['sku_garantia'];?></td>
                    <td><?=$row['folio_garantia'];?></td>
                    <td><?=$row['entrega'];?></td>
                    <td><?=nocero($row['costo_entrega']);?></td>
                    <td><?=$row['total'];?></td>
                    <td><?=$row['estatus_pago'];?></td>
                    <td><?=fecha($row['fecha_pago'],'novacio');?></td>
                    <td><?=$row['confirmo_pago'];?></td>
                    <td><?=fechamy2mx($row['compromiso_entrega'],'novacio');?></td>
                    <td><?=$row['folio_sap'];?></td>
                    <td><?=$row['order_date'];?></td>
                    <td><?=$row['saty'];?></td>
                    <td><?=$row['cantidad_pedido'];?></td>
                    <td><?=$row['delivery'];?></td>
                    <td><?=$row['delivery_date'];?></td>
                    <td><?=$row['cantidad_delivery'];?></td>
                    <td><?=$row['shipment'];?></td>
                    <td><?=$row['billing_doc'];?></td>
                    <td><?=fechamy2mx($row['fecha_factura']);?></td>
                    <td><?=$row['delivery_block'];?></td>
                    <td><?=nocero($row['reason_rejection']);?></td>
                    <td><?=$row['credit_status'];?></td>
                    <td><?=$row['overall_status'];?></td>
                    <td>&nbsp;</td>
                    <td><?=$row['planta'];?></td>
                    <td><?=$row['store_loc'];?></td>
                    <td><?=$row['organizacion'];?></td>
                    <td><?=$row['canal'];?></td>
                    <td><?=$row['division'];?></td>
                    <td><?=$row['proveedor'];?></td>
                    <td><?=$row['guia'];?></td>
                    <td><?=fechamy2mx($row['compromiso_entrega'],'novacio');?></td>
                    <td><?=fechamy2mx($row['fecha_entrega'],'novacio');?></td>
                    <td><?=$row['estatus_entrega'];?></td>
                    <td><?=$row['adicionales'];?></td>
                    <td><?=$row['estatus_material'];?></td>
                    <td><? 
            if ($row['avance_pedido']==1) echo '100%';
            elseif ($row['avance_pedido']==0) echo '0%'; 
            else  echo number_format($row['avance_pedido']*100,2).'%';?></td>
                    <td <? switch ($row['semaforo']) {
             case 'amarillo' : echo ' bgcolor="#FFFF99" '; break;
             case 'rojo' 	 : echo ' bgcolor="#FF0033" '; break;
             case 'verde'	 : echo ' bgcolor="#66CC33" '; break;
            }
         ?>></td>
                    <td><?=nocero($row['periodo_ped_entr']);?></td>
                    <td><?=nocero($row['periodo_fac_entr']);?></td>
                    <td><?=nocero($row['periodo_com_entr']);?></td>
                    <td><?=nocero($row['no_return']);?></td>
                    <td><?=$row['folio_devolucion'];?></td>
                    <td><?=nocero($row['folio_inconformidad']);?></td>
                    <td><?=$row['tipo_inconformidad'];?></td>
                    <td><?=fecha($row['fecha_inconformidad'],'novacio');?></td>
                    <td><?=$row['estatus_inconformidad'];?></td>
                    <td><?=$row['feedback'];?></td>
                    <td align="center"><!--a href="javascript:actualiza(<?=$row['id'];?>);" title="Actualizar pedido" alt="Actualizar pedido"><img src="images/icons/gear_wheel.png" /></a--></td>
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
/*
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {

        data1 = google.visualization.arrayToDataTable([
          ['Estatus', 		  'Cant. de pedidos'],
          ['Verde',     	  <?=$gr1_ve;?>],
          ['Amarillo',  	  <?=$gr1_am;?>],
          ['Rojo',  		  <?=$gr1_ro;?>],
          ['No Identificado', <?=$gr1_ni;?>]
        ]);
        data2 = google.visualization.arrayToDataTable([
          ['Estatus', 		  'Pedidos'],
          ['Completo',    	  <?=$gr2_co;?>],
          ['Incompleto',  	  <?=$gr2_in;?>],
          ['Cancelado',  	  <?=$gr2_ca;?>],
          ['No Identificado', <?=$gr2_ni;?>]
        ]);
        data3 = google.visualization.arrayToDataTable([
          ['Tipo', 			  'Cant. de pedidos'],
          ['Venta',     	  <?=$gr3_v;?>],
          ['Sustitución',     <?=$gr3_s;?>],
          ['Refacturación',   <?=$gr3_r;?>],
          ['No Identificado', <?=$gr3_ni;?>]
        ]);
						
        options1 = {
          title: 'Estatus de Pedidos',
		  is3D: false,
		  slices: {
            0: { color: 'green' },
            1: { color: 'yellow' },
            2: { color: 'red' },
            3: { color: 'gray' }
          },
		  pieSliceTextStyle: {
            color: 'black'
          }
        };

        options2 = {
          title: 'Estatus de Material',
		  is3D: true,

        };
		
        options3 = {
          title: 'Tipos de Pedidos',
          pieSliceText: 'label',
		  is3D: true
        };
				
        chart = new google.visualization.PieChart(document.getElementById('gra1'));
        
		chart2 = new google.visualization.ColumnChart(document.getElementById('gra2'));
		
        chart3 = new google.visualization.PieChart(document.getElementById('gra3'));

        chart.draw(data1, options1);
		
      }
	  */
</script>




</body>
</html>
