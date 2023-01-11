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
var Actualizar  = null
var ruta 		= '../../controlador/cfg/sigesp_ctr_cfg_scb_cuentabanco.php'; //ruta del controlador
var banderaGrabar 		= false;													// Indicador si se posee un Metodo de Guardar distinto al Original de funciones.js
var banderaEliminar		= false;													// Indicador si se posee un Metodo de Eliminar distinto al Original de funciones.js
var banderaNuevo		= false;													// Indicador si se posee un Metodo Nuevo distinto al Original de funciones.js
var banderaCatalogo     = 'generica';
var banderaImprimir     = false;
var formulario = '';

var Campos =new Array(
			['ctaban','novacio|'],
			['dencta','novacio|'],
	        ['ctabanext','novacio|'],
	        ['codtipcta','novacio|'],
	        ['codban','novacio|'],
	        ['sc_cuenta','novacio|'],
	        ['fecapr','novacio|'],
	        ['feccie',''],
	        ['estact','novacio|'],
	        ['nomtipcta',''],
	        ['nomban',''],
	        ['denctascg',''],
	        ['codmon',''],
		['denmon','']
	    )

//Validaci�n para fechas - No permite seleccionar una fecha de cierre menor a la decha de apertura
Ext.apply(Ext.form.VTypes, {
    daterange : function(val, field) {
        var date = field.parseDate(val);

        if(!date){
            return;
        }
        if (field.startDateField && (!this.dateRangeMax || (date.getTime() != this.dateRangeMax.getTime()))) {
            var start = Ext.getCmp(field.startDateField);
            start.setMaxValue(date);
            start.validate();
            this.dateRangeMax = date;
        } 
        else if (field.endDateField && (!this.dateRangeMin || (date.getTime() != this.dateRangeMin.getTime()))) {
            var end = Ext.getCmp(field.endDateField);
            end.setMinValue(date);
            end.validate();
            this.dateRangeMin = date;
        }
        /*
         * Always return true since we're only using this vtype to set the
         * min/max allowed values (these are tested for after the vtype test)
         */
        return true;
    }
});



Ext.onReady(function(){
	//, onkeypress: "return keyRestrict(event,'0123456789-');"
	
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	formulario = new Ext.FormPanel({
	applyTo: 'formulario_cuentabanco',
	width: 700,
	height: 400,
	title: 'Definici&#243;n de Cuenta de Bancos',
	frame:true,
	labelWidth:150,
	defaultType: 'textfield',
	style:'position:absolute;margin-left:163px;margin-top:80px;',
	items: [{
			xtype: 'textfield',
			fieldLabel: 'C&#243;digo',
			name: 'C&#243;digo',
			id: 'ctaban',
			autoCreate: {tag: 'input', type: 'text', size: '28', autocomplete: 'off', maxlength: '25', onkeypress: "return keyRestrict(event,'0123456789-');"},
			listeners:{'blur' : function(campo)
							{
							valor = String(campo.getValue()).trim();
							valor = rellenarCampoCerosIzquierda(valor,25);
							campo.setValue(valor);
							}
					  },
			width:200
		},{
			xtype: 'textfield',
			fieldLabel: 'Denominaci&#243;n',
			name: 'Denominaci&#243;n',
			autoCreate: {tag: 'input', type: 'text', maxlength: 50, onkeypress: "return keyRestrict(event,'0123456789����������abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!�;: ');"},
			id: 'dencta',		
			width: 400
		},{
			xtype: 'textfield',
			fieldLabel: 'Cuenta extendida ',
			name: 'Cuenta extendida ',
			id: 'ctabanext',
			autoCreate: {tag: 'input', type: 'text', size: '20', autocomplete: 'off', maxlength: '20', onkeypress: "return keyRestrict(event,'0123456789-');"},		
			width: 200
		},{
			xtype: 'textfield',
			fieldLabel: 'Tipo de cuenta ',
			name: 'Tipo de cuenta ',
			id: 'codtipcta',
			disabled:true,		
			width: 50
		},{
			xtype:'button',
			iconCls: 'menubuscar',
			style:'position:absolute;left:210px;top:78px;',
			handler: function (){
						catalogoTipoCuenta();
						}
		},{		
			xtype: 'textfield',
			labelSeparator :'',
			style:'border:none;background:#f1f1f1;',
			id: 'nomtipcta',
			disabled:true,		
			width: 400
		},{
			xtype: 'textfield',
			fieldLabel: 'Banco',
			name: 'Banco',
			id: 'codban',
			disabled:true,		
			width: 50
		},{
			xtype:'button',
			iconCls: 'menubuscar',
			style:'position:absolute;left:210px;top:128px;',
			handler: function (){
						catalogoBanco();
						}	
		},{
			xtype: 'textfield',
			labelSeparator :'',
			style:'border:none;background:#f1f1f1;',
			id: 'nomban',
			disabled:true,		
			width: 300
		},{
			xtype: 'textfield',
			fieldLabel: 'Cuenta contable',
			name: 'Cuenta contable',
			id: 'sc_cuenta',
			disabled:true,		
			width: 200
		},{
			xtype:'button',
			iconCls: 'menubuscar',
			style:'position:absolute;left:360px;top:178px;',
			handler: function (){
						mostrarCatalogoCuentaContable('catalogocuentamovimiento',Ext.getCmp('sc_cuenta'),Ext.getCmp('denctascg'));
						}	
		},{
			xtype: 'textfield',
			labelSeparator :'',
			style:'border:none;background:#f1f1f1;',
			id: 'denctascg',
			disabled:true,		
			width: 400
		},{
			xtype: 'datefield',
			fieldLabel: 'Fecha de apertura',
			name:'Fecha de apertura',
			id: 'fecapr',
			endDateField: 'feccie',
			vtype: 'daterange',
			width: 100,
			listeners:{
				'blur':function(objeto){
					var fechasta = new Date(Ext.getCmp('feccie').getValue());
					var fecdesde = new Date(objeto.getValue());
					if(!ue_comparar_intervalo(fecdesde.format(Date.patterns.fechacorta), fechasta.format(Date.patterns.fechacorta))){
						Ext.MessageBox.show({
			    			title:'Advertencia',
							msg: 'La fecha de apertura debe ser menor a la fecha de cierre',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.INFO
						});
						objeto.setValue('');
					}
				}
			}
		},{
			xtype: 'datefield',
			fieldLabel: 'Fecha de cierre',
			name:'Fecha de cierre',
			id: 'feccie',
			vtype: 'daterange',
			startDateField: 'fecapr',
			width: 100,
			listeners:{
				'blur':function(objeto){
					var fechasta = new Date(objeto.getValue());
					var fecdesde = new Date(Ext.getCmp('fecapr').getValue());
					if(!ue_comparar_intervalo(fecdesde.format(Date.patterns.fechacorta), fechasta.format(Date.patterns.fechacorta))){
						Ext.MessageBox.show({
			    			title:'Advertencia',
							msg: 'La fecha de cierre debe ser mayor a la fecha de apertura',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.INFO
						});
						objeto.setValue('');
					}
				}
			}
		},{
			xtype: 'checkbox',
			fieldLabel: 'Activa',
			id: 'estact',
			inputValue:1,
			checked: true
		},{
			xtype: 'textfield',
			fieldLabel: 'Moneda',
			name: 'Moneda',
			id: 'codmon',
			disabled:true,		
			width: 50
		},{
			xtype:'button',
			iconCls: 'menubuscar',
			style:'position:absolute;left:210px;top:305px;',
			handler: function (){
                                                if(Actualizar == null)
                                                {
                                                    CatalogoMonedaObjeto();
						}
                                            }
		},{
			xtype: 'textfield',
			labelSeparator :'',
			style:'border:none;background:#f1f1f1;',
			id: 'denmon',
			disabled:true,		
			width: 300
		}]
		
		
	});
	
});

