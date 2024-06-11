
import RPi.GPIO as GPIO
from mfrc522 import SimpleMFRC522
import time
import mysql.connector
from datetime import datetime

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

        # Obtener el ID del usuario asociado con la tarjeta NFC
        query_usuario = "SELECT ID_Usuario, Curso_Perteneciente FROM Usuarios WHERE NFC_Tag = (SELECT ID_Tarjeta_NFC FROM Tarjetas_NFC WHERE NFC_Tag = %s)"
        print("Consulta SQL usuario:", query_usuario)
        cursor.execute(query_usuario, (str(id),))
        result_usuario = cursor.fetchone()
        print("Resultado de la consulta usuario:", result_usuario)

        if result_usuario is not None:
            user_id = result_usuario[0]
            id_curso = result_usuario[1]  # Obtener el ID_Curso del usuario

            print("ID de usuario encontrado:", user_id)
            print("ID de curso del usuario:", id_curso)

            # Obtener la hora actual
            now = datetime.now()
            current_time = now.strftime("%H:%M")
            current_date = now.strftime("%Y-%m-%d")

            # Verificar si hay una hora de salida registrada en el rango de ejecución del script
            query_check = "SELECT Hora_Salida FROM Asistencia WHERE ID_Usuario = %s AND Fecha = %s AND Hora_Salida IS NOT NULL"
            print("Consulta de verificación de salida:", query_check)
            cursor.execute(query_check, (user_id, current_date))
            result_check = cursor.fetchone()
            print("Resultado de la verificación de salida:", result_check)

            if result_check is None:
                # Verificar si la hora actual está dentro del rango de ejecución del script (de 8:00 am a 3:00 pm)
                if "08:00:00" <= current_time <= "15:00:00":
                    # Insertar la hora como una nueva hora de entrada
                    insert_query = "INSERT INTO Asistencia (ID_Usuario, ID_Curso, Fecha, Hora_Entrada) VALUES (%s, %s, %s, %s)"
                    datos_entrada = (user_id, id_curso, current_date, current_time)
                    cursor.execute(insert_query, datos_entrada)
                    conexion.commit()  # Confirmar la inserción en la base de datos
                    print("Hora de entrada registrada con éxito.")
                else:
                    print("Fuera del rango de horas de entrada, no se registra.")

            else:
                # Insertar la hora actual como una nueva hora de salida
                insert_query = "INSERT INTO Asistencia (ID_Usuario, ID_Curso, Fecha, Hora_Salida) VALUES (%s, %s, %s, %s)"
                datos_salida = (user_id, id_curso, current_date, current_time)
                cursor.execute(insert_query, datos_salida)
                conexion.commit()  # Confirmar la inserción en la base de datos
                print("Hora de salida registrada con éxito.")

            GPIO.output(rojo_pin, GPIO.LOW)
            GPIO.output(verde_pin, GPIO.HIGH)
            time.sleep(2)
            GPIO.output(verde_pin, GPIO.LOW)

        else:
            print("No se encontró el usuario asociado con esta tarjeta.")

except Exception as e:
    print("Error:", e)

finally:
    GPIO.cleanup()  # Limpiar los pines GPIO después de la ejecución
    cursor.close()
    conexion.close()