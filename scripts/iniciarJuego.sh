#!/bin/bash
# Archivo JSON
json_file="puntajes.json"
log_file="resultado.log"

touch "$json_file"

retroarch="../../Retroarch/RetroArch-Linux-x86_64.AppImage -f --verbose --log-file=$log_file -L "
cores="../../Retroarch/RetroArch-Linux-x86_64.AppImage.home/.config/retroarch/cores/"
atari="stella2023_libretro.so "
nes="nestopia_libretro.so "
galaga="../../ROMs/galaga.nes"
iceclimber="../../ROMs/iceclimber.nes"
megamania="../../ROMs/megamania.bin"
dragonfire="../../ROMs/dragonfire.bin"

juegos=("MEGAMANIA" "DRAGONFIRE" "ICECLIMBER" "GALAGA")
jugados=()

# Función para capturar entrada con dialog
function get_input {
    local input
    input=$(dialog --inputbox "$1" 8 40 --output-fd 1)
    echo "$input"
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
    juegos=("${n_juegos[@]}")
    echo "${juegos[@]}"
}

# Función para obtener datos del usuario
function get_user_data {
    while true; do

        rm -r "$log_file"
        touch "$log_file"
            # Solicitar cada campo del nuevo registro
        nombre=$(get_input "¿Cuál es tu nombre?")
        apellido=$(get_input "¿Y tu apellido?")

        #Se selecciona un juego al azar

        juego=$(shuf -n 1 -e "${juegos[@]}")

        quitarJuego "$juego"

        #$(get_input "Introduce el nombre del juego:")

        case "$juego" in

            "MEGAMANIA")
                jugar="$retroarch$cores$atari$megamania"
                pausa=5
                ;;
            "GALAGA")
                jugar="$retroarch$cores$nes$galaga"
                pausa=5
                ;;
            "ICECLIMBER")
                jugar="$retroarch$cores$nes$iceclimber"
                pausa=5
                ;;
            "DRAGONFIRE")
                jugar="$retroarch$cores$atari$dragonfire"
                pausa=5
        esac


        $jugar &
        pid_retro=$!

        echo "iniciando el juego en el pid '$pid_retro'"

        tail -f "$log_file" | while read LINEA
            do
            echo "$LINEA" | grep -q "RCHEEVOS"
            if [ $? -eq 0 ]; then


                echo "$LINEA" | grep -q "Submitting"
                if [ $? -eq 0 ]; then
                    echo "Encontré: $LINEA"
                    puntos=$(echo "$LINEA"|cut -d" " -f4) #Extracción de puntos
                    echo "$puntos">puntos.txt
                    echo "Puntos: $puntos"
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
        dialog --msgbox "¡¡Lograste obtener $puntos puntos en esta partida!!" 5 70
        apodo=$(get_input "Introduce un apodo de tres letras (dejar vacío para generar automáticamente):")
        contacto=$(get_input "Introduce el contacto (opcional):")

        # Generar apodo si está vacío
        if [[ -z "$apodo" ]]; then
            apodo=$(echo "${nombre:0:1}${apellido:0:2}" | tr '[:lower:]' '[:upper:]')
        else
            apodo=$(echo "$apodo"|tr '[:lower:]' '[:upper:]')
        fi
        echo "$apodo"
        # Crear un objeto JSON
        new_entry=$(jq -n --arg nombre "$nombre" \
                        --arg apellido "$apellido" \
                        --arg juego "$juego" \
                        --arg puntos "$puntos" \
                        --arg apodo "$apodo" \
                        --arg contacto "$contacto" \
                        '{nombre: $nombre, apellido: $apellido, juego: $juego, puntos: ($puntos | tonumber), apodo: $apodo, contacto: $contacto}')

        # Agregar el nuevo objeto al archivo JSON
        echo "$new_entry"
        jq ". += [$new_entry]" "$json_file" > temp_json.json

        mv temp_json.json "$json_file"

        dialog --msgbox "Registro agregado correctamente!" 6 40


    done

}

get_user_data
