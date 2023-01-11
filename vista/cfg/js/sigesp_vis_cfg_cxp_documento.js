/***********************************************************************************
* @Archivo JavaScript que incluye tanto los componentes como los eventos asociados 
* a la definicion de Documentos. 
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
var pantalla    		= 'sigesp_vis_cfg_cxp_documento.php'; 							// Variable que contiene el nombre fÔøΩsico de la Pantalla
var sistema             = "CFG";                        						// Variable que contiene el nombre del sistema al que pertenece la pantalla
var ruta				= '../../controlador/cfg/sigesp_ctr_cfg_cxp_documento.php'; 	// Ruta del Controlador de la Pantalla
var banderaGrabar 		= false;												// Indicador si se posee un Metodo de Guardar distinto al Original de funciones.js
var banderaEliminar		= false;												// Indicador si se posee un Metodo de Eliminar distinto al Original de funciones.js
var banderaNuevo		= true;												// Indicador si se posee un Metodo Nuevo distinto al Original de funciones.js
var gridUsu				= null;
var Actualizar=null;
var banderaCatalogo		= 'estandar';

var arregloAfecPres = [  
                         [1,'Causa'],
                         [2,'Compromete y Causa'],
                         [3,'Ninguna']
                         ]; // Arreglo que contiene las Afectaciones Presupuestarias Disponibles

var dataStoreAfecPres = new Ext.data.SimpleStore({
 fields: ['codafespg', 'desafespg'],
 data : arregloAfecPres // Se asocian los documentos disponibles
});

var arregloAfecCont = [  
                       [1,'Credito']
                       //[2,'Sin afectacion']Opcion eliminada caso 10510 ivan valecillos
                      ]; // Arreglo que contiene las Afectaciones Contables Disponibles

var dataStoreAfecCont = new Ext.data.SimpleStore({
fields: ['codafescg', 'desafescg'],
data : arregloAfecCont // Se asocian los documentos disponibles
});

var Campos =new Array(
						['codtipdoc','novacio|'], 
						['dentipdoc','novacio|'], 
						['estcon','novacio|'],
						['estpre','novacio|'],
						['tipodocanti',''],
						['tipdoctesnac',''],
						['tipdocdon','']); // Arreglo que contiene la informacion del Registro, deben coincidir con la Tabla en la Base de Datos

Ext.onReady
(
  function()
	{
	 Ext.QuickTips.init();
	 Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	 
	 Xpos = ((screen.width/2)-(600/2));
	 Ypos = ((screen.height/2)-(600/2));	
     formulario = new Ext.form.FormPanel({
    	   	 title:"Definici&#243;n de Documento",
    		 frame:true,
    		 style: 'position:absolute;margin-left:'+Xpos+'px;margin-top:'+Ypos+'px',
    		 width: 600,
    		 height: 300,
    		 labelPad: 10,
    		 items:[{
				        layout:"form",
						border:false,
						defaultType: "textfield",
						style: "margin-top:30px;padding-left:25px;",
						labelWidth:100,
						items:[{
						        xtype:"textfield",
						        fieldLabel:"C&#243;digo",
						        labelWidth:40,
						        name:"codigo",
						        id:"codtipdoc",
								autoCreate: {tag: 'input', type: 'text', size: '5', autocomplete: 'off', maxlength: '5'},
						        width:60,
								disabled:true
				        	   },
					           {
						        xtype:"textfield",
						        fieldLabel:"Denominaci&#243;n",
						        labelWidth:60,
						        name:"denominacion",
						        id:"dentipdoc",
						        width:400,
								autoCreate: {tag: 'input', type: 'text', size: '60', autocomplete: 'off', maxlength: '60',onkeypress: "return keyRestrict(event,'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!=°;:[]{}·ÈÌÛ˙¡…Õ”⁄ ');"}
				        	   }]
			        		
			        },
			        {	
				   		xtype:"fieldset",
						title: "Afectaci&#243;n",
						height:60,
						labelWidth:60,
						border:true,
						style: "margin-top:10px;margin-left:25px;",
						width:550,
						items: [{
								 layout:"column",
								 defaults: {columnWidth: ".5",border: false},
								 items:[{
											layout:"form",
											border:false,
											defaultType: "textfield",  
											columnWidth:0.5,
											style: "margin-left:5px",
											labelWidth:100,
											items:[{
												 	xtype:"combo",
									                store: dataStoreAfecPres,
									                hiddenName:'afecspg',
									                hiddenId:'idafecspg',
									                displayField:'desafespg',
									                valueField:'codafespg',
													id:"estpre",
									                typeAhead: true,
									                mode: 'local',
									                triggerAction: 'all',
									                selectOnFocus:true,
									                fieldLabel:'Presupuestaria',
									           	    listWidth:150,
									           	    editable:false,
									                width:150
												   }]
										},
										{
											layout:"form",
											border:false,
											defaultType: "textfield",  
											columnWidth:0.5,
											style: "margin-left:10px",
											labelWidth:75,
											items:[{
											
												 	xtype:"combo",
									                store: dataStoreAfecCont,
									                hiddenName:'afecscg',
									                hiddenId:'idafecscg',
									                displayField:'desafescg',
									                valueField:'codafescg',
													id:"estcon",
									                typeAhead: true,
									                mode: 'local',
									                triggerAction: 'all',
									                fieldLabel:'Contable',
									           	    listWidth:150,
									           	    editable:false,
									                width:150
													
												   }]
										}] 
								}]
						},
						{
					        layout:"form",
							border:false,
							style: "margin-top:10px;padding-left:25px;",
							labelWidth:150,
							items:[{
			             			 xtype: "checkboxgroup",
			              			 fieldLabel: "Tipo de Documento",
			              			 columns: 2,
			                   		 vertical:true,
			              			 autoWidth:false,
			              			 items: [
			                  			{boxLabel: "Anticipo", id:"tipodocanti", inputValue:1},    
			                  			{boxLabel: "Donaci&#243;n", id:"tipdocdon", inputValue:1}
			              			 ] 
			          		}]
						},
						{
					        layout:"form",
							border:false,
							style: "margin-top:10px;padding-left:25px;",
							labelWidth:150,
							items:[{
			             			 xtype: "checkboxgroup",
			              			 fieldLabel: "Impuesto para la Tesoreria Nacional",
			              			 columns: 1,
			                   		 vertical:true,
			              			 autoWidth:false,
			              			 items: [
			                  			{boxLabel: "", id:"tipdoctesnac", inputValue:1}
			              			 ] 
			          		}]
						}]
    		});
	formulario.render("formulario_documento");
	Ext.getCmp('estcon').setValue(1);
});
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
			Ext.getCmp('codtipdoc').setValue(codigo);
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
			obtenerMensaje('procesar','','Guardando Datos');
			if (typeof(objjson) == 'object')
			{
				var parametros = 'ObjSon=' + cadjson;
				Ext.Ajax.request({
				url : ruta,
				params : parametros,
				method: 'POST',
				success: function ( result, request)
				{ 
					Ext.Msg.hide();
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
					
				},
				failure: function (result, request)
				{ 
					Ext.Msg.hide();
					Ext.MessageBox.alert('Error', result.responseText);
				}	
				});
				limpiarCampos();
				Actualizar=null;				
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
			obtenerMensaje('procesar','','Eliminando Datos');
			var objjson = Ext.util.JSON.decode(cadjson);
			if (typeof(objjson) == 'object')
			{
					var parametros = 'ObjSon=' + cadjson;
					Ext.Ajax.request({
					url : ruta,
					params : parametros,
					method: 'POST',
					success: function ( result, request) 
					{ 
						Ext.Msg.hide();
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
					},
					failure: function (result, request)
					{ 
						Ext.Msg.hide();
						Ext.MessageBox.alert('Error', result.responseText);
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