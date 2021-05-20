<?php
// Cambios
// Enero 2016
//	  Listado de clientes de proyectos

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
    form.action='lista_cliente_proyectos.php';
    form.submit();
  }
  function ordena(orden) {
    document.forma.ord.value = orden;
	document.forma.buscar.value = 1;
	document.forma.numpag.value = 1;
    document.forma.action='lista_cliente_proyectos.php';
    document.forma.submit();
  }
  function busca() {
    document.forma.numpag.value=1
	document.forma.buscar.value = 1;
    document.forma.action='lista_cliente_proyectos.php';
    document.forma.submit();
  }	  

  function borra(id) {
    document.forma.cliente.value = id;
	document.forma.buscar.value = 1;
	document.forma.accion.value = 'borrar';
    document.forma.action='lista_cliente_proyectos.php';
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
  	document.forma.action = 'lista_cliente_proyectos_excel.php';
    document.forma.submit();
  	document.forma.target = '_self';
  }

</script>
</head>

<body>
<div id="container">
	<? $tit='Listado de Clientes de Proyectos'; include('top.php'); ?>
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
   $epep = $_POST['epep'];
   if (!$estatus) $estatus = 'A';
   if (!isset($_POST['eflex'])) $eflex= 'x';
   if (!isset($_POST['epep'])) $epep= 'x';
   $buscar = $_POST['buscar'];


   if     ($ord=='nombre') $orden='ORDER BY cliente.nombre';
   elseif ($ord=='email') $orden='ORDER BY email';
   
   
?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <input name="cliente" type="hidden" id="cliente">
        <input name="accion" type="hidden" id="accion">
        <input name="ord" type="hidden" id="ord" value="<?= $ord; ?>" />
        <input name="numpag" type="hidden" id="numpag" value="<?= $numpag; ?>" />
        <input name="buscar" type="hidden" id="buscar" value="" />

        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td nowrap="nowrap"><input name="button" type="submit" class="boton_agregar" id="button" value="Agregar cliente nuevo" onclick="document.forma.action='abc_cliente_proyectos.php'; document.forma.submit();" /></td>
            <td align="right"><table width="80%" border="0" align="left" cellpadding="2" cellspacing="0" class="texto">
              <tr>
                <td valign="top"><div align="right">Empresa:</div></td>
                <td colspan="2"><div align="left">
                  <select name="empresa" class="campo" id="empresa">
                    <option value="" selected="selected">Cualquier empresa...</option>
                    <?
				$resEMP = mysql_query("SELECT clave, nombre FROM empresa WHERE empresa.empresa_proyectos = 1 ORDER BY nombre",$conexion);
				while ($rowEMP = mysql_fetch_array($resEMP)) {
				  echo '<option value="'.$rowEMP['clave'].'"';
				  if ($rowEMP['clave']==$empresa) echo ' selected';
				  echo '>'.$rowEMP['nombre'].'</option>';
				}
			  ?>
                    </select>
                </div></td>
              </tr>
              <tr>
                <td width="156" valign="top"><div align="right">Buscar:</div></td>
                <td width="594" colspan="2" nowrap="nowrap"><div align="left">
                    <input name="texto" type="text" class="campo" id="texto" value="<?= $texto; ?>" size="50" />
                    (Nombre, E-mail) <br />
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
                     $condicion = "WHERE empresa.empresa_proyectos = 1 ";

					 if (!empty($empresa)) 
					 	$condicion .= "AND empresa='$empresa'";

					if ($estatus=='A') $condicion .= " AND activo = 1 ";
					if ($estatus=='I') $condicion .= " AND activo = 0 ";

					 if (!empty($texto)) {
						// identificar si sólo hay 1 palabra o más de 1
						$trozos=explode(" ",$texto);
						$numero_palabras=count($trozos);
						$condicion .= " AND (cliente.nombre LIKE '%$texto%'  OR apellido_paterno LIKE '%$texto%' OR apellido_materno LIKE '%$texto%' OR email LIKE '%$texto%') ";	
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
                     echo "P&aacute;gina ";
					 echo '<input type="text" name="pagina" value="'.$numpag.'" size="2" onchange="javascript:ir(document.forma,this.value);" style="text-align:center"/>';
					 echo " de ".$totpags;
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
            <td>&nbsp;</td>
          </tr>
          <tr class="texto" bgcolor="#F4F4F2">
            <td nowrap="nowrap"><b>Nombre</b></td>
            <td nowrap="nowrap"><b>E-mail</b></td>
            <td><b>Empresa</b></td>
            <td align="center"><strong>Compras</strong></td>
            <td align="center"><strong>Activo</strong></td>
            <td><div align="center"><b>Acci&oacute;n</b></div></td>
          </tr>
          <?

			 $query = "SELECT cliente.*, CONCAT(cliente.nombre,' ',cliente.apellido_paterno,' ',cliente.apellido_materno) AS nombre 
						FROM cliente 
						LEFT JOIN empresa ON cliente.empresa = empresa.clave 
						$condicion $orden LIMIT $regini,$ver";

             $resultado= mysql_query($query,$conexion);
             while ($row = mysql_fetch_array($resultado)){ 
			 	
				$cve_empleado = $row['clave'];
				$cve_empresa=$row['empresa'];
	            $resEMP= mysql_query("SELECT * FROM empresa WHERE clave='$cve_empresa'",$conexion);
                $rowEMP= mysql_fetch_array($resEMP); 

				$cve_cliente=$row['clave'];
	            $resPED= mysql_query("SELECT 1 FROM pedido WHERE cliente=$cve_cliente",$conexion);

			    $cant_compras = mysql_num_rows($resPED);

          ?>
          <tr class="texto" valign="top" bgcolor="#FFFFFF">
            <td><?= $row['nombre']; ?></td>
            <td><?= $row['email']; ?></td>
            <td><?= $rowEMP['nombre']; ?></td>
            <td align="center">
				<? if ($cant_compras>0)
				    echo $cant_compras.' <a href="javascript:historial(\''.$row['clave'].'\',\''.$row['empresa'].'\');"><img src="images/foto.png" width="14" height="15" align="absmiddle" title="Ver historial de compras" /></a>';
				    else echo '&nbsp;'; ?></td>
            <td align="center"><? if ($row['activo']==1) echo 'SI'; else echo 'NO'; ?></td>
            <td align="center" nowrap="nowrap">
	            <a href="abc_cliente_proyectos.php?cliente=<?= $row['clave']; ?>"><img src="images/editar.png" title="Editar Cliente" width="14" height="16" border="0" align="absmiddle" /></a>
                <a href="direcciones_cliente_proyectos.php?cliente=<?= $row['clave']; ?>"><img src="images/shipping.png" align="absmiddle" title="Direcciones de envío" /></a>
       	  		<? if ($cant_compras==0 AND op_aut($modulo)) { ?><a onclick="return confirm('¿Estás seguro que deseas\nBorrar el Cliente?')" href="javascript:borra('<?= $row['clave']; ?>');"><img src="images/borrar.png" title="Borrar Cliente" width="14" height="15" border="0" align="absmiddle" /></a><? } else  { ?><img src="images/borrar_off.png" width="14" height="15" align="absbottom" /><? } ?>   			</td>
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
                     echo "P&aacute;gina ";
					 echo '<input type="text" name="pagina2" value="'.$numpag.'" size="2" onchange="javascript:ir(document.forma,this.value);" style="text-align:center"/>';
					 echo " de ".$totpags;
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
            <td align="right">&nbsp;</td>
          </tr>
        </table>
        <? } // if buscar ?>
      </form>    
    </div>
</div>
</body>
</html>