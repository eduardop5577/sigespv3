/***********************************************************************************
* @Archivo JavaScript que incluye tanto los componentes como los eventos asociados 
* al plan de cuentas presupuestario de la institucion  
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

var registrocuenta = null;
var datastorecuenta = null;
var datastorecuentaeliminada = null;
var gridPlanCuentaInstituto = null;
var formPlanctapre='';
var fieldSetEstructura = null; 
barraherramienta    = true;

var formpre =empresa["formpre"];
formpre=replaceAll(formpre,'-','');
formpre=replaceAll(formpre,' ','');
var longitud=formpre.length;

var ruta = '../../controlador/cfg/sigesp_ctr_cfg_spg_planinstitucional.php';

var objetocuenta={"raiz":[{"sig_cuenta":'',"denominacion":'',"sc_cuenta":'',"cueclaeco":''}]};	
	
var registrocuenta = Ext.data.Record.create([
			{name: 'sig_cuenta'},     
			{name: 'denominacion'},
			{name: 'sc_cuenta'},
			{name: 'cueclaeco'},
			{name: 'editable'}
		]);

Ext.onReady(
	function()
	{
		Ext.Ajax.timeout=36000000000;
	
	fieldSetEstructura = new com.sigesp.vista.comFieldSetEstructuraPresupuesto({
		titform: 'Estructura Presupuestaria',
		mostrarDenominacion:true,
		idtxt:'1',
		onAceptar:true,
		fnOnAceptar:limpiarGrid
	});
	
	datastorecuentaeliminada = new Ext.data.Store({
		proxy: new Ext.data.MemoryProxy(objetocuenta),		
		reader: new Ext.data.JsonReader({
					root: 'raiz',                
					id: "id"   
          		}
		,
        registrocuenta
		)});	
	 
	datastorecuenta = new Ext.data.Store({
		proxy: new Ext.data.MemoryProxy(objetocuenta),		
		reader: new Ext.data.JsonReader({
					root: 'raiz',                
					id: "id"   
          		}
		,
        registrocuenta
		)});

    
    /******FORMULARIO Y FUNCION PARA FILTRAR DATOS GRID PLAN GASTO*********/
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
            id:'codcuentaspg',
            labelSeparator : '',
			width:250,
			autoCreate: {tag: 'input', type: 'text', maxlength: 25},
			changeCheck: function()
			{
				var v = this.getValue();
				datastorecuenta.filter('sig_cuenta',v);
			},							 
			initEvents : function()
			{
				AgregarKeyPress(this);
			}               
      	},{
      		fieldLabel: 'Denominaci&#243;n',
		    id:'dencuentaspg',
		    labelSeparator : '',
		    width:400,
			changeCheck: function()
			{
				var v = this.getValue();
				datastorecuenta.filter('denominacion',v,true,false);
			},							 
			initEvents : function()
			{
				AgregarKeyPress(this);
			}
		}]
	});
	/******FORMULARIO Y FUNCION PARA FILTRAR DATOS GRID PLAN GASTO*********/


	var xTop = '180';
	if(parseInt(empresa['numniv'])==5)
	{
		xTop = '250';
	}
	var sm2 = new Ext.grid.CheckboxSelectionModel({});
    function creargrid()
	{
    	gridPlanCuentaInstituto = new Ext.grid.EditorGridPanel({
		width:850,
        height:200,
		frame:true,
		autoScroll:true,	
		title:'Cuentas de Gasto',
        style:'position:absolute;left:0px;top:'+xTop+'px;',
		viewConfig: {forceFit:true},
        id:'gridPlanCuentaInstituto',
       	ds: datastorecuenta,
       	cm: new Ext.grid.ColumnModel([
            sm2,
            {id:'sig_cuenta',header: "Cuenta presupuestaria", width: 20, sortable: true, dataIndex: 'sig_cuenta',
                        editor: new Ext.form.TextField({
                            allowBlank: false,
                            autoCreate: {tag: 'input', type: 'text', setEditable: false, maxLength: longitud, autocomplete: 'off', onkeypress: "return keyRestrict(event,'0123456789');"}
                        })},
            {id:'denominacion', header: "Denominaci&#243;n", width: 55, setEditable: true,sortable: true, dataIndex: 'denominacion', 
             editor: new Ext.form.TextField({allowBlank: false,autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '254', onkeypress: "return keyRestrict(event,'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!=¡;:[]{}áéíóúÁÉÍÓÚ ');"}})},
            {header: "Cta. Cont. Institucional", width: 25, setEditable: true, sortable: true, dataIndex: 'sc_cuenta'},
            {header: "Cta. Clasific. Economico", width: 25, setEditable: true, sortable: true, dataIndex: 'cueclaeco'}
        ]),
       	sm: new Ext.grid.CheckboxSelectionModel({}),
		viewConfig: {forceFit:true},
        columnLines: true,
        tbar:[{
            text:'Agregar cuenta de gasto',
            tooltip:'Agregar cuenta presupuestaria',
            iconCls:'agregar',
           	handler: function(){
				if(validarEstructura())
				{
					catalogoplanunicoregasto(gridPlanCuentaInstituto);
				}
				else
				{
					Ext.MessageBox.alert('Mensaje', 'Debe indicar la estructura presupuestaria');
				}
				
			}
        }, '-', 
		{
            text:'Agregar Cta. Cont. Institucional',
            tooltip:'Agregar cuenta contable',
            iconCls:'agregar',
            id:'agregar',
			handler: function(){
						arrcuentascg = gridPlanCuentaInstituto.getSelectionModel().getSelections();
						if(arrcuentascg.length>0)
						{
							      mostrarCatalogoCuentaContableCasamientoSPG('catalogocuentamovimientoSPG',arrcuentascg);
						}
						else
						{
							Ext.Msg.show({
										   title:'Mensaje',
										   msg: 'Debe seleccionar al menos una cuenta presupuestaria',
										   buttons: Ext.Msg.OK,
										   icon: Ext.MessageBox.INFO
										});
						}
					} 		
        }, '-', 
		{
            text:'Agregar Cta. Clasific. Economico',
            tooltip:'Agregar Cta. Clasific. Economico',
            iconCls:'agregar',
            id:'agregar',
			handler: function(){
						arrcuentascg = gridPlanCuentaInstituto.getSelectionModel().getSelections();
						if(arrcuentascg.length>0)
						{
							      mostrarCatalogoCuentaContableClasificadorSPG('catalogoclasificadoreconomicoSPG',arrcuentascg);
						}
						else
						{
							Ext.Msg.show({
										   title:'Mensaje',
										   msg: 'Debe seleccionar al menos una cuenta presupuestaria',
										   buttons: Ext.Msg.OK,
										   icon: Ext.MessageBox.INFO
										});
						}
					} 		
        }, '-', {
            text:'Eliminar fila',
            tooltip:'Eliminar',
            iconCls:'remover',
            id:'remover',
			handler: eliminar
           }]
        });

    gridPlanCuentaInstituto.on({
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
										msg: 'El Código Presupuestario no puede ser editado',
										icon: Ext.MessageBox.INFO
									});
									Grid.startEditing(numFila,2);
								}
                            }
                    	}
                    }
                })

	}
	
	//funcion para eliminarcuenta en grid
	function eliminar()
	{
		arreglocuentas = gridPlanCuentaInstituto.getSelectionModel().getSelections();
		if (arreglocuentas.length >0)
		{
			for (var i = arreglocuentas.length - 1; i >= 0; i--)
			{
				gridPlanCuentaInstituto.getStore().remove(arreglocuentas[i]);
				if(!arreglocuentas[i].isModified('sig_cuenta'))
				{
					datastorecuentaeliminada.add(arreglocuentas[i]);
				}
			};
		}
	}
	
	function getFormularioPlan()
	{
		Ext.QuickTips.init();
		var Xpos = ((screen.width/2)-(900/2));
		var Ypos = 40;
		formPlanctapre = new Ext.FormPanel({
			width: 900,
			height: 600,
			applyTo: 'formulario_planctapresupuestario',
			title: 'Definici&#243n Plan de Cuentas Presupuestario',
			frame:true,
		    bbar : formBusquedaPlan,
			labelWidth:200,
			style:'position:absolute;margin-left:'+Xpos+'px;margin-top:'+Ypos+'px;',
			items:[fieldSetEstructura.fieldSetEstPre,
			       gridPlanCuentaInstituto,
			       {
					xtype: 'hidden',
					name: 'estcla',
					id: 'estcla'
			}]
						
		});
	}
	creargrid();
	getFormularioPlan();
});

 
function irGuardar()
{
	var cadenajson = "{'operacion':'incluir','numniv':'"+empresa['numniv']+"','datosestructura':[{";
	//OJO GET ESTRUCTURA DEL COMPONENTE
	if(validarEstructura())
	{
		var arrest = fieldSetEstructura.obtenerArrayEstructura();
		for (var i = 0;i<arrest.length;i++)
		{
			if(i!=5)
			{
				cadenajson= cadenajson + "'codest"+i+"':'" + arrest[i]+ "',";
			}
			else
			{
				cadenajson= cadenajson + "'estcla':'"+arrest[i]+"'}]";
			}
		}
	}
	else
	{
		Ext.MessageBox.alert('Mensaje', 'Debe indicar la estructura presupuestaria');
		return false;
	}
	if(datastorecuenta.getCount()>0)
	{
		totalcuentas = datastorecuenta.getCount() - 1;
                cadenajson = cadenajson + ",'datoscuentas':[";
		for (var i = 0; i <= totalcuentas ; i++)
		{
			if (datastorecuenta.getAt(i).get('sc_cuenta') == '')
			{
				Ext.MessageBox.alert('Mensaje', 'No asocio una cuenta contable a este registro');
				return false;
                        }
			else
			{
				if (i == 0)
				{
					cadenajson = cadenajson + "{'sig_cuenta':'" + trim(datastorecuenta.getAt(i).get('sig_cuenta')) + "','dencuenta':'" + datastorecuenta.getAt(i).get('denominacion') + "','sc_cuenta':'" + datastorecuenta.getAt(i).get('sc_cuenta') + "','cueclaeco':'" + datastorecuenta.getAt(i).get('cueclaeco') + "'}";
				}
				else
				{
					cadenajson = cadenajson + ",{'sig_cuenta':'" + trim(datastorecuenta.getAt(i).get('sig_cuenta')) +"','dencuenta':'" + datastorecuenta.getAt(i).get('denominacion') +"','sc_cuenta':'" + datastorecuenta.getAt(i).get('sc_cuenta')+"','cueclaeco':'" + datastorecuenta.getAt(i).get('cueclaeco') + "'}";
				}
			}
		}
		cadenajson = cadenajson + "]";
	}
	else
	{
		if(datastorecuentaeliminada.getCount()==0)
		{
			Ext.MessageBox.alert('Mensaje', 'Debe cargar al menos una cuenta al plan');
			return false;
		}
	}
	if(datastorecuentaeliminada.getCount()>0)
	{
		cadenajson = cadenajson + ",'datoscuentaseliminar':[";
                totalcuentas = datastorecuentaeliminada.getCount() - 1;
		for (var i = 0; i <= totalcuentas; i++)
		{
			if (datastorecuentaeliminada.getAt(i).get('sc_cuenta') == '')
			{
				Ext.MessageBox.alert('Mensaje', 'No asocio una cuenta contable a este registro');
				return false;
                        }
			else
			{
				if (i == 0)
				{
					cadenajson = cadenajson + "{'sig_cuenta':'" + datastorecuentaeliminada.getAt(i).get('sig_cuenta') + "','dencuenta':'" + datastorecuentaeliminada.getAt(i).get('denominacion') + "','sc_cuenta':'" + datastorecuentaeliminada.getAt(i).get('sc_cuenta') + "'}";
				}
				else
				{
					cadenajson = cadenajson + ",{'sig_cuenta':'" + datastorecuentaeliminada.getAt(i).get('sig_cuenta') +"','dencuenta':'" + datastorecuentaeliminada.getAt(i).get('denominacion') +"','sc_cuenta':'" + datastorecuentaeliminada.getAt(i).get('sc_cuenta')+"'}";
				}
			}
		}
		cadenajson = cadenajson + "]";
	}
	cadenajson = cadenajson + "}";
	Obj = eval('(' + cadenajson + ')');
    ObjSon = JSON.stringify(Obj);
    parametros = 'ObjSon=' + ObjSon;
	obtenerMensaje('procesar','','Guardando Datos');	
    Ext.Ajax.request({
    	url : ruta,
        params : parametros,
        method: 'POST',
        success: function ( resultad, request)
		{
			Ext.Msg.hide();
	    	var datos = resultad.responseText;
	       	var resultado = datos.split("|");
			canterror=0;
			cantguardado=0;
			canteliminada=0;
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
			
			Ext.Msg.show({
				title:'Mensaje',
				msg: cantguardado+' Cuenta(s) guardada(s), '+canteliminada+' Cuenta(s) eliminada(s), '+canterror+'Cuenta(s) con error, '+mensajeinterno,
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.INFO
			});
			limpiarGrid();
		},
		failure: function ( result, request)
		{ 
			Ext.Msg.hide();
			Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo'); 
		} 
    });
}

