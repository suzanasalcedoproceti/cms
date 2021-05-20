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
//	include("_checa_vars.php");
	
	// obtener crédito disponible de la agencia
	
	if ($_POST['accion']=='procesar') { 
	// include("_checa_vars.php");
		$debito = $_POST['debito']+0;
		$cheque = $_POST['cheque']+0;	
		$cep 	= $_POST['cep']+0;	
		$puntos = $_POST['puntos']+0;	
		$odc	= $_POST['odc']+0;
		$msi03  = $_POST['msi03']+0;
		$msi06  = $_POST['msi06']+0;
		$msi09  = $_POST['msi09']+0;
		$msi10  = $_POST['msi10']+0;
		$msi12  = $_POST['msi12']+0;
		$msi18  = $_POST['msi18']+0;
		$msi24  = $_POST['msi24']+0;
		$sel_boxes  = $_POST['sel_boxes']+0;
		$ftipo  = $_POST['ftipo'];
		$texto  = $_POST['texto'];

		if($sel_boxes)
		{

			$condicion = "WHERE (estatus=1 OR estatus=2) ";


			// construir la condición de búsqueda

			if ($ftipo == 'P') $condicion .= " AND empresa_proyectos = 1 ";
			if ($ftipo == 'W') $condicion .= " AND empresa_whirlpool = 1 ";
			$condicion .= ($_SESSION['usr_service']==1) ? ' AND empresa_publica = 1 ' : '';

			if ($texto) $condicion.= " AND nombre LIKE '%$texto%' ";
			$query_busqueda = "SELECT * FROM empresa $condicion";
			$query = "UPDATE empresa SET pago_debito = $debito, pago_cheque = $cheque, pago_cep = $cep, pago_odc = $odc, puntos = $puntos, msi03 = $msi03, msi06 = $msi06, msi09 = $msi09, msi10 = $msi10, msi12 = $msi12, msi18 = $msi18, msi24 = $msi24, act = 1-act $condicion";
			$resultado = mysql_query($query);
						$act = mysql_affected_rows();
						if ($act<=0) {
							$error .= "No se pudo modificar las formas de pago de una empresa.";
						} else {
							$items_procesados ++;
						}
		}
		else
		{
			for (reset($_POST); list($variable, $valor) = each($_POST); ){
			   $encontrado=0;
			   if(substr($variable,0,6)=="pagos_") {
					if (!empty($valor)) {
						$empresa = trim(substr($variable,6,10));
						
						// cambiar estatus
						$query = "UPDATE empresa SET pago_debito = $debito, pago_cheque = $cheque, pago_cep = $cep, pago_odc = $odc, puntos = $puntos, msi03 = $msi03, msi06 = $msi06, msi09 = $msi09, msi10 = $msi10, msi12 = $msi12, msi18 = $msi18, msi24 = $msi24, act = 1-act WHERE clave = $empresa LIMIT 1";
						$resultado = mysql_query($query);
						$act = mysql_affected_rows();
						if ($act<=0) {
							$error .= "No se pudo modificar las formas de pago de una empresa.";
						} else {
							$items_procesados ++;
						}
						
						
						
					} // dato
				} // elemento 
			} // for
		}
	
	}
		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Panel de Control</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery.js"></script>

<script type="text/javascript">
 function valida() {
 	var fdp = "";
 	if(document.forma.debito.checked==true) fdp += "Debito CLABE\n"; 
 	if(document.forma.cheque.checked==true) fdp += "Cheque\n"; 
 	if(document.forma.cep.checked==true) fdp += "CEP\n"; 
 	if(document.forma.odc.checked==true) fdp += "Orden de Compra\n"; 
 	if(document.forma.puntos.checked==true) fdp += "Puntos\n"; 
 	if(document.forma.msi03.checked==true) fdp += "3 MSI\n"; 
 	if(document.forma.msi06.checked==true) fdp += "6 MSI\n"; 
 	if(document.forma.msi09.checked==true) fdp += "9 MSI\n"; 
 	if(document.forma.msi10.checked==true) fdp += "10 MSI\n"; 
 	if(document.forma.msi12.checked==true) fdp += "12 MSI\n"; 
 	if(document.forma.msi18.checked==true) fdp += "18 MSI\n"; 
 	if(document.forma.msi24.checked==true) fdp += "24 MSI\n"; 
    continuar = window.confirm("Las formas de pago que seleccionaste para la empresa, son las siguientes:\n" + fdp + "Deseas continuar?");
    if (!continuar) {
           return;
    }

	document.forma.action = 'cambia_fp.php';
	document.forma.accion.value = 'procesar';
	document.forma.submit();
	
 }

