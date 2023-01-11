/***********************************************************************************
* @fecha de modificacion: 04/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

var dataStoreProyecto="";
var formularioBusquedaProyecto="";
var gridProyecto="";
var ventanaProyecto="";


function creardataStoreProyecto()
{
    registroProyecto = Ext.data.Record.create([			  
                            {name:'codprosig'},
                            {name:'despro'},
                            {name:'nroptocta'},
                            {name:'fecptocta'}, 
                            {name:'monptocta'},
                            {name:'enteejecutor'},
                            {name:'rifenteejecutor'},
                            {name:'codmon'},
                            {name:'denmon'},
                            {name:'sc_cuentad'},
                            {name:'sc_cuentah'},
                            {name: 'spg_cuenta'},
                            {name: 'codestpro1'},
                            {name: 'codestpro2'},
                            {name: 'codestpro3'},
                            {name: 'codestpro4'},
                            {name: 'codestpro5'},
                            {name: 'estcla'},
                            {name: 'codestpro'},
                            {name: 'codfuefin'}                            
    ]);							
	
    var objetoProyecto={"raiz":[{"codprosig":"",
                                 "despro":"",
                                 "nroptocta":"",
                                 "fecptocta":"",
                                 "monptocta":"",
                                 "enteejecutor":"",
                                 "rifenteejecutor":"",
                                 "codmon":"",
                                 "denmon":"",
                                 "sc_cuentad":"",
                                 "sc_cuentah":"",
                                 "codestpro1":"",
                                 "codestpro2":"",
                                 "codestpro3":"",
                                 "codestpro4":"",
                                 "codestpro5":"",
                                 "spg_cuenta":"",
                                 "estcla":"",
                                 "codestpro":"",
                                 "codfuefin":""
            }]};
		
    dataStoreProyecto =  new Ext.data.Store({
            proxy: new Ext.data.MemoryProxy(objetoProyecto),
            reader: new Ext.data.JsonReader({
                root: 'raiz',             
                id: "codprosig"   
		},
		registroProyecto  
		),
		data: objetoProyecto
    })	
		
    var myJSONObject ={
        	'operacion': 'catalogo_proyecto',
                'codprosig' : Ext.getCmp('codigo').getValue(),
                'despro' : Ext.getCmp('denominacion').getValue()
    }
		
    ObjSon=Ext.util.JSON.encode(myJSONObject);
    parametros = 'ObjSon='+ObjSon;
    Ext.Ajax.request({
        url : '../../controlador/spg/sigesp_ctr_spg_sigeproden.php',
        params : parametros,
	method: 'POST',
        success: function ( result, request) 
	{ 
            datos = result.responseText;
            var objetoProyecto = eval('(' + datos + ')');
            if(objetoProyecto!='')
            {
                dataStoreProyecto.loadData(objetoProyecto);
            }
	}	
    })
}

function actdataStoreProyecto(criterio,cadena)
{
    dataStoreProyecto.filter(criterio,cadena,true,false);
}

function crearFormularioBusqueda()
{
    formularioBusquedaProyecto = new Ext.FormPanel({
        labelWidth: 75,
        frame:true,
        title: 'B&uacute;squeda',
        bodyStyle:'padding:5px 5px 0',
        width: 770,
	height:100,
        defaults: {width: 230},
        defaultType: 'textfield',
	items: [{
                    fieldLabel: 'C&#243;digo',
                    name: 'codigo',
                    id:'codigo',
                    changeCheck: function(){
                                    var v = this.getValue();
                                    actdataStoreProyecto('codprosig',v);
                                    if(String(v) !== String(this.startValue))
                                    {
                                        this.fireEvent('change', this, v, this.startValue);
                                    } 
                    },							 
                    initEvents : function(){
                                    AgregarKeyPress(this);
                    }               
      		},{
		    fieldLabel: 'Denominaci&#243;n',
		    name: 'denominacion',
		    id:'denominacion',
                    changeCheck: function(){
                                    var v = this.getValue();
                                    actdataStoreProyecto('despro',v);
                                    if(String(v) !== String(this.startValue))
                                    {
                                        this.fireEvent('change', this, v, this.startValue);
                                    } 
                    },							 
                    initEvents : function(){
                                    AgregarKeyPress(this);
                    }
                },{
                    style: 'position:absolute;left:450px;top:40px',
                    xtype: 'button',
                    fieldLabel: '',
                    id: 'btagregar',
                    text: 'Buscar',
                    iconCls: 'menubuscar',
                    handler: function(){
                         creardataStoreProyecto();
                    }
                }
            ]
    });				  
}

function creargridProyecto()
{
    crearFormularioBusqueda();
    creardataStoreProyecto();
     
    gridProyecto = new Ext.grid.GridPanel({
            width:770,
            height:350,
            tbar: formularioBusquedaProyecto,
            autoScroll:true,
            border:true,
            ds: dataStoreProyecto,
            cm: new Ext.grid.ColumnModel([
                {header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'codprosig'},
                {header: "Descripci&#243;n", width: 50, sortable: true, dataIndex: 'despro'},
                {header: "Punto de Cuenta", width: 50, sortable: true, dataIndex: 'nroptocta'},
                {header: "Ente", width: 50, sortable: true, dataIndex: 'enteejecutor'}
            ]),
            stripeRows: true,
            viewConfig: {
                forceFit:true
            }
    });            
}

function cargarDetalleProyecto(registro)
{
        var reDetGasCat = Ext.data.Record.create([
                {name: 'spg_cuenta'},
                {name: 'codestpro1'},
                {name: 'codestpro2'},
                {name: 'codestpro3'},
                {name: 'codestpro4'},
                {name: 'codestpro5'},
                {name: 'estcla'},
                {name: 'codestpro'},
                {name: 'codfuefin'}
        ]);
        var detgasIntCat = new reDetGasCat({
                'spg_cuenta':registro.get('spg_cuenta'),
                'codestpro':registro.get('codestpro'),
                'codestpro1':registro.get('codestpro1'),
                'codestpro2':registro.get('codestpro2'),
                'codestpro3':registro.get('codestpro3'),
                'codestpro4':registro.get('codestpro4'),
                'codestpro5':registro.get('codestpro5'),
                'estcla':registro.get('estcla'),
                'codfuefin':registro.get('codfuefin')
        });
        gridDetPresupuestario.store.insert(0,detgasIntCat);
}

function CatalogoProyecto(objetoProyecto)
{
    creargridProyecto();
    ventanaProyecto = new Ext.Window(
    {
        title: 'Cat&#225;logo de Proyectos',
	autoScroll:true,
        width:800,
        height:450,
        modal: true,
        closable:false,
        plain: false,
        items:[gridProyecto],
        buttons: [{
               text:'Aceptar',  
                    handler: function()
                    { 
                    	var registro = gridProyecto.getSelectionModel().getSelected();
                    	switch(objetoProyecto)   
                    	{
                    		case 'definicion':
                    			limpiarCampos();
                    			PasDatosGridDef(registro);
                                        cargarDetalleProyecto(registro);                    			
                                        Actualizar  = true;
	                    	break;
                    		case 'objeto':
	                    		PasDatosGridObjetoProyecto(registro);
	                    	break;
                    		case 'reporte':
		                    	PasDatosGridObjetoProyectoReporte(registro);
	                    	break;
                    	}          
                    	gridProyecto.destroy();
		      	ventanaProyecto.destroy();                      
                    }
                },{
                    text: 'Salir',
                    handler: function()
                    {
                      	gridProyecto.destroy();
		      	ventanaProyecto.destroy();
                    }
            }]
                    
        });
        ventanaProyecto.show();
 }
 
  function PasDatosGridObjetoProyecto(registro)
{	
    Ext.getCmp('codprosig').setValue(registro.get('codprosig'));
    Ext.getCmp('despro').setValue(registro.get('despro'));		
    Ext.getCmp('codestpro1').setValue(registro.get('codestpro1'));    
    Ext.getCmp('codestpro2').setValue(registro.get('codestpro2'));    
    Ext.getCmp('codestpro3').setValue(registro.get('codestpro3'));    
    Ext.getCmp('codestpro4').setValue(registro.get('codestpro4'));    
    Ext.getCmp('codestpro5').setValue(registro.get('codestpro5'));    
    Ext.getCmp('estcla').setValue(registro.get('estcla'));    
    Ext.getCmp('spg_cuenta').setValue(registro.get('spg_cuenta'));    
    Ext.getCmp('codfuefin').setValue(registro.get('codfuefin'));    
    Ext.getCmp('codmon').setValue(registro.get('codmon'));
    Ext.getCmp('denmon').setValue(registro.get('denmon'));
    buscarTasaCambio();
} 

  function PasDatosGridObjetoProyectoReporte(registro)
{	
    Ext.getCmp('codprosig').setValue(registro.get('codprosig'));
    Ext.getCmp('despro').setValue(registro.get('despro'));		
    Ext.getCmp('codestpro1').setValue(registro.get('codestpro1'));    
    Ext.getCmp('codestpro2').setValue(registro.get('codestpro2'));    
    Ext.getCmp('codestpro3').setValue(registro.get('codestpro3'));    
    Ext.getCmp('codestpro4').setValue(registro.get('codestpro4'));    
    Ext.getCmp('codestpro5').setValue(registro.get('codestpro5'));    
    Ext.getCmp('estcla').setValue(registro.get('estcla'));    
    Ext.getCmp('codfuefin').setValue(registro.get('codfuefin'));   
} 
