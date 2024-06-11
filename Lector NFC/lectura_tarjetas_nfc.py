import RPi.GPIO as GPIO
from mfrc522 import SimpleMFRC522
import time
import mysql.connector

GPIO.setmode(GPIO.BOARD)
GPIO.setwarnings(False)  # Desactivar las advertencias de GPIO

lector = SimpleMFRC522()

rojo_pin = 5
verde_pin = 3
GPIO.setup(rojo_pin, GPIO.OUT)
GPIO.setup(verde_pin, GPIO.OUT)

# Conexión a la base de datos
conexion = mysql.connector.connect(
    host="x.x.x.x",
    user="user_name",
    password="your_password",
    database="your_database"
)
cursor = conexion.cursor()

print("Lector activo...\n")

try:
    while True:
        print("Esperando para leer una tarjeta...")
        id, text = lector.read()
        print("UID: " + str(id))
        
        # Consultar la base de datos para verificar la existencia de la tarjeta
        consulta = "SELECT * FROM Tarjetas_NFC WHERE NFC_Tag = %s"
        cursor.execute(consulta, (str(id),))
        resultado = cursor.fetchone()
        
        if resultado:
            print("Acceso concedido")
            GPIO.output(rojo_pin, GPIO.LOW)
            GPIO.output(verde_pin, GPIO.HIGH)
            time.sleep(2)
            GPIO.output(verde_pin, GPIO.LOW)
        else:
            print("Acceso denegado")
            GPIO.output(rojo_pin, GPIO.HIGH)
            GPIO.output(verde_pin, GPIO.LOW)
            time.sleep(2)
            GPIO.output(rojo_pin, GPIO.LOW)
            
except Exception as e:
    print("Error:", e)            

finally:
    GPIO.cleanup()  # Limpiar los pines GPIO después de la ejecución
    cursor.close()
    conexion.close()  # Cerrar la conexión a la base de datos


