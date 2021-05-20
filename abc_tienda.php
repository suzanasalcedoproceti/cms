<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=17;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
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


<script language="JavaScript">
  function valida() {
    if (document.forma.nombre.value == "") {
     alert("Falta tienda.");
	 document.forma.nombre.focus();
     return;
     }

    if (document.forma.po_number.value.trim() == "") {
     alert("Indica al menos un PO_NUMBER.");
     document.forma.po_number.value="";
   document.forma.nombre.focus();
     return;
     }

	// combina claves de marcas seleccionadas en un string separado por comas
	var string_ma = '';
	for (var i=0; i < document.forma.lista_marcas.options.length; i++) {
	  string_ma += ' '+document.forma.lista_marcas.options[i].value+',';
	}
	document.forma.marcas.value = string_ma;
 
   document.forma.action='graba_tienda.php';
   document.forma.submit();
  }
  function descarta() {
   document.forma.action='lista_tienda.php';
   document.forma.submit();
  }

  function agregaMa(inForm,texto,valor) {
		var siguiente = inForm.lista_marcas.options.length;
		var encontrado = false;
		for (var i=0; i < inForm.lista_marcas.length; i++) {
			if (inForm.lista_marcas.options[i].value == valor) {
				encontrado = true;
			}
		}
		if (!encontrado) {
			eval("inForm.lista_marcas.options[siguiente]=" + "new Option(texto,valor,false,true)");
		}
  }
  function eliminaMa(inForm,indice) {
		var i = inForm.lista_marcas.options.length;
		inForm.lista_marcas.options[indice] = null;
  }

</script>
</head>

