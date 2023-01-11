/***********************************************************************************
* @Archivo JavaScript que incluye tanto los componentes como los eventos asociados 
* a la definicion de la Chequera. 
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
var ruta				= '../../controlador/cfg/sigesp_ctr_cfg_scb_chequera.php'; 	// Ruta del Controlador de la Pantalla
var banderaGrabar 		= true;												// Indicador si se posee un Metodo de Guardar distinto al Original de funciones.js
var banderaEliminar		= true;												// Indicador si se posee un Metodo de Eliminar distinto al Original de funciones.js
var banderaNuevo		= true;												// Indicador si se posee un Metodo Nuevo distinto al Original de funciones.js
var Actualizar=null;
var banderaImprimir 	= false;
var banderaCatalogo		= 'generica';

var dataStoreUsuariosEliminacion = new Ext.data.SimpleStore({
	fields: ['codemp','codban','ctaban','numche','estche','numchequera','estins','orden','codusu']
});

var registroGridUsuario = Ext.data.Record.create
([
  {name: 'codusu'}, 
  {name: 'nomusu'},
  {name: 'apeusu'}
  ]);

var registroGridCheque = Ext.data.Record.create
([
  {name: 'numche'}, 
  {name: 'codusu'},
  {name: 'orden'},
  {name: 'estche'}
  ]);

var chequeAsignar = Ext.data.Record.create
([
  {name: 'numche'},
  {name: 'codusuario'}
 ]);


var chequeNuevo = Ext.data.Record.create
([
  {name: 'numche'},
  {name: 'estche'},
  {name: 'codusu'},
  {name: 'orden'}
 ]);

var gridUsuariosChequera = null; // Variable que es el Grid con los usuario asoaciados a la Chequera
var gridChequesChequera  = null; // Variable que es el Grid con los cheques asociados a la Chequera

var Campos =new Array(
		['codemp',''],
		['codban','novacio|'],
		['ctaban','novacio|'],
		['numche','novacio|'],
		['estche','novacio|'],
		['numchequera','novacio|'],
		['estins','novacio|'],
		['orden','novacio|']); // Arreglo que contiene la informacion del Registro, deben coincidir con la Tabla en la Base de Datos

Ext.onReady
(
		function()
		{
			Ext.QuickTips.init();
			Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';

			var agregarCheque = new Ext.Action(
					{
						text: 'Agregar Cheque',
						handler: agregarChequeNuevo,
						iconCls: 'agregar',
						tooltip: 'Agregar usuario al cheque'
					});

			var quitarCheque = new Ext.Action(
					{
						text: 'Eliminar Cheque',
						handler: eliminarChequeChequera,
						iconCls: 'remover',
						tooltip: 'Quitar usuario del cheque'
					});

			var procesarCheque = new Ext.Action(
					{
						text: 'Generar Cheques',
						iconCls: 'procesar',
						tooltip: 'Generar Cheques',
						handler: generarCheques
					});

			var agregarUsuario = new Ext.Action(
					{
						text: 'Agregar Usuario',
						handler: agregarUsuarioChequera,
						iconCls: 'agregar',
						tooltip: 'Agregar usuario al cheque'
					});

			var quitarUsuario = new Ext.Action(
					{
						text: 'Quitar Usuario',
						handler: eliminarUsuario,
						iconCls: 'remover',
						tooltip: 'Quitar usuario del cheque'
					});

			var procesarUsuario = new Ext.Action(
					{
						text: 'Asignar Cheque',
						handler: asignarUsuariosCheque,
						iconCls: 'procesar',
						tooltip: 'Asociar Cheque a Usuario'
					});

			Xpos = ((screen.width/2)-(800/2));
			var Xposgrid = ((screen.width/2)-(625/2));
			Ypos = 75;	
			var formulario = new Ext.form.FormPanel({
				title:"Definici&#243;n de Chequera",
				autoScroll:true,
				frame:true,
				style: 'position:absolute;margin-left:'+Xpos+'px;margin-top:'+Ypos+'px',
				width: 900,
				height: 600,
				items:[{
					xtype:"hidden",
					name:"codemp",
					id:"codemp",
					value:''
				},
				{
					layout:"form",
					border:false,
					style: "margin-top:10px;padding-left:20px;padding-right:25px;",
					labelWidth:150,
					items:[
					       {
					    	   xtype:"textfield",
					    	   fieldLabel:"N&#250;mero de Chequera",
					    	   name:"codigo",
					    	   id:"numchequera",
					    	   autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '10',onkeypress: "return keyRestrict(event,'0123456789');"},
					    	   width:80,
					    	   allowBlank:false,
					    	   listeners:{
					    		   'blur' : function(campo){
					    		   var valor = ""; 
					    		   valor = ue_rellenarcampo(trim(campo.getValue()),10);
					    		   campo.setValue(valor);
					    		   if((Actualizar==null)&&(trim(Ext.getCmp('codban'))!="")&&(trim(Ext.getCmp('ctaban').getValue())!=""))
					    		   {
					    			   if(trim(campo.getValue())!="")
					    			   {
					    				   verificarExistenciaChequera();
					    			   }
					    		   }
					    	   }
					    	   }
					       },
					       {
					    	   layout : "column",
					    	   defaults : {
					    	   border : false
					       },
					       items : [
					                {
					                	layout : "form",
					                	border : false,
					                	defaultType : "textfield",
					                	columnWidth : 0.27,
					                	labelWidth : 150,
					                	items : [ {
					                		xtype : "textfield",
					                		fieldLabel : "Banco",
					                		name : "banco",
					                		id : "codban",
					                		allowBlank:false,
					                		readOnly:true,
					                		width : 30,
					                		autoCreate : {
					                		tag : 'input',
					                		type : 'text',
					                		size : '3',
					                		autocomplete : 'off',
					                		maxlength : '3'
					                	}
					                	} ]
					                },
					                {
					                	layout : "form",
					                	border : false,
					                	defaultType : "button",
					                	columnWidth : 0.05,
					                	items : [ {
					                		id:"btnbanco",
					                		iconCls : 'menubuscar',
					                		handler : function() {
					                		if(trim(Ext.getCmp('numchequera').getValue()) != "")
					                		{
						                		catalogoBanco();
						                		Ext.getCmp('ctaban').setValue("");
						                		Ext.getCmp('dencta').setValue("");
						                		Ext.getCmp('codtipcta').setValue("");
						                		Ext.getCmp('nomtipcta').setValue("");
					                		}
					                		else
					                		{
					                			Ext.Msg.alert("Mensaje","Debe indicar el n&#250;mero de chequera, verifique por favor !!");
					                		}
					                	}
					                	} ]
					                },
					                {
					                	layout : "form",
					                	border : false,
					                	defaultType : "textfield",
					                	columnWidth : 0.68,
					                	items : [{
					                		id:"nomban",
					                		hideLabel:true,
					                		style:'border:none;background:#f1f1f1;color:#000000;cursor:text;font-weight: bold;text-aling:left;',
					                		disabledClass :'',
					                		disabled:true,
					                		width: 400,
					                		autoCreate: {tag: 'input', type: 'text', size: '250', autocomplete: 'off', maxlength: '250'}
					                	}]
					                }]

					       },
					       {
					    	   layout : "column",
					    	   defaults : {
					    	   border : false
					       },
					       items : [{
					    	   layout : "form",
					    	   border : false,
					    	   defaultType : "textfield",
					    	   columnWidth : 0.48,
					    	   labelWidth : 150,
					    	   items : [ {
					    		   xtype : "textfield",
					    		   fieldLabel : "Cuenta Bancaria",
					    		   name : "cuenta bancaria",
					    		   id : "ctaban",
					    		   allowBlank:false,
					    		   readOnly:true,
					    		   width : 180,
					    		   autoCreate : {
					    		   tag : 'input',
					    		   type : 'text',
					    		   size : '25',
					    		   autocomplete : 'off',
					    		   maxlength : '25'
					    	   },
					    	   listeners:{
					    		   'blur': function(campo)
					    		   {
						    		   if(Actualizar == null)
					    			   {
					    				   verificarExistenciaChequera();
					    			   }
					    		   }
					    	   }
					    	   } ]
					       },
					       {
					    	   layout : "form",
					    	   border : false,
					    	   defaultType : "button",
					    	   columnWidth : 0.05,
					    	   items : [ {
					    		   id:"btnctabanco",
					    		   iconCls : 'menubuscar',
					    		   handler : function() {
					    		   if(trim(Ext.getCmp('numchequera').getValue()) != "")
					    		   {
					    			   mostrarCatalogoCuentaBancoFiltro(Ext.getCmp('codban'),Ext.getCmp('nomban'),Ext.getCmp('ctaban'),Ext.getCmp('dencta'),Ext.getCmp('codtipcta'),Ext.getCmp('nomtipcta'));
					    		   }
					    		   else
					    		   {
					    			   Ext.Msg.alert("Mensaje","Debe indicar el n&#250;mero de chequera, verifique por favor !!"); 
					    		   }
					    	   }
					    	   } ]
					       },
					       {
					    	   layout : "form",
					    	   border : false,
					    	   defaultType : "textfield",
					    	   columnWidth : 0.47,
					    	   items : [{
					    		   id:"dencta",
					    		   hideLabel:true,
					    		   style:'border:none;background:#f1f1f1;color:#000000;cursor:text;font-weight: bold;text-aling:left;',
					    		   disabledClass :'',
					    		   disabled:true,
					    		   width: 400,
					    		   autoCreate: {tag: 'input', type: 'text', size: '50', autocomplete: 'off', maxlength: '50'}
					    	   }]
					       }]

					       },
					       {
					    	   layout : "column",
					    	   defaults : {
					    	   border : false
					       },
					       items : [{
					    	   layout : "form",
					    	   border : false,
					    	   defaultType : "textfield",
					    	   columnWidth : 0.30,
					    	   labelWidth : 150,
					    	   items : [ {
					    		   xtype : "textfield",
					    		   fieldLabel : "Tipo de Cuenta",
					    		   name : "tipo de cuenta",
					    		   id : "codtipcta",
					    		   allowBlank:false,
					    		   readOnly:true,
					    		   width : 30,
					    		   autoCreate : {
					    		   tag : 'input',
					    		   type : 'text',
					    		   size : '3',
					    		   autocomplete : 'off',
					    		   maxlength : '3'
					    	   }
					    	   } ]
					       },
					       {
					    	   layout : "form",
					    	   border : false,
					    	   defaultType : "textfield",
					    	   columnWidth : 0.70,
					    	   items : [{
					    		   id:"nomtipcta",
					    		   hideLabel:true,
					    		   style:'border:none;background:#f1f1f1;color:#000000;cursor:text;font-weight: bold;text-aling:left;',
					    		   disabledClass :'',
					    		   disabled:true,
					    		   width: 100,
					    		   autoCreate: {tag: 'input', type: 'text', size: '50', autocomplete: 'off', maxlength: '50'}
					    	   }]
					       }]

					       },
					       {
					    	   layout : "column",
					    	   title: 'Cheques asociados a la Chequera',
					    	   defaults : {
					    	   border : false
					       },
					       items : [{
					    	   layout : "form",
					    	   border : false,
					    	   defaultType : "textfield",
					    	   columnWidth : 0.50,
					    	   labelWidth : 50,
					    	   style: "margin-top:10px;padding-left:50px;text-align:center;",
					    	   items : [ {
					    		   xtype : "textfield",
					    		   fieldLabel : "Desde",
					    		   name : "codigo desde",
					    		   id : "coddesde",
					    		   width : 150,
					    		   autoCreate : {
					    		   tag : 'input',
					    		   type : 'text',
					    		   size : '15',
					    		   autocomplete : 'off',
					    		   maxlength : '15',
					    		   onkeypress: "return keyRestrict(event,'0123456789');"
					    	   },
					    	   listeners:{
					    		   'blur' : function(campo){
					    		   var valor = ""; 
					    		   valor = ue_rellenarcampo(trim(campo.getValue()),15);
					    		   campo.setValue(valor);
					    		   if(Actualizar == null)
				    			   {
				    				   verificarExistenciaChequera();
				    			   }
						    		 
					    	   }
					    	   }
					    	   } ]
					       },
					       {
					    	   layout : "form",
					    	   border : false,
					    	   defaultType : "textfield",
					    	   columnWidth : 0.50,
					    	   labelWidth : 50,
					    	   style: "margin-top:10px;margin-left:25px;text-align:center;",
					    	   items : [{
					    		   id:"codhasta",
					    		   fieldLabel : "Hasta",
					    		   width: 150,
					    		   autoCreate: {
					    		   tag: 'input', 
					    		   type: 'text', 
					    		   size: '15', 
					    		   autocomplete: 'off', 
					    		   maxlength: '15',
					    		   onkeypress: "return keyRestrict(event,'0123456789');"
					    	   },
					    	   listeners:{
							    		   'blur' : function(campo){
							    		   var valor = ""; 
							    		   valor = ue_rellenarcampo(trim(campo.getValue()),15);
							    		   campo.setValue(valor);
					    	   			 }
					    	   }
					    	   }]
					       }]

					       },
					       {
					    	   xtype:'panel',
					    	   width:650,
					    	   height:200,
					    	   autoScroll:true,
					    	   title:'Cheques',
					    	   style:'margin-top:10px;margin-left:50px;',
					    	   tbar: [agregarCheque,quitarCheque,procesarCheque],
					    	   contentEl:'grid_panelcheques'
					       },{
					    	   xtype:'panel',
					    	   width:650,
					    	   height:200,
					    	   autoScroll:true,
					    	   style:'margin-top:10px;margin-left:50px;',
					    	   title:'Usuarios asociados a la Chequera',
					    	   tbar: [agregarUsuario,quitarUsuario,procesarUsuario],
					    	   contentEl:'grid_panelusuarios'
					       }]
				}]
			});
			formulario.render("formulario_Chequera");
			obtenerGridCheques();
			obtenerGridUsuario();
		}
);

function irCancelar()
{
	irNuevo();
}

function irNuevo()
{
	limpiarCampos();
	Ext.getCmp('ctaban').setValue("");
	Ext.getCmp('nomban').setValue("");
	Ext.getCmp('dencta').setValue("");
	Ext.getCmp('codtipcta').setValue("");
	Ext.getCmp('nomtipcta').setValue("");
	Ext.getCmp('coddesde').setValue("");
	Ext.getCmp('codhasta').setValue("");
	Ext.getCmp('numchequera').enable();
	Ext.getCmp('codban').enable();
	Ext.getCmp('nomban').enable();
	Ext.getCmp('ctaban').enable();
	Ext.getCmp('dencta').enable();
	Ext.getCmp('codtipcta').enable();
	Ext.getCmp('nomtipcta').enable();
	Ext.getCmp('btnbanco').enable();
	Ext.getCmp('btnctabanco').enable();
	gridUsuariosChequera.getStore().removeAll();
	gridChequesChequera.getStore().removeAll();
	Actualizar = null;
}

function agregarUsuarioChequera()
{
	if (Ext.getCmp('numchequera').getValue() !='')
	{
		mostrarCatalogoUsuario('catalogo',gridUsuariosChequera);
	}
	else
	{
		Ext.MessageBox.alert('Mensaje','Debe indicar un N&#250;mero de Chequera, verifique por favor');
	}
}

function eliminarUsuario()
{
	if(gridUsuariosChequera.getStore().getCount()>0)
	{
		var usuario = gridUsuariosChequera.getSelectionModel().getSelected();
		if(usuario != null)
		{
			if(!verificarUsuarioAsignado(usuario.get('codusu')))
				{
					gridUsuariosChequera.getStore().remove(usuario);
				}
				else
				{
					Ext.Msg.alert("Mensaje","El usuario <b>"+usuario.get('codusu')+" ("+usuario.get('apeusu')+", "+usuario.get('nomusu')+")</b> tiene cheques asignados, no se puede eliminar");
				}
		}
		else
		{
			Ext.Msg.alert("Mensaje","No ha seleccionado un usuario, verifique por favor!!");
		}
	}
	else
	{
		Ext.Msg.alert("Mensaje","No se han asociado Usuarios, verifique por favor!!");
	}
}

function eliminarChequeChequera()
{
	var chequeseliminar = gridChequesChequera.getSelectionModel().getSelections();
	var cheques = "";
	for(var i=0; i<chequeseliminar.length; i++)
	{
		if(i==0)
		{
			cheques += chequeseliminar[i].get('numche');	
		}
		else
		{
			cheques += ", "+chequeseliminar[i].get('numche');	
		}
	}
	
	function eliminarcheques(boton)
	{
		if(boton=='yes')
		{
			if(gridChequesChequera.getSelectionModel().getSelections().length > 0)
			{

				var chequeEliminar = gridChequesChequera.getSelectionModel().getSelections();
				var arregloJsonEliminar = "{'operacion':'eliminar','codmenu':'"+codmenu+"','chequeseliminar':[";
				for(var i=0;i<=chequeEliminar.length-1;i++)
				{	
					if(i==0)
					{
						arregloJsonEliminar = arregloJsonEliminar + "{'codban':'"+Ext.getCmp('codban').getValue()+"','ctaban':'"+Ext.getCmp('ctaban').value+"','numche':'"+chequeEliminar[i].get('numche')+"','estche':"+chequeEliminar[i].get('estche')+",'numchequera':'"+Ext.getCmp('numchequera').getValue()+"','estins':"+0+",'orden':"+chequeEliminar[i].get('orden')+",'codusu':'"+chequeEliminar[i].get('codusu')+"'}";
					}	
					else
					{
						arregloJsonEliminar = arregloJsonEliminar + ",{'codban':'"+Ext.getCmp('codban').getValue()+"','ctaban':'"+Ext.getCmp('ctaban').value+"','numche':'"+chequeEliminar[i].get('numche')+"','estche':"+chequeEliminar[i].get('estche')+",'numchequera':'"+Ext.getCmp('numchequera').getValue()+"','estins':"+0+",'orden':"+chequeEliminar[i].get('orden')+",'codusu':'"+chequeEliminar[i].get('codusu')+"'}";
					}
				}
				arregloJsonEliminar = arregloJsonEliminar + "]}";
				var cheques = eval('(' + arregloJsonEliminar + ')');
				ObjSon=Ext.util.JSON.encode(cheques);
				parametros = 'ObjSon='+ObjSon;
				Ext.Ajax.request({
					url : ruta,
					params : parametros,
					method: 'POST',
					success: function ( resultado, request ){ 
					datos = resultado.responseText;
					var registro = datos.split("|");
					var respuesta = registro[1];
					if(respuesta == '1')
					{
						Ext.Msg.show({
							title:'Mensaje',
							msg: 'Cheque(s) eliminado(s) con &#233;xito',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.INFO
						});
						
						for(var j=0; j<chequeEliminar.length;j++)
						{
							gridChequesChequera.getStore().remove(chequeEliminar[j]);
						}
					}
					else{
						if(respuesta[1]=='-9'){
							Ext.Msg.show({
								title:'Mensaje',
								msg: 'No se pudo eliminar el(los) cheque(s), este se encuentra asociado a otro registro',
								buttons: Ext.Msg.OK,
								icon: Ext.MessageBox.INFO
							});
					  		
						}
						else{
							Ext.Msg.show({
								title:'Mensaje',
								msg: 'No se pudo eliminar el(los) cheque(s)',
								buttons: Ext.Msg.OK,
								icon: Ext.MessageBox.ERROR
							});
						}
					}
				},
				failure: function ( result, request)
				{ 
					Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo'); 
				} 
				});
			}
			else
			{
				Ext.MessageBox.alert('Error','No ha seleccionado ningun cheque, verifique por favor'); 
			}
		}
	 }
	if(trim(cheques)!="")
	{
		Ext.MessageBox.confirm('Confirmar', '&#191; Est&#225; seguro de eliminar el (los) cheque(s) <b>'+cheques+'</b>, este proceso no tiene reverso &#63;', eliminarcheques);
	}
	else
	{
		Ext.MessageBox.alert('Error','No ha seleccionado ningun cheque, verifique por favor'); 
	}
}

function obtenerGridUsuario()
{	
	var datosNuevo = {'raiz':[{'codusu':'','nomusu':'','apeusu':''}]};
	var modoSeleccionControl = new Ext.grid.CheckboxSelectionModel({singleSelect:true});
	dsusuario =  new Ext.data.Store({
		proxy: new Ext.data.MemoryProxy(datosNuevo),
		reader: new Ext.data.JsonReader({
			root: 'raiz',               
			id: 'id'   
		},
		registroGridUsuario
		)
	});

	gridUsuariosChequera = new Ext.grid.GridPanel({
		width:625,
		autoScroll:true,
		height:120,
		border:true,
		ds: dsusuario,
		cm: new Ext.grid.ColumnModel([
                                      modoSeleccionControl,
		                              {header: 'C&#243;digo', width: 100, sortable: true,   dataIndex: 'codusu'},
		                              {header: 'Nombre', width: 200, sortable: true, dataIndex: 'nomusu'},
		                              {header: 'Apellido', width: 200, sortable: true, dataIndex: 'apeusu'}
		                              ]),
		                              viewConfig: {
		forceFit:true
	},
	stripeRows: true
	});
	gridUsuariosChequera.render('grid_panelusuarios');
}

function obtenerGridCheques()
{	
	var datosNuevo = {'raiz':[{'numche':'','codusu':'','estche':'','orden':''}]};
	var modoSeleccionControl = new Ext.grid.CheckboxSelectionModel({listeners:{
		'beforerowselect' : function(selection,indice,existencia,cheque)
		{
		   return (cheque.get('estche')==1)? false : true;
		}
	}});
	dsCheque =  new Ext.data.Store({
		proxy: new Ext.data.MemoryProxy(datosNuevo),
		reader: new Ext.data.JsonReader({
			root: 'raiz',               
			id: 'id'   
		},
		registroGridCheque
		)
	});
	gridChequesChequera = new Ext.grid.EditorGridPanel({
		width:625,
		autoScroll:true,
		height:120,
		border:true,
		ds: dsCheque,
		cm: new Ext.grid.ColumnModel([
		                              modoSeleccionControl,
		                              {header: 'N&#250;mero de Cheque', width: 100, sortable: true,   dataIndex: 'numche', editor: new Ext.form.TextField({autoCreate : {
							    		   tag : 'input',
							    		   type : 'text',
							    		   size : '15',
							    		   autocomplete : 'off',
							    		   maxlength : '15',
							    		   onkeypress: "return keyRestrict(event,'0123456789')"},
							    		   listeners:{
							    			   'blur': function(campo)
							    			   {
							    			   	var valor = "";
							    			   	valor =  ue_rellenarcampo(campo.getValue(),15);
							    			   	campo.setValue(valor);
							    			   },
							    			   'change': function( campo, nuevoValor, viejoValor)
							    			   {
							    				   if(verificarChequeUnico(nuevoValor))
							    				   {
							    					   Ext.Msg.alert("Mensaje","El N&#250;mero de Cheque "+nuevoValor+", ya se encuentra en la lista, debe indicar uno distinto, verifique por favor!! ");
							    					   campo.setValue(viejoValor);
							    					   
							    				   }
							    			   }
							    		   }})},
		                              {header: 'Usuario', width: 200, sortable: true, dataIndex: 'codusu'},
		                              {header: 'Orden', width: 50, align: "center", sortable:true, dataIndex: 'orden'},
		                              {header: 'Estatus', width: 50, sortable: true, dataIndex: 'estche', renderer: obtenerEstatusCheque}
		                              ]),
		                              viewConfig: {
		forceFit:true
	},
	stripeRows: true,
	sm: modoSeleccionControl,
	listeners:{
		'beforeedit':function(objeto)
		{
			if(objeto.record.get('estche') == 1)
			{
				Ext.Msg.show({
					title:'Mensaje',
					msg: "El cheque <b>"+objeto.record.get('numche')+"</b> se encuentra en estatus EMITIDO, no puede modificar el n&#250;mero, verifique por favor!!",
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
				});
				return false;
			}
			else
			{
				return true;
			}
		}
	}
	});
	gridChequesChequera.render('grid_panelcheques');
	gridChequesChequera.getView().getRowClass = function(record, index)
	{
		if(record.get('estche')==1)
		{
			return 'filanoeditable';
		}
	};
}

function generarCheques()
{
	var ls_desde	  = Ext.getCmp('coddesde').getValue();
	var ls_hasta	  = Ext.getCmp('codhasta').getValue();
	var li_desde	  = parseInt(ls_desde,10);
	var li_hasta	  = parseInt(ls_hasta,10);
	var li_diferencia = li_hasta-li_desde;
	var ls_chequera   = Ext.getCmp('numchequera').getValue();
	if (ls_chequera!='')
	{
		var ls_codban = trim(Ext.getCmp('codban').getValue());
		if (ls_codban!='')
		{
			var ls_ctaban = trim(Ext.getCmp('ctaban').getValue());
			if (ls_ctaban!='')
			{
				if ((li_diferencia>0)&&(li_diferencia<=49))
				{
					if ((ls_desde=!"")&&(ls_hasta!=""))
					{
						if(gridChequesChequera.store.getCount()==0)
						{
							 var orden = 1;
							 for(i=li_desde;i<=li_hasta;i++)
							 {
								 var cheque =  new chequeNuevo({'numche':"",'estche':"",'codusu':"",'orden':""});
								 cheque.set('numche',ue_rellenarcampo(i.toString(),15));
								 cheque.set('estche',0);
								 cheque.set('codusu',"");
								 cheque.set('orden',orden);
								 gridChequesChequera.getStore().add(cheque);
								 orden++;
							 }
						}
						else
						{
							Ext.Msg.alert("Mensaje","Existen cheques ya generados, use la opci&#243;n <b>Agregar Cheques</b>");
						}
						
					}
				}
				else
				{
					Ext.Msg.alert("Mensaje","No se pueden generar cheques en el rango seleccionado, verifique por favor!!");
				}
			}
			else
			{ 
				Ext.Msg.alert("Mensaje","La Cuenta Bancaria est&#225; vac&#237;a, verifique por favor!!");
			}
		}
		else
		{
			Ext.Msg.alert("Mensaje","El C&#243;digo del Banco est&#225; vac&#237;o, verifique por favor!!");
		}
	}
	else
	{
		Ext.Msg.alert("Mensaje","El N&#250;mero de la Chequera est&#225; vac&#237;o, verifique por favor!!");
	} 
}

function irGuardar()
{
	if(verificarCamposVacios())
	{
       	var numDetalle = 0;
       	if(gridChequesChequera.getStore().getCount()!=0)
		{
			if(Actualizar == null)
			{
				var arregloJson = "{'operacion':'incluir','codmenu':'"+codmenu+"','chequesincluir':[";
				var mensaje = 'Incluido';
			}
			else
			{
				var arregloJson = "{'operacion':'actualizar','codmenu':'"+codmenu+"','chequesincluir':[";
				var mensaje = 'Actualizado';
			}
			gridChequesChequera.store.each(function (Detalle)
			{
				if (numDetalle==0)
				{
					arregloJson = arregloJson + "{'codban':'"+Ext.getCmp('codban').getValue()+"','ctaban':'"+Ext.getCmp('ctaban').value+"','numche':'"+Detalle.get('numche')+"','estche':"+Detalle.get('estche')+",'numchequera':'"+Ext.getCmp('numchequera').getValue()+"','estins':"+0+",'orden':"+Detalle.get('orden')+",'codusu':'"+Detalle.get('codusu')+"'}";
					numDetalle++;
				}
				else
				{
					arregloJson = arregloJson + ",{'codban':'"+Ext.getCmp('codban').getValue()+"','ctaban':'"+Ext.getCmp('ctaban').value+"','numche':'"+Detalle.get('numche')+"','estche':"+Detalle.get('estche')+",'numchequera':'"+Ext.getCmp('numchequera').getValue()+"','estins':"+0+",'orden':"+Detalle.get('orden')+",'codusu':'"+Detalle.get('codusu')+"'}";
				}
			});
			arregloJson = arregloJson + "]}";
			var cheques = eval('(' + arregloJson + ')');
			var ObjSon=Ext.util.JSON.encode(cheques);
			parametros = 'ObjSon='+ObjSon;
			Ext.Ajax.request({
				url : ruta,
				params : parametros,
				method: 'POST',
				success: function ( resultado, request ){ 
				datos = resultado.responseText;
				var registro = datos.split("|");
				var respuesta = registro[1];
				if(respuesta=='1')
				{
					Ext.Msg.show({
						title:'Mensaje',
						msg: 'Registro '+mensaje+' con &#233;xito',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.INFO
					});
					irNuevo();									
				}
				else if(respuesta=='2')
				{
					Ext.Msg.show({
						title:'Mensaje',
						msg: 'Duplicada la clave primaria de la tabla scb_cheques',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.INFO
					});
				}
				else
				{
					Ext.MessageBox.alert('Mensaje');				
				}
			},
			failure: function ( result, request)
			{ 
				Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo'); 
			} 
			});
		}
		else
		{
			Ext.Msg.alert("Mensaje","La chequera debe tener al menos (1) un cheque y usuario asignado, verifique por favor!!");
		}
/*



			var chequesAsignados = gridChequesChequera.getStore().getModifiedRecords();
		if(chequesAsignados.length > 0)
		{
			if(Actualizar == null)
			{
				var arregloJson = "{'operacion':'incluir','codmenu':'"+codmenu+"','chequesincluir':[";
				for(var i=0;i<=chequesAsignados.length-1;i++)
				{	
					if(i==0)
					{
						arregloJson = arregloJson + "{'codban':'"+Ext.getCmp('codban').getValue()+"','ctaban':'"+Ext.getCmp('ctaban').value+"','numche':'"+chequesAsignados[i].get('numche')+"','estche':"+chequesAsignados[i].get('estche')+",'numchequera':'"+Ext.getCmp('numchequera').getValue()+"','estins':"+0+",'orden':"+chequesAsignados[i].get('orden')+",'codusu':'"+chequesAsignados[i].get('codusu')+"'}";
					}	
					else
					{
						arregloJson = arregloJson + ",{'codban':'"+Ext.getCmp('codban').getValue()+"','ctaban':'"+Ext.getCmp('ctaban').value+"','numche':'"+chequesAsignados[i].get('numche')+"','estche':"+chequesAsignados[i].get('estche')+",'numchequera':'"+Ext.getCmp('numchequera').getValue()+"','estins':"+0+",'orden':"+chequesAsignados[i].get('orden')+",'codusu':'"+chequesAsignados[i].get('codusu')+"'}";
					}		
				}
				arregloJson = arregloJson + "]}";
			} else
			{
				if(chequesAsignados.length > 0)
				{
					var arregloJson = "{'operacion':'actualizar','codmenu':'"+codmenu+"','chequesincluir':[";
					for(var i=0;i<=chequesAsignados.length-1;i++)
					{	
						var operacionbd = (chequesAsignados[i].isModified('orden'))?'I':'U';
						if(i==0)
						{

							arregloJson = arregloJson + "{'codban':'"+Ext.getCmp('codban').getValue()+"','ctaban':'"+Ext.getCmp('ctaban').value+"','numche':'"+chequesAsignados[i].get('numche')+"','estche':"+chequesAsignados[i].get('estche')+",'numchequera':'"+Ext.getCmp('numchequera').getValue()+"','estins':"+0+",'orden':"+chequesAsignados[i].get('orden')+",'codusu':'"+chequesAsignados[i].get('codusu')+"','operacionbd':'"+operacionbd+"'}";
						}	
						else
						{
							arregloJson = arregloJson + ",{'codban':'"+Ext.getCmp('codban').getValue()+"','ctaban':'"+Ext.getCmp('ctaban').value+"','numche':'"+chequesAsignados[i].get('numche')+"','estche':"+chequesAsignados[i].get('estche')+",'numchequera':'"+Ext.getCmp('numchequera').getValue()+"','estins':"+0+",'orden':"+chequesAsignados[i].get('orden')+",'codusu':'"+chequesAsignados[i].get('codusu')+"','operacionbd':'"+operacionbd+"'}";
						}		
					}
					var arregloJson = arregloJson + "]}";
					var cheques = eval('(' + arregloJson + ')');
					ObjSon=Ext.util.JSON.encode(cheques);
					parametros = 'ObjSon='+ObjSon;
					Ext.Ajax.request({
						url : ruta,
						params : parametros,
						method: 'POST',
						success: function ( resultado, request ){ 
						datos = resultado.responseText;
						var registro = datos.split("|");
						var respuesta = registro[1];
						if(respuesta=='1')
						{
							Ext.Msg.show({
								title:'Mensaje',
								msg: 'Registro actualizado con &#233;xito',
								buttons: Ext.Msg.OK,
								icon: Ext.MessageBox.INFO
							});
							irNuevo();									
						}
						else
						{
							Ext.MessageBox.alert('Mensaje');				
						}
					},
					failure: function ( result, request)
					{ 
						Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo'); 
					} 
					});
				}
			}
		}
		else
		{
			Ext.Msg.alert("Mensaje","La chequera debe tener al menos (1) un cheque y usuario asignado, verifique por favor!!");
		}*/
	}
	else
	{
		Ext.Msg.alert("Mensaje","Existen campos vacios, verifique por favor!!");
	}
}

