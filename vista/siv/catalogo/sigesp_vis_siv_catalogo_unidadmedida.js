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

var dsunidadmedida="";
var formunidadmedida="";
var gridunidadmedida="";
var ventanaunidadmedida="";


function crearDsUnidadMedida()
{

	registrounidadmedida = Ext.data.Record.create([
							{name: 'codunimed'},    
							{name: 'denunimed'}
						]);
	
	var objetounidadmedida={"raiz":[{"codunimed":'',"denunimed":''}]};
		
		dsunidadmedida =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objetounidadmedida),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"   
			}
			,
		    registrounidadmedida  
			),
			data: objetounidadmedida
	  	})	
		
		var JSONObject ={
			"oper": 'catalogo'
		}
		ObjSon=JSON.stringify(JSONObject);
		parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
		url : '../../controlador/siv/sigesp_ctr_siv_unidadmedida.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request) 
		{ 
			datos = resultado.responseText;
			var objetounidadmedida = eval('(' + datos + ')');
			if(objetounidadmedida!='')
			{
				dsunidadmedida.loadData(objetounidadmedida);
			}
		}	
	})
}

function actDsUnidadMedida(criterio,cadena)
{
	dsunidadmedida.filter(criterio,cadena);
}


function crearFormBusqueda()
{
		formunidadmedida = new Ext.FormPanel({
        labelWidth: 90, // label settings here cascade unless overridden
        url:'save-form.php',
        frame:true,
        title: 'Busqueda',
        bodyStyle:'padding:5px 5px 0',
        width: 350,
		height:100,
        defaultType: 'textfield',
		items: [{
                fieldLabel: 'C&#243;digo',
                name: 'cod&#243;digo',
				id:'cod',
				width:100,
				changeCheck: function(){
							var v = this.getValue();
							actDsUnidadMedida('codunimed',v);
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
							changeCheck: function()
							{
										var v = this.getValue();
										actDsUnidadMedida('denunimed',v);
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


function crearGridUnidadMedida()
{
	crearFormBusqueda();
	crearDsUnidadMedida();
		 
	 gridunidadmedida = new Ext.grid.GridPanel({
	 width:770,
	 height:400,
	 tbar: formunidadmedida,
	 autoScroll:true,
     border:true,
     ds: dsunidadmedida,
     cm: new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'codunimed'},
          {header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'denunimed'}
       ]),
       	stripeRows: true,
      	viewConfig: {
      	forceFit:true
      }
      ,
      });            
} 


function pasarDatosUnidadMedida(registro)
{
	Ext.getCmp('codunimed').setValue(registro.get('codunimed'));
	Ext.getCmp('denunimed').setValue(registro.get('denunimed'));
		
	Actualizar=true;			
} 

function catalogoUnidadMedida()
{
				   crearGridUnidadMedida();
				   ObjetoFuente='definicion';
                   ventanaunidadmedida = new Ext.Window(
                   {
                    //layout:'fit',
                    title: 'Cat&#225;logo unidad de medida',
		    		autoScroll:true,
                    width:800,
                    height:400,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridunidadmedida],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function()
                    { 
                    	registro = gridunidadmedida.getSelectionModel().getSelected();
                    	switch(ObjetoFuente)   
                    	{
                    		case 'definicion':
                    			pasarDatosUnidadMedida(registro);
	                    	break;
                    		case 'grid':
		                    	PasDatosGridGrid(Registro);
	                    	break;
                    		case 'objeto':
	                    		PasDatosGridObj(Registro);
	                    	break;
 
                    		
                    	}          
                    	gridunidadmedida.destroy();
		      			ventanaunidadmedida.destroy();                      
                    }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
                     	
                      	gridunidadmedida.destroy();
		      			ventanaunidadmedida.destroy();
                     }
                    }]
                    
                   });
                  ventanaunidadmedida.show();       
 

 }