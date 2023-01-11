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

var datos              = null;
var gridFirmas         = null;
var ventanaFirmas      = null;
var iniciargrid        = false;
var parametros         = '';
var ruta         = '../../controlador/sss/sigesp_ctr_sss_firmasdinamicas.php';

function catalogoFirmas()
{	
    this.mostrarCatalogoFirmas = mostrarCatalogoFirmas;
}

function actualizarDataFirmas(criterio,cadena)
{
		var myJSONObject ={
			'oper': 'catalogo',
			'cadena': cadena,
			'criterio': criterio,
			'sistema': sistema,
			'vista': vista
		};
		objdata=JSON.stringify(myJSONObject);
		parametros = 'objdata='+objdata; 
		Ext.Ajax.request({
		url : ruta,
		params : parametros,
		method: 'POST',
		success: function ( resultado, request )
		{ 
			datos = resultado.responseText;
			if (datos!='')
			{
				var DatosNuevo = eval('(' + datos + ')');
				gridFirmas.store.loadData(DatosNuevo);
			}
		}
		});
}
	
	
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
		

function cargarDetallesFirmas()
{
        codfir = Ext.getCmp('txtcodfir').getValue();
        var objdata ={
                        'oper': 'catalogodetalle',
                        'codfir': codfir,
                        'sistema': sistema,
                        'vista': vista					
        };
        objdata=JSON.stringify(objdata);
        parametros = 'objdata='+objdata;
        Ext.Ajax.request({
                url : ruta,
                params : parametros,
                method: 'POST',
                success: function (resultado,request)
                {
                        datos = resultado.responseText;
                        if (datos!='')
                        {
                                var myObject = eval('(' + datos + ')');
                                if(myObject.raiz[0].valido==true)
                                {
                                        gridDetalle.store.loadData(myObject);
                                }
                                else
                                {
                                        Ext.MessageBox.alert('Error', myObject.raiz[0].mensaje+' Al cargar los detalles.');
                                }
                        }
                }
        });
}	
	
	
function mostrarCatalogoFirmas(arrTxt, arrValores)
{
        var objdata ={
                'oper': 'catalogo', 
                'sistema': sistema,
                'vista': vista
        };
        objdata=JSON.stringify(objdata);
        parametros = 'objdata='+objdata;
        Ext.Ajax.request({
        url : ruta,
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
                                {name: 'codfir'},     
                                {name: 'denfir'},     
                                {name: 'tiprepfir'},
                                {name: 'tipclafir'},
                                {name: 'nrofir'}
                                ]);

                        gridFirmas = new Ext.grid.GridPanel({
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
                                                {header: 'Código', width: 30, sortable: true,   dataIndex: 'codfir'},
                                                {header: 'Denominacion', width: 30, sortable: true,   dataIndex: 'denfir'},
                                                {header: 'Reporte', width: 50, sortable: true, dataIndex: 'tiprepfir'},
                                        ]),
                            sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
                            viewConfig: {
                                            forceFit:true
                                        },
                            autoHeight:true,
                            stripeRows: true
                                   });
                            gridFirmas.getSelectionModel().singleSelect = true;	 


                                var panelFirmas = new Ext.FormPanel({
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
                                                name: 'codfir',
                                                id:'codfir',
                                                width:50,
                                                changeCheck: function()
                                                {
                                                          var v = this.getValue();
                                                          actualizarDataFirmas('codfir',v);
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
                                                fieldLabel: 'Denominación',
                                                name: 'denfir',
                                                id:'denfir',
                                                changeCheck: function()
                                                {
                                                          var v = this.getValue();
                                                          actualizarDataFirmas('denfir',v);
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
                                ventanaFirmas = new Ext.Window(
                                {
                                                title: 'Cat&aacute;logo de Firmas Dinamicas',
                                        autoScroll:true,
                                width:500,
                                height:400,
                                modal: true,
                                closeAction:'hide',
                                plain: false,
                                items:[panelFirmas,gridFirmas],
                                buttons: [{
                                        text:'Aceptar',  
                                    handler: function()
                                                        {                     	
                                                                for (i=0;i<arrTxt.length;i++)
                                                                { 
                                                                    if (arrTxt[i] == 'cmbReporte')
                                                                    {
                                                                        nuevovalor = gridFirmas.getSelectionModel().getSelected().get(arrValores[i]);
                                                                        if(nuevovalor == "")
                                                                        {
                                                                            datosClasificacion={'raiz':[{'tipclafir':'000','nomclafir':'Ninguno'}]};
                                                                        }
                                                                        if(nuevovalor == "SEP")
                                                                        {
                                                                            datosClasificacion={'raiz':[{'tipclafir':'000','nomclafir':'Ninguno'},
                                                                                                        {'tipclafir':'001','nomclafir':'Control numero'},
                                                                                                        {'tipclafir':'002','nomclafir':'Unidad ejecutora'},
                                                                                                        {'tipclafir':'003','nomclafir':'Usuario'},
                                                                                                        {'tipclafir':'004','nomclafir':'Tipo de SEP'}]};
                                                                        }
                                                                        if(nuevovalor == "SOC")
                                                                        {
                                                                            datosClasificacion={'raiz':[{'tipclafir':'000','nomclafir':'Ninguno'},
                                                                                                        {'tipclafir':'001','nomclafir':'Control numero'},
                                                                                                        {'tipclafir':'005','nomclafir':'Tipo Orden'}]};
                                                                        }
                                                                        if(nuevovalor == "CXP")
                                                                        {
                                                                            datosClasificacion={'raiz':[{'tipclafir':'000','nomclafir':'Ninguno'},
                                                                                                        {'tipclafir':'001','nomclafir':'Control numero'},
                                                                                                        {'tipclafir':'003','nomclafir':'Usuario'}]};
                                                                        }
                                                                        if(nuevovalor == "SCB")
                                                                        {
                                                                            datosClasificacion={'raiz':[{'tipclafir':'000','nomclafir':'Ninguno'},
                                                                                                        {'tipclafir':'003','nomclafir':'Usuario'}]};
                                                                        }
                                                                        dsClasificacion.loadData(datosClasificacion);
                                                                    }
                                                                    
                                                                    Ext.getCmp(arrTxt[i]).setValue(gridFirmas.getSelectionModel().getSelected().get(arrValores[i]));
                                                                }										
                                                                cargarDetallesFirmas();
                                                                panelFirmas.destroy();	
                                                                ventanaFirmas.destroy();
                                                                Actualizar=true;
                                                        }
                                                        },{
                                     text: 'Salir',
                                     handler: function()
                                     {
                                        panelFirmas.destroy();
                                        ventanaFirmas.destroy();
                                     }
                                                }]
                                        });

                                ventanaFirmas.show();
                                if(!iniciargrid)
                                {
                                        gridFirmas.render('miGrid');
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