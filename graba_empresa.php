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
</head>

<body>
<div id="container">
	<? $tit='Administrar Empresas'; include('top.php'); ?>
	<div class="main">
      <p>
        <? 

		$link='lista_empresa.php?texto='.$texto;

		$usuario=$_SESSION['usr_valido'];
		$autorizado=op_aut($modulo);
		include('../conexion.php');

		$error=FALSE;
		// comenzar transaccción	
		$resultado = mysql_query("SET AUTOCOMMIT = 0");
		$resultado = mysql_query("START TRANSACTION");
		
		
		// extrae variables del formulario
		$empresa=$_POST['empresa'];
		if (empty($empresa)) $empresa=$_GET['empresa'];
		$nombre=$_POST['nombre'];
		$rfc = $_POST['rfc'];
		$texto = $_POST['texto']; // filtro

		
		$estatus_t=$_POST['estatus'];
		$ftipo=$_POST['ftipo'];
		$lista_precios=$_POST['lista_precios'];
		$lista_precios_pos=$_POST['lista_precios_pos'];
		$listas_permitidas_pos = substr($_POST['listas_permitidas_pos'],0,-1);  // quitar ultima coma
		
		$lista_precios_especiales_tw=$_POST['lista_precios_especiales_tw'];
		$lista_precios_especiales_pos=$_POST['lista_precios_especiales_pos'];
		$lista_pe_2=$_POST['lista_pe_2'];
		$lista_pe_3=$_POST['lista_pe_3'];
		$lista_pe_4=$_POST['lista_pe_4'];
		$lista_pe_5=$_POST['lista_pe_5'];
		$lista_pe_5A=$_POST['lista_pe_5A'];
		$lista_pe_6=$_POST['lista_pe_6'];
		$lista_pe_7=$_POST['lista_pe_7'];
		$lista_pe_8=$_POST['lista_pe_8'];
		$cliente_sap = $_POST['cliente_sap'];
		$empresa_whirlpool = $_POST['empresa_whirlpool']+0;
		$empresa_proyectos = $_POST['empresa_proyectos']+0;
		$empresa_proyectos_credito = $_POST['empresa_proyectos_credito']+0;
		$dominio = $_POST['dominio'];
		
		$invita_amigos = $_POST['invita_amigos']+0;
		$lista_precios_invitados = $_POST['lista_precios_invitados'];
		
		$msi03 = $_POST['msi03']+0;
		$msi06 = $_POST['msi06']+0;
		$msi09 = $_POST['msi09']+0;
		$msi10 = $_POST['msi10']+0;
		$msi12 = $_POST['msi12']+0;
		$msi18 = $_POST['msi18']+0;
		$msi24 = $_POST['msi24']+0;
		
		$empresa_publica = $_POST['empresa_publica']+0;
		
		$tipo_interes = $_POST['tipo_interes'];
		
		$puntos = $_POST['puntos']+0;
		$combos = $_POST['combos']+0;
		
		$divisiones = $_POST['divisiones'];
		$divisiones = substr($divisiones,0,-1);

		$autorizar = $_POST['autorizar'];
		$pos_cb_tiendas = $_POST['pos_cb_tiendas'];
		$pos_cb_productos = $_POST['pos_cb_productos'];
		$cb_lista_precios_especial = $_POST['cb_lista_precios_especial'];
		$cb_lista_precios_entrega_inmediata = $_POST['cb_lista_precios_entrega_inmediata'];

		
		 if (empty($autorizar))  { // si no es proceso de autorización


			if (!empty($empresa) AND $autorizado) {   // Si es un registro editado autorizado
				$estatus=$estatus_t;
				$original=$empresa;

				$resultado= mysql_query("UPDATE empresa SET nombre='$nombre', rfc='$rfc',
															empresa_publica=$empresa_publica,
															lista_precios='$lista_precios',
															lista_precios_pos='$lista_precios_pos',
															listas_permitidas_pos='$listas_permitidas_pos',
															lista_precios_especiales_tw='$lista_precios_especiales_tw',
															lista_precios_especiales_pos='$lista_precios_especiales_pos',

															cliente_tipo_id='$ftipo',
															lista_pe_2='$lista_pe_2',
															lista_pe_3='$lista_pe_3',
															lista_pe_4='$lista_pe_4',
															lista_pe_5='$lista_pe_5',
															lista_pe_5A='$lista_pe_5A',
															lista_pe_6='$lista_pe_6',
															lista_pe_7='$lista_pe_7',
															lista_pe_8='$lista_pe_8',

															cliente_sap='$cliente_sap',
															empresa_whirlpool=$empresa_whirlpool,
															empresa_proyectos=$empresa_proyectos,
															empresa_proyectos_credito=$empresa_proyectos_credito,
															dominio='$dominio',
															invita_amigos=$invita_amigos, 
															lista_precios_invitados='$lista_precios_invitados',
															msi03=$msi03, msi06=$msi06, msi09=$msi09, msi10=$msi10,
															msi12=$msi12, msi18=$msi18, msi24=$msi24,
															tipo_interes='$tipo_interes',
															puntos=$puntos, 
															combos=$combos,
															estatus=$estatus_t,
															original=$original,
															editor=$usuario,
															act=1-act
													 WHERE clave=$empresa",$conexion);

				$reg= mysql_affected_rows();
				if   ($reg>0) {
					$mensaje='Se actualizó el registro...';
				} else { $error=TRUE; $mensaje='ERROR<br>No se actualizó el registro...'; $link='javascript:history.go(-1);'; }

			} else {  // si no es registro editado autorizado
				
				if (empty($empresa) AND $autorizado) {  // Si es un registro nuevo autorizado
				   $estatus=$estatus_t;
				   $original=0;
				}

				elseif (empty($empresa) AND !($autorizado)) {  // Si es un registro nuevo por autorizar
				   $estatus=3;
				   $original=0;
				}

				elseif (!empty($empresa) AND !($autorizado)) {   // Si es un registro editado por autorizar
					$estatus=3;
					$original=$empresa;
				}


				  $resultado= mysql_query("INSERT empresa (nombre,
				  											rfc,
				  											empresa_publica,
				  											lista_precios,
															lista_precios_pos,
															listas_permitidas_pos,
															lista_precios_especiales_tw,
															lista_precios_especiales_pos,

															cliente_tipo_id,
															lista_pe_2,
															lista_pe_3,
															lista_pe_4,
															lista_pe_5,
															lista_pe_5A,
															lista_pe_6,
															lista_pe_7,
															lista_pe_8,
															
															cliente_sap,
															empresa_whirlpool,
															empresa_proyectos,
															empresa_proyectos_credito,
															dominio,
															invita_amigos,
															lista_precios_invitados,
															msi03,
															msi06,
															msi09,
															msi10,
															msi12,
															msi18,
															msi24,
															tipo_interes,
															puntos, 
															combos,
															estatus,
															original,
															editor)
												  VALUES ('$nombre',
												  		  '$rfc',
												  		  $empresa_publica,
												  		  '$lista_precios',
														  '$lista_precios_pos',
														  '$listas_permitidas_pos',
														  '$lista_precios_especiales_tw',
														  '$lista_precios_especiales_pos',
														  '$ftipo',
														  '$lista_pe_2',
														  '$lista_pe_3',
														  '$lista_pe_4',
														  '$lista_pe_5',
														  '$lista_pe_5A',
														  '$lista_pe_6',
														  '$lista_pe_7',
														  '$lista_pe_8',
														  '$cliente_sap',
														  $empresa_whirlpool,
														  $empresa_proyectos,
														  $empresa_proyectos_credito,
														  '$dominio',
														  $invita_amigos, 
														  '$lista_precios_invitados',
														  $msi03,
														  $msi06,
														  $msi09,
														  $msi10,
														  $msi12,
														  $msi18,
														  $msi24,
														  '$tipo_interes',
 														  $puntos,
														  $combos,
														  $estatus,
														  $original,
														  $usuario)",$conexion); 
	
				  $reg= mysql_affected_rows();
				  $new_id= mysql_insert_id();
				  if     ($reg>0 AND empty($empresa)) $mensaje='Se agregó un nuevo registro...';
				  elseif ($reg>0 AND !empty($empresa)) $mensaje='Se actualizó el registro...';
				  else   { $error=TRUE; $mensaje='ERROR<br>No se agregó el registro...'; $link='javascript:history.go(-1);'; }
				  
				  if (!$autorizado && !$error) $mensaje.='<br><br>Se publicará cuando sea autorizado.';

				  if (!empty($empresa)) {  // Marca el registro original como bloqueado para editar
					  $resultado= mysql_query("UPDATE empresa SET estatus=2 WHERE clave=$empresa",$conexion);
				  }
				  
				  
			   }  // si no es registro editado autorizado

			}  // si no es proceso de autorización
				
			elseif ($autorizar==1) {  // si es proceso de autorización

					$res= mysql_query("SELECT * FROM empresa WHERE clave='$empresa'",$conexion);
					$row= mysql_fetch_array($res);

					$original=$row['original'];
					
					// borra registro original
	                $resultado= mysql_query("DELETE FROM empresa WHERE clave = '$original'" ,$conexion);

					if (empty($original)) $original=$empresa;

                    $resultado= mysql_query("UPDATE empresa SET clave='$original',
				                                                 nombre='$nombre',
																 rfc='$rfc',
																 empresa_publica=$empresa_publica,
																 lista_precios='$lista_precios',
																 lista_precios_pos='$lista_precios_pos',
																 listas_permitidas_pos='$listas_permitidas_pos',
																 lista_precios_especiales_tw='$lista_precios_especiales_tw',
																 lista_precios_especiales_pos='$lista_precios_especiales_pos',
																 cliente_tipo_id='$ftipo',
																 lista_pe_2='$lista_pe_2',
																 lista_pe_3='$lista_pe_3',
																 lista_pe_4='$lista_pe_4',
																 lista_pe_5='$lista_pe_5',
																 lista_pe_5A='$lista_pe_5A',
																 lista_pe_6='$lista_pe_6',
																 lista_pe_7='$lista_pe_7',
																 lista_pe_8='$lista_pe_8',
																 
																 cliente_sap='$cliente_sap',
																 empresa_whirlpool=$empresa_whirlpool,
																 empresa_proyectos=$empresa_proyectos,
																 empresa_proyectos_credito=$empresa_proyectos_credito,
																 dominio='$dominio',
															     invita_amigos=$invita_amigos, 
																 lista_precios_invitados='$lista_precios_invitados',
																 msi03=$msi03, msi06=$msi06, msi09=$msi09, msi10=$msi10,
																 msi12=$msi12, msi18=$msi18, msi24=$msi24,
																 tipo_interes='$tipo_interes',
																 puntos=$puntos,
																 combos=$combos,
																 estatus=$estatus_t,
																 act=1-act
													 	   WHERE clave=$empresa",$conexion);

				    $reg= mysql_affected_rows();
				    if   ($reg>0) { $mensaje='Se autorizó el registro...'; $link='lista_autorizar.php'; }
				    else { $error=TRUE; $mensaje='ERROR<br>No se autorizó el registro...'; $link='javascript:history.go(-1);'; }


			}  // si es proceso de autorización

		    if (!$error) {
				if (!$empresa) $empresa = $new_id;
				// actualizar divisiones
				$resultado = mysql_query("DELETE FROM empresa_division WHERE empresa = $empresa",$conexion);
				$arr_div = explode(",",$divisiones);
				$repe = '';
				for ($iarr = 0; $iarr <= count($arr_div); $iarr++) {
					$vdiv = trim($arr_div[$iarr]);
					if ($vdiv) {
						$query = "INSERT INTO empresa_division (empresa, division) VALUES ($empresa, '$vdiv')";
						$resultado = mysql_query($query,$conexion);
						$ins = mysql_affected_rows();
						if ($ins<=0) {
							$mensaje .= '<br>No se agregó división: '.$vdiv.'<br>';
							if (substr(mysql_error(),0,15)=='Duplicate entry' || mysql_errno()==1062) $mensaje.= 'División repetida en otra empresa';
						}
					}
			   }
			   $resultado = mysql_query("DELETE FROM cuadro_basico WHERE empresa_clave = $empresa",$conexion);
			   $arr_tiendas = explode(",",$pos_cb_tiendas);
			   $arr_productos = explode(",",$pos_cb_productos);

			   for ($iarr = 0; $iarr <= count($arr_tiendas); $iarr++) {
					$vtienda = trim($arr_tiendas[$iarr]);
					if ($vtienda) {
						for ($iarrt = 0; $iarrt <= count($arr_productos); $iarrt++) {
							$vproducto = trim($arr_productos[$iarrt]);
							if ($vproducto) 
								{
									$query = "INSERT INTO cuadro_basico (empresa_clave, producto_clave,tienda,precio_especial,precio_inmediata) VALUES ($empresa, $vproducto,$vtienda,'$cb_lista_precios_especial','$cb_lista_precios_entrega_inmediata')";
									$resultado = mysql_query($query,$conexion);
									//echo $query;
								}
						}
					}
			   }
		   }
		   $resultado = mysql_query("update cliente set tipo='$ftipo' WHERE empresa = $empresa",$conexion);
		   // revisar si hubo error o no
		   if ($error) mysql_query("ROLLBACK"); 
		   else mysql_query("COMMIT");
				
           mysql_close();

				
      ?>
      </p>
      <p>&nbsp;</p>
      <? include('mensaje.php'); ?>
      <p>&nbsp;</p>
      </div>
</div>
</body>
</html>