function irBuscar()
{
	mostrarCatalogoCuentaBanco();
	
};

function irCancelar()
{
	irNuevo();
}

function irNuevo()
{
	limpiarFormulario(formulario);
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
			cadjson=cargarJson(operacion);
			var objjson = Ext.util.JSON.decode(cadjson);
			if (typeof(objjson) == 'object')
			{
				var parametros = 'ObjSon=' + cadjson;
				obtenerMensaje('procesar','','Guardando Datos');
				Ext.Ajax.request({
					url : ruta,
					params : parametros,
					method: 'POST',	
					success: function ( resultad, request )
					{ 
						Ext.Msg.hide();
						var datos = resultad.responseText;
						var respuesta = datos.split("|");
						if (respuesta[1] == '1')
						{
							Ext.MessageBox.alert('mensaje','Registro '+mensajeexito+ '');
							Actualizar=null;
						}
						else
						{
							if(respuesta[1] == '-10')
							{
								Ext.MessageBox.alert('Error', 'La combinaci&#243;n afectaci&#243;n presupuestaria y tipo ya existe');
							}
							else
							{
								if(respuesta[0]!='')
								{
									Ext.MessageBox.alert('Error', respuesta[0]);
								}
								else
								{
									Ext.MessageBox.alert('Error', 'Ocurri&#243; un error el registro no pudo ser '+mensajeerror);
								}
							}
						}
						limpiarFormulario(formulario);
					},
					failure: function (result, request)
					{ 
						Ext.Msg.hide();
						Ext.MessageBox.alert('Error', resultad.responseText);
						mascara.hide();
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
	function eliminarRegistro(btn)
	{
		if(btn=='yes')
		{
			var	cadjson=cargarJson('eliminar');
			try
			{
				var objjson = Ext.util.JSON.decode(cadjson);
				if (typeof(objjson) == 'object')
				{
					var parametros = 'ObjSon=' + cadjson;
					obtenerMensaje('procesar','','Eiminando Datos');
					Ext.Ajax.request({
						url : ruta,
						params : parametros,
						method: 'POST',	
						success: function ( resultad, request )
						{ 
							Ext.Msg.hide();
							var datos = resultad.responseText;
							var Registros = datos.split("|");
						 	if (Registros[1] == '1')
							{
						 		Ext.MessageBox.alert('mensaje','Registro Eliminado con &#233;xito');
								Actualizar=null;
							}
							else
							{
								if(Registros[1]=='-9')
								{
									Ext.MessageBox.alert('Error', 'El registro no puede ser eliminado, esta vinculado con otros registros');
							  	}
								else
								{
									if (Registros[0]!='')
									{
										Ext.MessageBox.alert('Error', Registros[0]);
								  	}
									else
									{
										Ext.MessageBox.alert('Error', 'El registro no pudo ser eliminado');
								  	}
								}
							}
							limpiarFormulario(formulario);
						},
						failure: function (result, request)
						{ 
							Ext.Msg.hide();
							Ext.MessageBox.alert('Error', resultad.responseText);
						}
					});
				}
			}
			catch(e)
			{
			}
		}
	}
	Ext.MessageBox.confirm('Confirmar', '&#191;Desea eliminar este registro&#63;', eliminarRegistro);
}


