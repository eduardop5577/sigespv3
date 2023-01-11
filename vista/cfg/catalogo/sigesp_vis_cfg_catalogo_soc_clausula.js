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

var dsclausula="";
var formbusqueda="";
var gridclausula="";
var ventanaclausula="";

function crearDsClausula()
{

	registroclausula = Ext.data.Record.create([
							{name: 'codcla'},    
							{name: 'dencla'}
						]);
	
	var objclausula={"raiz":[{"codcla":'',"dencla":''}]};
		
		dsclausula =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objclausula),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"   
			}
			,
		    registroclausula  
			),
			data: objclausula
	  	})	
		
		var JSONObject ={
			"oper": 'catalogo'
		}
		ObjSon=JSON.stringify(JSONObject);
		parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_soc_clausula.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request) 
		{ 
			datos = resultado.responseText;
			var objclausula = eval('(' + datos + ')');
			if(objclausula!='')
			{
				dsclausula.loadData(objclausula);
			}
		}	
	})
}

function actDsClausula(criterio,cadena)
{
	dsclausula.filter(criterio,cadena,true,false);
}


function crearFormBusqueda()
{
		formbusqueda = new Ext.FormPanel({
        labelWidth: 75, // label settings here cascade unless overridden
        url:'save-form.php',
        frame:true,
        title: 'B&#250;squeda',
        bodyStyle:'padding:5px 5px 0',
        width: 350,
		height:100,
        defaults: {width: 230},
        defaultType: 'textfield',
		items: [{
                fieldLabel: 'C&#243;digo',
                name: 'cod',
				id:'cod',
				changeCheck: function(){
							var v = this.getValue();
							actDsClausula('codcla',v);
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
			                name: 'den',
			                id:'den',
							changeCheck: function()
							{
										var v = this.getValue();
										actDsClausula('dencla',v);
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


function crearGridCatalogo()
{
	crearFormBusqueda();
	crearDsClausula();
		 
	 gridclausula = new Ext.grid.GridPanel({
	 width:770,
	 height:400,
	 tbar: formbusqueda,
	 autoScroll:true,
     border:true,
     ds: dsclausula,
     cm: new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'codsis'},
          {header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'nomsis'}
       ]),
       	stripeRows: true,
      	viewConfig: {forceFit:true}
      });            
} 

function mostrar_catalogo()
{
				   crearGridCatalogo();
				   ObjetoFuente='definicion';
                   ventanaclausula = new Ext.Window(
                   {
                    //layout:'fit',
                    title: 'Cat&#225;logo c&#243;digos del sistema',
		    		autoScroll:true,
                    width:800,
                    height:400,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridclausula],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function()
                    { 
                    	Registro = gridclausula.getSelectionModel().getSelected();
                    	switch(ObjetoFuente)   
                    	{
                    		case 'definicion':
                    			limpiarCampos();
	                    		PasDatosGridDef(Registro);
	                    	break;
                    		case 'grid':
		                    	PasDatosGridGrid(Registro);
	                    	break;
                    		case 'objeto':
	                    		PasDatosGridObj(Registro);
	                    	break;
 
                    		
                    	}          
                    	gridclausula.destroy();
		      			ventanaclausula.destroy();                      
                    }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
                     	
                      	gridclausula.destroy();
		      			ventanaclausula.destroy();
                     }
                    }]
                    
                   });
                  ventanaclausula.show();       
 

 }