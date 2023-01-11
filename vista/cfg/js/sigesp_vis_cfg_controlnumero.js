/***********************************************************************************
* @Archivo JavaScript que incluye tanto los componentes como los eventos asociados 
* a la definicion de la Control de Numero. 
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
var formulario      = '';                           						// Variable que representa y contiene el panel que contiene los objetos
var cambiar         = false;	                    						// Variable que verifica el Estatus de la Operacion para Modificacion
var pantalla        = 'sigesp_vis_cfg_controlnumero.php'; 							// Variable que contiene el nombre f�sico de la Pantalla
var sistema         = "CFG";                        						// Variable que contiene el nombre del sistema al que pertenece la pantalla
var ruta	    = '../../controlador/cfg/sigesp_ctr_cfg_controlnumero.php'; 	// Ruta del Controlador de la Pantalla
var gridUsu	    = null;
var Actualizar      = null;

var arregloDocumentos = [
                              ['------','-- Seleccione --'],
                              ['SEPSPC','Solicitud de Ejecucion Presupuestaria'],
                              ['SOCCOC','Orden de Compra'],
                              ['SOCCOS','Orden de Servicio'],
                              ['CXPSOP','Solicitud de Pago'],
                              ['SCBBRE','Movimiento de Banco'],
                              ['SCGCMP','Comprobante Contabilidad General/Fiscal'],
                              ['SPGCMP','Comprobante Presupuestario'],
                              ['SPGCRA','Modificacion Presupuestaria - Credito Adicional'],
                              ['SPGTRA','Modificacion Presupuestaria - Traspaso'],
                              ['SPGINS','Modificacion Presupuestaria - Insubsitencia'],
                              ['SPGREC','Modificacion Presupuestaria - Rectificacion'],
                              ['SIVART','Articulo']
                              ]; // Arreglo que contiene los Documentos que se pueden controlar

var dataStoreDocumentos = new Ext.data.SimpleStore({
	  fields: ['procede', 'documento'],
	  data : arregloDocumentos // Se asocian los documentos disponibles
	});

var dataStoreUsuariosEliminacion = new Ext.data.SimpleStore({
    fields: ['codemp','id','codsis','procede','prefijo','codusu']
});

var registroGridUsuario = Ext.data.Record.create
	([
		{name: 'codusu'}, 
		{name: 'nomusu'},
		{name: 'apeusu'}
	]);
	
var usuarioEliminar = Ext.data.Record.create
	([
		{name: 'codemp'},
		{name: 'id'},
		{name: 'codsis'},
		{name: 'procede'},
		{name: 'prefijo'},
		{name: 'codusu'}
	]);


var Campos =new Array(
						['codemp',''],
						['id','novacio|'],
						['codsis','novacio|'],
						['procede','novacio|'],
						['nro_actual','novacio|'],
						['estcompscg','novacio|'],
						['prefijo','novacio|'],
						['nro_inicial','novacio|'],
						['nro_final','novacio|'],
						['maxlen','novacio|'],
						['estact','novacio|']); // Arreglo que contiene la informacion del Registro, deben coincidir con la Tabla en la Base de Datos

Ext.onReady(function(){
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	 
	var agregar = new Ext.Action({
		text: 'Agregar',
		handler: irAgregar,
		iconCls: 'agregar',
	    tooltip: 'Agregar usuario al control de n&#250;mero'
	});
		
	var quitar = new Ext.Action({
		text: 'Quitar',
		handler: irQuitar,
		iconCls: 'remover',
	    tooltip: 'Quitar usuario del control de n&#250;mero'
	});
	
	Xpos = ((screen.width/2)-(600/2));
	Ypos = 75;	
    var formulario = new Ext.form.FormPanel({
    	   	 title:"Registro de Control de N&#250;mero",
    		 frame:true,
    		 style: 'position:absolute;margin-left:'+Xpos+'px;margin-top:'+Ypos+'px',
    		 width: 630,
    		 height: 430,
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
						style: "margin-top:30px;padding-left:50px;",
						labelWidth:175,
						items:[
						       {
						        xtype:"textfield",
						        fieldLabel:"C&#243;digo",
						        labelWidth:40,
						        labelSeparator:'',
						        name:"codigo",
						        id:"id",
								autoCreate: {tag: 'input', type: 'text', size: '4', autocomplete: 'off', maxlength: '4'},
						        width:75,
								disabled:true
				        	   },
				        	   {
					                xtype:"combo",
					                labelSeparator:'',
					                store: dataStoreDocumentos,
					                hiddenName:'documentos',
					                hiddenid:'iddocumento',
					                displayField:'documento',
					                valueField:'procede',
									id:"procede",
					                typeAhead: true,
					                mode: 'local',
					                triggerAction: 'all',
					                selectOnFocus:true,
					                fieldLabel:'Documento',
					           	    listWidth:250,
					           	    editable:false,
					                width:250,
					                listeners: {
				        		   				'blur':function(combo){
				        		   										if (combo.getValue() == 'SCGCMP')
				        		   										{
				        		   											Ext.getCmp('estcompscg').enable();
				        		   										}
				        		   										else
				        		   										{
				        		   											Ext.getCmp('estcompscg').disable();
				        		   											Ext.getCmp('estcompscg').setValue(false);
				        		   										}
				        	   										  }
				        	   				   }
				         		},
					           {
						        xtype:"textfield",
						        fieldLabel:"Prefijo",
						        labelSeparator:'',
						        labelWidth:60,
						        name:"prefijo",
						        id:"prefijo",
						        width:100,
								autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '6', onkeyup:'calcularNumeroActual()', onblur:'verificarPrefijo()', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890-');"},
								listeners: {
											'change':function(campo,nuevovalor,antiguovalor)
											         {
													 	if((nuevovalor != antiguovalor)&&(Actualizar!=null))
														{
															verificarPrefijo();
														}
														
														//agregar funcion que agrege modificacion a todas las filas de la grid
														gridUsuariosControlNumero.store.each(function (registroGrid)
														{
															var nombre = registroGrid.get('nomusu');
															registroGrid.set('nomusu',nombre+' ');
														});
													 },
											 'blur' : function(campo)
											 {
                                                                                                valor = String(campo.getValue()).trim();
												valor = rellenarCampoCerosIzquierda(valor,6);
												campo.setValue(valor);
												verificarPrefijo();
												calcularNumeroActual();
											 }													 
										   }
				        	   },
					           {
						        xtype:"textfield",
						        fieldLabel:"N&#250;mero Actual",
						        labelSeparator:'',
						        labelWidth:100,
						        name:"nro_actual",
						        id:"nro_actual",
						        readOnly:true,
						        width:150,
								autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', onblur:'calcularNumeroActual()'}
				        	   },
					           {
						        xtype:"checkbox",
						        fieldLabel:"Inicializar a cero el consecutivo del n&#250;mero de comprobante al final del mes",
						        labelWidth:40,
						        name:"estcompscg",
						        labelSeparator:'',
						        id:"estcompscg",
						        width:100,
						        disabled:true,
								inputValue:'1'
								},
							   {
							        xtype:"hidden",
							        name:"maxlen",
							        id:"maxlen",
							        value:15
						       },
							   {
							        xtype:"hidden",
							        name:"nro_inicial",
							        id:"nro_inicial",
							        value:1
						       },
							   {
							        xtype:"hidden",
							        name:"nro_final",
							        id:"nro_final",
							        value:999999
						       },
							   {
							        xtype:"hidden",
							        name:"codsis",
							        id:"codsis",
									value:''
						       },
							   {
							        xtype:"hidden",
							        name:"estact",
							        id:"estact",
									value:1
						       },{
									xtype:'panel',
									width:550,
									height:200,
									autoScroll:true,
									title:'Usuarios para el N&#250;mero',
									tbar: [agregar,quitar],
									contentEl:'grid_panelusuarios'
						       }]
			        }]
    		});
     formulario.render("formulario_control_nro");
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
	var myJSONObject ={
		"oper":"nuevo"
	};
	
	var ObjSon=Ext.util.JSON.encode(myJSONObject);
	var parametros = 'ObjSon='+ObjSon;
	Ext.Ajax.request({
	url : '../../controlador/cfg/sigesp_ctr_cfg_controlnumero.php',
	params : parametros,
	method: 'POST',
	success: function ( result, request) 
	{ 
		datos = result.responseText;
		var codigo = eval('(' + datos + ')');
		if(codigo != "")
		{
			Ext.getCmp('id').setValue(codigo);
			Ext.getCmp('procede').enable();
			Ext.getCmp('prefijo').enable();			
			gridUsuariosControlNumero.store.removeAll();
		}
	}	
	})
}

function calcularNumeroActual(longitud)
{
	
	ls_prefijo=Ext.getCmp('prefijo').getValue();
	ls_inicial=Ext.getCmp('nro_inicial').value;
	ls_logitud=Ext.getCmp('maxlen').value;
	var mystring  =new String(ls_prefijo);
	var mystring2 =new String(ls_inicial);
	if (ls_prefijo!="" && ls_inicial!="" )
	{
	     cadena_ceros = "";
	     lenprefijo       = mystring.length;
		 leninicial       = mystring2.length;
	     total            = ls_logitud-lenprefijo;
		 totalfinal       = total-leninicial;
		 
	     for (i=1;i<=totalfinal;i++)
		 {
		   cadena_ceros=cadena_ceros+"0";
		 }
		 cadena =ls_prefijo+cadena_ceros+ls_inicial;
	    
		 Ext.getCmp('nro_actual').setValue(cadena);
	       
	 }
}

function verificarPrefijo()
{
	
	var myJSONObject ={
		"oper":"verificarprefijo",
		"procede":Ext.getCmp('procede').getValue(),
		"prefijo":Ext.getCmp('prefijo').getValue()
	};
	
	ObjSon=Ext.util.JSON.encode(myJSONObject);
	parametros = 'ObjSon='+ObjSon;
	Ext.Ajax.request({
	url : '../../controlador/cfg/sigesp_ctr_cfg_controlnumero.php',
	params : parametros,
	method: 'POST',
	success: function ( result, request) 
	{ 
		datos = result.responseText;
		var respuesta = eval('(' + datos + ')');
		prefijo = Ext.getCmp('prefijo').getValue();
		if((respuesta.existe)&&(Actualizar==null))
		{
			Ext.Msg.show({
			   	title:'Mensaje',
			   	msg: 'El prefijo '+prefijo+' ya esta asociado al documento '+dataStoreDocumentos.getAt(dataStoreDocumentos.find('procede',Ext.getCmp('procede').getValue())).get('documento')+', debe indicar uno distinto',
			   	buttons: Ext.Msg.OK,
			   	fn: '',
			   	animEl: 'elId',
			   	icon: Ext.MessageBox.ERROR
				});
			Ext.getCmp('prefijo').setValue('');
			Ext.getCmp('nro_actual').setValue('');
		}
	}	
	})
}

function irAgregar()
{
	if (Ext.getCmp('id').getValue() !='')
	{
		mostrarCatalogoUsuario('catalogoActivos',gridUsuariosControlNumero);
		if(dataStoreUsuariosEliminacion.getCount() > 0)
		{
			dataStoreUsuariosEliminacion.each(
											  function (control)
											  {
												gridUsuariosControlNumero.each(
																			   function(usuario)
																			   {
																				  if(control.get('codusu')==usuario.get('codusu'))
																				  {
																					 dataStoreUsuariosEliminacion.remove(control); 
																				  }
																			   }
																			  )
											  }
											 )
		}
	}
	else
	{
		Ext.MessageBox.alert('Mensaje','Debe seleccionar un c&#243;digo de Control de N&#250;mero, verifique por favor');
	}
}

function irQuitar()
{
	usuariosEliminar = gridUsuariosControlNumero.getSelectionModel().getSelections();
	
	for (i=0; i<usuariosEliminar.length; i++)
    {
		if(usuariosEliminar[i].isModified('codusu'))
		{
			gridUsuariosControlNumero.store.remove(usuariosEliminar[i]);
		}
		else
		{
			dataStoreUsuariosEliminacion.add(usuariosEliminar[i]);
			gridUsuariosControlNumero.store.remove(usuariosEliminar[i]);
		}
    }
}

function obtenerGridUsuario()
{	
	var datosNuevo = {'raiz':[{'codusu':'','nomusu':'','apeusu':''}]};
	var modoSeleccionControl = new Ext.grid.CheckboxSelectionModel({});
	dsusuario =  new Ext.data.Store({
	proxy: new Ext.data.MemoryProxy(datosNuevo),
	reader: new Ext.data.JsonReader({
		root: 'raiz',               
		id: 'id'   
		},
			registroGridUsuario
		),
		data: datosNuevo
		});
	
	gridUsuariosControlNumero = new Ext.grid.GridPanel({
			width:500,
			autoScroll:true,
			height:200,
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
			stripeRows: true,
			sm: new Ext.grid.CheckboxSelectionModel({})
	});
	gridUsuariosControlNumero.render('grid_panelusuarios');
}

function irGuardar()
{
	obtenerMensaje('procesar','','Guardando Datos');
	if(Actualizar == null)
	{
                if(gridUsuariosControlNumero.getStore().getCount()!=0)
		{
			var arregloJson = "{'oper':'incluir','codmenu':"+codmenu+",";
                        arregloJson = arregloJson + "'cabecera':[{'codemp':'"+Ext.getCmp('codemp').value+"','id':'"+Ext.getCmp('id').getValue()+"','codsis':'"+Ext.getCmp('codsis').value+"','procede':'"+Ext.getCmp('procede').getValue()+"','nro_actual':'"+Ext.getCmp('nro_actual').getValue()+"','estcompscg':"+Ext.getCmp('estcompscg').getValue()+",'prefijo':'"+Ext.getCmp('prefijo').getValue()+"','nro_inicial':"+Ext.getCmp('nro_inicial').value+",'nro_final':"+Ext.getCmp('nro_final').value+",'maxlen':"+Ext.getCmp('maxlen').value+",'estact':"+Ext.getCmp('estact').value+"}],";
			arregloJson = arregloJson + "'usuariosincluir':[";
                        numDetalle=0;
			gridUsuariosControlNumero.store.each(function (Detalle)
				{
					if (numDetalle==0)
					{
						arregloJson = arregloJson + "{'codemp':'"+Ext.getCmp('codemp').value+"','id':'"+Ext.getCmp('id').getValue()+"','codsis':'"+Ext.getCmp('codsis').value+"','procede':'"+Ext.getCmp('procede').getValue()+"','codusu':'"+Detalle.get('codusu')+"','prefijo':'"+Ext.getCmp('prefijo').getValue()+"'}";
						numDetalle++;
					}
					else
					{
						arregloJson = arregloJson + ",{'codemp':'"+Ext.getCmp('codemp').value+"','id':'"+Ext.getCmp('id').getValue()+"','codsis':'"+Ext.getCmp('codsis').value+"','procede':'"+Ext.getCmp('procede').getValue()+"','codusu':'"+Detalle.get('codusu')+"','prefijo':'"+Ext.getCmp('prefijo').getValue()+"'}";
					}
				}
			)
			var arregloJson = arregloJson + "]}";
			var usuarios    = eval('(' + arregloJson + ')');
			var ObjSon      = Ext.util.JSON.encode(usuarios);
			var parametros  = 'ObjSon='+ObjSon;
			Ext.Ajax.request({
				url : ruta,
				params : parametros,
				method: 'POST',
				success: function ( resultado, request )
				{
					Ext.Msg.hide();
					var datos = resultado.responseText;
					var Registros = datos.split("|");
					if(Registros[1]=='1')
					{
						Ext.Msg.show({
							title:'Mensaje',
							msg: 'Registro incluido con &#233;xito',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.INFO
						});
					}
					else
					{
						if(Registros[0]=='-2')
						{
							Ext.Msg.show({
								title:'Mensaje',
								msg: 'El registro que intenta incluir esta duplicado verifique los datos insertados',
								buttons: Ext.Msg.OK,
								icon: Ext.MessageBox.INFO
							});
						}
						else
						{
							Ext.Msg.show({
								title:'Mensaje',
								msg: 'Ha ocurrido un error el registro no fue incluido',
								buttons: Ext.Msg.OK,
								icon: Ext.MessageBox.INFO
							});
						}
					}
					gridUsuariosControlNumero.store.commitChanges();
					gridUsuariosControlNumero.store.removeAll();
					limpiarCampos();
				 },
				 failure: function ( result, request)
				 {
					 Ext.Msg.hide();
					 Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo'); 
				 }	 
			});
		}
		else
		{
			Ext.Msg.hide();
			Ext.Msg.show({
				title:'Advertencia',
				msg: 'Debe agregar al menos un usuario',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.WARNING
			});
		}
	}
	else
	{
                var arregloJson = "{'oper':'actualizar','codmenu':"+codmenu+",";
                arregloJson = arregloJson + "'cabecera':[{'codemp':'"+Ext.getCmp('codemp').value+"','id':'"+Ext.getCmp('id').getValue()+"','codsis':'"+Ext.getCmp('codsis').value+"','procede':'"+Ext.getCmp('procede').getValue()+"','nro_actual':'"+Ext.getCmp('nro_actual').getValue()+"','estcompscg':"+Ext.getCmp('estcompscg').getValue()+",'prefijo':'"+Ext.getCmp('prefijo').getValue()+"','nro_inicial':"+Ext.getCmp('nro_inicial').value+",'nro_final':"+Ext.getCmp('nro_final').value+",'maxlen':"+Ext.getCmp('maxlen').value+",'estact':"+Ext.getCmp('estact').value+"}],";
                arregloJson = arregloJson + "'usuariosincluir':[";
                numDetalle=0;
                gridUsuariosControlNumero.store.each(function (Detalle)
                        {
                                if (numDetalle==0)
                                {
                                        arregloJson = arregloJson + "{'codemp':'"+Ext.getCmp('codemp').value+"','id':'"+Ext.getCmp('id').getValue()+"','codsis':'"+Ext.getCmp('codsis').value+"','procede':'"+Ext.getCmp('procede').getValue()+"','codusu':'"+Detalle.get('codusu')+"','prefijo':'"+Ext.getCmp('prefijo').getValue()+"'}";
                                       
                                }
                                else
                                {
                                        arregloJson = arregloJson + ",{'codemp':'"+Ext.getCmp('codemp').value+"','id':'"+Ext.getCmp('id').getValue()+"','codsis':'"+Ext.getCmp('codsis').value+"','procede':'"+Ext.getCmp('procede').getValue()+"','codusu':'"+Detalle.get('codusu')+"','prefijo':'"+Ext.getCmp('prefijo').getValue()+"'}";
                                }
								numDetalle++;
								if (numDetalle>499)
								{
									arregloJson = arregloJson + "],'usuariosincluir1':[";
									numDetalle=0;
								}
                        }
                )
		var usuarioEliminar     = dataStoreUsuariosEliminacion.getRange(0,dataStoreUsuariosEliminacion.getCount()-1);
		arregloJson  = arregloJson + "],'usuarioseliminar':[";
		numDetalle=0;
		for(var i=0;i<=usuarioEliminar.length-1;i++)
		{
			if(numDetalle==0)
			{
                                arregloJson = arregloJson + "{'codemp':'"+Ext.getCmp('codemp').value+"','id':'"+Ext.getCmp('id').getValue()+"','codsis':'"+Ext.getCmp('codsis').value+"','procede':'"+Ext.getCmp('procede').getValue()+"','codusu':'"+usuarioEliminar[i].get('codusu')+"','prefijo':'"+Ext.getCmp('prefijo').getValue()+"'}";
			}	
			else
			{
                                arregloJson = arregloJson + ",{'codemp':'"+Ext.getCmp('codemp').value+"','id':'"+Ext.getCmp('id').getValue()+"','codsis':'"+Ext.getCmp('codsis').value+"','procede':'"+Ext.getCmp('procede').getValue()+"','codusu':'"+usuarioEliminar[i].get('codusu')+"','prefijo':'"+Ext.getCmp('prefijo').getValue()+"'}";
			}		
			numDetalle++;
			if (numDetalle>499)
			{
				arregloJson = arregloJson + "],'usuarioseliminar1':[";
				numDetalle=0;
			}
		}
		arregloJson = arregloJson + "]}";
		var usuarios   = eval('(' + arregloJson + ')');
		var ObjSon     = Ext.util.JSON.encode(usuarios);
		var parametros = 'ObjSon='+ObjSon;
		Ext.Ajax.request({
			url : ruta,
			params : parametros,
			method: 'POST',
			success: function ( resultado, request )
			{
				Ext.Msg.hide();
				var datos = resultado.responseText;
				var Registros = datos.split("|");
				var usuario = Registros[2];
				var msjUsuario = 'El correlativo definido para los usuarios';
				if(Registros[1]=='1')
				{
					if(usuario=='')
                                        {
						Ext.Msg.show({
							title:'Mensaje',
							msg: 'Registro actualizado con &#233;xito',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.INFO
						});
					}
					else
					{
						usuario = eval('(' + usuario + ')');
						msjUsuario = 'El correlativo definido para los usuarios';
                                                for (var index = 0; index < usuario.usuariosinvalidos.length; index++)
                                                {
                                                    if(index == 0)
                                                    {
                                                            msjUsuario += ' '+usuario.usuariosinvalidos[index];
                                                    }
                                                    else
                                                    {
                                                            msjUsuario += ', '+usuario.usuariosinvalidos[index];
                                                    }
                                                }
					    msjUsuario += ' no puede ser eliminado por que ya fue usado al registrar un documento.'
					    
					    Ext.Msg.show({
							title:'Mensaje',
							msg: 'Operaci&#243;n completada con &#233;xito, '+msjUsuario,
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.INFO
						});
					}
				}
				else
                                {
					if(usuario=='')
					{
						Ext.Msg.show({
						title:'Error',
						msg: 'El registro no pudo ser actualizado con &#233;xito',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.ERROR
						});
					}
					else
					{
						usuario = eval('(' + usuario + ')');
						msjUsuario = 'El correlativo definido para los usuarios';
					    for (var index = 0; index < usuario.usuariosinvalidos.length; index++)
						{
					    	if(index == 0)
							{
					    		msjUsuario += ' '+usuario.usuariosinvalidos[index];
					    	}
					    	else
							{
					    		msjUsuario += ', '+usuario.usuariosinvalidos[index];
					    	}
					    	
					    }
					    msjUsuario += ' no puede ser eliminado por que ya fue usado al registrar un documento.'
					    
					    Ext.Msg.show({
							title:'Mensaje',
							msg: 'Operaci&#243;n no pudo ser completada con &#233;xito, '+msjUsuario,
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.INFO
						});
					}
				}
				
				gridUsuariosControlNumero.store.commitChanges();
				gridUsuariosControlNumero.store.removeAll();
				dataStoreUsuariosEliminacion.removeAll();
				limpiarCampos();
			},
			failure: function ( result, request)
			{
				Ext.Msg.hide();
				Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo'); 
			} 
		});
	
	 }
}

function irEliminar()
{
	var respuesta;
        if(Actualizar)
	{
		function respuesta(btn)
		{
			if(btn=='yes')
			{
                                var arregloJsonEliminar = "{'oper':'eliminar','codmenu':"+codmenu+",";
                                arregloJsonEliminar = arregloJsonEliminar + "'cabecera':[{'codemp':'"+Ext.getCmp('codemp').value+"','id':'"+Ext.getCmp('id').getValue()+"','codsis':'"+Ext.getCmp('codsis').value+"','procede':'"+Ext.getCmp('procede').getValue()+"','prefijo':'"+Ext.getCmp('prefijo').getValue()+"'}],";
				if(gridUsuariosControlNumero.store.getCount() > 0)
				{
					usuarioEliminar = gridUsuariosControlNumero.store.getRange(0,gridUsuariosControlNumero.store.getCount()-1);
                                        arregloJsonEliminar = arregloJsonEliminar + "'usuarioseliminar':[";
					for(var i=0;i<=usuarioEliminar.length-1;i++)
					{	
						if(i==0)
						{
							arregloJsonEliminar = arregloJsonEliminar + "{'id':'"+Ext.getCmp('id').getValue()+"','codsis':'"+Ext.getCmp('codsis').getValue()+"','procede':'"+Ext.getCmp('procede').getValue()+"','codusu':'"+usuarioEliminar[i].get('codusu')+"','prefijo':'"+Ext.getCmp('prefijo').getValue()+"'}";
						}	
						else
						{
							arregloJsonEliminar = arregloJsonEliminar + ",{'id':'"+Ext.getCmp('id').getValue()+"','codsis':'"+Ext.getCmp('codsis').getValue()+"','procede':'"+Ext.getCmp('procede').getValue()+"','codusu':'"+usuarioEliminar[i].get('codusu')+"','prefijo':'"+Ext.getCmp('prefijo').getValue()+"'}";
								
						}
					}
					arregloJsonEliminar = arregloJsonEliminar + "]}";
					var usuarios = eval('(' + arregloJsonEliminar + ')');
					var ObjSon   = Ext.util.JSON.encode(usuarios);
					var parametros = 'ObjSon='+ObjSon;
					Ext.Ajax.request({
					url : ruta,
					params : parametros,
					method: 'POST',
					success: function ( resultado, request )
					{ 
						Ext.Msg.hide();
						var datos = resultado.responseText;
						var Registros = datos.split("|");
						var Cod = Registros[1];
						var usuario = Registros[2];
						var msjUsuario = 'El correlativo definido para los usuarios';
						if(Cod=='1')
						{
							if(usuario=='')
							{
								Ext.Msg.show({
									title:'Mensaje',
									msg: 'Registro eliminado con &#233;xito',
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.INFO
								});
							}
							else
							{
								usuario = eval('(' + usuario + ')');
								msjUsuario = 'El correlativo definido para los usuarios';
								for (var index = 0; index < usuario.usuariosinvalidos.length; index++)
								{
									if(index == 0)
									{
										msjUsuario += ' '+usuario.usuariosinvalidos[index];
									}
									else
									{
										msjUsuario += ', '+usuario.usuariosinvalidos[index];
									}
									
								}
								msjUsuario += ' no puede ser eliminado por que ya fue usado al registrar un documento.'
								
								Ext.Msg.show({
									title:'Mensaje',
									msg: 'Operaci&#243;n completada con &#233;xito, '+msjUsuario,
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.INFO
								});
							}
						}
						else
						{
							if(usuario=='')
							{
								Ext.Msg.show({
									title:'Error',
									msg: 'El registro no pudo ser eliminado con &#233;xito',
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.ERROR
								});
							}
							else
							{
								usuario = eval('(' + usuario + ')');
								msjUsuario = 'El correlativo definido para los usuarios';
								for (var index = 0; index < usuario.usuariosinvalidos.length; index++)
								{
									if(index == 0)
									{
										msjUsuario += ' '+usuario.usuariosinvalidos[index];
									}
									else
									{
										msjUsuario += ', '+usuario.usuariosinvalidos[index];
									}
									
								}
								msjUsuario += ' no puede ser eliminado por que ya fue usado al registrar un documento.'
								
								Ext.Msg.show({
									title:'Mensaje',
									msg: 'La operaci&#243;n no pudo ser completada con &#233;xito, '+msjUsuario,
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.INFO
								});
							}
						}
						gridUsuariosControlNumero.store.commitChanges();
						gridUsuariosControlNumero.store.removeAll();
						dataStoreUsuariosEliminacion.removeAll();
						limpiarCampos();
					  },
					  failure: function ( result, request)
					  { 
							Ext.Msg.hide();
							Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo'); 
					  } 
					  });
				}
				else
				{
				  Ext.MessageBox.alert('Error','El registro seleccionado no tiene usuarios asignados, verifique por favor'); 
				}
			}
		}
		Ext.MessageBox.confirm('Confirmar', '&#191;Desea eliminar este registro&#63;', respuesta);
		
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
