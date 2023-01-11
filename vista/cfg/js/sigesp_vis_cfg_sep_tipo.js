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
var formulariotiposep = null;
var Actualizar      = null
var banderaNuevo    = true; 												// Indicador si se posee un Metodo Nuevo distinto al Original de funciones.js
var banderaGrabar 	= true;												// Indicador si se posee un Metodo de Guardar distinto al Original de funciones.js
var banderaEliminar = true;												// Indicador si se posee un Metodo de Eliminar distinto al Original de funciones.js
var banderaCatalogo = 'estandar';
var ruta			= '../../controlador/cfg/sigesp_ctr_cfg_sep_tipo.php'; 	// Ruta del Controlador de la Pantalla
var Campos =new Array(
	        ['codtipsol','novacio|'],
	        ['dentipsol','novacio|'],
			['estope','|'],
			['modsep','|'],
			['estayueco','|'],
			['estdifiva','|']
	    )
	    
Ext.onReady(function(){
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';	
	Ext.QuickTips.init();
	var Xpos = ((screen.width/2)-(700/2));
	var Ypos = 150;
	formulariotiposep = new Ext.FormPanel({
		applyTo: 'formulario_sep_tipo',
		width: 700,
		height: 220,
		title: 'Tipo de solicitud de ejecuci&#243;n presupuestaria',
		frame:true,
		defaults: {width: 700},
	    defaultType: 'textfield',
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:'+Ypos+'px;',
		items: [{
			xtype: 'textfield',
			labelSeparator:'',
			fieldLabel: 'C&#243;digo',
			name: 'c&#243;digo',
			id: 'codtipsol',
			disabled:true,
			maxLength: 2,
			width: 40,
			binding:true,
			hiddenvalue:'',
			defaultvalue:'',
			allowBlank:false
			},{
			xtype: 'textfield',
			labelSeparator:'',
			fieldLabel: 'Denominaci&#243;n',
			name: 'denominaci&#243;n',
			autoCreate: {tag: 'input', type: 'text', maxlength: 80, onkeypress: "return keyRestrict(event,'0123456789·ÈÌÛ˙¡…Õ”⁄abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!°;: ');"},
			id: 'dentipsol',		
			width: 300,
			binding:true,
			hiddenvalue:'',
			defaultvalue:'',
			allowBlank:false
			},{
			xtype: 'radiogroup',
			labelSeparator:'',
			fieldLabel:'Afectaci&#243;n presupuestaria',
			id:'estope',
			style:'position:absolute;left:110px;top:10px;',
			binding:true,
			hiddenvalue:'',
			defaultvalue:'',
			allowBlank:false,
			items: [
					{	
						boxLabel: 'Precompromiso',
						name: 'rbestope',
						id:'precompromiso',
						inputValue:'R',
						listeners:{ 
									'check': function (checkbox, checked) {
										if(checked)
										{
											Ext.getCmp('modsep').items.items[2].disable();
											Ext.getCmp('estayueco').setValue(false);
											Ext.getCmp('estayueco').disable();
											Ext.getCmp('estdifiva').disable();
										}
									 }
									}
					},{
						boxLabel: 'Compromiso',
						name: 'rbestope',
						id: 'compromiso',
						inputValue:'O',
						listeners:{ 
									'check': function (checkbox, checked) {
										if(checked)
										{
											Ext.getCmp('modsep').items.items[0].enable();
											Ext.getCmp('modsep').items.items[1].enable();
											Ext.getCmp('modsep').items.items[2].enable();
											Ext.getCmp('estayueco').enable();
											Ext.getCmp('estdifiva').enable();
										}
									 }
									}
					},{
						boxLabel: 'Sin afectaci&#243;n',
						name: 'rbestope',
						id:'sinafectacion',
						inputValue:'S',
						listeners:{ 
									'check': function (checkbox, checked) {
										if(checked)
										{
											Ext.getCmp('modsep').items.items[2].disable();
											Ext.getCmp('estayueco').setValue(false);
											Ext.getCmp('estayueco').disable();
											Ext.getCmp('estdifiva').disable();
										}
									 }
									}
					}
			        ]// fin de radio group de afectacion
			},{
			xtype: 'radiogroup',
			id:'modsep',
			style:'position:absolute;left:110px;',
			fieldLabel:'Tipo',
			labelSeparator:'',
			binding:true,
			hiddenvalue:'',
			defaultvalue:'',
			allowBlank:false,
			items: [
					{
						boxLabel: 'Bienes',
						name: 'rbmodsep',
						id: 'bienes',
						inputValue:'B',
						listeners:{ 
									'check': function (checkbox, checked) {
										if(checked)
										{
											Ext.getCmp('estope').items.items[0].enable();
											Ext.getCmp('estope').items.items[1].enable();
											Ext.getCmp('estope').items.items[2].enable();
											Ext.getCmp('estayueco').setValue(false);
											Ext.getCmp('estayueco').disable();
											Ext.getCmp('estdifiva').setValue(false);
											Ext.getCmp('estdifiva').disable();
										}
									 }
									}
					},{
						boxLabel: 'Servicios',
						name: 'rbmodsep',
						id: 'servicios',
						inputValue:'S',
						listeners:{ 
									'check': function (checkbox, checked) {
										if(checked)
										{
											Ext.getCmp('estope').items.items[0].enable();
											Ext.getCmp('estope').items.items[1].enable();
											Ext.getCmp('estope').items.items[2].enable();
											Ext.getCmp('estayueco').setValue(false);
											Ext.getCmp('estayueco').disable();
											Ext.getCmp('estdifiva').setValue(false);
											Ext.getCmp('estdifiva').disable();
										}
									 }
									}
					},{
						boxLabel: 'Otros',
						name: 'rbmodsep',
						id: 'otros',
						inputValue:'O',
						listeners:{ 
									'check': function (checkbox, checked) {
										if(checked)
										{
											Ext.getCmp('estope').items.items[0].disable();
											Ext.getCmp('estope').items.items[1].enable();
											Ext.getCmp('estope').items.items[2].disable();
											Ext.getCmp('estayueco').enable();
											Ext.getCmp('estdifiva').enable();
										}
									 }
									}
					}
			        ]//fin del radio group de tipo
			},{
	            xtype: "checkbox",
	            fieldLabel: "Ayudas econ&#243;micas personales",
	            id: 'estayueco',
	           	inputValue: 'A',
	           	binding:true,
	           	defaultvalue:'',
	           	listeners:{ 
					'check': function (checkbox, checked) {
						if(checked)
						{
							Ext.getCmp('estope').items.items[0].disable();
							Ext.getCmp('estope').items.items[1].enable();
							Ext.getCmp('estope').items.items[2].disable();
							Ext.getCmp('modsep').items.items[0].disable();
							Ext.getCmp('modsep').items.items[1].disable();										
						}
					 }
				}
	        },{
	            xtype: "checkbox",
	            fieldLabel: "Diferencial de iva",
	            id: 'estdifiva',
	           	inputValue: '1',
	           	binding:true,
	           	defaultvalue:'',
	           	listeners:{ 
					'check': function (checkbox, checked) {
						if(checked)
						{
							Ext.getCmp('estope').items.items[0].disable();
							Ext.getCmp('estope').items.items[1].enable();
							Ext.getCmp('estope').items.items[2].disable();
							Ext.getCmp('modsep').items.items[0].disable();
							Ext.getCmp('modsep').items.items[1].disable();										
						}
					 }
				}
	        }
		]//fin de items del panel
	});
});

