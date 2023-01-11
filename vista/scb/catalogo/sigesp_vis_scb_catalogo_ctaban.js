/**************************************************************************
*@archivo javascript para el catálogo de CuentaBanco
*@version: 1.0
*@fecha de creación: 02/12/2011.
*@autor: Ing. Yesenia Moreno.
*****************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
******************************************************************************/
var datos              = null;
var gridCuentaBanco         = null;
var ventanaCuentaBanco      = null;
var iniciargrid        = false;
var parametros         = '';
var rutaCuentaBanco         = '../../controlador/scb/sigesp_ctr_scb_banco.php';
var rutaPermisos = '../../controlador/sss/sigesp_ctr_sss_usuariospermisos.php';


/******************************************************************************
* @Función genérica para el uso del catálogo de CuentaBanco
* @parametros: 
* @retorno: 
* @fecha de creación: 02/12/2011.
* @autor: Ing. Yesenia Moreno.
*****************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
*******************************************************************************/
	function catalogoCuentaBanco()
	{	
		this.mostrarCatalogoCuentaBanco = mostrarCatalogoCuentaBanco;
	}


/*************************************************************************
* @Función que acualiza el catalgo para buscar por determinado campo
* @parametros: criterio: campo por el que se actualiza
* 				cadena: campo a actualizar
* @retorno:
* @fecha de creación: 02/12/2011.
* @autor: Ing. Yesenia Moreno.
**************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
**************************************************************************/
	function actualizarDataCuentaBanco(criterio,cadena)
	{
		var myJSONObject ={
			'oper': 'obtenerCuentaBanco',
			'cadena': cadena,
			'criterio': criterio,
			'sistema': sistema,
			'vista': vista
		};
		objdata=JSON.stringify(myJSONObject);
		parametros = 'objdata='+objdata; 
		Ext.Ajax.request({
		url : rutaCuentaBanco,
		params : parametros,
		method: 'POST',
		success: function ( resultado, request )
		{ 
			datos = resultado.responseText;
			if (datos!='')
			{
				var DatosNuevo = eval('(' + datos + ')');
				gridCuentaBanco.store.loadData(DatosNuevo);
			}
		}
		});
	}
	
	
/***********************************************************************
* Obtener el valor de los caracteres de la caja texto
* @parámetros: obj --> caja de texto.
* @retorna: valor obtenido del objeto.
* @fecha de creación: 02/12/2011.
* @autor: Ing. Yesenia Moreno.
**************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
************************************************************************/		
	function agregarKeyPress(Obj)
	{
		Ext.form.TextField.superclass.initEvents.call(Obj);
		if(Obj.validationEvent == 'keyup')
		{
			Obj.validationTask = new Ext.util.DelayedTask(Obj.validate, Obj);
			Obj.el.on('keyup', Obj.filterValidation, Obj);
		}
		else if(Obj.validationEvent !== false)
		{
			Obj.el.on(Obj.validationEvent, Obj.validate, Obj, {buffer: Obj.validationDelay});
		}
		if(Obj.selectOnFocus || Obj.emptyText)
		{
			Obj.on('focus', Obj.preFocus, Obj);
			if(Obj.emptyText)
			{
				Obj.on('blur', Obj.postBlur, Obj);
				Obj.applyEmptyText();
			}
		}
		if(Obj.maskRe || (Obj.vtype && Obj.disableKeyFilter !== true && (Obj.maskRe = Ext.form.VTypes[Obj.vtype+'Mask']))){
			Obj.el.on('keypress', Obj.filterKeys, Obj);
		}
		if(Obj.grow)
		{
			Obj.el.on('keyup', Obj.onKeyUp,  Obj, {buffer:50});
			Obj.el.on('click', Obj.autoSize,  Obj);
		}
		Obj.el.on('keyup', Obj.changeCheck, Obj);
	}
		
	
/*****************************************************************************
* @Función para validar que el registro seleccionado de
* @la grid del catalogo no exista en la grid del formulario
* @parametros:
* @retorno: true si el registro ya está.
* @fecha de creación: 02/12/2011.
* @autor: Ing. Yesenia Moreno.
*****************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
*****************************************************************************/
	function validarExistenciaCatBan()
	{
		CuentaBancoCat    = gridCuentaBanco.getSelectionModel().getSelections();
		cantCuentaBanco   = gridCtaBan.store.getCount()-1;
		arrAuxCuentaBanco = gridCtaBan.store.getRange(0,cantCuentaBanco);
		existe=false;
		totalgrid=(CuentaBancoCat.length-1);
		totalcatalogo=(arrAuxCuentaBanco.length-1);
		for (i=1; i<=totalgrid; i++)
		{
	  		auxReg1 = CuentaBancoCat[i].get('codban');
	  		auxReg2 = CuentaBancoCat[i].get('ctaban');
	  		for (j=0; j<=totalcatalogo; j++)
	  		{
				if ((arrAuxCuentaBanco[j].get('codban')==auxReg1)&&(arrAuxCuentaBanco[j].get('ctaban')==auxReg2))
				{
					existe=true;
					return true;
				}
			}
		}		
		return existe;
	}


/****************************************************************************
* @Función para insertar el registro seleccionado
* @de la grid del catalgo a la grid del formulario.
* @fecha de creación: 02/12/2011.
* @autor: Ing. Yesenia Moreno.
*****************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
*****************************************************************************/
	function pasarDatosGridCtaBan(datos)
	{
		p = new RecordDefCtaBan
		({
		'codban':'',
		'ctaban':''
		});
		gridCtaBan.store.insert(0,p);
		p.set('codban',datos.get('codban'));
		p.set('ctaban',datos.get('ctaban'));
	}
	
	
