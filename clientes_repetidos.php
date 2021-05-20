<?
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
   if (!$estatus) $estatus = 'A';
   $dato_buscar = $_POST['dato_buscar'];
   if (!$dato_buscar) $dato_buscar = 'numero_empleado_wp';


   if     ($ord=='nombre') $orden='ORDER BY nombre';
   elseif ($ord=='email') $orden='ORDER BY email';
	
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
    form.action='clientes_repetidos.php';
    form.submit();
  }
  function recargar() {
    form.action='clientes_repetidos.php';
    form.submit();
  }
</script>
</head>

<body>
<div id="container">
	<? $tit='Listado de Clientes Repetidos'; include('top.php'); ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td>&nbsp;</td>
            <td align="right"><table width="606" border="0" align="left" cellpadding="2" cellspacing="0" class="texto">
              <tr>
                <td nowrap="nowrap"><div align="right">Buscar con:</div></td>
                <td><label>
                  <select name="dato_buscar" class="campo" id="dato_buscar">
                    <option value="numero_empleado_wp" <? if ($dato_buscar=='numero_empleado_wp') echo 'selected';?>>Número de empleado repetido en cualquier empresa WP</option>
                    <option value="numero_empleado" <? if ($dato_buscar=='numero_empleado') echo 'selected';?>>Número de empleado repetido en misma empresa</option>
                    <option value="nombre" <? if ($dato_buscar=='nombre') echo 'selected';?>>Nombre repetido</option>
                    <option value="email" <? if ($dato_buscar=='email') echo 'selected';?>>Correo-e repetido</option>
                  </select>
                </label></td>
              </tr>
              <tr>
                <td><div align="right">Empresa:</div></td>
                <td><div align="left"><span class="row1">
                  <select name="empresa" class="campo" id="empresa">
                    <option value="" selected="selected">Cualquier empresa...</option>
                    <?
				$resEMP = mysql_query("SELECT clave, nombre FROM empresa ORDER BY nombre",$conexion);
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
                <td width="54"><div align="right">Buscar:</div></td>
                <td width="544" nowrap="nowrap"><div align="left">
                    <input name="texto" type="text" class="campo" id="texto" value="<?= $texto; ?>" size="50" />
                    (Nombre, E-mail, No. Empleado) <br />
                </div></td>
              </tr>

              <tr>
                <td>&nbsp;</td>
                <td><div align="left">
                  <input name="Submit" type="submit" class="boton" onclick="document.forma.numpag.value=1" value="Buscar" />
                </div></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td bgcolor="#BBBBBB"><?
			
                  // obtener el total de registros que coinciden...
                  // y establecer algunas variables
					   

                     // construir la condición de búsqueda
                     $condicion = "WHERE 1=1 ";

					 if (!empty($empresa)) 
					 	$condicion .= "AND empresa='$empresa'";
	
				 
					 if (!empty($texto)) {
						// identificar si sólo hay 1 palabra o más de 1
						$trozos=explode(" ",$texto);
						$numero_palabras=count($trozos);
						if (1 || $numero_palabras==1) {
							//SI SOLO HAY UNA PALABRA DE BUSQUEDA SE ESTABLECE UNA INSTRUCION CON LIKE
							if ($dato_buscar == 'nombre') 
								$condicion .= "AND (nombre LIKE '%$texto%'  OR apellido_paterno LIKE '%$texto%' OR apellido_materno LIKE '%$texto%' )  OR (numero_empleado LIKE '%$texto%')";	
							if ($dato_buscar == 'email') 
								$condicion .= "AND (email LIKE '%$texto%') ";	
							if ($dato_buscar == 'numero_empleado' || $dato_buscar == 'numero_empleado_wp') 
								$condicion .= "AND (cliente.nombre LIKE '%$texto%'  OR apellido_paterno LIKE '%$texto%' OR apellido_materno LIKE '%$texto%' ) OR (numero_empleado LIKE '%$texto%') ";	
						} else  { // más de 1 palabras
							if ($dato_buscar == 'nombre') 
								$condicion .= " AND MATCH ( nombre ) AGAINST ( '$texto' IN BOOLEAN MODE ) ";
							if ($dato_buscar == 'email') 
								$condicion .= " AND MATCH ( email ) AGAINST ( '$texto' IN BOOLEAN MODE ) ";
						} 
					 }

                     // construir la condición de búsqueda

					 if ($dato_buscar == 'nombre') 
						 $query = "SELECT CONCAT(TRIM(nombre),' ',TRIM(apellido_paterno),' ',TRIM(apellido_materno)) AS nombre FROM cliente $condicion GROUP BY nombre HAVING COUNT(*) > 1";
					 if ($dato_buscar == 'email') 
						 $query = "SELECT email FROM cliente $condicion GROUP BY email HAVING COUNT(*) > 1";
					 if ($dato_buscar == 'numero_empleado') 
					 	 $query = "SELECT empresa, numero_empleado FROM cliente $condicion GROUP BY empresa, numero_empleado HAVING COUNT(*) > 1";
					 if ($dato_buscar == 'numero_empleado_wp') 
					 	 $query = "SELECT empresa, numero_empleado FROM cliente LEFT JOIN empresa ON cliente.empresa = empresa.clave $condicion AND empresa_whirlpool = 1 AND empresa.clave<>168 GROUP BY numero_empleado HAVING COUNT(*) > 1";


                       $resultadotot= mysql_query($query,$conexion);
                       $totres = mysql_num_rows ($resultadotot);
                       $totpags = ceil($totres/$ver);
                       if ($totres==0)
                          $numpag = 0;
						  
					 echo 'Total de clientes en la lista: <b>'.$totres.'</b>';
			
			  ?>            </td>
            <td align="right" bgcolor="#BBBBBB">
                <input name="cliente" type="hidden" id="cliente">
            	<input name="accion" type="hidden" id="accion">
                <input name="ord" type="hidden" id="ord" value="<?= $ord; ?>" />
                <input name="numpag" type="hidden" id="numpag" value="<?= $numpag; ?>" />
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
        </table>
        <table width="100%" border="0" align="center" cellpadding="2" cellspacing="1">
          <tr class="texto">
            <td colspan="3" nowrap="nowrap">&nbsp;</td>
          </tr>
          <tr class="texto" bgcolor="#F4F4F2">
            <td nowrap="nowrap"><b><?=strtoupper($dato_buscar);?></b></td>
            <td><div align="center"><strong>Repeticiones</strong></div></td>
            <td><div align="center"><b>Detalles</b></div></td>
          </tr>
          <?
		  
		  	 if ($dato_buscar == 'nombre') 
			  	 $query = "SELECT CONCAT(TRIM(nombre),' ',TRIM(apellido_paterno),' ',TRIM(apellido_materno)) AS nombre , email, count(*) as repeticiones FROM cliente $condicion 
				 			GROUP BY CONCAT(TRIM(nombre),' ',TRIM(apellido_paterno),' ',TRIM(apellido_materno)) HAVING COUNT(*) > 1 LIMIT $regini,$ver";
		  	 if ($dato_buscar == 'email') 
			  	 $query = "SELECT CONCAT(nombre,' ',apellido_paterno,' ',apellido_materno) AS nombre , email, count(*) as repeticiones FROM cliente $condicion GROUP BY email HAVING COUNT(*) > 1 LIMIT $regini,$ver";

			 if ($dato_buscar == 'numero_empleado') 
				 $query = "SELECT CONCAT(empresa.nombre, ' [', numero_empleado,']') AS numero_empleado, empresa, numero_empleado AS no_empleado, count(*) as repeticiones FROM cliente
				 	LEFT JOIN empresa ON cliente.empresa = empresa.clave
					$condicion
				 	 GROUP BY empresa, numero_empleado HAVING COUNT(*) > 1 ORDER BY repeticiones DESC   LIMIT $regini,$ver";

			 if ($dato_buscar == 'numero_empleado_wp') 
				 $query = "SELECT CONCAT('Empresa Whirlpool [', numero_empleado,']') AS numero_empleado_wp, empresa, numero_empleado AS no_empleado, count(*) as repeticiones FROM cliente
				 	LEFT JOIN empresa ON cliente.empresa = empresa.clave
					$condicion AND empresa_whirlpool = 1 AND empresa.clave<>168
				 	 GROUP BY numero_empleado HAVING COUNT(*) > 1 ORDER BY repeticiones DESC  LIMIT $regini,$ver ";

