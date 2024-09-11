# PulseSensor
IoT pulsesensor

In this project, a pulse sensor was built, programmed, and deployed.
The pulse sensor's data is transmitted to an OLED display, Blynk, and a database, from which it is retrieved and displayed on a website.

**Materials** 
Jumper wires _8_
Pulse sensor _1_
Adafruit Huzzah ESP32 _1_
OLED display    _1_


**Instructions**

**Install Arduino IDE v1.8.10**
Download from: https://www.arduino.cc/en/software

**Install the CP2014 USB Driver**
Follow the instructions at: https://learn.adafruit.com/adafruit-huzzah32-esp32-feather/using-with-arduino-ide

**Install Adafruit Huzzah with ESP32 Libraries in Arduino IDE**
Add the following URL to the Arduino IDE Preferences:
https://dl.espressif.com/dl/package_esp32_index.json

**Install the esp32 Board Package**
Open the Boards Manager in Arduino IDE and install the esp32 package.

**Select the Adafruit ESP32 Feather Board in Arduino IDE**

**Install Blynk-IoT on Your Phone**
Then, open Blynk.cloud in your computer's browser and register for an account. Follow the tutorial on the website to create a new ESP32 device.
Make sure to enter the correct SSID and password to generate the appropriate code template with the correct AuthTokens and TemplateID for your device.
