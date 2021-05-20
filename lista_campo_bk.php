<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');

	$modulo=5;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
    include("../conexion.php");  

	$buscar = $_POST['buscar'];
	$accion = $_POST['accion'];
	$campo = $_POST['campo'];
	$categoria = $_POST['categoria'];
	$subcategoria = $_POST['subcategoria'];
	
	$xorden = $_POST['xorden']+0;
	$xgrupo = $_POST['xgrupo']+0;
	$xnombre_campo = $_POST['xnombre_campo'];
	$xcampo_tabla = $_POST['xcampo_tabla'];
	$xtipo = $_POST['xtipo'];
	//$xvalores = mysql_real_escape_string($_POST['xvalores']);
	$xvalores = limpia_comillas($_POST['xvalores']);
	$xfiltro = $_POST['xfiltro']+0;
	$xcomparativo = $_POST['xcomparativo']+0;
	
//	include("_checa_vars.php");

	// acciones específicas de campos
	if ($accion == 'inserta') {
		// detectar que no se repita el valor del campo_tabla en caso de F5 
  		if ($subcategoria == 'x') $subcategoria = 0;
		$resultadoCAM = mysql_query("SELECT 1 FROM campo WHERE categoria = $categoria AND subcategoria = $subcategoria AND campo_tabla = '$xcampo_tabla'",$conexion);
		$enc = mysql_num_rows($resultadoCAM);
		if ($enc<=0) {
			$query = "INSERT campo (categoria, subcategoria, orden, grupo, nombre_campo, campo_tabla, tipo, valores, es_filtro, es_comparable)
						VALUES ($categoria, $subcategoria, $xorden, $xgrupo, '$xnombre_campo', '$xcampo_tabla', '$xtipo', '$xvalores', $xfiltro, $xcomparativo)";
			$resultadoI = mysql_query($query,$conexion);

		}
	}
	
	if ($accion == 'edita') {
	
		$resultado = mysql_query("SELECT * FROM campo WHERE id = $campo",$conexion);
		$row = mysql_fetch_array($resultado);
	}
	
	if ($accion == 'graba') {
		
		// detectar que no se repita el valor del campo_tabla en caso de F5 
		$query = "UPDATE campo SET orden = $xorden, grupo=$xgrupo, nombre_campo='$xnombre_campo', campo_tabla= '$xcampo_tabla', 
								   tipo='$xtipo', valores='$xvalores', es_filtro=$xfiltro, es_comparable=$xcomparativo,
								   act = 1-act
					WHERE id = $campo LIMIT 1";
		$resultadoI = mysql_query($query,$conexion);
	}

	if ($accion == 'borra') {
		$resultadoCAM = mysql_query("DELETE FROM campo WHERE id = $campo LIMIT 1",$conexion);	
	}

	/// ajax //////
	require ('./xajax/xajax_core/xajax.inc.php');
	$xajax = new xajax(); 
	$xajax->register(XAJAX_FUNCTION, 'cambia_orden');
	$xajax->register(XAJAX_FUNCTION, 'cambia_grupo');
	$xajax->processRequest(); 
	$xajax->configure('javascript URI','xajax/'); 
	
	function cambia_orden($campo,$orden) {
		$objResponse = new xajaxResponse();
		$objResponse->setCharacterEncoding('ISO-8859-1');
	    include('../conexion.php');
		$query = "UPDATE campo SET orden = $orden, act = 1-act WHERE id = $campo LIMIT 1";
		$resultadoI = mysql_query($query,$conexion);
		return $objResponse;   
	}
	function cambia_grupo($campo,$grupo) {
		$objResponse = new xajaxResponse();
		$objResponse->setCharacterEncoding('ISO-8859-1');
	    include('../conexion.php');
		$grupo+=0;
		$query = "UPDATE campo SET grupo = $grupo, act = 1-act WHERE id = $campo LIMIT 1";
		$resultadoI = mysql_query($query,$conexion);
		return $objResponse;   
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
<script type="text/javascript" src="js/jquery.js"></script>

<script language="JavaScript">
  function valida_sel() {
    if (document.forma.categoria.value == "") {
		alert("Selecciona Categoría");
		document.forma.categoria.focus();
		return;
	}
    if (document.forma.subcategoria.value == "") {
		alert("Selecciona Subcategoría");
		document.forma.subcategoria.focus();
		return;
	}
	document.forma.buscar.value='1';
	document.forma.submit();
  }
  function graba(inserta) {
  	if (document.forma.xorden.value == "") {
		alert("Indica en qué orden en la ficha de producto quieres que aparezca este campo");
		document.forma.xorden.focus();
		return;
	}
  	if (document.forma.xnombre_campo.value == "") {
		alert("Captura el nombre del campo o característica del producto.  Ejemplo: Peso Total");
		document.forma.xnombre_campo.focus();
		return;
	}
	if (document.forma.xtipo.value=='menu' && document.forma.xvalores.value == "") {
		alert("Para tipo Menú debes ingresar los valores posibles (uno por renglón)");
		document.forma.xvalores.focus();
		return;
	}
	if (inserta==1)
	  	document.forma.accion.value = 'inserta';
	else
	  	document.forma.accion.value = 'graba';	
	document.forma.buscar.value='1';
    document.forma.action='lista_campo.php';
    document.forma.submit();
  }
	
  function edita(id) {
    document.forma.campo.value = id;
  	document.forma.accion.value = 'edita';
	document.forma.nuevo.value = '0';
	document.forma.buscar.value='1';
    document.forma.action='lista_campo.php';
    document.forma.submit();
  }
  function borra(id) {
    document.forma.campo.value = id;
	document.forma.buscar.value = '1';
  	document.forma.accion.value = 'borra';
    document.forma.action='lista_campo.php';
    document.forma.submit();
  }
</script>
<?php 
 $xajax->printJavascript("xajax/"); 
?>
</head>

<body>
<div id="container">
  <? $tit='Administración de Campos'; include('top.php'); ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <input type="hidden" name="buscar" value="<?=$buscar;?>" id="buscar" />
        <input type="hidden" name="accion" id="accion" />
        <input type="hidden" name="campo" id="campo" value="<?=$campo;?>" />
        <input type="hidden" name="nuevo" id="nuevo" value="1"/>
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td><table width="500" border="0" align="center" cellpadding="2" cellspacing="0" class="texto">
              <tr>
                <td width="156"><div align="right">Categoría:</div></td>
                <td width="594"><select name="categoria" class="campo" id="categoria" onchange="document.forma.buscar.value='0'; document.forma.submit()">
                  <option value="">Seleccionar categor&iacute;a...</option>
                  <?  $resCAT= mysql_query("SELECT * FROM categoria ORDER BY orden, nombre",$conexion);
					  while ($rowCAT = mysql_fetch_array($resCAT)) { 
						echo '<option value="'.$rowCAT['clave'].'"';
						if ($rowCAT['clave']==$categoria) echo ' selected';
						echo '>'.$rowCAT['nombre'].'</option>';
					  }
				  ?>
                </select></td>
              </tr>
              <tr>
                <td><div align="right">Subcategor&iacute;a:</div></td>
                <td>
                  <select name="subcategoria" class="campo" id="subcategoria" onchange="document.forma.submit()">
                <? // detectar si tiene subcategorias 
				   $resSCAT = mysql_query ("SELECT 1 FROM subcategoria WHERE categoria = $categoria",$conexion);
				   $totSCAT = mysql_num_rows($resSCAT);
				   if ($totSCAT > 0 || !$categoria) {
				?>
                  <option value="">Seleccionar subcategor&iacute;a...</option>
                  <?  
				      $query = "SELECT * FROM subcategoria WHERE categoria = $categoria ORDER BY orden, nombre";
					  $resSCAT = mysql_query($query,$conexion);
					  while ($rowSCAT = mysql_fetch_array($resSCAT)) { 
						echo '<option value="'.$rowSCAT['clave'].'"';
						if ($rowSCAT['clave']==$subcategoria) echo ' selected';
						echo '>'.$rowSCAT['nombre'].'</option>';
					  }
				   } else { // categoria sin subcategorias ?>
                    <option value="x" selected>Sin subcategorías</option>
				  <? } ?>
                </select></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td><input name="Submit" type="button" class="boton" value="Seleccionar" onclick="valida_sel();" /></td>
              </tr>
            </table></td>
          </tr>
          <? if ($buscar) { ?>
          <tr>
            <td>
               <table width="100%" border="0" cellspacing="2" cellpadding="2">
                  <tr>
                    <th bgcolor="#F4F4F2" class="texto">Orden</th>
                    <th bgcolor="#F4F4F2" class="texto">Grupo</th>
                    <th bgcolor="#F4F4F2" class="texto">Nombre de campo (Caracter&iacute;stica)</th>
                    <th bgcolor="#F4F4F2" class="texto">Campo en tabla</th>
                    <th bgcolor="#F4F4F2" class="texto">Tipo</th>
                    <th bgcolor="#F4F4F2" class="texto"><p>Valores (solo para tipo menu)<br /> 
                      1 valor por rengl&oacute;n</p>                    </th>
                    <th bgcolor="#F4F4F2" class="texto">Es Filtro?</th>
                    <th bgcolor="#F4F4F2" class="texto">Comparativo?</th>
                    <th class="texto">&nbsp;</th>
                    <th class="texto">&nbsp;</th>
                  </tr>
                  <tr>
                    <td align="center" valign="top"><input name="xorden" type="text" id="xorden" size="3" maxlength="3" value="<?=$row['orden'];?>" /></td>
                    <td align="center" valign="top">
                    <select name="xgrupo" size="1" id="xgrupo">
                        <option value="0" selected="selected"></option>
                        <?
                        $resultadoGPO = mysql_query("SELECT clave, nombre FROM grupo_caracteristica ORDER BY orden, nombre",$conexion);
                        while ($rowGPO = mysql_fetch_array($resultadoGPO)){
                          echo '<option value="'.$rowGPO['clave'].'"';
                          if ($rowGPO['clave']==$row['grupo']) echo 'selected';
                          echo '>'.$rowGPO['nombre'].'</option>';
                        }
                      ?>
                    </select>
                    </td>
                    <td align="center" valign="top"><input name="xnombre_campo" type="text" id="xnombre_campo" size="40" maxlength="50" value="<?=$row['nombre_campo'];?>" /></td>
                    <td align="center" valign="top">
                      <select name="xcampo_tabla" size="1">
                        <?  for ($i = 1; $i<=40; $i++) {
                                $campo_buscar = "campo_".$i;
                                $query = "SELECT campo_tabla FROM campo WHERE categoria = $categoria AND subcategoria = $subcategoria AND campo_tabla = '$campo_buscar' LIMIT 1";
                                $resultadoCE = mysql_query($query,$conexion);
                                $encontrado = mysql_num_rows($resultadoCE);
								// si ya fue utilizado, se deshabilita, a menos que sea edición...
                                if (($encontrado <= 0 || ($encontrado > 0 && $accion == 'edita' && $row['campo_tabla'] == 'campo_'.$i))) {  
                                    echo '<option value="campo_'.$i.'"';
									if ($row['campo_tabla'] == 'campo_'.$i) echo ' selected ';
                                    echo'>campo_'.$i.'</option>';
								} else {

                                    echo '<optgroup label="'.$campo_buscar.'"></optgroup>';
                                }
                             }
                      ?>
                      </select>            </td>
                    <td align="center" valign="top">
                      <select name="xtipo" size="1" id="xtipo">
                        <option value="texto" <? if ($row['tipo'] == 'texto') echo 'selected';?>>Texto</option>
                        <option value="menu" <? if ($row['tipo'] == 'menu') echo 'selected';?>>Men&uacute;</option>
                      </select>            </td>
                    <td align="center"><textarea name="xvalores" cols="35" rows="3" class="campo" id="xvalores"><?=$row['valores'];?></textarea>            </td>
                    <td align="center" valign="top"><select name="xfiltro" size="1" id="xfiltro">
                      <option value="0" selected>NO</option>
                      <option value="1" <? if ($row['es_filtro'] == '1') echo 'selected';?>>SI</option>
                    </select></td>
                    <td align="center" valign="top"><select name="xcomparativo" size="1" id="xcomparativo">
                        <option value="1" <? if ($row['es_comparable'] == '1') echo 'selected';?>>SI</option>
                        <option value="0" <? if ($row['es_comparable'] == '0') echo 'selected';?>>NO</option>
                    </select></td>
                    <td valign="top">&nbsp;</td>
                    <td valign="top"><input name="button" type="button" class="boton_agregar" id="button" value="<? if ($accion!='edita') echo 'Agregar'; else echo 'Grabar';?>" onclick="graba(<? if ($accion!='edita') echo '1'; else echo '0';?>);" /></td>
                  </tr>
              </table>
            </td>
          </tr>
          <? } ?>
        </table>
        
        <? if ($buscar) { ?>
        <table width="100%" border="0" align="center" cellpadding="2" cellspacing="3">
          <tr class="texto">
            <td nowrap="nowrap">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr class="texto" bgcolor="#F4F4F2">
            <td align="center" nowrap="nowrap" class="texto"><strong>Orden</strong></td>
            <td nowrap="nowrap" class="texto"><strong>Grupo</strong></td>
            <td nowrap="nowrap" class="texto"><strong>Nombre campo</strong></td>
            <td><strong>Campo en tabla</strong></td>
            <td><strong>Tipo</strong></td>
            <td><strong>Valores</strong></td>
            <td><strong>Filtro?</strong></td>
            <td><strong>Comparativo?</strong></td>
            <td><div align="center"><b>Acci&oacute;n</b></div></td>
          </tr>
          <?
		  	$sig_campo = 1;
	  		 if ($subcategoria == 'x') $subcategoria = 0;
             $resultado= mysql_query("SELECT * FROM campo WHERE categoria = $categoria AND subcategoria = $subcategoria ORDER BY orden",$conexion);
             while ($row = mysql_fetch_array($resultado)){ 
				$sig_campo = $row['orden']+1;
          ?>
          <tr class="texto" valign="top">
            <td align="center" bgcolor="#FFFFFF"><input type="text" class="campo" value="<?= $row['orden']; ?>" size="3" maxlength="3"  onchange="xajax_cambia_orden(<?=$row['id'];?>,this.value);"/></td>
            <td bgcolor="#FFFFFF"><select name="grupo" size="1" id="grupo" class="campo" onchange="xajax_cambia_grupo(<?=$row['id'];?>,this.value);">
              <option value="0" selected="selected"></option>
              <?
                        $resultadoGPO = mysql_query("SELECT clave, nombre FROM grupo_caracteristica ORDER BY orden, nombre",$conexion);
                        while ($rowGPO = mysql_fetch_array($resultadoGPO)){
                          echo '<option value="'.$rowGPO['clave'].'"';
                          if ($rowGPO['clave']==$row['grupo']) echo 'selected';
                          echo '>'.$rowGPO['nombre'].'</option>';
                        }
                      ?>
            </select>
			</td>
            <td bgcolor="#FFFFFF"><?= $row['nombre_campo']; ?></td>
            <td bgcolor="#FFFFFF"><?= $row['campo_tabla']; ?></td>
            <td bgcolor="#FFFFFF"><?= $row['tipo']; ?></td>
            <td bgcolor="#FFFFFF"><?= str_replace(chr(10),'<br>',$row['valores']); ?></td>
            <td bgcolor="#FFFFFF"><?= ($row['es_filtro']) ? 'SI' : ''; ?></td>
            <td bgcolor="#FFFFFF"><?= ($row['es_comparable']) ? 'SI' : ''; ?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF">
                <a href="javascript:edita(<?= $row['id']; ?>)"><img src="images/editar.png" alt="Editar Campo" width="14" height="16" border="0" align="absmiddle" /></a>
       	  		<a onclick="return confirm('¿Estás seguro que deseas borrar el campo ?')" href="javascript:borra('<?= $row['id']; ?>');">
       	  		<img src="images/borrar.png" alt="Borrar campo" width="14" height="15" border="0" align="absmiddle" /></a>            </td>
		  </tr>
          <?
                 } // WHILE
                 mysql_close();
              ?>
        </table>
        <script language="javascript1.2">
			if (document.forma.xorden.value=="") {
				document.forma.xorden.value="<?=$sig_campo;?>";
				document.forma.xnombre_campo.focus();
			}
		</script>
        <? } ?>
      </form>    
    </div>
</div>
</body>
</html>
