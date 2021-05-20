		
		  <div id="mostrar_graficas">
            <h1>Gr&aacute;fica: <a href="javascript:void();" id="cerrar_graficas" class="cerrar_fil">Cerrar</a></h1>
            <div id="gra1" style="height:500px"></div>
            <div style="text-align:center">
            <table width="50%" border="0" align="center" cellpadding="3" cellspacing="0">
              <tr>
                <td width="5%" nowrap="nowrap"><div align="right">Eje Y:</div></td>
                <td width="6%"><select id="eje_y" name="eje_y">
                		<option value="Pedidos" <? if ($eje_y=='Pedidos') echo 'selected';?> >Pedidos</option>
                		<option value="Montos" <? if ($eje_y=='Montos') echo 'selected';?> >Montos</option>
                		<option value="Unidades" <? if ($eje_y=='Unidades') echo 'selected';?> >Unidades</option>
                    </select></td>
                <td width="3%" nowrap="nowrap"><div align="right">Eje X:</div></td>
                <td width="11%"><select id="eje_x" name="eje_x">
                  <option value="tienda" <? if ($eje_x=='tienda') echo 'selected';?> >Tiendas</option>
                  <option value="vendedor" <? if ($eje_x=='vendedor') echo 'selected';?> >Vendedores</option>
                  <!--option value="tiendavend" <? if ($eje_x=='tiendavend') echo 'selected';?> >Tiendas / Vendedores</option-->
                </select></td>
                <td width="4%" nowrap="nowrap"><div align="right">Series X:</div></td>
                <td width="14%"><select id="serie_x" name="serie_x">
                  <option value="vendedor" <? if ($serie_x=='vendedor') echo 'selected';?> >Vendedores</option>
                  <option value="tienda" <? if ($serie_x=='tienda') echo 'selected';?> >Tiendas</option>
                  <option value="estatus" <? if ($serie_x=='estatus') echo 'selected';?> >Estatus</option>
                  <option value="forma_pago" <? if ($serie_x=='forma_pago') echo 'selected';?> >Forma de Pago (Condition)</option>
                  <option value="categoria" <? if ($serie_x=='categoria') echo 'selected';?> >Categor&iacute;a</option>
                  <option value="lista_precios" <? if ($serie_x=='lista_precios') echo 'selected';?> >Lista Precios</option>
                  <option value="backorder" <? if ($serie_x=='backorder') echo 'selected';?> >Back Order</option>
                  <option value="facturado" <? if ($serie_x=='facturado') echo 'selected';?> >Facturado vs No facturado</option>
                </select></td>
                <td width="57%"><input type="button" value="Actualizar" class="boton" id="act_gra" onclick="xajax_grafica(document.forma_dashboard.eje_x.value, document.forma_dashboard.eje_y.value, document.forma_dashboard.serie_x.value);" /></td>
              </tr>
             </table>
             </div>
             <div id="tabla_grafica"></div>
		  </div>
          
          