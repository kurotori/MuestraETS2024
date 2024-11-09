const divUno = document.getElementById('uno')
const divDos = document.getElementById('dos')
const divTres = document.getElementById('tres')
const divCuatro = document.getElementById('cuatro')
const divCinco = document.getElementById('cinco')
const divSeis = document.getElementById('seis')

const divPtosGalaga = document.getElementById('ptosGalaga')
const divPtosDragonfire = document.getElementById('ptosDragonfire')
const divPtosMegamania = document.getElementById('ptosMegamania')
const divPtosIceClimber = document.getElementById('ptosIceClimber')

const divJuegos = [divDos,divTres,divCuatro,divCinco]

const datosPrueba = [
    { "nombre": "Juan","apellido":"Santos", "juego": "GALAGA", "puntos": 80 , 'apodo':'JSA','contacto':''},
    { "nombre": "Ana","apellido":"Rocha", "juego": "DRAGONFIRE", "puntos": 95 , 'apodo':'ARO','contacto':''},
    { "nombre": "Luis","apellido":"Berríoz", "juego": "GALAGA", "puntos": 60 , 'apodo':'LBE','contacto':''},
    { "nombre": "María","apellido":"Morales", "juego": "ICE CLIMBER", "puntos": 100 , 'apodo':'MMO','contacto':''}
]



function delay(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

async function verDivs() {
    const container = document.getElementById('contenido');
    const divs = container.querySelectorAll('.seccion');

    let turno = 0

    while (true) {  // Para que sea continuo

        divUno.scrollIntoView({ behavior: 'smooth' });
        await delay(5000);

        divJuegos[turno].scrollIntoView({ behavior: 'smooth' });
        await delay(12000);

        turno++;
        if (turno>3) {
            turno=0
        }

        divSeis.scrollIntoView({ behavior: 'smooth' });
        await delay(10000);
    }
}

async function cargarPuntajes() {
    while (true) {
        try {

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
                    instAxios.get('../puntajes')
                    .then(
                        function (response) {
                            //console.log(response.data)
                            const puntajes = response.data.puntajes
                            const puntajesMegamania = puntajes.filter((puntaje)=>puntaje.juego=='MEGAMANIA')
                            const puntajesGalaga = puntajes.filter((puntaje)=>puntaje.juego=='GALAGA')
                            const puntajesIceClimber = puntajes.filter((puntaje)=>puntaje.juego=='ICECLIMBER')
                            const puntajesDragonfire = puntajes.filter((puntaje)=>puntaje.juego=='DRAGONFIRE')

                            const objPuntajes=[
                                {
                                    'div':divPtosGalaga,
                                    'puntuacion':puntajesGalaga
                                },
                                {
                                    'div':divPtosDragonfire,
                                    'puntuacion':puntajesDragonfire
                                },
                                {
                                    'div':divPtosMegamania,
                                    'puntuacion':puntajesMegamania
                                },
                                {
                                    'div':divPtosIceClimber,
                                    'puntuacion':puntajesIceClimber
                                },
                                ]

                            objPuntajes.forEach(puntaje => {
                                puntaje.puntuacion = puntaje.puntuacion.sort( (a,b)=>b.puntos-a.puntos)
                                //console.log(puntaje)
                                const listaPuntos = puntaje.div.querySelectorAll('.listaPuntos')[0]
                                //console.log(listaPuntos)
                                listaPuntos.innerHTML=""
                                let posicion=0
                                puntaje.puntuacion.forEach(element => {
                                    if (posicion<3) {
                                        const liPuntos = document.createElement('li')
                                        liPuntos.innerText = element.apodo + " - " + element.puntaje
                                        listaPuntos.appendChild(liPuntos)
                                        posicion++
                                    }

                                });
                            });
                        }
                    )
                }
            )

        } catch (error) {

        }
        await delay(8000)
    }

}


verDivs()
cargarPuntajes()

