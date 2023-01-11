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

var dataStoreOtroCredito="";
var formularioBusquedaOtroCredito="";
var gridOtroCredito="";
var ventanaOtroCredito="";


function creardataStoreOtroCredito()
{
		var registroOtroCredito = Ext.data.Record.create([			  
								{name:'codcar'},
								{name:'dencar'},
								{name:'codestpro'},
								{name:'porcar'}, 
								{name:'spg_cuenta'}, 
								{name:'estlibcom'},
								{name:'formula'},
								{name:'tipo_iva'},
								{name:'estpagele'},
								{name:'estcla'}
						]);							
	
		dataStoreOtroCredito =  new Ext.data.Store({
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "codmon"   
			}
			,
		    registroOtroCredito  
			)
	  	})	
		
		var myJSONObject ={
			"oper": 'catalogo'
		}
		
		ObjSon=Ext.util.JSON.encode(myJSONObject);
		parametros = 'ObjSon='+ObjSon;
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_cxp_otroscreditos.php',
		params : parametros,
		method: 'POST',
		success: function ( result, request) 
		{ 
			datos = result.responseText;
			var objetoOtroCredito = eval('(' + datos + ')');
			if(objetoOtroCredito!='')
			{
				dataStoreOtroCredito.loadData(objetoOtroCredito);
			}
		}	
	})
}

function actdataStoreOtroCredito(criterio,cadena)
{
	dataStoreOtroCredito.filter(criterio,cadena,true,false);
}


