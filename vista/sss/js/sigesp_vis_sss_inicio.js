/* @Archivo javascript para el manejo de pantalla del inicio sessión
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

ruta  = 'controlador/sss/sigesp_ctr_sss_inicio.php';  

function irBasedatos()
{
    var objdata ={'operacion': 'obtenerbd'};
    objdata=JSON.stringify(objdata);
    parametros = 'objdata='+objdata; 
    $.ajax({
        data:  parametros,
        url:   ruta,
        dataType: 'html',
        type:  'post',
        success:  function (response)
        {
            datos = response;
            var datajson = eval('(' + datos + ')');
            if (datajson.raiz!=null)
            {   
                RemoveItemBaseDatos();
                for(let i=0; i< datajson.raiz.length; i++)
                {
                    let clave = datajson.raiz[i];
                    AddItemBaseDatos(clave.basedatos,clave.codbasedatos)
                }
                document.getElementById('cmbbasedatos').addEventListener('select',buscarEmpresas());
            }
        }
    });

}

function RemoveItemBaseDatos()
{
    obj = document.getElementById('cmbbasedatos');
    while (obj.options.length)
    {
        obj.remove(0);
    }
}

function AddItemBaseDatos(newtexto,newvalor)
{
    var opt = document.createElement("option");        
    opt.text = newtexto;
    opt.value = newvalor;
    document.getElementById('cmbbasedatos').options.add(opt);
}

function buscarEmpresas()
{
        valorBd=document.getElementById('cmbbasedatos').value;
        document.getElementById('cmbempresa').value='';
        document.getElementById('txtcodusuario').value='';
        document.getElementById('txtpasusuario').value='';
        irEmpresa(valorBd);	
}

function irEmpresa(bd)
{		
    var objdata ={'operacion': 'obtenerempresa','basedatos':bd};
    objdata=JSON.stringify(objdata);		
    parametros = 'objdata='+objdata; 
    
    $.ajax({
        data:  parametros,
        url:   ruta,
        dataType: 'html',
        type:  'post',
        success:  function (response)
        {
            datos = response;
            messageSuccesfull("Conecto con la Empresa!!!");
            var datajson = eval('(' + datos + ')');
            if (datajson.raiz!=null)
            {   
                RemoveItemEmpresa();
                for(let i=0; i< datajson.raiz.length; i++)
                {
                    let clave = datajson.raiz[i];
                    AddItemEmpresa(clave.nombre,clave.codemp)
                }
                document.getElementById('cmbempresa').addEventListener('select',irSession());
            }
        },
        error: function (response)
        { 
            messageError(response);
        }
        
    });
}

function RemoveItemEmpresa()
{
    obj = document.getElementById('cmbempresa');
    while (obj.options.length)
    {
        obj.remove(0);
    }
}

function AddItemEmpresa(newtexto,newvalor)
{
    var opt = document.createElement("option");        
    opt.text = newtexto;
    opt.value = newvalor;
    document.getElementById('cmbempresa').options.add(opt);
}

function irSession()
{
    basedatos=document.getElementById('cmbbasedatos').value;
    codemp=document.getElementById('cmbempresa').value;
    var objdata ={'operacion': 'cargarsession','basedatos':basedatos,'empresa':codemp};		
    objdata=JSON.stringify(objdata);		
    parametros = 'objdata='+objdata; 
    
    $.ajax({
        data:  parametros,
        url:   ruta,
        dataType: 'html',
        type:  'post',
        success:  function (response)
        {
            datos = response;
            messageSuccesfull("Cargo la Session!!!");
            document.getElementById('txtcodusuario').value='';
            document.getElementById('txtpasusuario').value='';
            document.getElementById('txtcodusuario').focus();
        },
        error: function (response)
        { 
            messageError(response);
        }
        
    });
}

function encriptar()
{			
    if (validarObjetos('txtpasusuario','50','novacio')!='0')
    {
        var pasusuario = document.getElementById('txtpasusuario').value;
        pasusuario = 'sigesp'+pasusuario;
        document.getElementById('txtpasusuario').value=b64_sha1(pasusuario);
    }
}

function irCancelar()
{
    document.getElementById('cmbbasedatos').value='';
    document.getElementById('cmbempresa').value='';
    document.getElementById('txtcodusuario').value='';
    document.getElementById('txtpasusuario').value='';
}

function irAceptar()
{
    encriptar();
    if ((validarObjetos('cmbbasedatos','100','novacio')!='0') && (validarObjetos('cmbempresa','100','novacio')!='0') && (validarObjetos('txtcodusuario','20','novacio|alfanumerico')!='0'))
    {	
        var objdata ={
                'operacion': 'iniciarsesion', 
                'basedatos': document.getElementById('cmbbasedatos').value, 
                'codempresa': document.getElementById('cmbempresa').value, 
                'codusuario': document.getElementById('txtcodusuario').value,
                'pasusuario': document.getElementById('txtpasusuario').value
        };
        objdata=JSON.stringify(objdata);		
        parametros = 'objdata='+objdata; 
        $.ajax({
            data:  parametros,
            url:   ruta,
            dataType: 'html',
            type:  'post',
            success: function (response)
            {
                datos = response;
                var dataJson= eval('(' + datos + ')');
                if (dataJson.raiz.valido==true)
                {
                        irCancelar();
                        ancho=screen.width-50;
                        alto=screen.height-50;
                        Xpos=((screen.width - ancho)/2); 
                        Ypos=((screen.height - alto) /2);
                        if(dataJson.raiz.iniciosession==1)
                        {
                                ventana=window.open("escritorio.html" , "SIGESP" , "menubar=0,toolbar=0,scrollbars=1,resizable=0,width="+ancho+",height="+alto+",left="+Xpos+",top="+Ypos+"");
                        }
                        else
                        {
                                ventana=window.open("vista/sss/sigesp_vis_sss_cambiopassword.html" , "SIGESP" , "menubar=0,toolbar=0,scrollbars=1,resizable=0,width="+ancho+",height="+alto+",left="+Xpos+",top="+Ypos+"");
                        }
                }
                else
                {
                    messageError(dataJson.raiz.mensaje);
                }
            },
            error: function (response) 
            {
                messageError('No se pudo iniciar sesion.');
            }					
        });	 
    }
}

function aceptarPassword(campo,e)
{
	var keycode;
	if (window.event)
	{
		keycode = window.event.keyCode;
	}
	else
	{
		if (e) 
		{
			keycode = e.which;
		}
		else 
		{
			return true;
		}
	}
	if (keycode == 13)
	{
		irAceptar();
		return false;
	}
	else
	{
		return true
	}
}

irBasedatos();