function irBuscar()
{
	if (validarEstructura())
	{
		var arrest = fieldSetEstructura.obtenerArrayEstructura();
		var cadenajson = "{'operacion':'buscarcuenta','numniv':'"+empresa['numniv']+"','cantnivel':'" + empresa['numniv'] + "','datosestructura':[{";
		var nivel = 0;
		for (var i = 0;i<arrest.length;i++)
		{
			if(i!=5)
			{
				cadenajson= cadenajson + "'codest"+i+"':'" + arrest[i]+ "',";
				if (arrest[i]!='0000000000000000000000000')
				{
					nivel = i;
				}
			}
			else
			{
				cadenajson= cadenajson + "'estcla':'"+arrest[i]+"'}]}";
			}
		}
	}
	else
	{
		Ext.MessageBox.alert('Mensaje', 'Debe indicar la estructura presupuestaria');
		return false;
	}
		parametros = 'ObjSon='+cadenajson;
		Ext.Ajax.request({
			url : '../../controlador/cfg/sigesp_ctr_cfg_spg_planinstitucional.php',
			params : parametros,
			method: 'POST',
			success: function ( resultad, request )
			{ 
				var datos = resultad.responseText;
				if (datos != '')
				{
					var DatosNuevo = eval('(' + datos + ')');
					if (DatosNuevo.raiz == null || DatosNuevo.raiz=='')
					{
						if(nivel==(empresa['numniv']-1))
						{
							Ext.Msg.show({
								title:'Mensaje',
								msg: 'No existen cuentas para la estructura seleccionada',
								buttons: Ext.Msg.OK,
								icon: Ext.MessageBox.INFO
							});
						}
					}
					else
					{
						gridPlanCuentaInstituto.store.loadData(DatosNuevo);
					}
				}
			},
			failure: function ( result, request)
			{ 
				Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo'); 
			} 
		});
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

function irCancelar()
{
	fieldSetEstructura.limpiarEstructuras(-1);
	gridPlanCuentaInstituto.getStore().removeAll();	
}

function irNuevo()
{
	fieldSetEstructura.limpiarEstructuras(-1);
	gridPlanCuentaInstituto.getStore().removeAll();	
}

function limpiarGrid()
{
	gridPlanCuentaInstituto.store.removeAll();
	datastorecuentaeliminada.removeAll();
	irBuscar();
} 