<body>
<div id="container">
	<? $tit='Administrar Tiendas POS'; include('top.php'); ?>
	<?
        include('../conexion.php');

		$tienda=$_POST['tienda'];
		$autorizar=$_GET['autorizar'];
		
		if (empty($tienda)) $tienda=$_GET['tienda'];
        $tienda+=0;
        if (!empty($tienda)) {
          $resultado= mysql_query("SELECT * FROM tienda WHERE clave='$tienda'",$conexion);
          $row = mysql_fetch_array($resultado);
        }

        
      ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">
          <tr>
            <td><div align="right">Activa:</div></td>
            <td><input name="activa" type="checkbox" id="activa" value="1" <? if ($row['activa']==1 OR empty($tienda)) echo 'checked'; ?> /></td>
          </tr>

          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><div align="right">Nombre:</div></td>
            <td><input name="nombre" type="text" class="campo" id="nombre" value="<?= $row['nombre']; ?>" size="60" maxlength="50" />            </td>
          </tr>
          <tr>
            <td><div align="right">Login:</div></td>
            <td><input name="login" type="text" class="campo" id="login" value="<?= $row['login']; ?>" size="11" maxlength="10" <? if (!empty($tienda)) echo 'readonly';?> />            </td>
          </tr>
          <tr>
            <td><div align="right">Solo acepta p&uacute;blico en general:</div></td>
            <td><input name="publico_general" type="checkbox" id="publico_general" value="1" <? if ($row['publico_general']==1) echo 'checked'; ?> /> 
            Empresa asociada:<span class="row1">
            <select name="empresa_asociada" class="campo" id="empresa_asociada">
              <option value="" selected="selected">Cualquier empresa...</option>
              <?
				$resEMP = mysql_query("SELECT clave, nombre FROM empresa WHERE empresa_publica ORDER BY nombre",$conexion);
				while ($rowEMP = mysql_fetch_array($resEMP)) {
				  echo '<option value="'.$rowEMP['clave'].'"';
				  if ($rowEMP['clave']==$row['empresa_asociada']) echo ' selected';
				  echo '>'.$rowEMP['nombre'].'</option>';
				}
			  ?>
            </select>
            </span></td>
          </tr>
          <tr>
            <td><div align="right">Tienda SERVICE:</div></td>
            <td><input name="tienda_service" type="checkbox" id="tienda_service"  <? if ($_SESSION['usr_service']==1) echo 'readonly onclick="javascript: return false;"'; ?> value="1" <? if ($_SESSION['usr_service']==1 || $row['tienda_service']==1) echo 'checked'; ?> /></td>
          </tr>
          <tr>
            <td><div align="right">Listas de precios garant&iacute;as extendidas:</div></td>
            <td><input name="listas_precios_garantias_extendidas" type="checkbox" id="listas_precios_garantias_extendidas"  value="1" <? if ($row['listas_precios_garantias_extendidas']==1) echo 'checked'; ?> /></td>
          </tr>
          <tr>
            <td><strong>PAGOS CON PINPAD</strong></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><div align="right">Activar Pagos con PINPAD:</div></td>
            <td><input name="pagos_pinpad" type="checkbox" id="pagos_pinpad" value="1" <? if ($row['pagos_pinpad']==1) echo 'checked'; ?> /></td>
          </tr>
          <tr>
            <td><div align="right">Modo PINPAD:</div></td>
            <td><select name="modo_pinpad" id="modo_pinpad">
                <option value="T" <? if ($row['modo_pinpad']=='T') echo 'selected';?>>TEST</option>
                <option value="P" <? if ($row['modo_pinpad']=='P') echo 'selected';?>>Producción (cobros)</option>
              </select>            </td>
          </tr>
          <tr>
            <td><div align="right">Puerto serial para PINPAD:</div></td>
            <td><select name="puerto_pinpad" id="puerto_pinpad">
              <option value="COM1" <? if ($row['puerto_pinpad']=='COM1') echo 'selected';?>>COM1</option>
              <option value="COM2" <? if ($row['puerto_pinpad']=='COM2') echo 'selected';?>>COM2</option>
              <option value="COM3" <? if ($row['puerto_pinpad']=='COM3') echo 'selected';?>>COM3</option>
              <option value="COM4" <? if ($row['puerto_pinpad']=='COM4') echo 'selected';?>>COM4</option>
              <option value="COM9" <? if ($row['puerto_pinpad']=='COM9') echo 'selected';?>>COM9</option>
            </select>            </td>
          </tr>
          <tr>
            <td><div align="right"><strong>Descuentos permitidos:</strong></div>
                <div align="right"><br />
                  Una opci&oacute;n por rengl&oacute;n</div></td>
            <td><textarea name="descuentos_permitidos" cols="45" rows="5" class="campo" id="descuentos_permitidos"><?= $row['descuentos_permitidos']; ?></textarea></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr> 
 
          <tr>
            <td><div align="right"><strong>RM Local:</strong></div></td>
            <td>
              <select name="rm_local" id="rm_local">
              <option value="" selected="selected">Seleccione...</option>
               <?
                $resPLT= mysql_query("SELECT * FROM planta  ORDER BY clave",$conexion);
                while ($rowPLT = mysql_fetch_array($resPLT)) {
                  echo '<option value="'.$rowPLT['clave'].'"';
                  if ($rowPLT['planta']==$row['rm_local']) echo ' selected';
                  echo '>'.$rowPLT['planta'].'/'.$rowPLT['loc'].'</option>';
                }
                ?>
            </select>
 
            </td>
          </tr>
          <tr>
            <td><div align="right">Entrega a Domicilio desde Tienda:</div></td>
            <td><input name="entrega_dom_tienda" type="checkbox" id="entrega_dom_tienda" value="1" <? if ($row['entrega_dom_tienda']==1) echo 'checked'; ?> /></td>
          </tr>
          <tr>
            <td><div align="right"><strong>PO_NUMBER:</strong></div>
              <div align="right"><br />
            Una opci&oacute;n por rengl&oacute;n</div></td>
            <td><textarea name="po_number" cols="45" rows="5" class="campo" id="po_number"><?= $row['po_number']; ?></textarea></td>
          </tr>
          <tr>
            <td><div align="right"><strong>Payer RG:</strong></div></td>
            <td><input name="payer_rg" type="text" class="campo" id="payer_rg" value="<?= $row['payer_rg']; ?>" size="30" maxlength="30" /></td>
          </tr>
          <tr>
            <td><div align="right"><strong>Purchase Order Type (po_method):</strong></div></td>
            <td><input name="po_method" type="text" class="campo" id="po_method" value="<?= $row['po_method']; ?>" size="30" maxlength="30" /></td>
          </tr>
          <tr>
            <td valign="top"><div align="right"><strong>Cliente SAP/PayerRG</strong>:</div></td>
            <td><input name="cliente_sap" type="text" class="campo" id="cliente_sap" value="<?= $row['cliente_sap']; ?>" size="20" maxlength="20" />
            <br />
            <strong>Instrucciones:</strong> Aplica solo para empleados de empresas Whirlpool.<br />
            Ingresa el n&uacute;mero de ClienteSAP y PayerRG fijo para esta tienda; o d&eacute;jalo en blanco si quieres que tome <br />
            el ClienteSAP de la empresa en la que est&aacute; registrado y el PayerRG de esta tienda.<br /></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input type="hidden" name="marcas" id="marcas" /></td>
          </tr>
          <tr>
            <td><div align="right"><strong>Marcas Excluidas</strong></div></td>
            <td><table border="0" cellpadding="0" cellspacing="0" class="texto">
              <tr>
                <td><strong>Marcas disponibles:</strong><br />
                  (dbl clic para seleccionar)</td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td><strong>Marcas   seleccionadas:</strong><br />
                  (dbl clic para eliminar de la lista)</td>
              </tr>
              <tr>
                <td><span class="rotulo">
                  <select name="mar_excluidas" size="10" class="campo" id="mar_excluidas" ondblclick="agregaMa(document.forma,this.options[this.selectedIndex].text,this.value);">
                    <?    
					      $CR = chr(10);
						  $query = "SELECT clave, nombre
						  			FROM marca
								   ORDER BY orden, nombre";
								   
					      $resMAR = mysql_query($query,$conexion);
                          while ($rowMAR = mysql_fetch_array($resMAR)) { 
			                echo $CR.'<option value="'.$rowMAR['clave'].'" title="'.$rowMAR['nombre'].'">'.$rowMAR['nombre'].'</option>';
                          }
                      ?>
                  </select>
                </span></td>
                <td>&nbsp;</td>
                <td><select name="lista_marcas" size="10" class="campo" id="lista_marcas" ondblclick="eliminaMa(document.forma,this.selectedIndex);" style="min-width:200px;">
                    <?  
					    $resultadoMO = mysql_query("SELECT marca_omitida.*, marca.nombre FROM marca_omitida LEFT JOIN marca ON marca_omitida.marca = marca.clave
														WHERE tienda = $tienda ORDER BY marca.nombre");
						while ($rowMO = mysql_fetch_array($resultadoMO)) {
						  echo $CR.'<option value="'.$rowMO['marca'].'">'.$rowMO['nombre'].'</option>';
						}
					?>
                </select></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input name="grabar" type="button" class="boton" onclick="valida();" value="GRABAR" />
                <input name="desc" type="button" class="boton" onclick="descarta();" value="REGRESAR AL LISTADO" id="desc" />
            <input name="tienda" type="hidden" id="tienda" value="<?= $tienda; ?>" />            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table>
      </form>    
    </div>
</div>
</body>
</html>
