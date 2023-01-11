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

var formulario          = '';                           									// Variable que representa y contiene el panel que contiene los objetos
var cambiar     		= false;	                    									// Variable que verifica el Estatus de la Operacion para Modificacion							// Variable que contiene el nombre fÌsico de la Pantalla
var sistema             = "CFG";                        									// Variable que contiene el nombre del sistema al que pertenece la pantalla
var ruta				= '../../controlador/cfg/sigesp_ctr_cfg_scb_fondosenavance.php'; 	// Ruta del Controlador de la Pantalla
var banderaGrabar 		= false;															// Indicador si se posee un Metodo de Guardar distinto al Original de funciones.js
var banderaEliminar		= false;															// Indicador si se posee un Metodo de Eliminar distinto al Original de funciones.js
var banderaNuevo		= true;																// Indicador si se posee un Metodo Nuevo distinto al Original de funciones.js
var banderaCatalogo		= 'estandar';
var gridUsu				= null;
var Actualizar=null;

var Campos =new Array(
						['codemp',''],
						['codtipfon','novacio|'],
						['dentipfon','novacio|'],
						['porrepfon','novacio|']); // Arreglo que contiene la informacion del Registro, deben coincidir con la Tabla en la Base de Datos

Ext.onReady
(
  function()
	{
	 Ext.QuickTips.init();
	 Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	 
	 Xpos = ((screen.width/2)-(600/2));
	 Ypos = ((screen.height/2)-(600/2));	
     formulario = new Ext.form.FormPanel({
    	   	 title:"Definici&#243;n de Tipos de Fondos en Avance",
    		 frame:true,
    		 style: 'position:absolute;margin-left:'+Xpos+'px;margin-top:'+Ypos+'px',
    		 width: 600,
    		 height: 200,
    		 labelPad: 10,
    		 items:[{
				        xtype:"hidden",
				        name:"codemp",
				        id:"codemp",	
						value:''
			        },
			        {
				        layout:"form",
						border:false,
						defaultType: "textfield",
						style: "margin-top:30px;padding-left:25px;",
						labelWidth:100,
						items:[
						       {
						        xtype:"textfield",
						        fieldLabel:"C&#243;digo",
						        labelSeparator:'',
						        labelWidth:40,
						        name:"codigo",
						        id:"codtipfon",
								autoCreate: {tag: 'input', type: 'text', size: '2', autocomplete: 'off', maxlength: '2'},
						        width:40,
								disabled:true
				        	   },
					           {
						        xtype:"textfield",
						        fieldLabel:"Denominaci&#243;n",
						        labelWidth:60,
						        labelSeparator:'',
						        name:"denominacion",
						        id:"dentipfon",
						        width:400,
								autoCreate: {tag: 'input', type: 'text', size: '60', autocomplete: 'off', maxlength: '60', onkeypress: "return keyRestrict(event,'0123456789·ÈÌÛ˙¡…Õ”⁄abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!°;: ');"}
				        	   },
					           {
							        xtype:"textfield",
							        fieldLabel:"Porcentaje para la Reposici&#243;n",
							        labelWidth:60,
							        labelSeparator:'',
							        name:"porcentaje",
							        id:"porrepfon",
							        width:50,
							        autoCreate: {tag: 'input', type: 'text', size: '6', autocomplete: 'off', maxlength: '6', onkeypress: "return keyRestrict(event,'0123456789.');"},
									listeners:{
												'blur':function(objeto)
																		{
																			var numero = objeto.getValue();
																				valor = formatoNumericoMostrar(objeto.getValue(),2,'.',',','','','-','');
																				objeto.setValue(valor);
																			
																		},
												'focus':function(objeto)
												{
													var numero = formatoNumericoEdicion(objeto.getValue());
														objeto.setValue(numero);
													
												}
											}
					        	}]
			        		
			        }]
    		});
     formulario.render("formulario_fondosenavance");
	}
);
function irBuscar()
{
	mostrar_catalogo();
}

function irCancelar()
{
	irNuevo();
}

function irNuevo()
{
	limpiarCampos();
	var myJSONObject ={
		"oper":"nuevo"
	};
	
	ObjSon=Ext.util.JSON.encode(myJSONObject);
	parametros = 'ObjSon='+ObjSon;
	Ext.Ajax.request({
	url : ruta,
	params : parametros,
	method: 'POST',
	success: function ( result, request) 
	{ 
		datos = result.responseText;
		var codigo = eval('(' + datos + ')');
		if(codigo != "")
		{
			Ext.getCmp('codtipfon').setValue(codigo);
		}
	}	
	})
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
							if(respuesta[0]!='')
							{
								Ext.MessageBox.alert('Error', respuesta[0]);
							}
							else
							{
								Ext.MessageBox.alert('Error', 'Ocurri&#243; un error el registro no pudo ser '+mensajeerror);
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
									if(Registros[1] == '-8')
									{
										Ext.MessageBox.alert('Error', 'El registro no puede ser eliminado, no puede eliminar registros intermedios');
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