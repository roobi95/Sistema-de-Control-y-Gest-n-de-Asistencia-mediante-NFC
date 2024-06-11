import RPi.GPIO as GPIO
from mfrc522 import SimpleMFRC522
import time
import mysql.connector

GPIO.setmode(GPIO.BOARD)
GPIO.setwarnings(False)  # Desactivar las advertencias de GPIO

lector = SimpleMFRC522()

# Pines para los LED
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
        
        print("Registrando la tarjeta en la base de datos...")
        # Insertar la tarjeta en la tabla de la base de datos
        insert_query = "INSERT INTO Tarjetas_NFC (NFC_Tag) VALUES (%s)"
        datos = (str(id),)
        cursor.execute(insert_query, datos)
        conexion.commit()  # Confirmar la inserción en la base de datos
        
        print("Tarjeta registrada con éxito.")
        GPIO.output(rojo_pin, GPIO.LOW)
        GPIO.output(verde_pin, GPIO.HIGH)
        time.sleep(2)
        GPIO.output(verde_pin, GPIO.LOW)

except Exception as e:
    print("Error:", e)

finally:
    GPIO.cleanup()  # Limpiar los pines GPIO después de la ejecución
    cursor.close()
    conexion.close()  # Cerrar la conexión a la base de datos

