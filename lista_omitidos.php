<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=4;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este m&oacute;dulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
	$empresa = $_POST['empresa'];
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Panel de Control</title>
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<link href="css/menu.css" rel="stylesheet" type="text/css" />
<!--[if lte IE 6]><link rel="stylesheet" href="css/menu_ie6.css" /><![endif]-->
<script type="text/javascript" src="js/menu.js"></script>
<script type="text/javascript" src="js/jquery.js"></script>


<script language="JavaScript">
  function ir(form, Pag) {
    form.numpag.value = Pag;
    form.action='lista_omitidos.php';
    form.submit();
  }
  function ordena(orden) {
    document.forma.ord.value = orden;
	document.forma.numpag.value = 1;
    document.forma.action='lista_omitidos.php';
    document.forma.submit();
  }
  function borra(empresa,producto) {
    document.forma.producto.value = producto;
	document.forma.accion.value='elimina';
    document.forma.action='lista_omitidos.php';
    document.forma.submit();
  }
  function valida_bus() {
  	if (document.forma.busqueda.value=="") {
		alert("Debes ingresar el modelo del producto");
		document.forma.busqueda.focus();
		return;
	}
	document.forma.accion.value = 'agrega';
	document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
  <? $tit='Listado de Productos omitidos por empresa'; include('top.php'); ?>
  <?
   $ver = $_POST['ver'];
   $numpag = $_POST['numpag'];
   $ord = $_POST['ord'];
   $accion = $_POST['accion'];
   $busqueda = $_POST['busqueda'];
   $producto = $_POST['producto'];
   if (empty($ver)) $ver='20';
   if (empty($numpag)) $numpag='1';
   if (empty($ord)) $ord='codigo';

   if     ($ord=='codigo') $orden='ORDER BY modelo';
   elseif ($ord=='nombre') $orden='ORDER BY nombre';
   
   include('../conexion.php');
   
   // agregar modelo a empresa
   
   if ($accion == 'agrega' && $busqueda && $empresa) {
		
		// obtener clave del producto de acuerdo al modelo
		
		$resultado = mysql_query("SELECT clave FROM producto WHERE modelo = '$busqueda' LIMIT 1",$conexion);
		$row = mysql_fetch_array($resultado);
		$clave_producto = $row['clave'];
		
		if (!$clave_producto) {
			$mensaje = "Ese modelo de producto no existe";
		} else {
			$query = "INSERT producto_omitido (empresa, producto) VALUES ($empresa, $clave_producto)";
			
			$resultado = mysql_query($query,$conexion);
			$ins = mysql_affected_rows();
			if ($ins <= 0){
				$mensaje = "Ese producto ya se había agregado al listado para esta empresa<br>";
			} else {
				$mensaje = "Producto agregado";
			}
		}   
   
   }
   if ($accion == 'elimina' && $empresa && $producto) {
		
		// obtener clave del producto de acuerdo al modelo
		
		$query = "DELETE FROM producto_omitido WHERE empresa = $empresa AND producto = $producto LIMIT 1";
		
		$resultado = mysql_query($query,$conexion);
		$ins = mysql_affected_rows();
		if ($ins > 0){
			$mensaje = "Producto eliminado de la lista";
		}
   
   }
   
?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <input name="ord" type="hidden" id="ord" value="<?= $ord; ?>" />
        <input name="numpag" type="hidden" id="numpag" value="<?= $numpag; ?>" />
        <input name="accion" type="hidden" id="accion" value="" />
        <input name="producto" type="hidden" id="producto" />
        <input type="hidden" name="texto_buscar" id="texto_buscar" />

      
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td colspan="2"><table width="500" border="0" cellpadding="2" cellspacing="0" class="texto">
              <tr>
                <td width="156"><div align="right">Empresa:</div></td>
                <td width="594"><select name="empresa" class="campo" id="empresa">
                  <option value="">Selecciona la empresa...</option>
                  <?  include('../conexion.php');
			    $resEMP= mysql_query("SELECT * FROM empresa WHERE (estatus=1 OR estatus=2) ORDER BY nombre",$conexion);
				while ($rowEMP = mysql_fetch_array($resEMP)) { 
					echo '<option value="'.$rowEMP['clave'].'"';
					if ($rowEMP['clave'] == $empresa) echo ' selected ';
					echo '>'.$rowEMP['nombre'].'</option>';
						  }
					  ?>
                </select></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td><input name="Submit" type="submit" class="boton" onclick="document.forma.numpag.value=1" value="Buscar" /></td>
              </tr>
            </table></td>
          </tr>
        </table>
          
        <? if ($empresa) { ?>
		<table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td bgcolor="#BBBBBB"><?
			
                  // obtener el total de registros que coinciden...
                  // y establecer algunas variables
					   

                     // construir la condición de búsqueda
                     $condicion = "WHERE empresa = $empresa ";

                     // construir la condición de búsqueda
                       $resultadotot= mysql_query("SELECT * FROM producto_omitido $condicion",$conexion);
                       $totres = mysql_num_rows ($resultadotot);
                       $totpags = ceil($totres/$ver);
                       if ($totres==0)
                          $numpag = 0;
						  
					 echo 'Total de productos en la lista: <b>'.$totres.'</b>';
			
			  ?></td>
            <td align="right" bgcolor="#BBBBBB">
                <?


                     $regini = ($numpag * $ver) - $ver;
					 if ($regini<0) $regini=0;

                     // poner flechitas anterior, primero, último, etc.
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
                         echo '<a href="javascript:ir(document.forma,'.($numpag+1).');"><img src="images/siguiente_on.gif" border="0" width="13" height="11" align="absmiddle" alt="Siguiente página"></a>&nbsp;';
                         echo '<a href="javascript:ir(document.forma,'.$totpags.');"><img src="images/ultima_on.gif" border="0" width="16" height="11" align="absmiddle" alt="Última p&aacute;gina"></a>';
                     }
              ?>            </td>
          </tr>
        </table>
        <table width="100%" border="0" align="center" cellpadding="2" cellspacing="3">
          <tr class="texto">
            <td colspan="6" nowrap="nowrap"><strong>Agregar Producto</strong></td>
          </tr>
          <tr class="texto">
            <td colspan="6" nowrap="nowrap">

            Modelo: <!--onkeyup="xajax_autocomplete(this.value);"-->
                <input name="busqueda" type="text" id="busqueda" onblur="document.forma.texto_buscar.value = this.value;" 
        onkeypress="if (event.keyCode==13) {  document.forma.texto_buscar.value = this.value; document.forma.submit(); }" value="<? echo $texto_buscar;?>"/>
               
                <input name="bus2" type="button" class="boton" onclick="valida_bus();" value="Agregar" id="bus2" /> 
                <?=$mensaje;?>
            </td>
          </tr>
          <tr class="texto" bgcolor="#F4F4F2">
            <td nowrap="nowrap"><b>Modelo </b></td>
            <td nowrap="nowrap"><b>Nombre</b></td>
            <td><strong>Categor&iacute;a</strong></td>
            <td><strong>Subcategor&iacute;a</strong></td>
            <td><strong>Marca</strong></td>
            <td><div align="center"><b>Acci&oacute;n</b></div></td>
          </tr>
          <?

			 $query = "SELECT producto_omitido.*, producto.*
			 			FROM producto_omitido 
						LEFT JOIN producto ON producto_omitido.producto = producto.clave
						$condicion 
						ORDER BY modelo LIMIT $regini,$ver";
             $resultado= mysql_query($query,$conexion);
             while ($row = mysql_fetch_array($resultado)){ 

				 $cata=$row['categoria'];
			     $resCATA= mysql_query("SELECT * FROM categoria WHERE clave=$cata",$conexion);
			     $rowCATA= mysql_fetch_array($resCATA);

				 $cate=$row['subcategoria'];
			     $resCATE= mysql_query("SELECT * FROM subcategoria WHERE clave=$cate",$conexion);
			     $rowCATE= mysql_fetch_array($resCATE);

				 $marca=$row['marca'];
			     $resMAR= mysql_query("SELECT * FROM marca WHERE clave=$marca",$conexion);
			     $rowMAR= mysql_fetch_array($resMAR);


          ?>
          <tr class="texto" valign="top" bgcolor="<? if ($color) echo '#FFFFFF'; else echo '#F4F4F2'; ?>">
            <td bgcolor="#FFFFFF"><?= $row['modelo']; ?></td>
            <td bgcolor="#FFFFFF"><?= $row['nombre']; ?></td>
            <td bgcolor="#FFFFFF"><?= $rowCATA['nombre']; ?></td>
            <td bgcolor="#FFFFFF"><?= $rowCATE['nombre']; ?></td>
            <td bgcolor="#FFFFFF"><?= $rowMAR['nombre']; ?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><a onclick="return confirm('¿Estás seguro que deseas\neliminar el producto de la lista?')" href="javascript:borra(<?=$empresa;?>,<?= $row['producto']; ?>);"><img src="images/borrar.png" alt="Borrar Producto"  border="0" align="absmiddle" /></a>            </td>
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
        <? }  // isste empresa?>
        
         </form>
          
    </div>
</div>
</body>
</html>