function irNuevo()
{
	limpiarFormulario(formulariotiposep);
	var myJSONObject = {
		"operacion":"nuevo"
	};
	
	var ObjSon     = Ext.util.JSON.encode(myJSONObject);
	var parametros = 'ObjSon='+ObjSon;
	Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_sep_tipo.php',
		params : parametros,
		method: 'POST',
		success: function ( result, request)
		{
			var codigo = result.responseText;
			if(codigo != "")
			{
				Ext.getCmp('codtipsol').setValue(codigo);
				Ext.getCmp('estope').items.items[0].enable();
				Ext.getCmp('estope').items.items[1].enable();
				Ext.getCmp('estope').items.items[2].enable();
				Ext.getCmp('modsep').items.items[0].enable();
				Ext.getCmp('modsep').items.items[1].enable();
				Ext.getCmp('modsep').items.items[2].enable();
				Ext.getCmp('estayueco').enable();
			}
		}	
	});
}

function irCancelar()
{
	 irNuevo();
}

function irGuardar()
{
	if(Actualizar==null)
	{
		operacion='incluir';
		mensaje='incluido';
	}
	else
	{	
		operacion='actualizar';
		mensaje='modificado';			
	}

	var Json    = getItems(formulariotiposep,operacion,'N',null,null);
	try
	{
		var objjson = Ext.util.JSON.decode(Json);
		if (typeof(objjson) == 'object')
		{
			var parametros = 'ObjSon=' + Json;
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
						Ext.MessageBox.alert('mensaje','Registro '+mensaje + ' con &#233;xito');
						Actualizar=null;
					}
					else
					{
						if(respuesta[1] == '-10')
						{
							Ext.MessageBox.alert('Error', respuesta[0]);
						}
						else
						{
							if(respuesta[0]!='')
							{
								Ext.MessageBox.alert('Error', respuesta[0]);
							}
							else
							{
								Ext.MessageBox.alert('Error', 'Ocurri&#243; un error el registro no pudo ser '+mensaje);
							}
						}
					}
					limpiarFormulario(formulariotiposep);
					Ext.getCmp('estope').items.items[0].enable();
					Ext.getCmp('estope').items.items[1].enable();
					Ext.getCmp('estope').items.items[2].enable();
					Ext.getCmp('modsep').items.items[0].enable();
					Ext.getCmp('modsep').items.items[1].enable();
					Ext.getCmp('modsep').items.items[2].enable();
					Ext.getCmp('estayueco').enable();
					Ext.getCmp('estdifiva').enable();
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
	catch(e)
	{
		//no imprimo la excepcion
	}
}

function irEliminar()
{
	function eliminarRegistro(btn)
	{
		if(btn=='yes')
		{
			var Json    = getItems(formulariotiposep,'eliminar','N',null,null);
			try
			{
				var objjson = Ext.util.JSON.decode(Json);
				if (typeof(objjson) == 'object')
				{
					var parametros = 'ObjSon=' + Json;
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
									if(Registros[1]=='-8')
									{
										Ext.MessageBox.alert('Error','El registro no puede ser eliminado, no puede eliminar registros intermedios');
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
							}
							limpiarFormulario(formulariotiposep);
							Ext.getCmp('estope').items.items[0].enable();
							Ext.getCmp('estope').items.items[1].enable();
							Ext.getCmp('estope').items.items[2].enable();
							Ext.getCmp('modsep').items.items[0].enable();
							Ext.getCmp('modsep').items.items[1].enable();
							Ext.getCmp('modsep').items.items[2].enable();
							Ext.getCmp('estayueco').enable();
							Ext.getCmp('estdifiva').enable();
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
				//no imprimo la excepcion
			}
		}
	}
	Ext.MessageBox.confirm('Confirmar', '&#191;Desea eliminar este registro&#63;', eliminarRegistro);
}