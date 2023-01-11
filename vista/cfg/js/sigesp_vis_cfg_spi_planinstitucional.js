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
var gridPlanIngresoIns			= '';
var dscuentaelimi      = null;
var Actualizar		   = null

var formspi =empresa["formspi"];
formspi=replaceAll(formspi,'-','');
formspi=replaceAll(formspi,' ','');
var longitud=formspi.length;
var estpreing =empresa["estpreing"];
var fieldSetEstructura = null; 
var dsplaningreso = null;

Ext.onReady(function(){

		Ext.QuickTips.init();
		Ext.Ajax.timeout=36000000000;
	
	fieldSetEstructura = new com.sigesp.vista.comFieldSetEstructuraPresupuesto({
		titform: 'Estructura Presupuestaria',
		mostrarDenominacion:true,
		idtxt:'1',
		onAceptar:true,
		fnOnAceptar:irNuevo
	});

/******CATALOGO DE RECURSOS*********/
	function catRecuros()
	{
		var registroPlanUnicoRecurso = Ext.data.Record.create([
		    {name: 'sig_cuenta'},    
		    {name: 'denominacion'},
		    {name: 'sc_cuenta'},
                    {name: 'cueclaeco'}
		]);
		                                                 		                                 	
		var dsPlanUnicoRecurso =  new Ext.data.Store({
			reader: new Ext.data.JsonReader({root: 'raiz', id: "id"},registroPlanUnicoRecurso)
		});
		
		var formBusquedaPlanUnicoRecurso = new Ext.FormPanel({
	        labelWidth: 80,
	        frame:true,
	        title: 'B&uacute;squeda',
	        bodyStyle:'padding:5px 5px 0',
	        width: 630,
			height:100,
	        defaultType: 'textfield',
			items: [{
					fieldLabel: 'Cuenta',
					name: 'codplanunicore',
					labelSeparator:'',
					id:'codplanunicore',
					width:200,
					autoCreate: {tag: 'input', type: 'text', maxlength: 25, onkeypress: "return keyRestrict(event,'0123456789');"},
					changeCheck: function()
					{
						var v = this.getValue();
						dsPlanUnicoRecurso.filter('sig_cuenta',v);
					},							 
					initEvents : function()
					{
						AgregarKeyPress(this);
					}               
				},{
					fieldLabel: 'Denominaci&#243;n',
					name: 'denplanunicore',
					id:'denplanunicore',
					labelSeparator:'',
					autoCreate: {tag: 'input', type: 'text', maxlength: 254, onkeypress: "return keyRestrict(event,'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!=°;:[]{}·ÈÌÛ˙¡…Õ”⁄ ');"},
					width:500,
					changeCheck: function() {
						var v = this.getValue();
						dsPlanUnicoRecurso.filter('denominacion',v,true,false);
					},							 
					initEvents : function()
					{
						AgregarKeyPress(this);
					}
				}]
		});
		
		var JSONObject = {"oper": 'catalogorecursos'};
		var ObjSon=JSON.stringify(JSONObject);
		var	parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
			url : '../../controlador/cfg/sigesp_ctr_cfg_scg_planunico.php',
			params : parametros,
			method: 'POST',
			success: function ( resultado, request) 
			{ 
				datos = resultado.responseText;
				var objetoPlanUnicoRecursoEgreso = eval('(' + datos + ')');
				if(objetoPlanUnicoRecursoEgreso!='')
				{
					dsPlanUnicoRecurso.loadData(objetoPlanUnicoRecursoEgreso);
				}
			}	
		});
				
		var gridRecurso = new Ext.grid.GridPanel({
			width:770,
			height:400,
			tbar: formBusquedaPlanUnicoRecurso,
			autoScroll:true,
		    border:true,
		    ds: dsPlanUnicoRecurso,
		    cm: new Ext.grid.ColumnModel([new Ext.grid.CheckboxSelectionModel({}),
		          {header: "Cuenta", width: 20, sortable: true,   dataIndex: 'sig_cuenta'},
		          {header: "Denominaci&#243;n", width: 80, sortable: true, dataIndex: 'denominacion'}
		    ]),
		    sm: new Ext.grid.CheckboxSelectionModel({}),
		    stripeRows: true,
		    viewConfig: {forceFit:true}
		});
		
		
		var venCatRecursoEgreso = new Ext.Window({
			title: 'Cat&#225;logo de cuentas del plan &#250;nico de recursos y egresos',
			autoScroll:true,
	        width:785,
	        height:485,
	        modal: true,
	        closable:false,
	        plain: false,
	        items:[gridRecurso],
	        buttons: [{
						text:'Aceptar',  
				        handler: function()
						{
				        		var arrRegistro = gridRecurso.getSelectionModel().getSelections();
				        		for(var int = 0; int < arrRegistro.length; int++)
								{
									var cuenta = arrRegistro[int];
									if(validarExistenciaRegistroGrid(cuenta,gridPlanIngresoIns,'sig_cuenta','spi_cuenta',true))
									{
										var cuentaInt = new registrocuentaspi({
											'spi_cuenta':'',
											'denominacion':'',
											'sc_cuenta':'',
                                                                                        'cueclaeco':'',
											'editable' : '1'
										});
										gridPlanIngresoIns.startEditing(0, 0);
										gridPlanIngresoIns.store.insert(0,cuentaInt);
										cuentaInt.set('spi_cuenta',cuenta.get('sig_cuenta'));
										cuentaInt.set('denominacion',cuenta.get('denominacion'));
										if(cuenta.get('sc_cuenta')!='')
										{
											cuentaInt.set('sc_cuenta',cuenta.get('sc_cuenta'));
										}
										if(cuenta.get('cueclaeco')!='')
										{
											cuentaInt.set('cueclaeco',cuenta.get('cueclaeco'));
										}
									}
								}
				        		venCatRecursoEgreso.destroy();
							}
				       },
				       {
				      	text: 'Salir',
				        handler: function()
						{
				        	venCatRecursoEgreso.destroy();
				       	}
	                  }]
	    });
	      
	    venCatRecursoEgreso.show();
	}
	/******FIN CATALOGO DE RECURSOS*********/
	
	
	/******DATASTORE Y CARGA DE DATOS*********/
	var registrocuentaspi = Ext.data.Record.create([
        {name: 'spi_cuenta'},     
	    {name: 'denominacion'},
	    {name: 'sc_cuenta'},
            {name: 'cueclaeco'},
            {name: 'editable'}
	]);
	
	dsplaningreso = new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},registrocuentaspi)
	});
    /******DATASTORE Y CARGA DE DATOS*********/
    
    /******FORMULARIO Y FUNCION PARA FILTRAR DATOS GRID PLAN INGRESO*********/
	var formBusquedaPlan = new Ext.FormPanel({
        labelWidth: 150,
        frame:true,
        title: 'B&uacute;squeda de Cuenta Presupuestaria',
        bodyStyle:'padding:5px 5px 0',
        width: 850,
		height:100,
        defaultType: 'textfield',
		items: [{
			fieldLabel: 'Cuenta Presupuestaria',
            id:'codcuentaspi',
            labelSeparator : '',
			width:250,
			autoCreate: {tag: 'input', type: 'text', maxlength: 25},
			changeCheck: function()
			{
				var v = this.getValue();
				dsplaningreso.filter('spi_cuenta',v);
			},							 
			initEvents : function()
			{
				AgregarKeyPress(this);
			}               
      	},{
      		fieldLabel: 'Denominaci&#243;n',
		    id:'dencuentaspi',
		    labelSeparator : '',
		    width:400,
			changeCheck: function()
			{
				var v = this.getValue();
				dsplaningreso.filter('denominacion',v,true,false);
			},							 
			initEvents : function()
			{
				AgregarKeyPress(this);
			}
		}]
	});
	/******FORMULARIO Y FUNCION PARA FILTRAR DATOS GRID PLAN INGRESO*********/

    /******CREANDO LA GRID DE PLAN DE INGREO*********/
    function creargrid()
	{
		var Ypos = 10;
		var alto = 440;
		if (estpreing=='1')
		{
			Ypos = '180';
			alto = 280;
			if(parseInt(empresa['numniv'])==5)
			{
				Ypos = '250';
			}
		}
		var Xpos = ((screen.width/2)-(740/2));
		gridPlanIngresoIns = new Ext.grid.EditorGridPanel({
			width:850,
			height:alto,
			frame:true,
			autoScroll:true,	
			style:'position:absolute;left:0px;top:'+Ypos+'px;',
			viewConfig: {forceFit:true},
			title:'Cuentas de Ingreso',
			id:'gridPlanCuentaInstituto',
			ds: dsplaningreso,
			cm: new Ext.grid.ColumnModel([new Ext.grid.CheckboxSelectionModel({}),
				{header: "Cuenta presupuestaria", width: 20, sortable: true, dataIndex: 'spi_cuenta',
							editor: new Ext.form.TextField({
								allowBlank: false,
								autoCreate: {tag: 'input', setEditable: false,type: 'text',  maxLength: longitud, autocomplete: 'off', onkeypress: "return keyRestrict(event,'0123456789');"}
							})},
				{header: "Denominaci&#243;n", width: 60, setEditable: true, sortable: true, dataIndex: 'denominacion',
							editor: new Ext.form.TextField({
								allowBlank: false,
								autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', onkeypress: "return keyRestrict(event,'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ·ÈÌÛ˙¡…Õ”⁄ ');"}
							})},
				{header: "Cta Cont. Institucional", width: 20, setEditable: true, sortable: true, dataIndex: 'sc_cuenta'},
				{header: "Cta Clasif. Economico", width: 20, setEditable: true, sortable: true, dataIndex: 'cueclaeco'}
			]),
			sm: new Ext.grid.CheckboxSelectionModel({}), 
			viewConfig: {forceFit:true},
			columnLines: true,
			tbar:[{
					text:'Agregar cuenta presupuestaria',
					tooltip:'Agregar cuenta presupuestaria',
					iconCls:'agregar',
					id:'agregarpre',
					handler: catRecuros
				  },'-', 
				  {
					text:'Agregar Cta Cont. Institucional',
					tooltip:'Agregar Cta Cont. Institucional',
					iconCls:'agregar',
					id:'agregarcon',
					handler: function()
					{
						var arrcuenta = gridPlanIngresoIns.getSelectionModel().getSelections();
						if(arrcuenta.length > 0)
						{
							mostrarCatalogoCuentaContableCasamientoSPI('catalogocuentamovimientoSPI',arrcuenta);
						}
						else
						{
							Ext.Msg.show({
								title:'Mensaje',
								msg: 'Debe seleccionar una cuenta presupuestaria',
								buttons: Ext.Msg.OK,
								icon: Ext.MessageBox.INFO
							});
						}
					}		
                                },'-', 
                                          {
                                                text:'Agregar Cta Clasif. Economico',
                                                tooltip:'Agregar Cta Clasif. Economico',
                                                iconCls:'agregar',
                                                id:'agregarcon',
                                                handler: function()
                                                {
                                                        var arrcuenta = gridPlanIngresoIns.getSelectionModel().getSelections();
                                                        if(arrcuenta.length > 0)
                                                        {
                                                                mostrarCatalogoCuentaContableClasificadorSPI('catalogoclasificadoreconomicoSPI',arrcuenta);
                                                        }
                                                        else
                                                        {
                                                                Ext.Msg.show({
                                                                        title:'Mensaje',
                                                                        msg: 'Debe seleccionar una cuenta presupuestaria',
                                                                        buttons: Ext.Msg.OK,
                                                                        icon: Ext.MessageBox.INFO
                                                                });
                                                        }
                                                }		
                                }, '-', {
                                        text:'Eliminar fila',
                                        tooltip:'Quita registro de la grid, la eliminaci&#243;n se efectua al guardar',
                                        iconCls:'remover',
                                        id:'remover',
                                        handler: eliminar
			}]
		});
	
		gridPlanIngresoIns.on({
						'celldblclick':
						{
							fn: function(Grid, numFila, numColumna, e)
							{
								if(numColumna=='1')
								{
									var v1 = Grid.getSelectionModel().getSelected().get('editable');
									if (v1 != 1)
									{
										Ext.Msg.show({
											title:'Mensaje',
											msg: 'El CÛdigo Presupuestario no puede ser editado',
											icon: Ext.MessageBox.INFO
										}); 
										Grid.startEditing(numFila,2);
									}
								}
							}
						}
					})
	}
	/******CREANDO LA GRID DE PLAN DE INGRESO*********/
	
	/******CREANDO FUNCION PARA ELIMINAR REGISTRO DE LA GRID Y DATASTORE PARA CONTENERLOS HASTA PROCESAR EL GUARDAR*******/
	dscuentaelimi = new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz', id: "id"}, registrocuentaspi)
	});
	
	function eliminar()
	{
		var arreglocuentas = gridPlanIngresoIns.getSelectionModel().getSelections();
		if (arreglocuentas.length >0)
		{
			for (var i = arreglocuentas.length - 1; i >= 0; i--)
			{
				gridPlanIngresoIns.getStore().remove(arreglocuentas[i]);
				if(!arreglocuentas[i].isModified('sig_cuenta'))
				{
					dscuentaelimi.add(arreglocuentas[i]);
				}
			}
		}
	}


	function getFormularioPlan()
	{
		Ext.QuickTips.init();
		var Xpos = ((screen.width/2)-(900/2));
		var Ypos = 40;
		if (estpreing==1)
		{
			formPlanctapre = new Ext.FormPanel({
				width: 900,
				height: 600,
				applyTo: 'formulario_plan_ingreso_institucional',
				title: 'Definici&#243n Plan de Cuentas de Ingreso',
				frame:true,
				bbar : formBusquedaPlan,
				labelWidth:200,
				style:'position:absolute;margin-left:'+Xpos+'px;margin-top:'+Ypos+'px;',
				items:[fieldSetEstructura.fieldSetEstPre,
					   gridPlanIngresoIns,
					   {
						xtype: 'hidden',
						name: 'estcla',
						id: 'estcla'
				}]
							
			});
		}
		else
		{
			formPlanctapre = new Ext.FormPanel({
				width: 900,
				height: 600,
				applyTo: 'formulario_plan_ingreso_institucional',
				title: 'Definici&#243n Plan de Cuentas de Ingreso',
				frame:true,
				bbar : formBusquedaPlan,
				labelWidth:200,
				style:'position:absolute;margin-left:'+Xpos+'px;margin-top:'+Ypos+'px;',
				items:[gridPlanIngresoIns,
					   {
						xtype: 'hidden',
						name: 'estcla',
						id: 'estcla'
				}]
							
			});
			
		}
	}
	creargrid();
	getFormularioPlan();
	irNuevo();
	/***FIN CREANDO FUNCION PARA ELIMINAR REGISTRO DE LA GRID Y DATASTORE PARA CONTENERLOS HASTA PROCESAR EL GUARDAR*******/
	
});


