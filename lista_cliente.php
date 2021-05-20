<?php
// Cambios
// Nov 2015
//	  Precios especiales configurables en config

    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=8;
    include('../conexion.php');
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
   if ($_POST['accion'] == 'borrar') {
   		$cliente = $_POST['cliente'];
   		$resultado = mysql_query("DELETE FROM cliente WHERE clave = $cliente LIMIT 1",$conexion);
   }
   // obtener datos de configuracion
   $resultadoCFG = mysql_query("SELECT * FROM config WHERE reg = 1",$conexion);
   $rowCFG = mysql_fetch_array($resultadoCFG);

   $limite_kad = $rowCFG['limite_kad'];
   $limite_precios_especiales = $rowCFG['limite_precios_especiales']+0;
   
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
<script type="text/javascript" src="js/jquery.js"></script>
<link href="js/src/shadowbox.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/src/shadowbox.js"></script>
<script type="text/javascript">
	Shadowbox.init({
		language: 'es',
		players:  ['img', 'html', 'iframe', 'qt', 'wmp', 'swf', 'flv']
	});
</script>

<script language="JavaScript">
  function ir(form, Pag) {
    form.numpag.value = Pag;
	form.buscar.value = 1;
    form.action='lista_cliente.php';
    form.submit();
  }
  function ordena(orden) {
    document.forma.ord.value = orden;
	document.forma.buscar.value = 1;
	document.forma.numpag.value = 1;
    document.forma.action='lista_cliente.php';
    document.forma.submit();
  }
  function busca() {
    document.forma.numpag.value=1
	document.forma.buscar.value = 1;
    document.forma.action='lista_cliente.php';
    document.forma.submit();
  }	  
  function reiniciar() {
    document.forma.action='resetea_precios_especiales.php';
    document.forma.submit();
  }	  
  	
  function borra(id) {
    document.forma.cliente.value = id;
	document.forma.buscar.value = 1;
	document.forma.accion.value = 'borrar';
    document.forma.action='lista_cliente.php';
    document.forma.submit();
  }
  function historial(id,empresa) {
    document.forma.empresa.value = empresa;
    document.forma.cliente.value = id;
	document.forma.buscar.value = 1;
	document.forma.estatus.value = 'x';
    document.forma.action='lista_pedidos.php';
    document.forma.submit();
  }
  function reporte() {
  	document.forma.target = '_blank';
  	document.forma.action = 'lista_cliente_excel.php';
    document.forma.submit();
  	document.forma.target = '_self';
  }
  function reporte_csv() {
  	document.forma.target = '_blank';
  	document.forma.action = 'lista_cliente_csv.php';
    document.forma.submit();
  	document.forma.target = '_self';
  }
</script>
</head>

<body>
<div id="container">
	<? $tit='Listado de Clientes'; include('top.php'); ?>
  <?
   $ver = $_POST['ver'];
   $numpag = $_POST['numpag'];
   $ord = $_POST['ord'];
   if (empty($ver)) $ver='20';
   if (empty($numpag)) $numpag='1';
   if (empty($ord)) $ord='nombre';

   $empresa = $_POST['empresa'];
   $texto = $_POST['texto'];
   $tipo = $_POST['tipo'];
   $estatus = $_POST['estatus'];
   $eflex = $_POST['eflex'];
   $puntos = $_POST['puntos'];
   $puntos_convenio = $_POST['puntos_convenio'];
   $epep = $_POST['epep'];
   if (!$estatus) $estatus = 'A';
   if (!isset($_POST['puntos'])) $puntos= 'x';
   if (!isset($_POST['puntos_convenio'])) $puntos_convenio= 'x';
   if (!isset($_POST['eflex'])) $eflex= 'x';
   if (!isset($_POST['epep'])) $epep= 'x';
   $buscar = $_POST['buscar'];


  
   if($ord){
    $orden = "ORDER BY cliente.".$ord." ASC";
   }
   
   
