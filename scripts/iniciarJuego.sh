#!/bin/bash
titulo="Sistema de Torneo de VideoJuegos"
version="1"

# Archivo JSON
json_file="puntajes.json"
log_file="resultado.log"
ip_servidor=""

# pwd

# SCRIPTPATH="$( cd -- "$(dirname "$0")" >/dev/null 2>&1 ; pwd -P )"
# echo "$SCRIPTPATH"

# 

ip_servidor=$(dialog --backtitle "$titulo v$version" --title "Configurar Sistema" --inputbox "Ingresa el IP del Servidor" 8 40 --output-fd 1)
intentos=1

while (( intentos < 6 )); do
    dialog --backtitle "$titulo v$version" --infobox "Conectándose al servidor $ip_servidor \nIntento $intentos de 5" 8 40
    respuesta=$(curl -s --max-time 5 "$ip_servidor:8000/estado")

    if [[ -n "$respuesta" ]]; then
        NombreServidor=$(echo "$respuesta"|jq -r '.servidor')
        NombreTorneo=$(echo "$respuesta"|jq -r '.torneo')
        dialog --backtitle "$titulo v$version" --title "¡¡Conexión Exitosa!!" --msgbox "Conectado al servidor: $NombreServidor" 8 60
        break
    else
        dialog --backtitle "$titulo v$version" --title "ERROR DE CONEXIÓN" --infobox "No se pudo obtener respuesta del servidor $ip_servidor.\nIntento $intentos de 5\nEsperando 3 segundos para reintentar" 8 40
        ((intentos++))
        sleep 3s
    fi

done

if (( intentos == 6 )); then
    dialog --backtitle "$titulo v$version" --title "ERROR DE CONEXIÓN" --infobox "No se pudo obtener una respuesta del servidor $ip_servidor después de 5 intentos." 8 40
    sleep 5s
    bash "$0"
fi


#echo "$respuesta"

#sleep 30s

touch "$json_file"

retroarch="../Retroarch/RetroArch-Linux-x86_64.AppImage -f --verbose --log-file=$log_file -L "
cores="../Retroarch/RetroArch-Linux-x86_64.AppImage.home/.config/retroarch/cores/"
atari="stella2023_libretro.so "
nes="nestopia_libretro.so "
galaga="../ROMs/galaga.nes"
iceclimber="../ROMs/iceclimber.nes"
megamania="../ROMs/megamania.bin"
dragonfire="../ROMs/dragonfire.bin"

juegos=("MEGAMANIA" "DRAGONFIRE" "ICECLIMBER" "GALAGA")
jugados=()

# Función para capturar entrada con dialog
function get_input {
    local input
    input=$(dialog --backtitle "$NombreTorneo" --inputbox "$1" 8 40 --output-fd 1)
    echo "$input"
}

function mostrarAviso {
    mensaje="$1"
    titulo="$2"
    tiempo="$3"
    dialog --backtitle "$NombreTorneo" --title "$titulo" --infobox "$mensaje" 8 40
    sleep "$tiempo"
}

function extraerDato {
    clave="$1"
    respuesta="$2"
    dato=$(echo "$respuesta"|jq -r ".$clave")
    echo "$dato"
}


function enviarAServidorGET {
    ruta="$1"
    datos="$2"
    header_content="Content-Type: application/json"

    respuesta=$(curl -s -X GET -H "$header_content" -d "$datos" --max-time 5 "$ip_servidor:8000/$ruta")
    echo "$respuesta"
}

function enviarAServidorPOST {
    ruta="$1"
    datos="$2"
    header_content="Content-Type: application/json"

    respuesta=$(curl -s -X POST -H "$header_content" -d "$datos" --max-time 5 "$ip_servidor:8000/$ruta")
    echo "$respuesta"GET
}



#Argumentos
function quitarJuego {
    n_juegos=()

    for elemento in "${juegos[@]}"
    do
        if [ "$1" == "$elemento" ]; then
            jugados+=("$elemento")
        else
            n_juegos+=("$elemento")
        fi
    done

    if [ "${#juegos[@]}" -lt 1 ]; then
        juegos=("${jugados[@]}")
    else
         juegos=("${n_juegos[@]}")
    fi
}

