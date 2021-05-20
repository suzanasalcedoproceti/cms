<?php
// Control de Cambios
// Oct 2016 : B+ : Se agregan meses sin intereses (3,9,10,12,18,24) para NetPay
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=6;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
    include('../conexion.php');

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
    form.action='lista_empresa.php';
    form.submit();
  }
  function ordena(orden) {
    document.forma.ord.value = orden;
	document.forma.numpag.value = 1;
    document.forma.action='lista_empresa.php';
    document.forma.submit();
  }
  function recarga() {
    document.forma.submit();
  }
  function borra(id) {
    document.forma.empresa.value = id;
    document.forma.action='borra_empresa.php';
    document.forma.submit();
    document.forma.action='';
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
function hayChecados(FormName, FieldName) {
	if(!document.forms[FormName])
		return 0;
	var objCheckBoxes = document.forms[FormName].elements[FieldName];
	if(!objCheckBoxes)
		return 0;
	var countCheckBoxes = objCheckBoxes.length;
	if(!countCheckBoxes)
		if (objCheckBoxes.checked) return 1; else return 0;
	else {
		// set the check value for all check boxes
		
		var ich = 0;
		for(var i = 0; i < countCheckBoxes; i++) {
			if (objCheckBoxes[i].checked) ich++;
		}
		return ich;
	}
}
function hayNoChecados(FormName, FieldName) {
  if(!document.forms[FormName])
    return 0;
  var objCheckBoxes = document.forms[FormName].elements[FieldName];
  if(!objCheckBoxes)
    return 0;
  var countCheckBoxes = objCheckBoxes.length;
  if(!countCheckBoxes)
    if (!objCheckBoxes.checked) return 1; else return 0;
  else {
    // set the check value for all check boxes
    
    var ich = 0;
    for(var i = 0; i < countCheckBoxes; i++) {
      if (!objCheckBoxes[i].checked) ich++;
    }
    if(ich>=0) 
      document.forma.sel_boxes.checked=false;
    else
      document.forma.sel_boxes.checked=true;
  }
}
function armaGetChecados(FormName, FieldName) {
	if(!document.forms[FormName])
		return 0;
	var objCheckBoxes = document.forms[FormName].elements[FieldName];
	if(!objCheckBoxes)
		return 0;
	var countCheckBoxes = objCheckBoxes.length;
	if(!countCheckBoxes)
		if (objCheckBoxes.checked) return "&"+objCheckBoxes.name + "=1"; else return '';
	else {
		// set the check value for all check boxes
		
		var ich = 0;
		var vch = '';
		for(var i = 0; i < countCheckBoxes; i++) {
			if (objCheckBoxes[i].checked) {
				ich++;
				vch = vch + "&"+objCheckBoxes[i].name + "=1";
			}
		}
		return vch;
	}
}
function cambiar_fp(forma){
  	var hay = hayChecados('forma','pagos');
	if (hay<=0) {
		alert("Debes seleccionar empresas a modificar su formas de pago");
		return;
	}
	// armar cadena get con items seleccionados.. para pasarlo por get al shadowbox
  	var vget = armaGetChecados('forma','pagos');
//	alert (vget); return;
	Shadowbox.open({ content:'cambia_fp.php?'+vget, player:'iframe', width:450, height:280, options: {displayNav: true, enableKeys: true, modal: true}});
  
}

  function exportar() {
    document.forma.target = '_self';
    document.forma.action='lista_empresa_xls.php';
    document.forma.buscar.value=1;
    document.forma.submit();
  document.forma.target = '_self';
  document.forma.action='';
  }

  function importar() {
    document.forma.target = '_blank';
    document.forma.action='importa_empresa.php';
    document.forma.buscar.value=1;
    document.forma.submit();
  document.forma.target = '_self';
  document.forma.action='';
  }


function disableHandler (form, inputName) {
    var inputs = form.elements[inputName];
    for (var i = 0; i < inputs.length; i++) {
    var input = inputs[i];
    input.onclick = function (evt) {
    if (this.checked) {
    disableInputs(this, inputs);
    }
    else {
    enableInputs(this, inputs);
    }
    return true;
    };
    }
    }

    function disableInputs (input, inputs) {
    for (var i = 0; i < inputs.length; i++) {
    var currentInput = inputs[i];
    if (currentInput != input) {
    currentInput.disabled = true;
    }
    }
    }

    function enableInputs (input, inputs) {
    for (var i = 0; i < inputs.length; i++) {
    var currentInput = inputs[i];
    if (currentInput != input) {
    currentInput.disabled = false;
    }
    }
    }
</script>
</head>

<body>
<div id="container">
  <? $tit='Listado de Empresas'; include('top.php'); ?>
  <?
   $ver = $_POST['ver'];
   $numpag = $_POST['numpag'];
   $ord = $_POST['ord'];
   $texto = $_POST['texto'];
   $ftipo = $_POST['ftipo'];
   $estatus = $_POST['estatus'];
   if (!$texto) $texto = $_GET['texto'];
   if (empty($ver)) $ver='20';
   if (empty($numpag)) $numpag='1';
   if (empty($ord)) $ord='nombre';

   if ($ord=='nombre') $orden='ORDER BY nombre';
   
   
?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td><input name="button" type="button" class="boton_agregar" id="button" value="Agregar empresa nueva" onClick="document.forma.action='abc_empresa.php'; document.forma.submit();" /></td>
            <td align="right">
              <table border="0" align="center" cellpadding="2" cellspacing="0" class="texto">
              <tr>
                <td align="right">Estatus:</td>
                <td><select name="estatus" id="estatus">
                  <option value="" <? if ($estatus=='') echo 'selected';?>>Cualquiera</option>
                  <option value="0" <? if ($estatus=='0') echo 'selected';?>>Inactivo</option>
                  <option value="1" <? if ($estatus=='1') echo 'selected';?>>Activo</option>
                </select></td>
              </tr>              <tr>
                <td align="right">Tipo:</td>
                <td><select name="ftipo" id="ftipo">
                  <option value="" <? if ($ftipo=='') echo 'selected';?>>Cualquiera</option>
                    <?php

                   $resultado = mysql_query('SELECT * FROM cliente_tipo',$conexion);
                   while($row = mysql_fetch_array($resultado)){
                   $selected =  ($ftipo==$row['id']) ? "selected" : "" ;
                   //claves.push("<?php echo strtoupper($row['id']); ");
                    echo "<option value='".$row['id']."' $selected>".$row['nombre']."</option>";
                   }
                   ?>
                </select></td>
              </tr>
              <tr>
                <td width="156" valign="top"><div align="right">Buscar:</div></td>
                <td width="594"><div align="left">
                  <input name="texto" type="text" class="campo" id="texto" value="<?= $texto; ?>" size="50" />
                  <br />
                </div></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td><div align="left">
                  <input name="Submit" type="submit" class="boton" onclick="document.forma.numpag.value=1" value="Buscar" />
                </div></td>
                <td><div align="left">
                  <input name="exp_xls" type="button" class="boton" onclick="javascript:exportar();" value="Descargar Registros" />
                </div></td>
                <td><div align="left">
                  <input name="imp_xls" type="button" class="boton" onclick="javascript:importar();" value="Subir Registros" />
                </div></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td bgcolor="#BBBBBB"><?
			
                  // obtener el total de registros que coinciden...
                  // y establecer algunas variables
					   

                     // construir la condición de búsqueda
            if ($estatus == '') $condicion = " WHERE 1=1 ";
            if ($estatus == '1') $condicion = " WHERE estatus=1 ";
            if ($estatus == '0') $condicion = " WHERE estatus=0 ";

                     // construir la condición de búsqueda
					  
					  if ($ftipo) $condicion .= " AND cliente_tipo_id = '$ftipo' ";

            $condicion .= ($_SESSION['usr_service']==1) ? ' AND empresa_publica = 1 ' : '';
					 
					  if ($texto) $condicion.= " AND nombre LIKE '%$texto%' ";
            $query_busqueda = "SELECT * FROM empresa $condicion";
                       $resultadotot= mysql_query("SELECT * FROM empresa $condicion",$conexion);
                       $totres = mysql_num_rows ($resultadotot);
                       $totpags = ceil($totres/$ver);
                       if ($totres==0)
                          $numpag = 0;
						  
					 echo 'Total de empresas en la lista: <b>'.$totres.'</b>';
			
			  ?>            </td>
            <td align="right" bgcolor="#BBBBBB"><input name="empresa" type="hidden" id="empresa" />
                <input name="ord" type="hidden" id="ord" value="<?= $ord; ?>" />
                <input name="numpag" type="hidden" id="numpag" value="<?= $numpag; ?>" />
                <input name="buscar" type="hidden" id="buscar" />
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
        <table width="100%" border="0" align="center" cellpadding="2" cellspacing="3">
          <tr class="texto">
            <td nowrap="nowrap">&nbsp;</td>
            <td>&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td colspan="5" align="center" bgcolor="#f4f4f2"><strong>Formas de pago disponibles</strong></td>
            <td colspan="7" align="center" bgcolor="#f4f4f2"><strong>TDC msi</strong></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr class="texto" bgcolor="#F4F4F2">
            <td nowrap="nowrap" bgcolor="<? if($ord=='nombre') echo '#DDDDDD'; ?>"><b><a href="javascript:ordena('nombre');" class="texto">Empresa <img src="images/orden.png" width="14" height="15" border="0" align="absmiddle" /></a></b></td>
            <td><div align="center"><b>Tipo de Cliente</b></div></td>
            <td><div align="left"><strong>Dominios</strong></div></td>
            <td><div align="center"><b>Tarjetas [Usadas/Sin usar]</b></div></td>
            <td><div align="center"><b>Clientes</b></div></td>
            <td bgcolor="#F4F4F2"><div align="center"><strong>Lista de Precios WEB</strong></div></td>
            <td bgcolor="#F4F4F2"><div align="center"><strong>Lista de Precios POS</strong></div></td>
            <td align="center">CLABE</td>
            <td align="center">Cheque</td>
            <td align="center">CEP</td>
            <td align="center" nowrap="nowrap">ODC</td>
            <td nowrap="nowrap">Puntos</td>
            <td align="center" nowrap="nowrap">3</td>
            <td align="center" nowrap="nowrap">6</td>
            <td align="center" nowrap="nowrap">9</td>
            <td align="center" nowrap="nowrap">10</td>
            <td align="center" nowrap="nowrap">12</td>
            <td align="center" nowrap="nowrap">18</td>
            <td align="center" nowrap="nowrap">24</td>
            <td align="center" nowrap="nowrap"><strong>Seleccione</strong></td>
            <td><div align="center"><b>Acci&oacute;n</b></div></td>
            <td><div align="center"><b>Estatus</b></div></td>
          </tr>
          <?

             $resultado= mysql_query("SELECT * FROM empresa $condicion $orden LIMIT $regini,$ver",$conexion);
             while ($row = mysql_fetch_array($resultado)){ 

				$empresa= $row['clave'];
			    $resCLI= mysql_query("SELECT 1 FROM cliente WHERE empresa=$empresa",$conexion);
			    $clientes = mysql_num_rows($resCLI);
			    
				$resTAR= mysql_query("SELECT 1 FROM tarjeta WHERE empresa=$empresa",$conexion);
			    $tarjetas = mysql_num_rows($resTAR);

                $resTAR= mysql_query("SELECT COUNT(*) AS tarjetas FROM tarjeta WHERE empresa='$empresa'",$conexion);
                $rowTAR= mysql_fetch_array($resTAR);

                $resTAR2= mysql_query("SELECT COUNT(*) AS tarjetas_usadas FROM tarjeta WHERE empresa='$empresa' AND cliente>0",$conexion);
                $rowTAR2= mysql_fetch_array($resTAR2);

                $resTAR3= mysql_query("SELECT COUNT(*) AS tarjetas_sinusar FROM tarjeta WHERE empresa='$empresa' AND cliente=0",$conexion);
                $rowTAR3= mysql_fetch_array($resTAR3);
                
                $tipo_cliente= $row['cliente_tipo_id'];
				        $resTC= mysql_query("SELECT nombre FROM cliente_tipo WHERE id='$tipo_cliente'",$conexion);
                $rowTC= mysql_fetch_array($resTC);

          ?>
          <tr class="texto" valign="top" bgcolor="<? if ($color) echo '#FFFFFF'; else echo '#F4F4F2'; ?>">
            <td bgcolor="#FFFFFF"><?= $row['nombre']; ?></td>
            <td bgcolor="#FFFFFF"><div align="center">
                <?= $rowTC['nombre']; ?>
            </div></td>
            <td bgcolor="#FFFFFF"><?=$row['dominio'];?>            </td>
            <td bgcolor="#FFFFFF"><div align="center">
                <?= $rowTAR['tarjetas'].' ['.$rowTAR2['tarjetas_usadas'].'/'.$rowTAR3['tarjetas_sinusar'].']'; ?>
            </div></td>
            <td bgcolor="#FFFFFF"><div align="center"><?= $clientes; ?></div></td>
            <td bgcolor="#FFFFFF"><div align="center">
              <?=$row['lista_precios'];?>            
            </div></td>
            <td bgcolor="#FFFFFF"><div align="center">
              <?=$row['lista_precios_pos'];?>            
            </div></td>
            
            <td align="center" bgcolor="#FFFFFF"><? if ($row['pago_debito']) echo '<img src="images/tick.png"'; ?></td>
            
            <td align="center" bgcolor="#FFFFFF"><? if ($row['pago_cheque']) echo '<img src="images/tick.png"'; ?></td>
            
            <td align="center" bgcolor="#FFFFFF"><? if ($row['pago_cep']) echo '<img src="images/tick.png"'; ?></td>
            <td bgcolor="#FFFFFF" align="center"><? if ($row['pago_odc']) echo '<img src="images/tick.png"'; ?></td>
            
            <td bgcolor="#FFFFFF" align="center"><? if ($row['puntos']) echo '<img src="images/tick.png"'; ?></td>
            <td align="center" bgcolor="#FFFFFF"><? if ($row['msi03']) echo '<img src="images/tick.png"'; ?></td>
            <td align="center" bgcolor="#FFFFFF"><? if ($row['msi06']) echo '<img src="images/tick.png"'; ?></td>
            <td align="center" bgcolor="#FFFFFF"><? if ($row['msi09']) echo '<img src="images/tick.png"'; ?></td>
            <td align="center" bgcolor="#FFFFFF"><? if ($row['msi10']) echo '<img src="images/tick.png"'; ?></td>
            <td align="center" bgcolor="#FFFFFF"><? if ($row['msi12']) echo '<img src="images/tick.png"'; ?></td>
            <td align="center" bgcolor="#FFFFFF"><? if ($row['msi18']) echo '<img src="images/tick.png"'; ?></td>
            <td align="center" bgcolor="#FFFFFF"><? if ($row['msi24']) echo '<img src="images/tick.png"'; ?></td>
            <td bgcolor="#FFFFFF" align="center"><input type="checkbox" name="pagos_<?=$row['clave'];?>" id="pagos" onclick="hayChecados('forma','pagos');"/></td>
            
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF">
               	<? if ($row['estatus']==1 || $row['estatus']==0) { ?><a href="abc_empresa.php?empresa=<?= $row['clave']; ?>&texto=<?=$texto;?>"><img src="images/editar.png" alt="Editar Empresa" width="14" height="16" border="0" align="absmiddle" /></a><? } else  { ?><img src="images/editar_off.png" width="14" height="16" align="absmiddle" /><? } ?>
       	  		<? if ($clientes<=0 AND $tarjetas<=0 AND op_aut($modulo) AND $row['estatus']==1) { ?><a onclick="return confirm('¿Estás seguro que deseas\nBorrar la Empresa?')" href="javascript:borra('<?= $row['clave']; ?>');"><img src="images/borrar.png" alt="Borrar Empresa" width="14" height="15" border="0" align="absmiddle" /></a><? } else  { ?><img src="images/borrar_off.png" width="14" height="15" align="absmiddle" /><? } ?></td>
            <td align="center" bgcolor="#FFFFFF"><? if ($row['estatus']) echo 'Activo'; else echo 'Inactivo'; ?></td>

		  </tr>

              <?
                 } // WHILE
                 mysql_close();
              ?>
          <tr class="texto">
            <td colspan="6" >&nbsp;</td>
            <td colspan="13" align="right" bgcolor="#FFFFFF">
            <input name="bcam" type="button" class="boton" value="Cambiar Formas de Pago" id="bcanb" style="font-weight:normal" onclick="javascript:cambiar_fp(document.forma);" /></td>
            <td>&nbsp;</td>
          </tr>
        </table>
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td align="right">&nbsp;</td>
          </tr>
          <tr>
            <td align="right" bgcolor="#BBBBBB"><?


                     $regini = ($numpag * $ver) - $ver;
					 if ($regini<0) $regini=0;

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
              ?>            </td>
          </tr>
          <tr>
            <td align="right">&nbsp;</td>
          </tr>
        </table>
      </form>  
      <script type="text/javascript">
    disableHandler(document.forms.forma, 'pagos');
    </script>  
    </div>
</div>
</body>
</html>