/*****************************************************************************
* @Función que busca el listado de personal.
* @parámetros: 	form: id del formulario, 
* 				fieldset: id del fieldset,
* 				array: arreglo con los campos del formulario
* 				arrValores: arreglo con los campos de la base de datos.
* @fecha de creación: 02/12/2011.
* @autor: Ing. Yesenia Moreno.
*****************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
******************************************************************************/
	function mostrarCatalogoCuentaBanco(arrTxt, arrValores)
	{
		var objdata ={
			'operacion': 'obtenerCuentaBanco', 
			'sistema': sistema,
			'vista': vista
		};
		objdata=JSON.stringify(objdata);
		parametros = 'objdata='+objdata;
		Ext.Ajax.request({
		url : rutaCuentaBanco,
		params : parametros,
		method: 'POST',
		success: function ( resultado, request ) 
		{ 
			datos = resultado.responseText;
			if (datos!='')
			{
				var myObject = eval('(' + datos + ')');
				if (myObject.raiz[0].valido==true)
				{
					var RecordDef = Ext.data.Record.create([
					{name: 'codban'},     
					{name: 'nomban'},
					{name: 'ctaban'}
					]);
			      
			      	gridCuentaBanco = new Ext.grid.GridPanel({
						width:500,
						autoScroll:true,
			            border:true,
			            ds: new Ext.data.Store({
						proxy: new Ext.data.MemoryProxy(myObject),
						reader: new Ext.data.JsonReader({
						    root: 'raiz',               
						    id: 'id'   
			                },
						RecordDef
						),
						data: myObject
			            }),
			            cm: new Ext.grid.ColumnModel([
			            new Ext.grid.CheckboxSelectionModel(),
							{header: 'Banco', width: 30, sortable: true,   dataIndex: 'codban'},
			                {header: 'Nombre', width: 50, sortable: true, dataIndex: 'nomban'},
			                {header: 'Cuenta', width: 50, sortable: true, dataIndex: 'ctaban'},
						]),
						 sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
			            viewConfig: {
			                            forceFit:true
			                        },
						autoHeight:true,
						stripeRows: true
			                   });
					
					if (pantalla=='usuariosCuentaBanco')
					{
						gridCuentaBanco.getSelectionModel().singleSelect = true;	 
					}
					else 
					{
						gridCuentaBanco.getSelectionModel().singleSelect = false;	
					}
					
			                  
					var panelCuentaBanco = new Ext.FormPanel({
						labelWidth: 75, 
						frame:true,
						title: 'Búsqueda',
						bodyStyle:'padding:5px 5px 0',
						width: 350,
						height:120,
						defaults: {width: 230},
						defaultType: 'textfield',
						items: [{
							fieldLabel: 'Código',
							name: 'codban',
							id:'codban',
							width:50,
							changeCheck: function()
							{
								  var v = this.getValue();
								  actualizarDataCuentaBanco('codban',v);
								  if (String(v) !== String(this.startValue))
								  {
									  this.fireEvent('change', this, v, this.startValue);
								  } 
							},
							initEvents : function()
							{
								agregarKeyPress(this);
							}
						},{
							fieldLabel: 'Nombre',
							name: 'nomban',
							id:'nomban',
							changeCheck: function()
							{
								  var v = this.getValue();
								  actualizarDataCuentaBanco('nomban',v);
								  if (String(v) !== String(this.startValue))
								  {
									  this.fireEvent('change', this, v, this.startValue);
								  } 
							},
							initEvents : function()
							{
								agregarKeyPress(this);
							}
						}]
					});
						ventanaCuentaBanco = new Ext.Window(
						{
							title: 'Cat&aacute;logo de CuentaBanco',
					    	autoScroll:true,
			                width:500,
			                height:400,
			                modal: true,
			                closeAction:'hide',
			                plain: false,
			                items:[panelCuentaBanco,gridCuentaBanco],
			                buttons: [{
			                	text:'Aceptar',  
			                    handler: function()
								{                     	
									if (pantalla=='usuariosCuentaBanco')
									{
										for (i=0;i<arrTxt.length;i++)
										{											
											Ext.getCmp(arrTxt[i]).setValue(gridCuentaBanco.getSelectionModel().getSelected().get(arrValores[i]));
										}										
									}
									else
									{
										if (validarExistenciaCatBan()==true)
										{
											Ext.Msg.alert('Mensaje','Registro ya agregado');
										}											
										else
										{
											seleccionados = gridCuentaBanco.getSelectionModel().getSelections();
											for (i=0; i<seleccionados.length; i++)
											{
												pasarDatosGridCtaBan(seleccionados[i]);
											}
										}
									}
									panelCuentaBanco.destroy();
			                      	ventanaCuentaBanco.destroy();									
								}
								},{
			                     text: 'Salir',
			                     handler: function()
			                     {
			                      	panelCuentaBanco.destroy();
			                      	ventanaCuentaBanco.destroy();
			                     }
							}]
						});
			        
					ventanaCuentaBanco.show();
					if(!iniciargrid)
					{
						gridCuentaBanco.render('miGrid');
			            iniciargrid=false;
			        }
		        }
			    else
			    {
					Ext.MessageBox.alert('Error', myObject.raiz[0].mensaje);
					close();
			    }
			}
			else
			{
				Ext.MessageBox.alert('Mensaje', 'No hay datos para mostrar');
			}		    
        },
        failure: function ( resultado, request)
		{ 
			Ext.MessageBox.alert('Error', resultado.responseText); 
        }
	   });
	};
