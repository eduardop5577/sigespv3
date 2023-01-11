/***********************************************************************************
* @Archivo JavaScript que incluye tanto los componentes como los eventos asociados 
* a la definicion de Banco. 
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
var formulario          = '';                           						// Variable que representa y contiene el panel que contiene los objetos
var cambiar     		= false;	                    						// Variable que verifica el Estatus de la Operacion para Modificacion
var sistema             = "CFG";                        						// Variable que contiene el nombre del sistema al que pertenece la pantalla
var ruta				= '../../controlador/cfg/sigesp_ctr_cfg_scb_banco.php'; 	// Ruta del Controlador de la Pantalla
var banderaGrabar 		= false;												// Indicador si se posee un Metodo de Guardar distinto al Original de funciones.js
var banderaEliminar		= false;												// Indicador si se posee un Metodo de Eliminar distinto al Original de funciones.js
var banderaNuevo		= true;												// Indicador si se posee un Metodo Nuevo distinto al Original de funciones.js
var Actualizar			= null;
var banderaCatalogo 	= 'estandar';
var banderaImprimir 	= false;

var Campos =new Array(  ['codban','novacio|'],
						['nomban','novacio|'],
						['dirban',''],
						['gerban',''],
						['telban',''],
						['conban',''],
						['movcon',''],
						['esttesnac',''],
						['codsudeban',''],
						['codswift','']); // Arreglo que contiene la informacion del Registro, deben coincidir con la Tabla en la Base de Datos

Ext.onReady
(
  function()
	{
	 Ext.QuickTips.init();
	 Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	 Xpos = ((screen.width/2)-(650/2));
	 Ypos = 100;
     formulario = new Ext.form.FormPanel({
    	   	 title:"Definici&#243;n de Banco",
    		 frame:true,
    		 style: 'position:absolute;margin-left:'+Xpos+'px;margin-top:'+Ypos+'px',
    		 width: 650,
    		 height: 350,
    		 labelPad: 10,
    		 items:[{
				        layout:"form",
						border:false,
						defaultType: "textfield",
						style: "margin-top:20px;padding-left:20px;",
						labelWidth:125,
						items:[{
						        xtype:"textfield",
						        fieldLabel:"C&#243;digo",
						        labelWidth:40,
						        name:"codigo",
						        id:"codban",
								autoCreate: {tag: 'input', type: 'text', size: '3', autocomplete: 'off', maxlength: '3'},
						        width:40,
								disabled:true
				        	   },
					           {
						        xtype:"textfield",
						        fieldLabel:"Nombre",
						        labelWidth:40,
						        name:"nombre",
						        id:"nomban",
						        width:250,
								autoCreate: {tag: 'input', type: 'text', size: '150', autocomplete: 'off', maxlength: '60', onkeypress: "return keyRestrict(event,'0123456789·ÈÌÛ˙¡…Õ”⁄abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!°;: ');"}
				        	   },
					           {
						        xtype:"textfield",
						        fieldLabel:"Direcci&#243;n",
						        labelWidth:40,
						        name:"direccion",
						        id:"dirban",
						        width:450,
								autoCreate: {tag: 'input', type: 'text', size: '200', autocomplete: 'off', maxlength: '80', onkeypress: "return keyRestrict(event,'0123456789·ÈÌÛ˙¡…Õ”⁄abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!°;: ');"}
					           },
					           {
						        xtype:"textfield",
						        fieldLabel:"Gerente",
						        labelWidth:40,
						        name:"gerente",
						        id:"gerban",
						        width:200,
								autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '60', onkeypress: "return keyRestrict(event,'0123456789·ÈÌÛ˙¡…Õ”⁄abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!°;: ');"}
						       },
					           {
						        xtype:"textfield",
						        fieldLabel:"Tel&#233;fono",
						        labelWidth:40,
						        name:"telefono",
						        id:"telban",
						        width:150,
								autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '20', onkeypress: "return keyRestrict(event,'0123456789');"}
						       },
					           {
						        xtype:"textfield",
						        fieldLabel:"M&#243;vil de Contacto",
						        labelWidth:40,
						        name:"movil",
						        id:"movcon",
						        width:150,
								autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '60', onkeypress: "return keyRestrict(event,'0123456789');"}
							   },
					           {
						        xtype:"textfield",
						        fieldLabel:"Email de Contacto",
						        labelWidth:40,
						        name:"email",
						        id:"conban",
						        width:200,
						        vtype:'email',
								autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '20', onkeypress: "return keyRestrict(event,'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.-_@');"}
					           },
					           {
						        xtype:"textfield",
						        fieldLabel:"C&#243;digo SUDEBAN",
						        labelWidth:40,
						        name:"codigo SUDEBAN",
						        id:"codsudeban",
						        width:40,
								autoCreate: {tag: 'input', type: 'text', size: '4', autocomplete: 'off', maxlength: '4'}
					           },
					           {
						        xtype:"checkbox",
						        fieldLabel:"Tesorer&#237;a Nacional",
						        labelWidth:40,
						        name:"tesoreria nacional",
						        id:"esttesnac",
						        inputValue:1
					           },
					           {
						        xtype:"textfield",
						        fieldLabel:"C&#243;digo SWIFT",
						        labelWidth:40,
						        name:"codigo SWIFT",
						        id:"codswift",
						        width:80,
								autoCreate: {tag: 'input', type: 'text', size: '12', autocomplete: 'off', maxlength: '12'}
						       }]
			        }]
    		});
     formulario.render("formulario_Banco");
	}
);

function irNuevo()
{
	limpiarCampos();
	var myJSONObject ={
		"oper":"nuevo"
	};
	
	ObjSon=Ext.util.JSON.encode(myJSONObject);
	parametros = 'ObjSon='+ObjSon;
	Ext.Ajax.request({
	url : '../../controlador/cfg/sigesp_ctr_cfg_scb_banco.php',
	params : parametros,
	method: 'POST',
	success: function ( result, request) 
	{ 
		datos = result.responseText;
		var codigo = eval('(' + datos + ')');
		if(codigo != "")
		{
			Ext.getCmp('codban').setValue(codigo);
		}
	}	
	})
}
function irBuscar()
{
	 mostrar_catalogo();
}

function irCancelar()
{
	 irNuevo();
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
									if(Registros[1]=='-8')
									{
										Ext.MessageBox.alert('Error', 'El registro no puede ser eliminado, no puede eliminar registros intermedios');
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