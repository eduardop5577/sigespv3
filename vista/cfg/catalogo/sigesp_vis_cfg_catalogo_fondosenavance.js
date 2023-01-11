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

var dataStoreFondoEnAvance="";           // DataStore asociado al Grid que muestra los registros
var formularioBusquedaFondoEnAvance="";  // Formulario que sirve como topbar del Grid, para la busqueda
var gridFondoEnAvance="";                // Grid que muestra los datos
var ventanaFondoEnAvance="";             // Ventana que contiene el Grid y el Formulario de Búsqueda 


function creardataStoreFondoEnAvance()
{

		registroFondoEnAvance = Ext.data.Record.create([
								{name:'codtipfon'},			  
								{name:'dentipfon'},
								{name:'porrepfon'}
						]);							
	
		var objetoFondoEnAvance={"raiz":[{"codtipfon":"","dentipfon":"","porrepfon":""}]};
		
		dataStoreFondoEnAvance =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objetoFondoEnAvance),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"   
			}
			,
		    registroFondoEnAvance  
			),
			data: objetoFondoEnAvance
	  	});	
		
		var myJSONObject ={
			"oper": 'catalogo'
		};
		
		ObjSon=Ext.util.JSON.encode(myJSONObject);
		parametros = 'ObjSon='+ObjSon;
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_scb_fondosenavance.php',
		params : parametros,
		method: 'POST',
		success: function ( result, request) 
		{ 
			datos = result.responseText;
			var objetoFondoEnAvance = eval('(' + datos + ')');
			if(objetoFondoEnAvance!='')
			{
				dataStoreFondoEnAvance.loadData(objetoFondoEnAvance);
			}
		}	
	})
}

function actdataStoreFondoEnAvance(criterio,cadena)
{
	dataStoreFondoEnAvance.filter(criterio,cadena,true,false);
}


function crearFormularioBusqueda()
{
		formularioBusquedaFondoEnAvance = new Ext.FormPanel({
        labelWidth: 75,
        frame:true,
        title: 'B&uacute;squeda',
        bodyStyle:'padding:5px 5px 0',
        width: 350,
		height:100,
        defaults: {width: 230},
        defaultType: 'textfield',
		items: [{
                fieldLabel: 'C&#243;digo',
                name: 'codtipfon',
				id:'codigo',
				changeCheck: function(){
							var v = this.getValue();
							actdataStoreFondoEnAvance('codtipfon',v);
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
			                name: 'dentipfon',
			                id:'denominacion',
							changeCheck: function()
							{
										var v = this.getValue();
										actdataStoreFondoEnAvance('dentipfon',v);
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


function creargridFondoEnAvance()
{
	crearFormularioBusqueda();
	creardataStoreFondoEnAvance();
		 
	 gridFondoEnAvance = new Ext.grid.GridPanel({
	 width:770,
	 height:350,
	 tbar: formularioBusquedaFondoEnAvance,
	 autoScroll:true,
     border:true,
     ds: dataStoreFondoEnAvance,
     cm: new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'codtipfon'},
          {header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'dentipfon'}
       ]),
       	stripeRows: true,
      	viewConfig: {
      	forceFit:true
      },
      listeners:{'celldblclick' : function( grid, fila, columna, evento ){
    	  registro = grid.getSelectionModel().getSelected();
    	  limpiarCampos();
		  pasarDatosFondoEnAvance(registro);
		  grid.destroy();
		  ventanaFondoEnAvance.destroy();
      }}
      });            
}

function pasarDatosFondoEnAvance(registro)
{
	Actualizar=true;
	Ext.getCmp('codtipfon').setValue(registro.get('codtipfon'));
	Ext.getCmp('dentipfon').setValue(registro.get('dentipfon'));
	Ext.getCmp('porrepfon').setValue(registro.get('porrepfon'));
}

function mostrar_catalogo()
{
				   creargridFondoEnAvance();
				   objetoFondoEnAvance='definicion';
                   ventanaFondoEnAvance = new Ext.Window(
                   {
                    title: 'Cat&#225;logo de Fondos en Avance',
		    		autoScroll:true,
                    width:800,
                    height:450,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridFondoEnAvance],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function()
                    { 
                    	registro = gridFondoEnAvance.getSelectionModel().getSelected();
                    	limpiarCampos();
                    	pasarDatosFondoEnAvance(registro);          
                    	gridFondoEnAvance.destroy();
		      			ventanaFondoEnAvance.destroy();                      
                    }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
                     	
                      	gridFondoEnAvance.destroy();
		      			ventanaFondoEnAvance.destroy();
                     }
                    }]
                    
                   });
                  ventanaFondoEnAvance.show();       
 

 }