</script>
</head>
<body class="body_sb">
 <? if ($_POST['accion'] == 'procesar' && !$error) { ?>
	<script type="text/javascript">
	   parent.recarga();
 	   parent.Shadowbox.close();
   	</script>
 <? return;
    } ?>
 
  
 <?	if ($_POST['accion'] == 'procesar') { ?>
   <form name="forma" id="forma" method="post">
   <input name="accion" id="accion" value="" type="hidden" />
   <p><br />
   <strong><?=$mensaje;?></strong><br />
   <?=$error;?></p>
   
   <p>   <input name="desc" type="button" class="boton" onclick="parent.recarga(); parent.Shadowbox.close();" value=" Cerrar " id="desc" />   </p>

  </form>
  
 <? } else { ?>


  <form name="forma" id="forma" method="post">
   <input name="accion" id="accion" value="" type="hidden" />
   
   <?
    for (reset($_GET); list($variable, $valor) = each($_GET); ){
	  echo '<input name="'.$variable.'" id="'.$variable.'" value="'.$valor.'" type="hidden" /> '.chr(10);
	  $empresa_ = trim(substr($variable,6,10)); 
	}

   
   ?>
  
	<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" class="tabla">
	  		  <tr class="encab">
				<th class="thencab" style="height:30px;">Modificar Formas de Pago de empresas seleccionadas</th>
			  </tr> 
                

		  <tr class="texto hls">
		    <td class="sinfondo"><table width="30%" border="0" align="center" cellpadding="2" cellspacing="3">
              <tr class="texto">
                <td colspan="5" align="center" bgcolor="#f4f4f2"><strong>Formas de pago disponibles</strong></td>
                <td colspan="11" align="center" bgcolor="#f4f4f2"><strong>MSI</strong></td>
              </tr>
              <tr class="texto" bgcolor="#F4F4F2">
                <td align="center" bgcolor="#F4F4F2"><div align="center">D&eacute;bito CLABE</div></td>
                <td align="center" bgcolor="#F4F4F2"><div align="center">Cheque</div></td>
                <td align="center" bgcolor="#F4F4F2"><div align="center">CEP</div></td>
                <td align="center" bgcolor="#F4F4F2"><div align="center">ODC</div></td>
                <td nowrap="nowrap" bgcolor="#F4F4F2"><div align="center">Puntos</div></td>
                <td nowrap="nowrap" bgcolor="#F4F4F2"><div align="center">3 </div></td>
                <td nowrap="nowrap" bgcolor="#F4F4F2"><div align="center">6 </div></td>
                <td nowrap="nowrap" bgcolor="#F4F4F2"><div align="center">9</div></td>
                <td nowrap="nowrap" bgcolor="#F4F4F2"><div align="center">10 </div></td>
                <td nowrap="nowrap" bgcolor="#F4F4F2"><div align="center">12 </div></td>
                <td nowrap="nowrap" bgcolor="#F4F4F2"><div align="center">18 </div></td>
                <td nowrap="nowrap" bgcolor="#F4F4F2"><div align="center">24</div></td>
              </tr>
              <?
              

	 		$resultado= mysql_query("SELECT * FROM empresa WHERE clave=$empresa_",$conexion);
            $row = mysql_fetch_array($resultado);


          ?>

              <tr class="texto">
                <td align="center" bgcolor="#FFFFFF"><input name="debito" type="checkbox" id="debito" value="1" <? if ($row['pago_debito']) echo 'checked'; ?> /></td>
                <td align="center" bgcolor="#FFFFFF"><input name="cheque" type="checkbox" id="cheque" value="1" <? if ($row['pago_cheque']) echo 'checked'; ?> /></td>
                <td align="center" bgcolor="#FFFFFF"><input name="cep" type="checkbox" id="cep" value="1" <? if ($row['pago_cep']) echo 'checked'; ?>/></td>
                <td align="center" bgcolor="#FFFFFF"><input name="odc" type="checkbox" id="odc" value="1" <? if ($row['pago_odc']) echo 'checked'; ?>/></td>
                <td bgcolor="#FFFFFF" align="center"><input name="puntos" type="checkbox" id="puntos" value="1" <? if ($row['puntos']) echo 'checked'; ?>/></td>
                <td bgcolor="#FFFFFF" align="center"><input name="msi03" type="checkbox" id="msi03" value="1" <? if ($row['msi03']) echo 'checked'; ?>/></td>
                <td bgcolor="#FFFFFF" align="center"><input name="msi06" type="checkbox" id="msi06" value="1" <? if ($row['msi06']) echo 'checked'; ?>/></td>
                <td bgcolor="#FFFFFF" align="center"><input name="msi09" type="checkbox" id="msi09" value="1" <? if ($row['msi09']) echo 'checked'; ?>/></td>
                <td bgcolor="#FFFFFF" align="center"><input name="msi10" type="checkbox" id="msi10" value="1" <? if ($row['msi10']) echo 'checked'; ?>/></td>
                <td bgcolor="#FFFFFF" align="center"><input name="msi12" type="checkbox" id="msi12" value="1" <? if ($row['msi12']) echo 'checked'; ?>/></td>
                <td bgcolor="#FFFFFF" align="center"><input name="msi18" type="checkbox" id="msi18" value="1" <? if ($row['msi18']) echo 'checked'; ?>/></td>
                <td bgcolor="#FFFFFF" align="center"><input name="msi24" type="checkbox" id="msi24" value="1" <? if ($row['msi24']) echo 'checked'; ?>/></td>
              </tr>

            </table>
        </td>
      </tr>
		  <tr class="texto hls">
		    <td align="center" class="sinfondo">&nbsp;</td>
      </tr>
		  <tr class="texto hls">
		    <td align="center" class="sinfondo"><strong>NOTA:</strong> Se aplicar&aacute;n los cambios (habilitar o deshabilitar forma de pago) aqu&iacute; indicados a todas las empresas seleccionadas.</td>
      </tr>
        </table>
    <p>
     <input name="desc2" type="button" class="boton" onclick="javascript:valida()" value="Grabar" id="desc2" />
    </p>
  </form>
  
 <? } ?>

</body>
</html>