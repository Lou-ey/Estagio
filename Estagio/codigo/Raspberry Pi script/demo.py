import time
import board
import busio
from adafruit_pcf8591.analog_in import AnalogIn
import adafruit_pcf8591.pcf8591 as pcf8591
import adafruit_sht31d
import requests
from datetime import datetime
import mysql.connector
#import cv2

# Database config
DB_HOST = "HOST"
DB_USER = 'USERNAME'
DB_PASSWORD = "PASSWORD"
DB_NAME = "digitalp_comfortevent"


# Function to obtain all sensor usernames and passwords from the database
def get_all_sensor_credentials():
    try:
        connection = mysql.connector.connect(
            host=DB_HOST,
            user=DB_USER,
            password=DB_PASSWORD,
            database=DB_NAME
        )

        cursor = connection.cursor(dictionary=True)
        cursor.execute("SELECT username, password FROM sensors WHERE status = 'ativo'")
        result = cursor.fetchall()

        if result:
            return result
        else:
            raise ValueError("No active sensors found in the database.")

    except mysql.connector.Error as err:
        print(f"Error connecting to the data base: {err}")
        raise

    finally:
        if connection.is_connected():
            cursor.close()
            connection.close()


# I2C bus initialization
i2c = busio.I2C(board.SCL, board.SDA)

# Initialization of sensors
try:
    sht = adafruit_sht31d.SHT31D(i2c)
    print("SHT31D sensor initialized successfully.")
except Exception as e:
    print(f"Error inicializing SHT31D: {e}")

try:
    pcf = pcf8591.PCF8591(i2c)
    pcf_a0 = AnalogIn(pcf, 0)  # Canal A0 (KY-038)
    pcf_a1 = AnalogIn(pcf, 1)  # Canal A1 (MQ-5)
    print("PCF8591 sensor initialized successfully.")
except Exception as e:
    print(f"Error inicializing PCF8591: {e}")

print("\nInicializing sensor reading...")

while True:
    try:
        # Get all sensor credentials
        sensor_credentials = get_all_sensor_credentials()

        # Read the values of analog sensors
        raw_value_ky038 = pcf_a0.value
        scaled_value_ky038 = (raw_value_ky038 / 65535) * pcf_a0.reference_voltage
        print("KY-038 (A0): {:.2f}".format(scaled_value_ky038))

        raw_value_mq5 = pcf_a1.value
        scaled_value_mq5 = (raw_value_mq5 / 65535) * pcf_a1.reference_voltage
        print("MQ-5 (A1): {:.2f}".format(scaled_value_mq5))

        # Reading temperature and humidity sensor values
        humidity = round(sht.relative_humidity)
        temperature = round(sht.temperature, 1)
        print("Humidity: {:.2f} %".format(humidity))
        print("Temperature: {:.1f} °C".format(temperature))

        for credentials in sensor_credentials:
            username = credentials["username"]
            password = credentials["password"]

            # Sending data to the server
            data = {
                "sensor_username": username,
                "sensor_password": password,
                "temperature": temperature,
                "humidity": humidity,
                "noise": scaled_value_ky038,
                "air_quality": scaled_value_mq5,
                "timestamp": datetime.now().isoformat()
            }

            r = requests.post("http://192.168.10.31/app1/insert_data.php", data=data)
            print(f"Status Code: {r.status_code}, Response: {r.text}")
            print('-' * 55 )

    except RuntimeError as e:
        print(f"Erro ao ler dados dos sensores: {e}")

    except requests.exceptions.RequestException as e:
        print(f"Erro ao enviar dados para o servidor: {e}")

    except Exception as e:
        print(f"Erro geral: {e}")

    time.sleep(5)

    #if cv2.waitKey(1) & 0xFF == ord('q'):
    #    break
