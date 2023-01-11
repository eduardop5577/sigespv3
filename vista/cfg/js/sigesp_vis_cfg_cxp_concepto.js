/***********************************************************************************
* @Archivo JavaScript que incluye tanto los componentes como los eventos asociados 
* a la definicion de Conceptos. 
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

barraherramienta = true;

var formulario          = '';                           						// Variable que representa y contiene el panel que contiene los objetos
var cambiar     		= false;	                    						// Variable que verifica el Estatus de la Operacion para Modificacion
var pantalla    		= 'sigesp_vis_cfg_cxp_concepto.php'; 							// Variable que contiene el nombre fÌsico de la Pantalla
var sistema             = "CFG";                        						// Variable que contiene el nombre del sistema al que pertenece la pantalla
var ruta				= '../../controlador/cfg/sigesp_ctr_cfg_cxp_concepto.php'; 	// Ruta del Controlador de la Pantalla
var gridUsu				= null;
var Actualizar          = null;
var Usado          = 0;
var esCtaContable = empresa['clactacon'] == 0;

var Campos =new Array(
						['codemp',''],
						['codcla','novacio|'],
						['dencla','novacio|'],
						['sc_cuenta','']); // Arreglo que contiene la informacion del Registro, deben coincidir con la Tabla en la Base de Datos

Ext.onReady
(
  function()
	{
	 Ext.QuickTips.init();
	 Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	 
	 Xpos = ((screen.width/2)-(600/2));
	 Ypos = ((screen.height/2)-(600/2));	
     var formulario = new Ext.form.FormPanel({
    	   	 title:"Definici&#243;n de Concepto",
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
						        labelWidth:40,
						        name:"C&#243;digo",
						        id:"codcla",
								autoCreate: {tag: 'input', type: 'text', size: '2', autocomplete: 'off', maxlength: '2'},
						        width:40,
								disabled:true
				        	   },
					           {
						        xtype:"textfield",
						        fieldLabel:"Denominaci&#243;n",
						        labelWidth:60,
						        name:"Denominaci&#243;n",
						        id:"dencla",
						        width:400,
								autoCreate: {tag: 'input', type: 'text', size: '60', autocomplete: 'off', maxlength: '60',onkeypress: "return keyRestrict(event,'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!=°;:[]{}·ÈÌÛ˙¡…Õ”⁄ ');"}
				        	   }]
			        		
			        },
			        {
					 layout:"column",
					 layoutConfig: {
 						renderHidden:esCtaContable
		           		},
					 defaults: {columnWidth: ".5",border: false},
					 items:[{
								layout:"form",
								border:false,
								defaultType: "textfield",  
								columnWidth:0.50,
								style: "padding-left:25px;",
								labelWidth:100,
								items:[{
								 		xtype:"textfield",
								        fieldLabel:"Cuenta Contable",
								        labelWidth:100,
								        name:"Cuenta Contable",
								        id:"sc_cuenta",
								        width:150,
										autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '0'}
										}]
							},
							{
								layout:"form",
								border:false,
								defaultType: "button",  
								columnWidth:0.50,
								items:[{
						        		iconCls: 'menubuscar',
										handler : function(){
																if (Usado==0)
																{
																	mostrarCatalogoCuentaContable('catalogocuentamovimiento',Ext.getCmp('sc_cuenta'),null);
																}
																else
																{
																	Ext.Msg.show({
																		title:'Mensaje',
																		msg: 'Ya el clasificador fue usado en una RecepciÛn de Documentos, no puede cambiar la cuenta contable ',
																		buttons: Ext.Msg.OK,
																		icon: Ext.MessageBox.ERROR
																		});
																}
															}			      
						        	   }]
							}]
			        
			        }]
    		});
     formulario.render("formulario_concepto");
	}
);
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
	Usado = 0;
	Actualizar = null;
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
			Ext.getCmp('codcla').setValue(codigo);
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
				Ext.Ajax.request({
				url : ruta,
				params : parametros,
				method: 'POST',
				success: function ( result, request){ 
					var datos = result.responseText;
					var codigo = datos.split("|");
					if(codigo[1] == '1')
					{
							Ext.Msg.show({
								title:'Mensaje',
								msg: mensajeexito,
								buttons: Ext.Msg.OK,
								icon: Ext.MessageBox.INFO
								});
							limpiarCampos();
							Actualizar=null;
							Usado = 0;							
					}
					else
					{
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
		var	cadjson=cargarJson('eliminar');
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
							Usado = 0;							
						}
						else
						{
							if(codigo[1] == '-8')
							{
								Ext.Msg.show({
									title:'Mensaje',
									msg: 'El registro no puede ser eliminado, no puede eliminar registros intermedios',
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.ERROR
								});
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

function irBuscar()
{
	mostrar_catalogo();	
}