/***********************************************************************************
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

barraherramienta    = true;
var Actualizar=null
var formProcedencia     = null;
var ruta 				= '../../controlador/cfg/sigesp_ctr_cfg_procedencia.php'; //ruta del controlador
var banderaGrabar 		= false;													// Indicador si se posee un Metodo de Guardar distinto al Original de funciones.js
var banderaEliminar		= false;													// Indicador si se posee un Metodo de Eliminar distinto al Original de funciones.js
var banderaNuevo		= true;													// Indicador si se posee un Metodo Nuevo distinto al Original de funciones.js
var Actualizar=null;
var banderaCatalogo = 'estandar';
var banderaImprimir = false;


var Campos =new Array(
		
	        ['procede','novacio|'],
	        ['codsis','novacio|'],
			['opeproc','novacio|'],
			['desproc','novacio|']
	    )



Ext.onReady(function(){
	
	Ext.QuickTips.init();
	var Xpos = ((screen.width/2)-(700/2));
	
	formProcedencia = new Ext.FormPanel({
	applyTo: 'formulario_procedencia',
	width: 700,
	height: 150,
	title: 'Procedencia',
	frame:true,
	labelWidth:200,
	style:'position:absolute;margin-left:'+Xpos+'px;margin-top:150px;',
	items: [{
		xtype: 'textfield',
		fieldLabel: 'C&#243;digo',
		name: 'C&#243;digo',
		id: 'procede',
		maxLength: 6,
		width:60,
		autoCreate: {tag: 'input', type: 'text', size: '6', autocomplete: 'off', maxlength: '6'}
		},{
		xtype: 'textfield',
		fieldLabel: 'C&#243;digo sistema',
		name: 'C&#243;digo sistema',
		id: 'codsis',
		maxLength: 3,
		readOnly:true,
		width:60
		},{
		xtype:'button',
		iconCls: 'menubuscar',
		width:60,
		style:'position:absolute;left:270px;top:27px;',
		handler: function (){
								mostrarCatalogoSistema(Ext.getCmp('codsis'))
							}
		},{
		xtype: 'textfield',
		fieldLabel: 'Operaci&#243;n procedencia',
		name: 'Operaci&#243;n',
		id: 'opeproc',
		maxLength: 3,
		autoCreate: {tag: 'input', type: 'text', size: '3', autocomplete: 'off', maxlength: '3'},
		width:60
		},{
		xtype: 'textfield',
		fieldLabel: 'Descripci&#243;n',
		name: 'Descripci&#243;n',
		id: 'desproc',		
		width: 400
		}]
	});
	
});

function irBuscar()
{
	mostrar_catalogo();
	
};

function irCancelar()
{
	irNuevo();
}

function irNuevo()
{
	formProcedencia.findById('procede').setDisabled(false);
	limpiarFormulario(formProcedencia);
}

function irGuardar()
{
	var mensajeexito = 'Registro <operacion> con &#233;xito';
    var mensajeerror = 'Error al <operacion> registro';
	var cadjson = '';
	if(Actualizar == null)
	{
		operacion='incluir';
	    mensajeexito = mensajeexito.replace('<operacion>','incluido');
	    mensajeerror = mensajeerror.replace('<operacion>','incluir');
    } 
    else
	{
		operacion='actualizar';
    	mensajeexito = mensajeexito.replace('<operacion>','actualizado');
    	mensajeerror = mensajeerror.replace('<operacion>','actualizar');
    }
	try
	{
		if(validarObjetos2()==false)
		{
			return false;
		}
		else
		{
			cadjson    = cargarJson(operacion);
			var objjson = Ext.util.JSON.decode(cadjson);
			if (typeof(objjson) == 'object')
			{
				var parametros = 'ObjSon=' + cadjson;
				Ext.Ajax.request({
				url : '../../controlador/cfg/sigesp_ctr_cfg_procedencia.php',
				params : parametros,
				method: 'POST',
				success: function ( result, request){ 
					var datos = result.responseText;
					var codigo = datos.split("|");
					if(codigo[1] == '1'){
							Ext.Msg.show({
								title:'Mensaje',
								msg: mensajeexito,
								buttons: Ext.Msg.OK,
								icon: Ext.MessageBox.INFO
								});
							limpiarCampos();
							Actualizar=null;
					}
					else{
							Ext.Msg.show({
								title:'Mensaje',
								msg: mensajeerror,
								buttons: Ext.Msg.OK,
								icon: Ext.MessageBox.ERROR
								});
					}
					
				}	
				});
			}
		}
	}	
	catch(e)
	{
	}
}

function irEliminar()
{
	var respuesta;
	
	function respuesta(btn)
	{
	 if(btn=='yes')
	 {
		var cadjson    = cargarJson('eliminar');
		try
		{
			var objjson = Ext.util.JSON.decode(cadjson);
			if (typeof(objjson) == 'object') {
					var parametros = 'ObjSon=' + cadjson;
					Ext.Ajax.request({
					url : ruta,
					params : parametros,
					method: 'POST',
					success: function ( result, request) 
					{ 
						var datos = result.responseText;
						var codigo = datos.split("|");
						if(codigo[1] == '1')
						{
							Ext.Msg.show({
								title:'Mensaje',
								msg: 'Registro eliminado con &#233;xito',
								buttons: Ext.Msg.OK,
								icon: Ext.MessageBox.INFO
							});
							limpiarCampos();
							Actualizar=null;
						}
						else
						{
							var respuesta = eval('('+result.responseText+')');
							if(respuesta.mensaje != "")
							{
								Ext.Msg.show({
									title:'Mensaje',
									msg: 'Error al tratar de eliminar el registro <br>'+respuesta.mensaje,
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.ERROR
								}); 
							}
							else
							{
								Ext.Msg.show({
									title:'Mensaje',
									msg: 'Error al tratar de eliminar el registro ',
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.ERROR
								});
							}
						}
						
					}	
					})
				}
			}
			catch(e)
			{
			}
	 }
	}
	if(Actualizar)
	{
			var mensajeconfirma = '&#191;Desea eliminar este registro&#63?';
			Ext.MessageBox.confirm('Confirmar', mensajeconfirma, respuesta);
	}
	else
	{
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'El registro debe estar guardado para poder eliminarlo, verifique por favor',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			}); 
	}	

}

