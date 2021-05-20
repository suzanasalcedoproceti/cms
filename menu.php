<?if (strpos($_SESSION['ss_opciones'], '33') !== false) {
   $ventas = 1;

 }else{
   $ventas = 0;
 }?>
        <div id="cnav">
          <ul id="nav">
            <? 
			if (!function_exists('op')) { 
				include_once("funciones.php");			
			}
			
			if (op(2) OR op(3) OR op(5) OR op(4) OR op(10) OR op(11) OR op(12)) { ?>
            <li class="sup w130">Productos
              <ul class="subul clearfix">
                <? if (op(3)) { ?><li><a href="lista_categoria.php">Categor&iacute;as</a></li><? } ?>
                <? if (op(2)) { ?><li><a href="lista_subcategoria.php">Subcategor&iacute;as</a></li><? } ?>
                <? if (op(5)) { ?><li><a href="lista_campo.php">Campos de subcategor&iacute;as</a></li><? } ?>
                <? if (op(4)) { ?><li><a href="lista_marca.php">Marcas</a></li><? } ?>
                <? if (op(4)) { ?><li><a href="lista_producto.php">Productos</a></li><? } ?>
                <? if (op(4)) { ?><li><a href="lista_omitidos.php">Productos omitidos</a></li><? } ?>
                <? if (op(21)) { ?><li><a href="lista_combo.php">Combos</a></li><? } ?>
                <? if (op(11)) { ?><li><a href="lista_stage.php">Stages</a></li><? } ?>
                <? if (op(10)) { ?><li><a href="lista_promo.php">Promos TW</a></li><? } ?>
                <? if (op(10)) { ?><li><a href="lista_promo_producto.php">Promos Productos</a></li><? } ?>
                <? if (op(12)) { ?><li><a href="lista_comentario.php">Comentarios de productos</a></li><? } ?>
                <? /*if (op(4)) { ?><li><a href="importa_precios.php">Importar Precios</a></li><? } ?>
                <? if (op(4)) { ?><li><a href="importa_existencias.php">Importar Existencias</a></li><? } */?>
                <li class="sep"><a href="reporte_detalles_productos.php">Reporte detalles de productos</a></li>
                <? if (op(23)) { ?>
	                <li class="sep subsub"><a>Comparativo de Productos</a>
                        <ul class="subul clearfix" style="margin:-13px 0 0 160px;">
                          <li><a href="lista_marca_comp.php">Marcas Competencia</a></li>
                          <li><a href="lista_producto_comp.php">Productos Competencia</a></li>
                          <li><a href="lista_matriz_comp.php">Matriz de Productos</a></li>
                        </ul>
                     </li>
				<? } ?>
				
              </ul>
            </li>
            <? } ?>
            <? if (op(6) OR op(7) OR op(8)) { ?>
            <li class="sup w110">Empresas
              <ul class="subul clearfix">
                <? if (op(6)) { ?><li><a href="lista_empresa.php">Empresas</a></li><? } ?>
                <? if (op(7)) { ?><li><a href="lista_tarjeta.php">Tarjetas</a></li><? } ?>
                <? if (op(8)) { ?><li class="sep"><a href="lista_cliente.php">Clientes TAW/POS</a></li><? } ?>
                <? if (op(8)) { ?><li><a href="lista_cliente_proyectos.php">Clientes Proyectos</a></li><? } ?>
                <? if (op(8)) { ?><li class="sep"><a href="reactivar_clientes.php">Recordatorio clientes inactivos</a></li><? } ?>
                <? if (op(8)) { ?><li class="sep"><a href="clientes_repetidos.php">Consultar Clientes Repetidos</a></li><? } ?>
              </ul>
            </li>
            <? } ?>
            <? if (op(9) OR op(13) OR op(30)) { ?>
            <li class="sup w110">Pedidos
              <ul class="subul clearfix">
                <? if (op(13)) { ?><li><a href="lista_pedidos.php">Consulta pedidos</a></li><? } ?>
                <? if (op(13)) { ?><li><a href="corte_caja.php">Corte de Caja</a></li><? } ?>
                <? if (op(30)) { ?><li><a href="reporte_polizas.php">Reporte de Pólizas</a></li><? } ?>
                <? if (op(13)) { ?><li class="sep"><a href="lista_comentario_pedido.php">Inconformidades / Felicitaciones</a></li><? } ?>
				<? if (op(13)) { ?><li><a href="lista_oc.php">Orden de Compra Corporate</a></li><? } ?>
              </ul>
            </li>
            <? } ?>

            <? if (op(17)) { ?>
            <li class="sup w100">POS
              <ul class="subul clearfix">
                <? if (op(17)) { ?><li><a href="lista_tienda.php">Tiendas</a></li><? } ?>
                <? if (op(17)) { ?><li><a href="lista_usuario_tienda.php">Usuarios</a></li><? } ?>
                <? if (op(17)) { ?><li><a href="lista_plazo.php">Payment Terms (%)</a></li><? } ?>
                <? if (op(17)) { ?><li class="sep"><a href="lista_motivo_sust.php">Motivos de Sust y Refacuraci&oacute;n</a></li><? } ?>
                <? if (op(17)) { ?><li class="sep"><a href="lista_noticia.php">Noticias</a></li><? } ?>
              </ul>
            </li>
            <? } ?>

            <? if (op(24) ||op(25) || op(26) || op(27) || op(32)) { ?>
            <li class="sup w100">Dashboard
              <ul class="subul clearfix">
				<? if (op(24) || op(32)) { ?><li><a href="dashboard.php">Dashboard</a></li><? } ?>
                <? if (op(25) || op(32)) { ?><li class="sep"><a href="importa_dash.php">Importar datos SAP</a></li><? } ?>
                <? if (op(26)) { ?><li><a href="importa_dash_logistica.php">Importar datos log&iacute;stica</a></li><? } ?>
              </ul>
            </li>
			<? } ?>

            <? if (op(20) || op(22) || op(9) || op(31) || op(33)) { ?>
            <li class="sup w100">Log&iacute;stica 
              <ul class="subul clearfix">
                <?if($ventas==0){?>
                <? if (op(9)) { ?><li><a href="lista_estado.php">Estados</a></li><? } ?>                 
                <? if (op(9)) { ?><li><a href="lista_ciudad.php">Ciudades </a></li><? } ?> 
                <? if (op(9)) { ?><li><a href="lista_subcluster.php">Subcluster </a></li><? } ?>
                <? if (op(9)) { ?><li><a href="lista_sucursal.php">Sucursales </a></li><? } ?> 
                <? if (op(9)) { ?><li><a href="lista_cobertura.php">Cobertura </a></li><? } ?>
                <? if (op(9)) {?><li><a href="lista_preciosservicios.php">Precio Servicio </a></li><?  }?>
                <? if (op(9)) { ?><li><a href="lista_determinapta.php">Determina Planta</a></li><? } ?>
                <? if (op(9)) { ?><li><a href="lista_determinaptaserv.php">Determina Planta Servicio </a></li>
                <? if (op(9)) { ?><li><a href="lista_excepcioncp.php">Excepciones CP's</a></li><? } ?>

                <? if (op(20)) { ?><li><a href="lista_cp.php">Administrar CP's</a></li><? } ?>
                <? if (op(20)) { ?><li><a href="importa_sepomex.php">Importa CP's SEPOMEX</a></li><? } ?>
                <? if (op(20)) { ?><li><a href="importa_cp.php">Importar y Actualizar CP's</a></li><? } ?>
                <? if (op(20)) { ?><li><a href="lista_sucursal_ocurre.php">Sucursales ALMEX</a></li><? } ?>
                <? if (op(22)) { ?><li class="sep"><a href="importa_precios.php">Importar Listas de Precios</a></li><? } ?>
                <? if (op(31)) { ?><li><a href="importa_precios_proy.php">Importar Listas de Precios B2B</a></li><? }}} if($ventas==1){?>
                 <? if (op(9) || op(33)) {?><li><a href="lista_preciosservicios.php">Precio Servicio </a></li><?  }?>
                    <?}?>
              </ul>
            </li>
			<? } ?>


            <? if (op(18)) { ?>
            <li class="sup w150">Tiendas de Marca
              <ul class="subul clearfix">
                <li><a href="lista_tienda_marca.php">Tiendas</a></li>
                <li><a href="lista_usuario_tienda_marca.php">Usuarios</a></li>
                <li><a href="lista_tecnologia.php">Tecnolog&iacute;as</a></li>
              </ul>
            </li>
            <? } ?>
            <? if (op(19)) { ?>
            <li class="sup w100">Puntos
              <ul class="subul clearfix">
                <li><a href="lista_regla_puntos.php">Reglas de Puntos</a></li>
                <li><a href="resetea_puntos.php">Resetear Puntos</a></li>
                <li><a href="importa_puntos.php">Importar Puntos</a></li>
                <li><a href="importa_puntos_convenio.php">Importar Puntos Convenio</a></li>
                <li class="sep"><a href="reglas_puntos_flex.php">Reglas Puntos Flex/PEP</a></li>
                <li><a href="activar_puntos_flex.php">Activar Puntos Flex/PEP</a></li>
                <li><a href="resetea_puntos_flex.php">Resetear Puntos Flex/PEP</a></li>
                <li><a href="importa_puntos_flex.php">Importar Puntos Flex/PEP</a></li>
	                <li class="sep subsub"><a>Reportes</a>
                        <ul class="subul clearfix" style="margin:-13px 0 0 160px;">
			                <li><a href="lista_puntos_generados.php">Generaci&oacute;n de puntos</a></li>
            			    <li><a href="lista_puntos_flex.php">Puntos Flex</a></li>
            			    <li><a href="lista_puntos_vtex.php">Puntos utilizados Vtex</a></li>
                        </ul>
                    </li>
              </ul>
            </li>
            <? } ?>
            <? if (op(29)) { ?>
            <li class="sup w100">ODC
              <ul class="subul clearfix">
                <!--li><a href="importa_odc.php">Importar Empleados y Pagos</a></li-->
                <!--li class="sep"><a href="popup_odc.php">Configura Popup ODC</a></li-->
              </ul>
            </li>
            <? } ?>

            <li class="sup w150">Administraci&oacute;n
              <ul class="subul clearfix">
                <? if (op(1)) { ?><li><a href="lista_usuario.php">Usuarios</a></li><? } ?>
                <? if (!empty($_SESSION['ss_autorizar'])) { ?><li><a href="lista_autorizar.php">Autorizaci&oacute;n de contenido</a></li><? } ?>
                <? if (op(14)) { ?><li><a href="abc_datos_pedido.php">Datos fijos (pedidos, contacto)</a></li><? } ?>
                <? if (op(14)) { ?><li><a href="conf_family.php">Configuraci&oacute;n Family & Friends</a></li><? } ?>
                <? if (0) { ?><li><a href="conf_prod_dest.php">Configurar Productos Destacados</a></li><? } ?>
                <? if (op(16)) { ?><li><a href="abc_fecha_entrega.php">C&aacute;lculo de Fecha Entrega y Disponibilidad y Puntos</a></li><? } ?>
                <? if (op(14) || op(19)) { ?>
	                <li class="sep subsub"><a>Log de importaciones</a>
                        <ul class="subul clearfix" style="margin:-13px 0 0 160px;">
							<? if (op(14)) { ?><li><a href="log_precios.php">Importaci&oacute;n de precios</a></li><? } ?>
                            <? if (op(14)) { ?><li><a href="log_exist.php">Importaci&oacute;n de existencias</a></li><? } ?>
                            <? if (op(14)) { ?><li><a href="log_dashboard.php">Importaci&oacute;n de dashboard</a></li><? } ?>
                            <? if (op(14)) { ?><li><a href="log_odc.php">Importaci&oacute;n de RH</a></li><? } ?>
                            <? if (op(14) || op(19)) { ?><li><a href="log_puntos.php">Importaci&oacute;n de puntos</a></li><? } ?>
                            <? if (op(14) || op(19)) { ?><li><a href="log_guias.php">Importaci&oacute;n de gu&iacute;as</a></li><? } ?>
                        </ul>
                     </li>
                	<li>
                <? } ?>
                <? if (0 || op(28)) { ?><li class="sep"><a href="lista_tipo_bugs.php">Cat&aacute;logo de tipos de bugs</a></li><? } ?>
                <? if (0 || op(28)) { ?><li><a href="lista_bugs.php">Seguimiento de bugs</a></li><? } ?>
                <li class="sep"><a href="abc_password.php">Cambiar contrase&ntilde;a</a></li>
              </ul>
            </li>
            <li class="sup w100">Cat&aacute;logo
              <ul class="subul clearfix">
                <li><a href="lista_tipos_clientes.php">Tipos de clientes</a></li> 
                <? if (op(9)) { ?><li><a href="lista_planta.php">Plantas</a></li><? } ?>
                <li><a href="lista_servicios.php">Servicios</a></li> 
              </ul>
            </li>

          </ul>
        </div>
        <script type="text/javascript">activateMenu('nav');</script>