function crearFormularioBusqueda()
{
		formularioBusquedaOtroCredito = new Ext.FormPanel({
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
                name: 'codigo',
				id:'codigo',
				changeCheck: function(){
							var v = this.getValue();
							actdataStoreOtroCredito('codcar',v);
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
			                name: 'denominacion',
			                id:'denominacion',
							changeCheck: function()
							{
										var v = this.getValue();
										actdataStoreOtroCredito('dencar',v);
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


function creargridOtroCredito()
{
	crearFormularioBusqueda();
	creardataStoreOtroCredito();
		 
	 gridOtroCredito = new Ext.grid.GridPanel({
	 width:770,
	 height:400,
	 tbar: formularioBusquedaOtroCredito,
	 autoScroll:true,
     border:true,
     ds: dataStoreOtroCredito,
     cm: new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'codcar'},
          {header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'dencar'},
          {header: "Estructura Presupuestaria", width: 50, sortable: true, dataIndex: 'codestpro', renderer: this.formatoEstructuraPresupuestaria},
          {header: "Porcentaje", width: 50, sortable: true, dataIndex: 'porcar'},
		  {header: "F&#243;rmula", width: 50, sortable: true, dataIndex: 'formula'}
       ]),
       	stripeRows: true,
      	viewConfig: {
      	forceFit:true
      }
      });            
}

function bloquearCamposPrimarios()
{
	var myJSONObject ={
		"oper":"claveprimaria"
	};
	
	ObjSon=Ext.util.JSON.encode(myJSONObject);
	parametros = 'ObjSon='+ObjSon;
	Ext.Ajax.request({
	url : '../../controlador/cfg/sigesp_ctr_cfg_cxp_otroscreditos.php',
	params : parametros,
	method: 'POST',
	success: function ( result, request) 
	{ 
		datos = result.responseText;
		var pk = eval('(' + datos + ')');
		if(pk.length>0)
		{
			for(i=0; i < pk.length; i++)
			{
				Ext.getCmp(pk[i].toString()).setDisabled(true);
			}
		}
	}	
	})
}

function formatoEstructuraPresupuestaria(estructura)
{
 var formatoEstructura="";
 switch(parseInt(empresa['numniv']))
 {
  case 1 : formatoEstructura = estructura.substr(-empresa['loncodestpro1']);
           break;
           
  case 2 : 
	      var estructura1 = estructura.substr(0,25);
	      var estructura2 = estructura.substr(25,25);
	      formatoEstructura = estructura1.substr(-empresa['loncodestpro1'])+" - "+estructura2.substr(-empresa['loncodestpro2']);
	      break
	      
  case 3 : 
      var estructura1 = estructura.substr(0,25);
      var estructura2 = estructura.substr(25,25);
      var estructura3 = estructura.substr(50,25);
      formatoEstructura = estructura1.substr(-empresa['loncodestpro1'])+" - "+estructura2.substr(-empresa['loncodestpro2'])+" - "+estructura3.substr(-empresa['loncodestpro3']);
  break;
  
  case 4 : 
      var estructura1 = estructura.substr(0,25);
      var estructura2 = estructura.substr(25,25);
      var estructura3 = estructura.substr(50,25);
      var estructura4 = estructura.substr(75,25);
      formatoEstructura = estructura1.substr(-empresa['loncodestpro1'])+" - "+estructura2.substr(-empresa['loncodestpro2'])+" - "+estructura3.substr(-empresa['loncodestpro3'])+" - "+estructura4.substr(-empresa['loncodestpro4']);
  break;
  
  case 5 : 
      var estructura1 = estructura.substr(0,25);
      var estructura2 = estructura.substr(25,25);
      var estructura3 = estructura.substr(50,25);
      var estructura4 = estructura.substr(75,25);
      var estructura5 = estructura.substr(100,25);
      formatoEstructura = estructura1.substr(-empresa['loncodestpro1'])+" - "+estructura2.substr(-empresa['loncodestpro2'])+" - "+estructura3.substr(-empresa['loncodestpro3'])+" - "+estructura4.substr(-empresa['loncodestpro4'])+" - "+estructura5.substr(-empresa['loncodestpro4']);
 }
 
 return formatoEstructura;
}

function pasarDatosGridOtroCredito(registro)
{   
	Ext.getCmp('codcar').setValue(registro.get('codcar'));
	Ext.getCmp('dencar').setValue(registro.get('dencar'));
	Ext.getCmp('spg_cuenta').setValue(registro.get('spg_cuenta'));
	Ext.getCmp('formula').setValue(registro.get('formula'));
	Ext.getCmp('porcar').setValue(registro.get('porcar'));
	Ext.getCmp('codestpro').setValue(formatoEstructuraPresupuestaria(registro.get('codestpro')));
	Ext.getCmp('codestpro1').setValue(registro.get('codestpro').substr(0,25));
	Ext.getCmp('codestpro2').setValue(registro.get('codestpro').substr(25,25));
	Ext.getCmp('codestpro3').setValue(registro.get('codestpro').substr(50,25));
	Ext.getCmp('codestpro4').setValue(registro.get('codestpro').substr(75,25));
	Ext.getCmp('codestpro5').setValue(registro.get('codestpro').substr(100,25));
	Ext.getCmp('estcla').setValue(registro.get('estcla'));
	Ext.getCmp('tipo_iva').setValue(parseInt(registro.get('tipo_iva')));
	Ext.getCmp('estpagele').setValue(parseInt(registro.get('estpagele')));
	if(registro.get('estlibcom')==1)
	{
	 Ext.getCmp('estlibcom').setValue(true);	
	}
	else
	{
	 Ext.getCmp('estlibcom').setValue(false);
	}
	
	Actualizar=true;			
}

function mostrarCatalogoOtroCredito(tipo)
{
				   creargridOtroCredito();
				   objetoOtroCredito=tipo;
                   ventanaOtroCredito = new Ext.Window(
                   {
                    title: 'Cat&#225;logo de Otros Cr&#233;ditos',
		    		autoScroll:true,
                    width:825,
                    height:475,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridOtroCredito],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function()
                    { 
                    	var registro = gridOtroCredito.getSelectionModel().getSelected();
                    	limpiarCampos();
                    	pasarDatosGridOtroCredito(registro);    
                    	gridOtroCredito.destroy();
		      			ventanaOtroCredito.destroy();                      
                    }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
                     	
                      	gridOtroCredito.destroy();
		      			ventanaOtroCredito.destroy();
                     }
                    }]
                    
                   });
                  ventanaOtroCredito.show();       
 }