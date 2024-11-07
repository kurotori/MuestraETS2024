import sys
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.desired_capabilities import DesiredCapabilities
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC


# Verificar argumentos
if len(sys.argv) != 4:
    print("Uso: python script.py <jugadorId> <partidaID> <ipServidor>")
    sys.exit(1)

# Captura argumentos de línea de comandos
jugador_id = sys.argv[1]
partida_id = sys.argv[2]
ip_server = sys.argv[3]


options = Options()
#options.add_argument("--headless")  # Ejecuta el navegador en modo sin interfaz gráfica
options.add_experimental_option("excludeSwitches", ["enable-logging"])  # Evitar mensajes innecesarios en modo headless
options.set_capability("goog:loggingPrefs", {"performance": "ALL","server":"ALL"})  # Configura para obtener logs de red

# Ruta del controlador de Chrome (cambia esta ruta según tu sistema)
service = Service('./webdriver/chromedriver')  # Cambia 'ruta/al/chromedriver' por la ruta real de chromedriver

# Iniciar navegador Chrome
driver = webdriver.Chrome(service=service, options=options)



try:
    # Abre la página web
    driver.get("http://"+ip_server+":8000/partidas/iniciar")

    driver.execute_script("console.log('algo')")

    element = WebDriverWait(driver, 10).until(
    EC.presence_of_element_located((By.ID, "funciones"))
)

    # Encuentra los campos de entrada del formulario usando sus IDs
    campo_jugador = driver.find_element(By.ID, "idJugador")
    campo_partida = driver.find_element(By.ID, "idPartida")
    div_respuesta = driver.find_element(By.ID, "respuesta")
    print(div_respuesta.text)
    # Ingresa los valores en los campos
    campo_jugador.send_keys(jugador_id)
    campo_partida.send_keys(partida_id)

    # Envía el formulario (presionando Enter en uno de los campos)
    campo_partida.send_keys(Keys.RETURN)

    # Espera un momento para que se genere la respuesta en consola
    driver.implicitly_wait(10)

    print(div_respuesta.text)
    # Captura y muestra los logs de la consola de JavaScript
    logs = driver.get_log("performance")
    for entry in logs:
        print(f"Log Nivel: {entry['level']}, Mensaje: {entry['message']}")
    
    #logs2 = driver.get_log("performance")
    #for entry in logs2:
     #   print(f"Log Nivel: {entry['level']}, Mensaje: {entry['message']}")
        #log_message = entry["message"]
        # Filtra las respuestas de red (Network.responseReceived)
        #if "Network.responseReceived" in log_message:
         #   print(log_message)

finally:
    # Cierra el navegador
    driver.quit()