function recargarDatos()
{
	if (estpreing==1)
	{
		if (validarEstructura())
		{
			var arrest = fieldSetEstructura.obtenerArrayEstructura();
			var myJSONObject = "{'oper':'catalogo','numniv':'"+empresa['numniv']+"','cantnivel':'" + empresa['numniv'] + "','datosestructura':[{";
			for (var i = 0;i<arrest.length;i++)
			{
				if(i!=5)
				{
					myJSONObject= myJSONObject + "'codest"+i+"':'" + arrest[i]+ "',";
				}
				else
				{
					myJSONObject= myJSONObject + "'estcla':'"+arrest[i]+"'}]}";
				}
			}
		}
		else
		{
			Ext.MessageBox.alert('Mensaje', 'Debe indicar la estructura presupuestaria');
			return false;
		}
	}
	else
	{
		var myJSONObject ="{'oper':'catalogo'}";		
	}
	var	parametros ='ObjSon='+myJSONObject;
    Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_spi_planinstitucional.php',
		params : parametros,
		method: 'POST',
		success: function ( resultad, request )
		{ 
			var datos = resultad.responseText;
			var objetodata = eval('(' + datos + ')');
			if(objetodata != '')
			{
				if(objetodata.raiz == null)
				{
					Ext.MessageBox.alert('Informaci&#243;n','No se encontraron datos')
				}
				else
				{
					gridPlanIngresoIns.getStore().loadData(objetodata);
				}
			}
	    },
	    failure: function ( result, request)
		{ 
	    			Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo'); 
	    } 
	});
}