?>
	<div class="main" style="overflow-x: scroll;">
      <form action="" method="post" name="forma" id="forma">
        <input name="cliente" type="hidden" id="cliente">
        <input name="accion" type="hidden" id="accion">
        <input name="ord" type="hidden" id="ord" value="<?= $ord; ?>" />
        <input name="numpag" type="hidden" id="numpag" value="<?= $numpag; ?>" />
        <input name="buscar" type="hidden" id="buscar" value="" />

        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td nowrap="nowrap"><a onclick="reporte_csv();" target="_blank" class="texto"><img src="images/icon_excel.gif" alt="" width="25" height="16" align="absmiddle" />Exportar a Excel</a></td>
            <td align="right"><table width="80%" border="0" align="left" cellpadding="2" cellspacing="0" class="texto">
              <tr>
                <td valign="top"><div align="right">Empresa:</div></td>
                <td colspan="2"><div align="left"><span class="row1">
                  <select name="empresa" class="campo" id="empresa">
                    <option value="" selected="selected">Cualquier empresa...</option>
                    <?
				$resEMP = mysql_query("SELECT clave, nombre FROM empresa WHERE empresa.empresa_proyectos = 0 ORDER BY nombre",$conexion);
				while ($rowEMP = mysql_fetch_array($resEMP)) {
				  echo '<option value="'.$rowEMP['clave'].'"';
				  if ($rowEMP['clave']==$empresa) echo ' selected';
				  echo '>'.$rowEMP['nombre'].'</option>';
				}
			  ?>
                    </select>
                </span></div></td>
              </tr>
              <tr>
                <td valign="top"><div align="right">Tipo:</div></td>
                <td colspan="2"><div align="left">
                    <select name="tipo" class="campo" id="tipo">
              <option value="" <? if ($tipo=='') echo 'selected';?>>Cualquiera...</option>
              <?php
          $resLP = mysql_query("SELECT * FROM cliente_tipo",$conexion);
          while ($rowLP = mysql_fetch_assoc($resLP)) {
            echo '<option value="'.$rowLP['id'].'"';
            if ($tipo==$rowLP['id']) echo 'selected';
            echo '>'.$rowLP['nombre'].'</option>';
          } // while
        ?>
            </select>
                </div></td>
                
              </tr>
              <tr>
                <td width="156" valign="top"><div align="right">Buscar:</div></td>
                <td width="594" colspan="2" nowrap="nowrap"><div align="left">
                    <input name="texto" type="text" class="campo" id="texto" value="<?= $texto; ?>" size="50" />
                    (Nombre, E-mail, #empleado) <br />
                </div></td>
              </tr>
              <tr>
                <td><div align="right">Estatus:</div></td>
                <td colspan="2"><div align="left">
                  <select name="estatus" class="campo" id="estatus">
                    <option value="x" <? if ($estatus=='x') echo 'selected';?>>Cualquiera...</option>
                    <option value="A" <? if ($estatus=='A') echo 'selected';?>>Activos</option>
                    <option value="I" <? if ($estatus=='I') echo 'selected';?>>Inactivos</option>
                    </select>
                </div></td>
              </tr>
              <tr>
                <td><div align="right">Puntos:</div></td>
                <td colspan="2"><div align="left">
                    <select name="puntos" class="campo" id="puntos">
                      <option value="x" <? if ($puntos=='x') echo 'selected';?>>Cualquiera...</option>
                      <option value="1" <? if ($puntos=='1') echo 'selected';?>>Con puntos</option>
                      <option value="0" <? if ($puntos=='0') echo 'selected';?>>Sin puntos</option>
                    </select>
                </div></td>
              </tr>              
              <tr>
                <td><div align="right">Puntos Convenio:</div></td>
                <td colspan="2"><div align="left">
                    <select name="puntos_convenio" class="campo" id="puntos_convenio">
                      <option value="x" <? if ($puntos_convenio=='x') echo 'selected';?>>Cualquiera...</option>
                      <option value="1" <? if ($puntos_convenio=='1') echo 'selected';?>>Con puntos</option>
                      <option value="0" <? if ($puntos_convenio=='0') echo 'selected';?>>Sin puntos</option>
                    </select>
                </div></td>
              </tr>              
			  <tr>
                <td><div align="right">e-Flex:</div></td>
                <td colspan="2"><div align="left">
                    <select name="eflex" class="campo" id="eflex">
                      <option value="x" <? if ($eflex=='x') echo 'selected';?>>Cualquiera...</option>
                      <option value="1" <? if ($eflex=='1') echo 'selected';?>>Con puntos</option>
                      <option value="0" <? if ($eflex=='0') echo 'selected';?>>Sin puntos</option>
                    </select>
                </div></td>
              </tr>
              <tr>
                <td><div align="right">e-PEP:</div></td>
                <td><div align="left">
                    <select name="epep" class="campo" id="epep">
                      <option value="x" <? if ($epep=='x') echo 'selected';?>>Cualquiera...</option>
                      <option value="1" <? if ($epep=='1') echo 'selected';?>>Con puntos</option>
                      <option value="0" <? if ($epep=='0') echo 'selected';?>>Sin puntos</option>
                    </select>
                </div></td>
                <td align="right"><input name="Button2" type="button" class="boton" onclick="javascript:reiniciar();" value="Reiniciar Precios Especiales" /></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td colspan="2"><div align="left">
                  <input name="Button" type="button" class="boton" onclick="javascript:busca();" value="Buscar" />
                </div></td>
              </tr>
            </table></td>
          </tr>
          
          <? if ($buscar) { ?>
          <tr>
            <td bgcolor="#BBBBBB"><?
			
                  // obtener el total de registros que coinciden...
                  // y establecer algunas variables
					   

                     // construir la condición de búsqueda
                     $condicion = "WHERE empresa.empresa_proyectos = 0 ";

					 if (!empty($empresa)) 
					 	$condicion .= "AND empresa='$empresa'";

					 if (!empty($tipo)) {
					/*	if ($tipo=='I') 
						 	$condicion .= "AND invitado = 1 ";
						else
							$condicion .= "AND invitado = 0 ";
					*/
						$condicion .= " AND cliente.tipo = '$tipo' ";
							
					}
					if ($estatus=='A') $condicion .= " AND activo = 1 ";
					if ($estatus=='I') $condicion .= " AND activo = 0 ";

					if ($puntos=='1') $condicion .= " AND cliente.puntos > 0 ";
					if ($puntos=='0') $condicion .= " AND cliente.puntos <= 0 ";

					if ($puntos_convenio=='1') $condicion .= " AND cliente.puntos_convenio > 0 ";
					if ($puntos_convenio=='0') $condicion .= " AND cliente.puntos_convenio <= 0 ";

					if ($eflex=='1') $condicion .= " AND puntos_flex > 0 ";
					if ($eflex=='0') $condicion .= " AND puntos_flex <= 0 ";
					
					if ($epep=='1') $condicion .= " AND puntos_pep > 0 ";
					if ($epep=='0') $condicion .= " AND puntos_pep <= 0 ";

					 
					 if (!empty($texto)) {
						// identificar si sólo hay 1 palabra o más de 1
						$trozos=explode(" ",$texto);
						$numero_palabras=count($trozos);
						if (1 || $numero_palabras==1) {
							//SI SOLO HAY UNA PALABRA DE BUSQUEDA SE ESTABLECE UNA INSTRUCION CON LIKE
							$condicion .= "AND (cliente.nombre LIKE '%$texto%'  OR apellido_paterno LIKE '%$texto%' OR apellido_materno LIKE '%$texto%' OR email LIKE '%$texto%'  OR numero_empleado LIKE '%$texto%') ";	
						} else  { // más de 1 palabras
							//SI HAY UNA FRASE SE UTILIZA EL ALGORTIMO DE BUSQUEDA AVANZADO DE MATCH AGAINST
							//busqueda de frases con mas de una palabra y un algoritmo especializado
							//$condicion .= " SELECT titulo, descripcion , MATCH ( titulo, descripcion ) AGAINST ( '$texto' ) AS Score FROM anuncio WHERE MATCH ( titulo, descripcion, ciudad, estado ) AGAINST ( '$texto' ) ORDER BY score DESC";
							$condicion .= " AND MATCH ( cliente.nombre, email, numero_empleado ) AGAINST ( '$texto' IN BOOLEAN MODE ) ";
						} 
					 }

                     // construir la condición de búsqueda

                       $resultadotot= mysql_query("SELECT * FROM cliente LEFT JOIN empresa ON cliente.empresa = empresa.clave $condicion",$conexion);
                       $totres = mysql_num_rows ($resultadotot);
                       $totpags = ceil($totres/$ver);
                       if ($totres==0)
                          $numpag = 0;
						  
					 echo 'Total de clientes en la lista: <b>'.$totres.'</b>';
					 
			
			  ?>            </td>
            <td align="right" bgcolor="#BBBBBB">
                <?


                     $regini = ($numpag * $ver) - $ver;
					 if ($regini<0) $regini=0;

                     // poner flechitas anterior, primero, último, etc.
                     if ($numpag<=1) {
                         echo '<img src="images/primera_off.gif" width="16" height="11" align="absmiddle">&nbsp;';
                         echo '<img src="images/anterior_off.gif" width="13" height="11" align="absmiddle">';
                     } else {
                         echo '<a href="javascript:ir(document.forma,1);"><img src="images/primera_on.gif" border="0" width="16" height="11" align="absmiddle" alt="Primera página"></a>&nbsp;';
                         echo '<a href="javascript:ir(document.forma,'.($numpag-1).');"><img src="images/anterior_on.gif" border="0" width="13" height="11" align="absmiddle" alt="Página anterior"></a>';
                     }
                     echo '&nbsp;&nbsp;<span class="texto">';
                     echo "Página ".$numpag." de ".$totpags;
                     echo "&nbsp;</span>&nbsp;";
                     if ($numpag>=$totpags) {
                         echo '<img src="images/siguiente_off.gif" width="13" height="11" align="absmiddle">&nbsp;';
                         echo '<img src="images/ultima_off.gif" width="16" height="11" align="absmiddle">';
                     } else {
                         echo '<a href="javascript:ir(document.forma,'.($numpag+1).');"><img src="images/siguiente_on.gif" border="0" width="13" height="11" align="absmiddle" alt="Siguiente página"></a>&nbsp;';
                         echo '<a href="javascript:ir(document.forma,'.$totpags.');"><img src="images/ultima_on.gif" border="0" width="16" height="11" align="absmiddle" alt="Última página"></a>';
                     }
              ?>            </td>
          </tr>
          <? } ?>
        </table>
        <? if ($buscar) { ?>
        <table width="100%" border="0" align="center" cellpadding="2" cellspacing="3">
          <tr class="texto">
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr class="texto" bgcolor="#F4F4F2">
            <td nowrap="nowrap" bgcolor="<? if($ord=='nombre') echo '#DDDDDD'; ?>">
              <b>
                <a href="javascript:ordena('nombre');" class="texto">
                  Nombre 
                  <img src="images/orden.png" alt="" width="14" height="15" border="0" align="absmiddle" />
                </a>
              </b>
            </td>
             <td nowrap="nowrap" bgcolor="<? if($ord=='apellido_paterno') echo '#DDDDDD'; ?>">
              <b>
                <a href="javascript:ordena('apellido_paterno');" class="texto">
                  Apellido Paterno 
                  <img src="images/orden.png" alt="" width="14" height="15" border="0" align="absmiddle" />
                </a>
              </b>
            </td>
             <td nowrap="nowrap" bgcolor="<? if($ord=='apellido_materno') echo '#DDDDDD'; ?>">
              <b>
                <a href="javascript:ordena('apellido_materno');" class="texto">
                  Apellido Materno 
                  <img src="images/orden.png" alt="" width="14" height="15" border="0" align="absmiddle" />
                </a>
              </b>
            </td>           
            <td nowrap="nowrap" bgcolor="<? if($ord=='email') echo '#DDDDDD'; ?>"><b><a href="javascript:ordena('email');" class="texto">E-mail <img src="images/orden.png" alt="" width="14" height="15" border="0" align="absmiddle" /></a></b></td>
            <td><b>Empresa</b></td>
            <td align="center"><strong># N&oacute;mina</strong></td>
            <td align="center"><strong>Compras</strong></td>
            <td align="center"><strong>Tipo</strong></td>            
            <td align="center"><strong>Invitados</strong></td>
            <td align="center"><strong>Puntos</strong></td>
            <td align="center"><strong>Puntos Flex</strong></td>
            <td align="center"><strong>Puntos PEP</strong></td>
            <td align="center"><strong>Puntos Convenio<br />
            disponibles</strong></td>
            <td align="center"><strong>PE<br />
            Disponibles</strong></td>
            <td align="center"><strong>Recepci&oacute;n de Promociones</strong></td>
            <td align="center"><strong>Estudio de Mercado</strong></td>
            <td align="center"><strong>Activo</strong></td>
            <td align="center"><strong>Fecha Registro</strong></td>
            <td align="center"><strong>Fecha Ultima Actualizaci&oacute;n</strong></td>
            <td><div align="center"><b>Acci&oacute;n</b></div></td>
          </tr>
          <?

			 $query = "SELECT cliente.*,  cliente_tipo.nombre as cliente_tipo 
						FROM cliente 
						LEFT JOIN empresa ON cliente.empresa = empresa.clave
            LEFT JOIN cliente_tipo ON cliente.tipo = cliente_tipo.id 
						$condicion $orden LIMIT $regini,$ver";
//echo $query;
             $resultado= mysql_query($query,$conexion);
             while ($row = mysql_fetch_array($resultado)){ 
			 	
				$cve_empleado = $row['clave'];
				$cve_empresa=$row['empresa'];
	            $resEMP= mysql_query("SELECT * FROM empresa WHERE clave='$cve_empresa'",$conexion);
                $rowEMP= mysql_fetch_array($resEMP); 

				$cve_cliente=$row['clave'];
	            $resPED= mysql_query("SELECT * FROM pedido WHERE cliente='$cve_cliente'",$conexion);
			    $cant_compras = mysql_num_rows($resPED);

	            $resCOM= mysql_query("SELECT * FROM comentario WHERE cliente='$cve_cliente'",$conexion);
			    $cant_comentarios = mysql_num_rows($resCOM);
				
				$resultadoINVS = mysql_query("SELECT COUNT(*) AS total_invitados FROM cliente WHERE invitado_por = $cve_cliente AND invitado",$conexion);
				$rowINVS = mysql_fetch_array($resultadoINVS);
                $total_invitados = $rowINVS['total_invitados'];


				if ($rowEMP['empresa_whirlpool']) {

					$kad_comprados = $row['kad_comprados'];
					$kad_disponibles = $limite_kad - $kad_comprados;
					if ($kad_disponibles < 0) $kad_disponibles = 0;

			   } else{
			   		$kad_disponibles = 0;
			   
			   }				
        /*echo "<pre>";
				print_r($row);
        echo "</pre>";   
        die;*/
          ?>
          <tr class="texto" valign="top" bgcolor="#FFFFFF">
            <td><?= $row['nombre']; ?></td>
            <td><?= $row['apellido_paterno']; ?></td>
            <td><?= $row['apellido_materno']; ?></td>
            <td><?= $row['email']; ?></td>
            <td><?= $rowEMP['nombre']; ?></td>
            <td><?= $row['numero_empleado']; ?></td>
            <td align="center">
				<? if ($cant_compras>0)
				    echo $cant_compras.' <a href="javascript:historial(\''.$row['clave'].'\',\''.$row['empresa'].'\');"><img src="images/foto.png" width="14" height="15" align="absmiddle" alt="Ver historial de compras" /></a>';
				    else echo '&nbsp;'; ?></td>
            <td align="center"><?= $row['cliente_tipo']; ?></td>
            <td align="center"><? if($total_invitados) echo $total_invitados; else echo '&nbsp;'; ?></td>
            <td align="center"><? if($row['puntos']) echo $row['puntos']; else echo '&nbsp;'; ?></td>
            <td align="center"><? if($row['puntos_flex']) echo $row['puntos_flex']; else echo '&nbsp;'; ?></td>
            <td align="center"><? if($row['puntos_pep']) echo $row['puntos_pep']; else echo '&nbsp;'; ?></td>
            <td align="center"><? if($row['puntos_convenio']) echo $row['puntos_convenio']; else echo '&nbsp;'; ?></td>
            <td align="center"><? echo nocero($row['pe_disponibles']); // nocero($pe_disponibles)?></td>
            <td align="center"><? echo ($row['recibir_informacion']==1) ? 'SI' : (($row['recibir_informacion']=='') ? '' : 'NO');?></td>
            <td align="center"><? echo ($row['participar_estudios']==1) ? 'SI' : (($row['participar_estudios']=='') ? '' : 'NO');?></td>            
            <td align="center"><? if ($row['activo']==1) echo 'SI'; else echo 'NO'; ?></td>
            <td align="center"><? if($row['fecha_registro']) echo substr($row['fecha_registro'],0,10); else echo '&nbsp;'; ?></td>
            <td align="center"><? if($row['fecha_actualizacion']) echo substr($row['fecha_actualizacion'],0,10); else echo '&nbsp;'; ?></td>
            <td align="center" nowrap="nowrap">
	            <a href="abc_cliente.php?cliente=<?= $row['clave']; ?>"><img src="images/editar.png" alt="Editar Cliente" width="14" height="16" border="0" align="absmiddle" /></a> <a href="detalle_cliente.php?cliente=<?= $row['clave']; ?>" rel="shadowbox;width=720;height=500"><img src="images/foto.png" width="14" height="15" align="absmiddle" alt="Detalles del cliente" /></a>
       	  		<? if ($cant_compras==0 AND $cant_comentarios==0 AND op_aut($modulo)) { ?><a onclick="return confirm('¿Estás seguro que deseas\nBorrar el Cliente?')" href="javascript:borra('<?= $row['clave']; ?>');"><img src="images/borrar.png" alt="Borrar Cliente" width="14" height="15" border="0" align="absmiddle" /></a><? } else  { ?><img src="images/borrar_off.png" width="14" height="15" align="absbottom" /><? } ?>   			</td>
          </tr>
          <?
			 } // WHILE
			 mysql_close();
		  ?>
        </table>
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td align="right">&nbsp;</td>
          </tr>
          <tr>
            <td bgcolor="#BBBBBB" align="right"><?

                     // poner flechitas anterior, primero, &uacute;ltimo, etc.
                     if ($numpag<=1) {
                         echo '<img src="images/primera_off.gif" width="16" height="11" align="absmiddle">&nbsp;';
                         echo '<img src="images/anterior_off.gif" width="13" height="11" align="absmiddle">';
                     } else {
                         echo '<a href="javascript:ir(document.forma,1);"><img src="images/primera_on.gif" border="0" width="16" height="11" align="absmiddle" alt="Primera p&aacute;gina"></a>&nbsp;';
                         echo '<a href="javascript:ir(document.forma,'.($numpag-1).');"><img src="images/anterior_on.gif" border="0" width="13" height="11" align="absmiddle" alt="P&aacute;gina anterior"></a>';
                     }
                     echo '&nbsp;&nbsp;<span class="texto">';
                     echo "P&aacute;gina ".$numpag." de ".$totpags;
                     echo "&nbsp;</span>&nbsp;";
                     if ($numpag>=$totpags) {
                         echo '<img src="images/siguiente_off.gif" width="13" height="11" align="absmiddle">&nbsp;';
                         echo '<img src="images/ultima_off.gif" width="16" height="11" align="absmiddle">';
                     } else {
                         echo '<a href="javascript:ir(document.forma,'.($numpag+1).');"><img src="images/siguiente_on.gif" border="0" width="13" height="11" align="absmiddle" alt="Siguiente p&aacute;gina"></a>&nbsp;';
                         echo '<a href="javascript:ir(document.forma,'.$totpags.');"><img src="images/ultima_on.gif" border="0" width="16" height="11" align="absmiddle" alt="&Uacute;ltima p&aacute;gina"></a>';
                     }
              ?>
            </td>
          </tr>
          <tr>
            <td align="right"><a onclick="reporte_csv();" target="_blank" class="texto">_</a></td>
          </tr>
        </table>
        <? } // if buscar ?>
      </form>    
    </div>
</div>
</body>
</html>