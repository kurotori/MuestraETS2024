const inptNombre = document.getElementById('nombre')
const inptApellido = document.getElementById('apellido')
const divTelonFormRegistro = document.getElementById('telonFormRegistro')
const spanFormRegError = document.getElementById('formRegError')

const divResultado = document.getElementById('resultadoJyP')
const spanIdJugador = document.getElementById('idJugador')
const spanIdPartida = document.getElementById('idPartida')
//Formulario De Regreso
const divDatosJugador = document.getElementById('datosJugador')
const spanFormRegresoError = document.getElementById('formRegresoError')
const spanNombreJugador = document.getElementById('nombreJugador')
const spanIdJugadorR = document.getElementById('idJugadorR')
const spanUltimoJuego = document.getElementById('ultimoJuego')
const spanIdPartidaR = document.getElementById('idPartidaR')

const divResultadoR = document.getElementById('resultadoR')
const spanIdJugadorRF = document.getElementById('idJugadorRF')

function divMostrar(elementoDiv){
    elementoDiv.classList.remove('cerrado')
    elementoDiv.classList.add('abierto')
}

function divCerrar(elementoDiv){
    elementoDiv.classList.remove('abierto')
    elementoDiv.classList.add('cerrado')
}

document.getElementById('formRegistro').addEventListener('submit',function(event){
    event.preventDefault()

    let datosFormulario = new FormData(this);
    const datos = {}
    let habilitado = false

    // Iterar sobre los datos del formulario y mostrar los valores
    datosFormulario.forEach(function(value, key) {
        if (value.length==0) {
            if (key=="apodo") {
                console.log("apodo 0")
                let nick=inptNombre.value[0]+inptApellido.value[0]+inptApellido.value[1]
                console.log(nick)
                value=nick.toUpperCase()
                datos[key] = value;
            }
            else{
                spanFormRegError.innerText="Los campos con * son obligatorios"
                habilitado=false
            }
        }
        else{
            habilitado=true
            console.log(key + ": " + value);//BORRAR!!!!!
            datos[key] = value;
        }

    });


    if (habilitado) {
        divMostrar(divTelonFormRegistro)

        const instAxios = axios.create(
            {
                withCredentials:true,
                xsrfCookieName: 'XSRF-TOKEN',
                xsrfHeaderName: 'X-XSRF-TOKEN',
            }
        )
        instAxios.get('../sanctum/csrf-cookie')
        .then(function () {
                instAxios.post(
                    '../jugador/nuevo',
                    datos,
                    {
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    }
                )
                .then(function(response) {
                    //alert("Jugador Creado")
                    console.log('Respuesta:', response.data);
                    divCerrar(divTelonFormRegistro)
                    spanIdJugador.innerText=response.data.jugadorId

                    if (response.data.estado="OK") {
                        let datosJugador={
                            'jugadorId':response.data.jugadorId
                        }
                        instAxios.post(
                            '../partida/nueva',
                            datosJugador,
                            {
                                headers: {
                                    'Content-Type': 'application/json'
                                }
                            }
                        )
                        .then(function (response) {
                            console.log('Partida:', response.data);
                            divCerrar(document.getElementById('formRegistro'))
                            divMostrar(divResultado)
                            spanIdPartida.innerText=response.data.partida.codigo
                        })
                    }
                    else{
                        console.log("No funco")
                    }
                    //verPublicaciones()
                    //Recepción de la respuesta de la API
                })
                .catch(function(error) {
                    console.error('Error al enviar los datos:', error);
                    // Manejo de los errores
                })
            }

        )


    }

})


/**
 * Maneja los datos del formulario de regreso
 */

document.getElementById('formRegreso').addEventListener('submit', function (event) {
    event.preventDefault()

    let datosFormulario = new FormData(this);
    const datos = {}
    let habilitado = false

    datosFormulario.forEach(function(value, key) {
        if (value.length==0) {
            spanFormRegresoError.innerText="Los campos con * son obligatorios"
            habilitado=false

        }
        else{
            habilitado=true
            //console.log(key + ": " + value);//BORRAR!!!!!
            datos[key] = value;
            console.log(datos)
        }

        if (habilitado) {
            document.getElementById('jugadorId').disabled=true

            const instAxios = axios.create(
                {
                    withCredentials:true,
                    xsrfCookieName: 'XSRF-TOKEN',
                    xsrfHeaderName: 'X-XSRF-TOKEN',
                }
            )

            instAxios.get('../sanctum/csrf-cookie')
                .then(function () {
                    instAxios.get(
                        '../jugador/ver/'+datos.jugadorId,

                    )
                    .then(
                        function(response) {
                            console.log(response.data)
                            if (response.data.estado=="OK") {
                                //divCerrar(document.getElementById('btnEnviarR'))
                                document.getElementById('btnEnviarR').style.display='none'
                                divMostrar(divDatosJugador)
                                spanIdJugadorR.innerText=response.data.jugador.apodo+response.data.jugador.id
                                spanNombreJugador.innerText=response.data.jugador.nombre + " " + response.data.jugador.apellido
                                spanUltimoJuego.innerText=response.data.ultimoJuego
                            } else {
                                if (response.data.estado=="ERROR") {
                                    spanFormRegresoError.innerText=response.data.estado + ": " +response.data.mensaje
                                } else {
                                    spanFormRegresoError.innerText="Ha ocurrido un error"
                                }
                            }
                        }
                    )
                    .catch(function(error) {
                        console.error('Error al enviar los datos:', error);
                        // Manejo de los errores
                        spanFormRegresoError.innerText="Ha ocurrido un error"
                    })
                })

            }
        });

})


async function crearPartida() {
    jugadorId=spanIdJugadorR.innerText
    const instAxios = axios.create(
        {
            withCredentials:true,
            xsrfCookieName: 'XSRF-TOKEN',
            xsrfHeaderName: 'X-XSRF-TOKEN',
        }
    )
    instAxios.get('../sanctum/csrf-cookie')
    .then(
        function () {
            let datosJugador={
                'jugadorId':jugadorId
            }
            instAxios.post(
                '../partida/nueva',
                datosJugador,
                {
                    headers: {
                        'Content-Type': 'application/json'
                    }
                }
            )
            .then(
                function (response) {
                    console.log('Partida:', response.data);
                    divMostrar(divResultadoR)
                    divCerrar(divDatosJugador)
                    spanIdPartidaR.innerText=response.data.partida.codigo
                    spanIdJugadorRF.innerText=jugadorId
                }
            )
        }
    )
}

function reiniciar() {
    divCerrar(divResultado)
    divMostrar(document.getElementById('formRegistro'))
    spanFormRegError.innerText=""
    document.getElementById('formRegistro').querySelectorAll('input').forEach(element => {
        element.value=""
    });
}

function reiniciarFormRegreso() {
    document.getElementById('jugadorId').disabled=false
    document.getElementById('jugadorId').value=""
    spanIdJugadorR.innerText=""
    spanIdPartidaR.innerText=""
    spanNombreJugador.innerText=""
    spanIdJugadorRF.innerText=''
    spanFormRegresoError.innerText=''
    divCerrar(divDatosJugador)
    divCerrar(divResultadoR)
    document.getElementById('btnEnviarR').style.display='block'
}