function irEliminar()
{
	if(Actualizar)
	{
		if(!verificarChequeEmitido())
		{
			function respuesta(btn)
			{
				if(btn=='yes')
				{
					if(gridChequesChequera.getStore().getCount() > 0)
					{

						var chequeEliminar = gridChequesChequera.getStore().getRange(0,gridChequesChequera.getStore().getCount()-1);
						var arregloJsonEliminar = "{'operacion':'eliminar','codmenu':'"+codmenu+"','chequeseliminar':[";
						for(var i=0;i<=chequeEliminar.length-1;i++)
						{	
							if(i==0)
							{
								arregloJsonEliminar = arregloJsonEliminar + "{'codban':'"+Ext.getCmp('codban').getValue()+"','ctaban':'"+Ext.getCmp('ctaban').value+"','numche':'"+chequeEliminar[i].get('numche')+"','estche':"+chequeEliminar[i].get('estche')+",'numchequera':'"+Ext.getCmp('numchequera').getValue()+"','estins':"+0+",'orden':"+chequeEliminar[i].get('orden')+",'codusu':'"+chequeEliminar[i].get('codusu')+"'}";
							}	
							else
							{
								arregloJsonEliminar = arregloJsonEliminar + ",{'codban':'"+Ext.getCmp('codban').getValue()+"','ctaban':'"+Ext.getCmp('ctaban').value+"','numche':'"+chequeEliminar[i].get('numche')+"','estche':"+chequeEliminar[i].get('estche')+",'numchequera':'"+Ext.getCmp('numchequera').getValue()+"','estins':"+0+",'orden':"+chequeEliminar[i].get('orden')+",'codusu':'"+chequeEliminar[i].get('codusu')+"'}";
							}
						}
						arregloJsonEliminar = arregloJsonEliminar + "]}";
						var cheques = eval('(' + arregloJsonEliminar + ')');
						ObjSon=Ext.util.JSON.encode(cheques);
						parametros = 'ObjSon='+ObjSon;
						Ext.Ajax.request({
							url : ruta,
							params : parametros,
							method: 'POST',
							success: function ( resultado, request ){ 
							datos = resultado.responseText;
							var registro = datos.split("|");
							var respuesta = registro[1];
							if(respuesta == '1')
							{
								Ext.Msg.show({
									title:'Mensaje',
									msg: 'Registro eliminado con &#233;xito',
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.INFO
								});
								irNuevo();									
							}
							else
							{
								Ext.Msg.show({
									title:'Mensaje',
									msg: 'No se pudo eliminar el registro',
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.INFO
								});				
							}
						},
						failure: function ( result, request)
						{ 
							Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo'); 
						} 
						});
					}
					else
					{
						Ext.MessageBox.alert('Error','El registro seleccionado no tiene cheques, verifique por favor'); 
					}
				}
			}
			Ext.MessageBox.confirm('Confirmar', '&#191;Desea eliminar este registro&#63;', respuesta);
	  }
	  else
	  {
		  Ext.Msg.show({
				title:'Mensaje',
				msg: 'No se puede eliminar la chequera, existen cheques que han sido emitidos, verifique por favor',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
	  }
			
	}
	else
	{
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Opci&#243;n inv&#225;lida, el registro debe estar previamente guardado, verifique por favor',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}

}

function asignarUsuariosCheque()
{
	if(gridUsuariosChequera.getStore().getCount()>0)
	{
		if(gridChequesChequera.getStore().getCount()>0)
		{
			var dataStoreUsuarios = new Ext.data.SimpleStore({
				fields: ['codusu']
			});
			
			var datosNuevo = {'raiz':[{'numcheque':'','codusuario':''}]};
			var modoSeleccionCheque = new Ext.grid.CheckboxSelectionModel({
				listeners:{ 'rowselect': function(selection,indicefila,registro)
				{
					if(trim(Ext.getCmp('cmbusuario').getValue()) != "")
					{
						actual= registro.get('codusuario');
						actualizar = actual + ':'+trim(Ext.getCmp('cmbusuario').getValue())+':';
						registro.set('codusuario',actualizar);   
					}
					else
					{
						Ext.Msg.alert("Mensaje","Usuario no v&#225;lido, verifique por favor!!");
					}
	
					},
					'rowdeselect': function(selection,indicefila,registro)
					{
						registro.set('codusuario',"");
					}
			}
			});
			var chequesDisponibles =  new Ext.data.Store({
				proxy: new Ext.data.MemoryProxy(datosNuevo),
				reader: new Ext.data.JsonReader({
					root: 'raiz',               
					id: 'id'   
				},
				registroGridUsuario
				)
			});
			
			for(var i = 0; i<dsCheque.getCount(); i++)
			{
				
				if(dsCheque.getAt(i).get('estche') == 0)
				{
					var chequeDisponible = new chequeAsignar({
						'numcheque':trim(dsCheque.getAt(i).get('numche')),
						'codusuario':trim(dsCheque.getAt(i).get('codusu'))
					});
					
					chequesDisponibles.add(chequeDisponible);
				}
			}

			var gridChequesProcesados = new Ext.grid.GridPanel({
				width:700,
				autoScroll:true,
				height:300,
				border:true,
				style:"margin-left:50px;",
				ds: chequesDisponibles,
				cm: new Ext.grid.ColumnModel([
				                              {header: 'N&#250;mero de Cheque', width: 50, sortable: true,   dataIndex: 'numcheque'},
				                              {header: 'Usuario', width: 100, sortable: true, dataIndex: 'codusuario'},
				                               modoSeleccionCheque
				                              ]),
				                              viewConfig: {
				forceFit:true
			},
			stripeRows: true,
			sm:modoSeleccionCheque
			});

			dataStoreUsuarios.data = gridUsuariosChequera.getStore().data;

			var ventanaAsignacionUsuarioCheque = new Ext.Window(
					{
						title: 'Asignaci&#243;n de Cheques a Usuario',
						autoScroll:true,
						resizable:false,
						width:800,
						height:450,
						modal: true,
						closable:false,
						plain: false,
						items:[{
							xtype:"fieldset",
							autoScroll:true,
							border:false,
							height:50,
							width:500,
							labelWidth:150,
							items:[{xtype:"combo",
								store: dataStoreUsuarios,
								displayField:'codusu',
								valueField:'codusu',
								id:"cmbusuario",
								typeAhead: true,
								style:"margin-left:50px;",
								mode: 'local',
								triggerAction: 'all',
								selectOnFocus:true,
								fieldLabel:'Usuarios Disponibles',
								listWidth:200,
								editable:false,
								width:200
							}]
						},gridChequesProcesados],
						buttons: [{
							text:'Procesar Asignaci&#243;n',
							iconCls:'procesar',
							handler: function()
							{ 
							 if(procesarAsignacionChequeUsuario(chequesDisponibles))
							 {
								 ventanaAsignacionUsuarioCheque.destroy();	 
							 }
							}
						}
						,
						{
							text: 'Cancelar',
							handler: function()
							{
							ventanaAsignacionUsuarioCheque.destroy();
							}
						}]

					});
			ventanaAsignacionUsuarioCheque.show();
		}
		else
		{
			Ext.Msg.alert("Mensaje","No hay cheques para asignar, verifique por favor!!");
		}
	}
	else
	{
		Ext.Msg.alert("Mensaje","No hay usuarios para asignar, verifique por favor!!");
	}

}

function procesarAsignacionChequeUsuario(storeChequeAsignado)
{
	var exito = true;
	if(verificarTotalChequesAsignados(storeChequeAsignado))
	{
		gridChequesChequera.store.each(function(chequenoasignado){
			storeChequeAsignado.each(function(chequeasignado)
					{
						if((trim(chequenoasignado.get('numche')) == trim(chequeasignado.get('numcheque'))) &&
						   (chequenoasignado.get('estche') == 0) && (chequeasignado.isModified('codusuario')))
						{
							chequenoasignado.set('codusu',trim(chequeasignado.get('codusuario')));
						}
					});
		});
	}
	else
	{
		Ext.Msg.alert("Mensaje","Debe asignar todos los cheques, verifique por favor!!");
		exito= false;
	}
	
 return exito;
}

function agregarChequeNuevo()
{
	var ls_chequera   = Ext.getCmp('numchequera').getValue();
	if (ls_chequera!='')
	{
		var ls_codban = trim(Ext.getCmp('codban').getValue());
		if (ls_codban!='')
		{
			var ls_ctaban = trim(Ext.getCmp('ctaban').getValue());
			if (ls_ctaban!='')
			{
				var totalCheques = 0;
				var nextCheque = 0;
				totalCheques = gridChequesChequera.getStore().getCount();
				if(totalCheques>0)
				{
					nextCheque  = parseInt(gridChequesChequera.getStore().getAt(totalCheques-1).get('numche'),10)+1;
				}
				else
				{
					nextCheque = 1;
				}

				var cheque =  new chequeNuevo({'numche':"",'estche':"",'codusu':"",'orden':""});
				cheque.set('numche',ue_rellenarcampo(nextCheque.toString(),15));
				cheque.set('estche',0);
				cheque.set('codusu',"");
				cheque.set('orden',totalCheques+1);
				gridChequesChequera.getStore().add(cheque);	
			}
			else
			{
				Ext.Msg.alert("Mensaje","La Cuenta Bancaria est&#225; vac&#237;a, verifique por favor!!");
			}
		}
		else
		{
			Ext.Msg.alert("Mensaje","El Banco est&#225; vac&#237;o, verifique por favor!!");
		}
	}
	else
	{
		Ext.Msg.alert("Mensaje","El N&#250;mero de Chequera est&#225; vac&#237;o, verifique por favor!!");
	}
}

function verificarTotalChequesAsignados(storeCheques)
{
	var asignados = true;
	storeCheques.each(function(cheque){
		   if(trim(cheque.get('codusuario')) == "" )
		   {
			   asignados = false;
		   }
	});
	return asignados;
}

function obtenerEstatusCheque(estatus)
{
	return (estatus==1) ? 'EMITIDO':'SIN EMITIR';
}

function verificarChequeUnico(numcheque)
{
	var existe = false;
	gridChequesChequera.getStore().each(function(cheque){

		if(trim(cheque.get('numche')) == numcheque)
		{
			existe = true;

			return !existe;
		}

	});

	return existe;
}

function verificarUsuarioAsignado(usuario)
{
	var tienechequeasignado = false;
	gridChequesChequera.getStore().each(function(cheque){
		if(trim(cheque.get("codusu")) == trim(usuario))
		{
			tienechequeasignado = true;
			return false;
		}
	});
	
	return tienechequeasignado;
}

function verificarChequeEmitido()
{
	var existechequeemitido = false;
	gridChequesChequera.getStore().each(function(cheque){
		if(cheque.get("estche") == 1)
		{
			existechequeemitido = true;
			return false;
		}
	});
	
	return existechequeemitido;
}

function buscarGenerica()
{
	mostrarCatalogoChequeraDefinicion(gridChequesChequera,gridUsuariosChequera);
}

function verificarCamposVacios()
{
 	if(trim(Ext.getCmp('numchequera').getValue()) == "")
 	{
 		Ext.getCmp('numchequera').focus(true);
 		return false;
 	}
 	else if(trim(Ext.getCmp('codban').getValue()) == "")
 	{
 		Ext.getCmp('codban').focus(true);
 		return false;
 	}
 	else if(trim(Ext.getCmp('ctaban').getValue()) == "")
 	{
 		Ext.getCmp('ctaban').focus(true);
 		return false;
 	}
 	else
 	{
 		return true;
 	}	
}

function verificarExistenciaChequera()
{
 var objetoJson = "{'operacion':'verificarchequera','numchequera':'"+Ext.getCmp('numchequera').getValue()+"','codban':'"+Ext.getCmp('codban').getValue()+"','ctaban':'"+Ext.getCmp('ctaban').getValue()+"'}";
 var objetoJson = eval('(' + objetoJson + ')');
 var ObjSon=Ext.util.JSON.encode(objetoJson);
	parametros = 'ObjSon='+ObjSon;
	Ext.Ajax.request({
		url : ruta,
		params : parametros,
		method: 'POST',
		success: function ( resultado, request ){ 
		var respuesta = eval('(' + resultado.responseText + ')');
		if(respuesta.existe)
		{
			Ext.Msg.show({
				title:'Mensaje',
				msg: "El n&#250;mero de chequera indicado, ya se encuentra asociado a la cuenta y banco indicados, debe indicar uno distinto, verifique por favor!!",
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
			Ext.getCmp('numchequera').setValue("");
			Ext.getCmp('numchequera').focus(true);
		}
	} 
	});
}

function irBuscar()
{
	mostrarCatalogoChequeraDefinicion();	
}