//echo $query;
             $resultado= mysql_query($query,$conexion);
             while ($row = mysql_fetch_array($resultado)){ 
			 	
				$cve_empleado = $row['clave'];
				$cve_empresa=$row['empresa'];
	            $resEMP= mysql_query("SELECT * FROM empresa WHERE clave='$cve_empresa'",$conexion);
                $rowEMP= mysql_fetch_array($resEMP); 
				
				if ($dato_buscar == 'nombre') $param = '?dato_buscar=nombre&dato='.$row['nombre'];
				if ($dato_buscar == 'email') $param = '?dato_buscar=email&dato='.$row['email'];
				if ($dato_buscar == 'numero_empleado') $param = '?dato_buscar=numero_empleado&dato1='.$row['empresa'].'&dato2='.$row['no_empleado'];
				if ($dato_buscar == 'numero_empleado_wp') $param = '?dato_buscar=numero_empleado_wp&dato1=wp&dato2='.$row['no_empleado'];


          ?>
          <tr class="texto" valign="top" bgcolor="<? if ($color) echo '#FFFFFF'; else echo '#F4F4F2'; ?>">
            <td valign="top" bgcolor="#FFFFFF"><?= $row[$dato_buscar]; ?></td>
            <td valign="top" bgcolor="#FFFFFF" align="center"><?=$row['repeticiones'];?></td>
            <td align="center" valign="top" nowrap="nowrap" bgcolor="#FFFFFF"><a href="detalle_repetido.php<?= $param; ?>" rel="shadowbox;width=1220;height=700"><img src="images/foto.png" width="14" height="15" align="absmiddle" alt="Detalles del cliente" /></a>&nbsp;</td>
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
            <td align="right">&nbsp;</td>
          </tr>
        </table>
      </form>    
    </div>
</div>
</body>
</html>