function irGuardar()
{
	if (estpreing==1)
	{
		if (validarEstructura())
		{
			var arrest = fieldSetEstructura.obtenerArrayEstructura();
			var reg = "{'oper':'incluirvarios','numniv':'"+empresa['numniv']+"','cantnivel':'" + empresa['numniv'] + "','datosestructura':[{";
			for (var i = 0;i<arrest.length;i++)
			{
				if(i!=5)
				{
					reg= reg + "'codest"+i+"':'" + arrest[i]+ "',";
				}
				else
				{
					reg= reg + "'estcla':'"+arrest[i]+"'}],'datoscuenta':[";
				}
			}
		}
		else
		{
			Ext.MessageBox.alert('Mensaje', 'Debe indicar la estructura presupuestaria');
			return false;
		}
	}
	else
	{
		var reg = "{'oper':'incluirvarios','datoscuenta':[";		
	}

	if(dsplaningreso.getCount()>0)
	{
		totalcuentas = dsplaningreso.getCount() - 1;
		for(var i=0;i<=totalcuentas;i++)
		{
			if(i==0)
			{
					reg = reg + "{'spi_cuenta':'" + dsplaningreso.getAt(i).get('spi_cuenta') +"','sc_cuenta':'" + dsplaningreso.getAt(i).get('sc_cuenta')+"','cueclaeco':'" + dsplaningreso.getAt(i).get('cueclaeco')+"','denominacion':'" + dsplaningreso.getAt(i).get('denominacion')+"'}";
			}	
			else
			{
					reg = reg + ",{'spi_cuenta':'" + dsplaningreso.getAt(i).get('spi_cuenta') +"','sc_cuenta':'" + dsplaningreso.getAt(i).get('sc_cuenta')+"','cueclaeco':'" + dsplaningreso.getAt(i).get('cueclaeco')+"','denominacion':'" + dsplaningreso.getAt(i).get('denominacion')+"'}";
			}		
		}
    }
	reg = reg + "]";
	var ncuentaeli = dscuentaelimi.getCount();
	reg = reg + ",'datoscuentaseliminar':[";
	for(var i=0;i<=ncuentaeli-1;i++)
	{
		if(i==0)
		{
			reg = reg + "{'spi_cuenta':'" + dscuentaelimi.getAt(i).get('spi_cuenta') +"','sc_cuenta':'" + dscuentaelimi.getAt(i).get('sc_cuenta')+"','denominacion':'" + dscuentaelimi.getAt(i).get('denominacion')+"'}";
		}	
		else
		{
			reg = reg + ",{'spi_cuenta':'" + dscuentaelimi.getAt(i).get('spi_cuenta') +"','sc_cuenta':'" + dscuentaelimi.getAt(i).get('sc_cuenta')+"','denominacion':'" + dscuentaelimi.getAt(i).get('denominacion')+"'}";
		}		
	}
	reg = reg + "]}";
	
	
	var Obj    = eval('(' + reg + ')');
	var ObjSon = JSON.stringify(Obj);
	var parametros = 'ObjSon='+ObjSon; 
	obtenerMensaje('procesar','','Guardando Datos');	
	Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_spi_planinstitucional.php',
		params : parametros,
		method: 'POST',
		success: function ( resultad, request )
		{ 
			Ext.Msg.hide();
			var datos = resultad.responseText;
	       	var resultado = datos.split("|");
			if(resultado.length>3)
			{
				mensajeinterno="<br><br> Resumen de Errores : <br>";
				for (var i = 0; i <= resultado.length-1; i++)
				{
					if( i < resultado.length-4)
					{
						mensajeinterno= mensajeinterno + resultado[i];
					}
					else
					{
						if( i == resultado.length-4)
						{
							canterror=resultado[i];	
						}
						else if( i == resultado.length-3)
						{
							cantguardado=resultado[i];
						}
						else if( i == resultado.length-2)
						{
							canteliminada=resultado[i];	
						}
						else if( i == resultado.length-1)
						{
							mensajeinterno=resultado[i];	
						}
					}
				};
			}
			else
			{
				canterror=resultado[0];
				cantguardado=resultado[1];
				canteliminada=resultado[2];
				mensajeinterno="<br><br> Resumen de Errores : <br><br>No se generaron errores<br>";
			}
			gridPlanIngresoIns.getStore().commitChanges();
			gridPlanIngresoIns.getSelectionModel().clearSelections();
			irNuevo();
			Ext.Msg.show({
				title:'Mensaje',
				msg: cantguardado+'Cuenta(s) guardada(s) ,'+canteliminada+'Cuenta(s) eliminada(s) ,'+canterror+'Cuenta(s) con error, '+mensajeinterno,
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.INFO
			});
	  },
      failure: function ( result, request)
	  { 
		Ext.Msg.hide();
		Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo'); 
      } 
	});
}

function irNuevo()
{
	gridPlanIngresoIns.store.removeAll();
	dscuentaelimi.removeAll();	
	recargarDatos();	
}

function validarEstructura()
{
	var arrest = fieldSetEstructura.obtenerArrayEstructura();
	for (var i = 0; i < arrest.length; i++)
	{
		if (arrest[i]=='')
		{
			return false;
		}
	}
	return true;
}
