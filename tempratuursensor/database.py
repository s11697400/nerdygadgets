import time
import datetime
import MySQLdb
from sense_hat import SenseHat
from time import strftime
import RPi.GPIO as GPIO

GPIO.setmode(GPIO.BCM)
PIR_PIN = 21
GPIO.setup(PIR_PIN, GPIO.IN)

# Variables for MySQL
db = MySQLdb.connect(host="192.168.2.180",    user="nerd",passwd="password", db="nerdygadgets")
cur = db.cursor()
sh = SenseHat()
stemp = sh.get_temperature()
stemp = round(stemp, 2)

while True:

    i = GPIO.input(PIR_PIN)
    print(i)
    now = datetime.datetime.now()
    print("now =", now)
    dt_string = now.strftime("%Y-%m-%d %H:%M:%S")
    print("date and time =", dt_string)
 #datetimeWrite = (time.strftime("%Y-%m-%d ") + time.strftime("%H:%M:%S"))
   # print(datetimeWrite)

    sql = ("""INSERT INTO coldroomtemperatures_archive (ColdRoomSensorNumber, RecordedWhen, Temperature, ValidFrom, ValidTo) VALUES (%s,%s,%s,%s,%s)""",(4,dt_string,stemp,dt_string, "9999-12-12 00:00:00")) # sql = ("""INSERT INTO templog (datetime, temprature) VALUES '22-12-2021', 3""")
    try:
        print("Writing to database...")
        # Execute the SQL command
        cur.execute(*sql)
        # Commit your changes in the database
        db.commit()
        print("Write Complete")
        time.sleep(10)
    except:
        # Rollback in case there is any error
        db.rollback()
        print("Failed writing to database")
cur.close()
db.close()
