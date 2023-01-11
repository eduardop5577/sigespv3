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

var gridOnOff = false;
var winOnOff = false;
var datos = null;
var gridMeta = '';
var win = null;
var unavez = false;
var parametros='';
var ruta = '';
var Mygrid="";
var panelMeta ='';
var ParamGridTarget='';
rutaArt ='../../controlador/cfg/sigesp_ctr_cfg_plan_unico_re.php';
function CatArticulo()
{		
	this.MostrarCatalogo =MostrarCatalogoArticulo;
	this.ActualizarData=ActualizarData;  
}

function ActualizarDataMeta(criterio,cadena)
{
	var myJSONObject ={
		"oper": 'buscarcadena',
		"cadena": cadena,
		"criterio": criterio
	};

	ObjSon=JSON.stringify(myJSONObject);
	parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
	url : rutaArt,
	params : parametros,
	method: 'POST',
	success: function ( resultado, request ){ 
	datos = resultado.responseText;
	if(datos!='')
	{
		var DatosNuevo = eval('(' + datos + ')');
		gridMeta.store.loadData(DatosNuevo);
	}	
	}
});
	
}
			
/*
function PasarDatosGrids3()
{
	p = new RecordDefVar
	({
		'cod_var':'',
		'meta':'',
		'unidad':'',
		'genero':''
	});
	
	gridIntVar.store.insert(0,p);
	//gridIntProb.startEditing(0,0);
	p.set('meta',gridMeta.getSelectionModel().getSelected().get('meta'));
	p.set('cod_var',gridMeta.getSelectionModel().getSelected().get('cod_var'));
	p.set('unidad',gridMeta.getSelectionModel().getSelected().get('unidad'));
	p.set('genero',gridMeta.getSelectionModel().getSelected().get('genero'));
	//gridIntProb.stopEditing();	
}
  */             
function MostrarCatalogoArticulo()
{
		var myObject={"raiz":[{"sig_cuenta":'',"denominacion":''}]};	
		var RecordDef = Ext.data.Record.create([
			{name: 'sig_cuenta'},     
			{name: 'denominacion'}
		]);


            gridMeta = new Ext.grid.GridPanel({
			width:600,
			autoScroll:true,
            border:true,
            ds: new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(myObject),		
			reader: new Ext.data.JsonReader({
			    root: 'raiz',                
			    id: "id"   
            }
			,
               RecordDef
			),
			data: myObject
             }),
                        cm: new Ext.grid.ColumnModel([
                        
                        	{header: "Codigo", width: 100, sortable:true,dataIndex:'sig_cuenta'},
                            {header: "Denominacion", width: 200,dataIndex:'denominacion'}
                        ]),
                       sm:new Ext.grid.CellSelectionModel({singleSelect:false}),
                        viewConfig: {
                        forceFit:true
                        },
			autoHeight:true,
			stripeRows: true
        });
        
	  		  
		panelMeta = new Ext.FormPanel({
        labelWidth: 75, // label settings here cascade unless overridden
        url:'save-form.php',
        frame:true,
        title: 'B&uacute;squeda',
        bodyStyle:'padding:5px 5px 0',
        width: 350,
		height:120,
        defaults: {width: 230},
        defaultType: 'textfield',
		items: [{
                fieldLabel: 'Cuenta',
                name: 'cuenta',
				id:'sig_cuenta',
				changeCheck: function(){
							  var v = this.getValue();
							 ActualizarDataMeta('sig_cuenta',v);
							if(String(v) !== String(this.startValue)){
								this.fireEvent('change', this, v, this.startValue);
							} 
							 }
							 ,
							initEvents : function()
							{
								AgregarKeyPress(this);
							}
               
            },{
			                fieldLabel: 'Denominacion',
			                name: 'denominacion',
							changeCheck: function(){
							  var v = this.getValue();
							 ActualizarDataMeta('denominacion',v);
							if(String(v) !== String(this.startValue))
							{
								this.fireEvent('change', this, v, this.startValue);
							} 
							}
							,
							initEvents : function()
							{
								AgregarKeyPress(this);
							}
            }]
		});
				
                 
                   win = new Ext.Window(
                   {
                    //layout:'fit',
                    title: 'Catalogo',
		    		autoScroll:true,
                    width:600,
                    height:400,
                    modal: true,
                    closeAction:'hide',
                    plain: false,
                    items:[panelMeta,gridMeta],
                    buttons: [{
                     text:'Aceptar',  
                     handler: function()
                     {
	                    PasarDatosGrids3();	    
			      		win.hide();
                      
                     }
                    },
                    {
                     text: 'Salir',
                     handler: function()
                     {
                      win.hide();
                     }
                    }]
                   });
                  	win.show();
                   
                   gridMeta.getSelectionModel().addListener('cellselect',function(sel,fila,col){
                   		
              			alert(col)
                   		
                   });
      
      
      
 
      
 };

 
 