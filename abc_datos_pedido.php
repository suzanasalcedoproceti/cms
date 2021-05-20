<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=14;
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
  function isEmail(string) {
    if (string.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) != -1)
        return true;
    else
        return false;
  }
  function valida() {
   if (!isEmail(document.forma.email_contacto.value)) {
   		alert ("Correo inválido");
		document.forma.email_contacto.focus();
		return;
   }
   document.forma.action='graba_datos_pedido.php';
   document.forma.submit();
  }
  function descarta() {
   document.forma.action='principal.php';
   document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
	<? $tit='Cargar datos fijos para exportar pedidos y envio de correo'; include('top.php'); ?>
	<?
        include('../conexion.php');
		
        $resultado= mysql_query("SELECT * FROM datos_pedido",$conexion);
        $row = mysql_fetch_array($resultado);

        $resultado= mysql_query("SELECT * FROM mail ",$conexion);
        $rowM = mysql_fetch_array($resultado);

        $resultado= mysql_query("SELECT * FROM config WHERE reg = 1",$conexion);
        $rowCFG = mysql_fetch_array($resultado);
        
        
      ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">
          <tr>
            <td colspan="3"><strong>DESTINATARIO PARA MAILS DE CONTACTO</strong></td>
          </tr>
          <tr>
            <td><div align="right">Nombre:</div></td>
            <td colspan="2"><input name="nombre_contacto" type="text" class="campo" id="nombre_contacto" value="<?= $rowM['nombre_contacto']; ?>" size="50" maxlength="50" />            </td>
          </tr>
          <tr>
            <td><div align="right">Correo electr&oacute;nico:</div></td>
            <td colspan="2"><input name="email_contacto" type="text" class="campo" id="email_contacto" value="<?= $rowM['email_contacto']; ?>" size="80" maxlength="100" />            </td>
          </tr>
          <tr>
            <td><div align="right">Nombre 2:</div></td>
            <td colspan="2"><input name="nombre_contacto2" type="text" class="campo" id="nombre_contacto2" value="<?= $rowM['nombre_contacto2']; ?>" size="50" maxlength="50" />            </td>
          </tr>
          <tr>
            <td><div align="right">Correo electr&oacute;nico 2:</div></td>
            <td colspan="2"><input name="email_contacto2" type="text" class="campo" id="email_contacto2" value="<?= $rowM['email_contacto2']; ?>" size="80" maxlength="100" />            </td>
          </tr>
          <tr>
            <td colspan="3">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="3"><strong>DESTINATARIO PARA MAILS DE LOGS DE IMPORTACI&Oacute;N</strong></td>
          </tr>
          <tr>
            <td><div align="right">Nombre:</div></td>
            <td colspan="2"><input name="nombre_logs" type="text" class="campo" id="nombre_logs" value="<?= $rowM['nombre_logs']; ?>" size="50" maxlength="50" />            </td>
          </tr>
          <tr>
            <td><div align="right">Correo electr&oacute;nico:</div></td>
            <td colspan="2"><input name="email_logs" type="text" class="campo" id="email_logs" value="<?= $rowM['email_logs']; ?>" size="80" maxlength="100" />            </td>
          </tr>
          <tr>
            <td colspan="3">&nbsp;</td>
          </tr>
          <tr>
            <td valign="top"><div align="right">Destinatarios para seguimiento de Bugs:</div></td>
            <td valign="top"><textarea name="correo_seguimiento_bugs" id="correo_seguimiento_bugs" cols="65" rows="5" class="campo"><?=$rowCFG['correo_seguimiento_bugs'];?></textarea></td>
            <td valign="top">Un correo por l&iacute;nea</td>
          </tr>
          <tr>
            <td colspan="3">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="3">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="3"><strong>LIMITE DE VENTAS DE PRODUCTOS MAYORES KAID PARA EMPLEADOS WHIRLPOOL</strong></td>
          </tr>
          <tr>
            <td><div align="right">Unidades:</div></td>
            <td colspan="2"><input name="limite_kad" type="text" class="campo" id="limite_kad" value="<?= $rowCFG['limite_kad']; ?>" size="5" maxlength="3" />            </td>
          </tr>
          <tr>
            <td colspan="3">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="3"><strong>LIMITE DE VENTAS, PRECIOS ESPECIALES PARA EMPLEADOS WHIRLPOOL</strong></td>
          </tr>
          <tr>
            <td><div align="right">Unidades:</div></td>
            <td colspan="2"><input name="limite_precios_especiales" type="text" class="campo" id="limite_precios_especiales" value="<?= $rowCFG['limite_precios_especiales']; ?>" size="5" maxlength="3" /></td>
          </tr>
          <tr>
            <td colspan="3">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="3"><strong>IMPORTE M&Iacute;NIMO DE VENTA EN TIENDA WHIRLPOOL</strong></td>
          </tr>
          <tr>
            <td><div align="right">Importe m&iacute;nimo:</div></td>
            <td colspan="2"><input name="minimo_venta_tw" type="text" class="campo" id="minimo_venta_tw" value="<?= $rowCFG['minimo_venta_tw']; ?>" size="7" maxlength="8" /> 
            del total de la venta <em><strong>(Solo aplica para categor&iacute;as seleccionadas)</strong></em></td>
          </tr>
          <tr>
            <td><div align="right">Mensaje:</div></td>
            <td width="27%"><textarea name="mensaje_minimo" id="mensaje_minimo" cols="45" rows="5" class="campo"><?=$rowCFG['mensaje_minimo'];?>
            </textarea></td>
            <td width="61%"> <p>##categorias## 
            = lista de categor&iacute;as donde aplica el m&iacute;nimo.<br />
            ##minimo## = Importe m&iacute;nimo.</p>
              <p>&lt;strong&gt;<em>texto</em>&lt;/strong&gt; = resaltar <em>texto </em>en negritas<br />
              &lt;br&gt; = salto de l&iacute;nea</p>            </td>
          </tr>
          <tr>
            <td colspan="3">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="3"><strong>DATOS FIJOS PARA EXPORTACI&Oacute;N DE PEDIDOS</strong></td>
          </tr>
          <tr>
            <td width="12%"><div align="right">SALES_ORG:</div></td>
            <td colspan="2"><input name="sales_org" type="text" class="campo" id="sales_org" value="<?= $row['sales_org']; ?>" size="20" maxlength="20" />            </td>
          </tr>
          <tr>
            <td><div align="right">DISTR_CHAN:</div></td>
            <td colspan="2"><input name="distr_chan" type="text" class="campo" id="distr_chan" value="<?= $row['distr_chan']; ?>" size="20" maxlength="20" />            </td>
          </tr>
          <tr>
            <td><div align="right">DIVISION:</div></td>
            <td colspan="2"><input name="division" type="text" class="campo" id="division" value="<?= $row['division']; ?>" size="20" maxlength="20" />            </td>
          </tr>
          <tr>
            <td><div align="right">SALES_GRP:</div></td>
            <td colspan="2"><input name="sales_grp" type="text" class="campo" id="sales_grp" value="<?= $row['sales_grp']; ?>" size="20" maxlength="20" />            </td>
          </tr>
          <tr>
            <td><div align="right">SALES_OFF:</div></td>
            <td colspan="2"><input name="sales_off" type="text" class="campo" id="sales_off" value="<?= $row['sales_off']; ?>" size="20" maxlength="20" />            </td>
          </tr>
          <tr>
            <td><div align="right">PO_METHOD:</div></td>
            <td colspan="2"><input name="po_method" type="text" class="campo" id="po_method" value="<?= $row['po_method']; ?>" size="20" maxlength="20" />            </td>
          </tr>
          <tr>
            <td><div align="right">PMNTTRMS:</div></td>
            <td colspan="2"><input name="pmnttrms" type="text" class="campo" id="pmnttrms" value="<?= $row['pmnttrms']; ?>" size="20" maxlength="20" />            </td>
          </tr>
          <tr>
            <td><div align="right">PURCH_NO_C:</div></td>
            <td colspan="2"><input name="purch_no_c" type="text" class="campo" id="purch_no_c" value="<?= $row['purch_no_c']; ?>" size="20" maxlength="20" />            </td>
          </tr>
          <tr>
            <td><div align="right">SHIP_COND:</div></td>
            <td colspan="2"><input name="ship_cond" type="text" class="campo" id="ship_cond" value="<?= $row['ship_cond']; ?>" size="20" maxlength="20" />            </td>
          </tr>
          <tr>
            <td><div align="right">PARTN_ROLE:</div></td>
            <td colspan="2"><input name="partn_role" type="text" class="campo" id="partn_role" value="<?= $row['partn_role']; ?>" size="20" maxlength="20" />            </td>
          </tr>
          <tr>
            <td><div align="right">COUNTRY:</div></td>
            <td colspan="2"><input name="country" type="text" class="campo" id="country" value="<?= $row['country']; ?>" size="20" maxlength="20" />            </td>
          </tr>
          <tr>
            <td><div align="right">PARTN_ROLE2:</div></td>
            <td colspan="2"><input name="partn_role2" type="text" class="campo" id="partn_role2" value="<?= $row['partn_role2']; ?>" size="20" maxlength="20" />            </td>
          </tr>
          <tr>
            <td><div align="right">COUNTRY2:</div></td>
            <td colspan="2"><input name="country2" type="text" class="campo" id="country2" value="<?= $row['country2']; ?>" size="20" maxlength="20" />            </td>
          </tr>
          <tr>
            <td><div align="right">PARTN_ROLE3:</div></td>
            <td colspan="2"><input name="partn_role3" type="text" class="campo" id="partn_role3" value="<?= $row['partn_role3']; ?>" size="20" maxlength="20" />            </td>
          </tr>
          <tr>
            <td><div align="right">PARTN_NUMB:</div></td>
            <td colspan="2"><input name="partn_numb3" type="text" class="campo" id="partn_numb3" value="<?= $row['partn_numb3']; ?>" size="20" maxlength="20" />            </td>
          </tr>
          <tr>
            <td><div align="right">COUNTRY3:</div></td>
            <td colspan="2"><input name="country3" type="text" class="campo" id="country3" value="<?= $row['country3']; ?>" size="20" maxlength="20" />            </td>
          </tr>
          <tr>
            <td><div align="right">TEXT_ID:</div></td>
            <td colspan="2"><input name="text_id" type="text" class="campo" id="text_id" value="<?= $row['text_id']; ?>" size="20" maxlength="20" />            </td>
          </tr>
          <tr>
            <td><div align="right">LANGU:</div></td>
            <td colspan="2"><input name="langu" type="text" class="campo" id="langu" value="<?= $row['langu']; ?>" size="20" maxlength="20" />            </td>
          </tr>
          <tr>
            <td><div align="right">TEXT_ID2:</div></td>
            <td colspan="2"><input name="text_id2" type="text" class="campo" id="text_id2" value="<?= $row['text_id2']; ?>" size="20" maxlength="20" />            </td>
          </tr>
          <tr>
            <td><div align="right">LANGU2:</div></td>
            <td colspan="2"><input name="langu2" type="text" class="campo" id="langu2" value="<?= $row['langu2']; ?>" size="20" maxlength="20" />            </td>
          </tr>
          <tr>
            <td><div align="right">TEXT_LINE (2)<br />
            Shipping Instr:</div></td>
            <td colspan="2"><input name="text_line2" type="text" class="campo" id="text_line2" value="<?= $row['text_line2']; ?>" size="160" maxlength="500" />            </td>
          </tr>
          <tr>
            <td><div align="right">ITM_NUMBER:</div></td>
            <td colspan="2"><input name="itm_number" type="text" class="campo" id="itm_number" value="<?= $row['itm_number']; ?>" size="20" maxlength="20" />            </td>
          </tr>
          <tr>
            <td><div align="right">CTyp:</div></td>
            <td colspan="2"><input name="ctyp" type="text" class="campo" id="langu4" value="<?= $row['ctyp']; ?>" size="20" maxlength="20" />            </td>
          </tr>
          <tr>
            <td><div align="right">CURRENCY:</div></td>
            <td colspan="2"><input name="currency" type="text" class="campo" id="langu7" value="<?= $row['currency']; ?>" size="20" maxlength="20" />            </td>
          </tr>
          <tr>
            <td><div align="right">CONDCOINHD:</div></td>
            <td colspan="2"><input name="condcoinhd" type="text" class="campo" id="langu8" value="<?= $row['condcoinhd']; ?>" size="20" maxlength="20" />            </td>
          </tr>
          <tr>
            <td><div align="right">AcctAssgGr:</div></td>
            <td colspan="2"><input name="acctassggr" type="text" class="campo" id="langu5" value="<?= $row['acctassggr']; ?>" size="20" maxlength="20" />            </td>
          </tr>
          <tr>
            <td><div align="right">PARVW:</div></td>
            <td colspan="2"><input name="parvw" type="text" class="campo" id="langu6" value="<?= $row['parvw']; ?>" size="20" maxlength="20" />            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="2"><input name="grabar" type="button" class="boton" onclick="valida();" value="GRABAR" />
                <input name="desc" type="button" class="boton" onclick="descarta();" value="DESCARTAR" id="desc" />            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="2">&nbsp;</td>
          </tr>
        </table>
      </form>    
    </div>
</div>
</body>
</html>
