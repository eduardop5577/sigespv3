/***********************************************************************************
* @Archivo JavaScript que incluye tanto los componentes como los eventos asociados 
* al proceso de traspaso de cuentas entre estructuras 
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

var formTraspasoCuenta = null;
var fieldSetEstOrigen  = null;
var fieldSetEstDestino = null;
barraherramienta    = true;

Ext.onReady(function(){
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	
	fieldSetEstOrigen = new com.sigesp.vista.comFieldSetEstructuraPresupuesto({
		titform: 'Estructura Presupuestaria Origen',
		mostrarDenominacion:true,
		idtxt:'comfsori'
	});
	
	fieldSetEstDestino = new com.sigesp.vista.comFieldSetEstructuraPresupuesto({
		titform: 'Estructura Presupuestaria Destino',
		mostrarDenominacion:true,
		idtxt:'comfsdes'
	});
	
	var Xpos = ((screen.width/2)-(450));
	formTraspasoCuenta = new Ext.FormPanel({
		applyTo: 'formulario_traspasocuenta',
		width: 900,
		height: 500,
		title: 'Traspaso de Cuentas entre Estructuras Presupuestarias',
		frame:true,
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:50px;',
		items: [
			fieldSetEstOrigen.fieldSetEstPre,
			fieldSetEstDestino.fieldSetEstPre,
			{
				xtype: "checkbox",
	        	fieldLabel: "Incluir Montos",
				inputValue:1,
	        	id: 'estincmon'
	   		}
	    ] 
	});
});

function irProcesar(){
	
	var cadenaJson = "{'operacion':'procesar','codmenu':'"+codmenu+"', 'inlcuirMonto':" +Ext.getCmp('estincmon').getValue()+","+
					 "'estOrigen':[{'codestpro1':'" + fieldSetEstOrigen.obtenerCodigoEstructuraNivel(1)+"'," +
					 "'codestpro2':'" + fieldSetEstOrigen.obtenerCodigoEstructuraNivel(2)+"'," +
					 "'codestpro3':'" + fieldSetEstOrigen.obtenerCodigoEstructuraNivel(3)+"'," +
					 "'codestpro4':'" + fieldSetEstOrigen.obtenerCodigoEstructuraNivel(4)+"'," +
					 "'codestpro5':'" + fieldSetEstOrigen.obtenerCodigoEstructuraNivel(5)+"'," +
					 "'estcla':'" + fieldSetEstOrigen.obtenerEstClaEstructura()+"'}]," +
					 "'estDestino':[{'codestpro1':'" + fieldSetEstDestino.obtenerCodigoEstructuraNivel(1)+"'," +
					 "'codestpro2':'" + fieldSetEstDestino.obtenerCodigoEstructuraNivel(2)+"'," +
					 "'codestpro3':'" + fieldSetEstDestino.obtenerCodigoEstructuraNivel(3)+"'," +
					 "'codestpro4':'" + fieldSetEstDestino.obtenerCodigoEstructuraNivel(4)+"'," +
					 "'codestpro5':'" + fieldSetEstDestino.obtenerCodigoEstructuraNivel(5)+"'," +
					 "'estcla':'" + fieldSetEstDestino.obtenerEstClaEstructura()+"'}]}";
	var cadenaTemp = eval('(' + cadenaJson + ')');
	var ObjSon     = JSON.stringify(cadenaTemp);
	var parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_spg_traspasocuentas.php',
		params : parametros,
		method: 'POST',
		success: function ( resultad, request ){
			var respuesta = resultad.responseText;
			var	resultado = respuesta.split("|");
			var mensajeAd = '';
			if(resultado[0]!='-1')
			{
				cuentaExiste = resultado[2];
				if(cuentaExiste!='')
				{
					mensajeAd += '<br> La(s) cuenta(s) '+cuentaExiste+' ya existe(n)';
				}
				Ext.Msg.show({
					title:'mensaje',
					msg: 'Operaci&#243;n ejecutada con exito, '+resultado[0]+' cuentas insertada, '+resultado[1]+' cuentas no insertadas.<br><br>'+mensajeAd,
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.INFO
				});			
			}
			else
			{
				Ext.Msg.show({
					title:'Error',
					msg: 'Ha ocurrido un error la operaci&#243;n no fue ejecutada',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
				});
			}
			limpiarFormulario(formTraspasoCuenta);
	       	fieldSetEstOrigen.limpiarDenominaciones();
			fieldSetEstDestino.limpiarDenominaciones();
		},
		failure: function ( result, request){ 
			Ext.Msg.show({
				title:'Error',
				msg: 'Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			}); 
		} 
	});
}
function irCancelar()
{
	irNuevo();
}

function irNuevo()
{
	limpiarFormulario(formTraspasoCuenta);
	fieldSetEstOrigen.limpiarDenominaciones();
	fieldSetEstDestino.limpiarDenominaciones();
}

/*
function irProcesar(){
	 
        var cadenaJson = "{'operacion':'procesar','codmenu':'"+codmenu+"', 'inlcuirMonto':" +Ext.getCmp('estincmon').getValue()+","+
                                         "'estOrigen':[{'codestpro1':'" + fieldSetEstOrigen.obtenerCodigoEstructuraNivel(1)+"'," +
                                         "'codestpro2':'" + fieldSetEstOrigen.obtenerCodigoEstructuraNivel(2)+"'," +
                                         "'codestpro3':'" + fieldSetEstOrigen.obtenerCodigoEstructuraNivel(3)+"'," +
                                         "'codestpro4':'" + fieldSetEstOrigen.obtenerCodigoEstructuraNivel(4)+"'," +
                                         "'codestpro5':'" + fieldSetEstOrigen.obtenerCodigoEstructuraNivel(5)+"'," +
                                         "'estcla':'" + fieldSetEstOrigen.obtenerEstClaEstructura()+"'}]," +
                                         "'estDestino':[{'codestpro1':'" + fieldSetEstDestino.obtenerCodigoEstructuraNivel(1)+"'," +
                                         "'codestpro2':'" + fieldSetEstDestino.obtenerCodigoEstructuraNivel(2)+"'," +
                                         "'codestpro3':'" + fieldSetEstDestino.obtenerCodigoEstructuraNivel(3)+"'," +
                                         "'codestpro4':'" + fieldSetEstDestino.obtenerCodigoEstructuraNivel(4)+"'," +
                                         "'codestpro5':'" + fieldSetEstDestino.obtenerCodigoEstructuraNivel(5)+"'," +
                                         "'estcla':'" + fieldSetEstDestino.obtenerEstClaEstructura()+"'}]}";
        var cadenaTemp = eval('(' + cadenaJson + ')');
        var ObjSon     = JSON.stringify(cadenaTemp);
        var parametros = 'ObjSon='+ObjSon; 
        Ext.Ajax.request({
                url : '../../controlador/cfg/sigesp_ctr_cfg_traspasocuentas.php',
                params : parametros,
                method: 'POST',
                success: function ( resultad, request ){
                        var respuesta = resultad.responseText;
                        var	resultado = respuesta.split("|");
                        var mensajeAd = '';
                        if(resultado[0]!='-1'){
                                cuentaExiste = resultado[2].split(",");
                                if(cuentaExiste.length>1){
                                        for (var index = 0; index < cuentaExiste.length; index++) {
                                                mensajeAd += '<br> La cuenta '+cuentaExiste[index]+' ya existe';
                                        }
                                }
                                Ext.Msg.show({
                                        title:'mensaje',
                                        msg: 'Operaci&#243;n ejecutada con exito, '+resultado[0]+' cuentas insertada, '+resultado[1]+' cuentas no insertadas.<br><br>'+mensajeAd,
                                        buttons: Ext.Msg.OK,
                                        icon: Ext.MessageBox.INFO
                                });			
                        }
                        else{
                                Ext.Msg.show({
                                        title:'Error',
                                        msg: 'Ha ocurrido un error la operaci&#243;n no fue ejecutada',
                                        buttons: Ext.Msg.OK,
                                        icon: Ext.MessageBox.ERROR
                                });
                        }
                        limpiarFormulario(formTraspasoCuenta);
                       fieldSetEstOrigen.limpiarDenominaciones();
                        fieldSetEstDestino.limpiarDenominaciones();
                },
                failure: function ( result, request){ 
                        Ext.Msg.show({
                                title:'Error',
                                msg: 'Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo',
                                buttons: Ext.Msg.OK,
                                icon: Ext.MessageBox.ERROR
                        }); 
                } 
        });
}
*/