# Función para obtener datos del usuario
function get_user_data {
    while true; do

        rm -r "$log_file"
        touch "$log_file"
            # Solicitar cada campo del nuevo registro
        idJugador=$(get_input "Hola. Ingresa tu ID")
        if [[ -z "$idJugador" ]]; then
            mostrarAviso "Debes ingresar una ID.\nPrueba nuevamente" "ERROR" 5
        else
            idJugador=$(echo "$idJugador" | tr '[:lower:]' '[:upper:]')
            
            respuesta=$(enviarAServidorGET "jugador/ver/$idJugador")
            
            estado=$(extraerDato "estado" "$respuesta")
            if [[ "$estado" == "OK" ]]; then
                nombreJugador=$(extraerDato "jugador.nombre" "$respuesta")
                apellidoJugador=$(extraerDato "jugador.apellido" "$respuesta")
                idNumJugador=$(extraerDato "jugador.id" "$respuesta")
                apodoJugador=$(extraerDato "jugador.apodo" "$respuesta")

                idPartida=$(get_input "¡Hola $nombreJugador $apellidoJugador!.\n Ingresa la ID de tu partida")
                respuesta=$(enviarAServidorGET "partida/ver/$idPartida")
                estado=$(extraerDato "estado" "$respuesta")

                if [[ "$estado" == "OK" ]]; then
                    juego=$(extraerDato "partida.juego" "$respuesta")

                    respuesta=$(enviarAServidorGET "partida/iniciar/$idPartida/$idJugador")

                    echo "$respuesta"
                    dialog --backtitle "$NombreTorneo" --title "Comenzando Juego" --pause "Tu juego comienza en 5 segundos" 8 40 5
                    
                    case "$juego" in

                        "MEGAMANIA")
                            jugar="$retroarch$cores$atari$megamania"
                            pausa=5
                            ;;
                        "GALAGA")
                            jugar="$retroarch$cores$nes$galaga"
                            pausa=13
                            ;;
                        "ICECLIMBER")
                            jugar="$retroarch$cores$nes$iceclimber"
                            pausa=5
                            ;;
                        "DRAGONFIRE")
                            jugar="$retroarch$cores$atari$dragonfire"
                            pausa=3
                    esac
                    

                    $jugar &
                    pid_retro=$!

                    #echo "iniciando el juego en el pid '$pid_retro'"

                    tail -f "$log_file" | while read LINEA
                        do
                        echo "$LINEA" | grep -q "RCHEEVOS"
                        if [ $? -eq 0 ]; then


                            echo "$LINEA" | grep -q "Submitting"
                            if [ $? -eq 0 ]; then
                                #echo "Encontré: $LINEA"
                                puntos=$(echo "$LINEA"|cut -d" " -f4) #Extracción de puntos
                                echo "$puntos">puntos.txt
                                #echo "Puntos: $puntos"
                                sleep "$pausa"
                                kill $pid_retro
                                exit 0
                            fi
                            #echo "Terminating process with PID: $PROCESS_PID"
                            #kill $PROCESS_PID
                            #exit 0
                        fi
                    done

                    puntos=$(<puntos.txt)
                    dialog  --title "Fin de la Partida" --infobox "¡¡Lograste obtener $puntos puntos en esta partida!!" 5 70
                    sleep 5s
                    dialog --title "Fin de la Partida" --infobox "Estamos registrando tu puntaje\n¡¡Gracias por Participar!!" 5 70
                    #sleep 3s
                    respuesta=$(enviarAServidorGET "partida/finalizar/$idPartida/$idJugador/$puntos")

                    if [[ "$estado" == "OK" ]]; then
                        dialog --title "Fin de la Partida" --infobox "Registro Completo\n¡¡Gracias por Participar!!" 5 70
                        sleep 3s
                    else
                        dialog --backtitle "$NombreTorneo" --title "ERROR" --msgbox "Ha ocurrido un error en el registro\nNotifica a los encargados\nJugador: $idJugador\nPartida: $idPartida\nPuntos: $puntos" 8 60
                    fi


                else
                    mostrarAviso "Esa no es una ID de Partida válida.\nPrueba nuevamente" "ERROR" 5
                fi

            else
                mostrarAviso "Esa ID no esta registrada.\nPrueba nuevamente" "ERROR" 5
            fi

            ###    
        fi
            ###
        

    done

}

get_user_data
