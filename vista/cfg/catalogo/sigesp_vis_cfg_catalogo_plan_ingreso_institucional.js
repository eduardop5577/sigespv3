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

var dsplaningreso           ="";
var formbusquedaplaningreso ="";
var gridplaningreso         ="";
var ventanaplaningreso      ="";

function crearDsPlanIngreso()
{

	registroplaningreso = Ext.data.Record.create([
							{name: 'spi_cuenta'},
							{name: 'denominacion'},     
							{name: 'sc_cuenta'}
						]);
	
	var objetoplaningreso={"raiz":[{"spi_cuenta":'',"denominacion":'',"sc_cuenta":''}]};
		
		dsplaningreso =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objetoplaningreso),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"   
			}
			,
		    registroplaningreso  
			),
			data: objetoplaningreso
	  	})	
		
		var JSONObject ={
			"oper": 'catalogo'
		}
		ObjSon=JSON.stringify(JSONObject);
		parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_plan_ingreso_institucional.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request) 
		{ 
			datos = resultado.responseText;
			var objetoplaningreso = eval('(' + datos + ')');
			if(objetoplaningreso!='')
			{
				dsplaningreso.loadData(objetoplaningreso);
			}
		}	
	})
}

function actualizarDsPlanIngreso(criterio,cadena)
{
	dsplaningreso.filter(criterio,cadena,true,false);
}


function crearFormBusquedaPlanIngresos()
{
		formBusquedaPlanIngreso = new Ext.FormPanel({
        labelWidth: 150, // label settings here cascade unless overridden
        url:'save-form.php',
        frame:true,
        title: 'B&#250;squeda',
        bodyStyle:'padding:5px 5px 0',
        width: 500,
		height:100,
        defaults: {width: 230},
        defaultType: 'textfield',
		items: [{
                fieldLabel: 'Cuenta',
                name: 'cuenta',
				id:'spi_cuenta',
				changeCheck: function(){
							var v = this.getValue();
							actualizarDsPlanIngreso('spi_cuenta',v);
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
			                fieldLabel: 'Denominacion',
			                name: 'denominaci&#243;n',
			                id:'denominacion',
							changeCheck: function()
							{
										var v = this.getValue();
										actualizarDsPlanIngreso('denominacion',v);
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


var sm2 = new Ext.grid.CheckboxSelectionModel({
   
    });

function gridCatalogoPlanIngreso()
{
	crearFormBusquedaPlanIngresos();
	crearDsPlanIngreso();
		 
	 gridplaningreso = new Ext.grid.GridPanel({
	 width:770,
	 height:400,
	 tbar: formBusquedaPlanIngreso,
	 autoScroll:true,
     border:true,
     ds: dsplaningreso,
     cm: new Ext.grid.ColumnModel([
          sm2,
          {header: "Cuenta presupuestaria", width: 40, sortable: true,   dataIndex: 'spi_cuenta'},
          {header: "Denominaci&#243;n", width: 50, sortable: true,   dataIndex: 'denominacion'},
          {header: "Cuenta contable", width: 40, sortable: true, dataIndex: 'sc_cuenta'}
       ]),
       //sm: new Ext.grid.RowSelectionModel(),
     stripeRows: true,
     viewConfig: {
     forceFit:true
      }
      ,
      });            
} 

function pasarDatosGridPlanIngreso()
{
	p = new registroplaningreso
	({
		'spi_cuenta':'',
		'denominacion':'',
		'sc_cuenta':''
	});
	
	grid4.store.insert(0,p);
	
	p.set('spi_cuenta',gridplaningreso.getSelectionModel().getSelected().get('spi_cuenta'));
	p.set('denominacion',gridplaningreso.getSelectionModel().getSelected().get('denominacion'));
	p.set('sc_cuenta',gridplaningreso.getSelectionModel().getSelected().get('sc_cuenta'));	
}


function catalogoPersonalizado(registroActual)
{
				   gridCatalogoPlanIngreso();
				   ventanaplaningreso = new Ext.Window(
                   {
                    //layout:'fit',
                    title: 'Cat&#225;logo de cuentas de ingreso',
		    		autoScroll:true,
                    width:800,
                    height:400,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridplaningreso],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function()
                    	{ 
                    		rec = gridplaningreso.getSelectionModel().getSelected().get('sig_cuenta')
				        	if (validarExistencia2(rec,grid4,'spi_cuenta','spi_cuenta')){
				        	}
				        	else
				        	{
	                    		pasarDatosGridPlanIngreso(registroActual);	    
							    ventanaplaningreso.hide();
							    gridplaningreso.destroy();
			      				ventanaplaningreso.destroy();
 							}		
                    		
                    	}          
                              
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
                     	
                      	gridplaningreso.destroy();
		      			ventanaplaningreso.destroy();
                     }
                    }]
                    
                   });
                  ventanaplaningreso.show();       
 

}

