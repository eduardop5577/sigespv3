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

var dsTipoServicio="";
var formTipoServicio="";
var gridTipoServicio="";
var ventanaTipoServicio="";

function crearDsTipoServicio()
{

	registrotiposervicio = Ext.data.Record.create([
							{name: 'codtipser'},    
							{name: 'dentipser'},
							{name: 'codmil'},
							{name: 'denmil'}
						]);
	
	var objetotiposervicio={"raiz":[{"codtipser":'',"dentipser":'',"codmil":''}]};
		
		dsTipoServicio =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objetotiposervicio),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"   
			}
			,
		    registrotiposervicio  
			),
			data: objetotiposervicio
	  	})	
		
		var JSONObject ={
			"oper": 'catalogo'
		}
		ObjSon=JSON.stringify(JSONObject);
		parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_soc_tiposervicio.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request) 
		{ 
			datos = resultado.responseText;
			var objetotiposervicio = eval('(' + datos + ')');
			if(objetotiposervicio!='')
			{
				dsTipoServicio.loadData(objetotiposervicio);
			}
		}	
	})
}


function actDataStoreTipoServicio(criterio,cadena)
{
	dsTipoServicio.filter(criterio,cadena,true,false);
}


function crearFormBusquedaTipoServicio()
{
		formTipoServicio = new Ext.FormPanel({
        labelWidth: 80, // label settings here cascade unless overridden
        url:'save-form.php',
        frame:true,
        title: 'B&#250;squeda',
        bodyStyle:'padding:5px 5px 0',
        width: 400,
		height:100,
        defaultType: 'textfield',
		items: [{
                fieldLabel: 'C&#243;digo',
                name: 'c&#243;digo',
				id:'cod',
				width:100,
				changeCheck: function(){
							var v = this.getValue();
							actDataStoreTipoServicio('codtipser',v);
							if(String(v) !== String(this.startValue))
							{
								this.fireEvent('change', this, v, this.startValue);
							} 
							},							 
							initEvents : function()
							{
								AgregarKeyPress(this);
							}               
      			},{
				fieldLabel: 'Denominaci&#243;n',
			    name: 'denominaci&#243;n',
			    id:'den',
			    width:230,
				changeCheck: function(){
							var v = this.getValue();
							actDataStoreTipoServicio('dentipser',v);
							if(String(v) !== String(this.startValue))
							{
								this.fireEvent('change', this, v, this.startValue);
							} 
							},							 
							initEvents : function()
							{
									AgregarKeyPress(this);
							}
			            }]
					});				  

}


function crearGridTipoServicio()
{
	crearFormBusquedaTipoServicio();
	crearDsTipoServicio();
		 
	 gridTipoServicio = new Ext.grid.GridPanel({
	 width:770,
	 height:350,
	 tbar: formTipoServicio,
	 autoScroll:true,
     border:true,
     ds: dsTipoServicio,
     cm: new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'codtipser'},
          {header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'dentipser'}          
       ]),
       	stripeRows: true,
      	viewConfig: {forceFit:true}
      });            
}

function pasarDatosTipoServicio(registro)
{
	Ext.getCmp('codtipser').setValue(registro.get('codtipser'));
	Ext.getCmp('dentipser').setValue(registro.get('dentipser'));
	Ext.getCmp('codmil').setValue(registro.get('codmil'));
	Ext.getCmp('denmil').setValue(registro.get('denmil'));
	
	Actualizar=true;			
}

function pasarDatosTipoServicio2(registro)
{
	Ext.getCmp('codtipser').setValue(registro.get('codtipser'));
	Ext.getCmp('dentipser').setValue(registro.get('dentipser'));
		
	Actualizar=true;			
} 

function mostrar_catalogo()
{
	crearGridTipoServicio();
	ObjetoFuente='definicion';
    ventanaTipoServicio = new Ext.Window(
    	{
        title: 'Cat&#225;logo tipos de servicio',
		autoScroll:true,
        width:800,
        height:450,
        modal: true,
        closable:false,
        plain: false,
        items:[gridTipoServicio],
        buttons: [{
        			text:'Aceptar',  
                    handler: function()
                    { 
                    	registro = gridTipoServicio.getSelectionModel().getSelected();
                    	switch(ObjetoFuente)   
                    	{
                    		case 'definicion':
                    			pasarDatosTipoServicio(registro);
	                    	break;
                    		case 'grid':
		                    	PasDatosGridGrid(Registro);
	                    	break;
                    		case 'objeto':
	                    		PasDatosGridObj(Registro);
	                    	break;
	                    }          
                    	gridTipoServicio.destroy();
		      			ventanaTipoServicio.destroy();                      
              		}
                   }
                   ,
                   {
                   text: 'Salir',
                   handler: function()
                   {  	
                      	gridTipoServicio.destroy();
		      			ventanaTipoServicio.destroy();
                   }
                  }]
		});
        ventanaTipoServicio.show();       
}

function catalogoTipoServicio()
{
	crearGridTipoServicio();
	ObjetoFuente='definicion';
    ventanaTipoServicio = new Ext.Window(
    	{
        title: 'Cat&#225;logo tipos de servicio',
		autoScroll:true,
        width:800,
        height:450,
        modal: true,
        closable:false,
        plain: false,
        items:[gridTipoServicio],
        buttons: [{
        			text:'Aceptar',  
                    handler: function()
                    { 
                    	registro = gridTipoServicio.getSelectionModel().getSelected();
                    	switch(ObjetoFuente)   
                    	{
                    		case 'definicion':
                    			pasarDatosTipoServicio2(registro);
	                    	break;
                    		case 'grid':
		                    	PasDatosGridGrid(Registro);
	                    	break;
                    		case 'objeto':
	                    		PasDatosGridObj(Registro);
	                    	break;
	                    }          
                    	gridTipoServicio.destroy();
		      			ventanaTipoServicio.destroy();                      
              		}
                   }
                   ,
                   {
                   text: 'Salir',
                   handler: function()
                   {  	
                      	gridTipoServicio.destroy();
		      			ventanaTipoServicio.destroy();
                   }
                  }]
		});
        ventanaTipoServicio.show();       
}