#define BLYNK_TEMPLATE_ID "YourBlynkID" 
#define BLYNK_TEMPLATE_NAME "YourBlynkTemplateName" 
#define BLYNK_AUTH_TOKEN "YourBlynkAuthToken" 
#include <ESP32Time.h> 
#include <SPI.h> 
#include <Wire.h> 
#include <Adafruit_Sensor.h> 
#include <PulseSensorPlayground.h> 
#include <Adafruit_SSD1306.h> 

#ifdef ESP32 
  #include <WiFi.h> 
  #include <HTTPClient.h> 
  #include <WebServer.h> 
  #include <BlynkSimpleEsp32.h> 
#else 
  #include <ESP8266WiFi.h> 
  #include <ESP8266HTTPClient.h> 
  #include <WiFiClient.h> 
  #include <BlynkSimpleEsp8266.h> 
#endif 
#include <Adafruit_SSD1306.h> 
#include <Wire.h> 

 
#define SCREEN_WIDTH 128 // OLED display width, in pixels 
#define SCREEN_HEIGHT 64 // OLED display height, in pixels 

Adafruit_SSD1306 display (SCREEN_WIDTH, SCREEN_HEIGHT, &Wire); 

char auth[] = BLYNK_AUTH_TOKEN; 

const char* ssid = "YourSSID"; 
const char* password = "YourPassword";  // WiFi 

const char* serverName = "YourServerIP"; 

const char* api_key = "YourApiKey"; 

BlynkTimer timer; 
WebServer server(80); 
ESP32Time rtc(3600); 

 
int PulseSensor = 36;        // Pulse Sensor purple wire connected to analog pin 36 (ESP32) 
int Signal;                  // Holds the incoming raw data. Signal value can range from 0-4000 
int Threshold = 2000;        // Determines which Signal to "count as a beat", and which to ignore 
int pulseData[SCREEN_WIDTH];  // Array to hold the pulse data for the graph 

 
volatile int BPM; // Beats per minute 
unsigned long lastBeat = 0;  // Timestamp of the last beat 

 

void handle_OnConnect() { 
  Signal = analogRead(PulseSensor); 
  server.send(200, "text/plain", "Pulse Signal: " + String(Signal)); 

} 

 

void setup() { 
  Serial.begin(9600); 
  Serial.println("Connecting to WiFi..."); 
  Serial.println(ssid); 

  WiFi.begin(ssid, password); 

  while (WiFi.status() != WL_CONNECTED) { 
    delay(1000); 
    Serial.print("."); 
  } 

  Serial.println(""); 
  Serial.println("WiFi connected!"); 
  Serial.print("IP: "); Serial.println(WiFi.localIP()); 

 

  server.on("/", handle_OnConnect); 
  server.begin(); 
  Serial.println("HTTP server started"); 

 

  Blynk.begin(auth, ssid, password); 
  pinMode(LED_BUILTIN, OUTPUT); 

  timer.setInterval(200L, TimerEvent); 

  if(!display.begin(SSD1306_SWITCHCAPVCC, 0x3D)) { // Address 0x3D for 128x64 
    Serial.println (F("SSD1306 allocation failed")); 
    for ( ;; ); // Don't proceed, loop forever 

  } 

  display.display(); 
  display.clearDisplay(); 
  memset(pulseData, 0, sizeof(pulseData)); 
} 

 

void loop() { 
  Blynk.run(); 
  timer.run(); 

   
  Signal = analogRead(PulseSensor); 
  memmove(pulseData, pulseData + 1, (SCREEN_WIDTH - 1) * sizeof(int)); 
  pulseData[SCREEN_WIDTH - 1] = Signal / 64;  

  display.clearDisplay(); 

 

  for (int i = 0; i < SCREEN_WIDTH - 1; i++) { 
    display.drawLine(i, SCREEN_HEIGHT - pulseData[i], i + 1, SCREEN_HEIGHT - pulseData[i + 1], WHITE); 
  } 

  display.display(); 
  delay(40); 

 

  // Peak detection 
  if (Signal > Threshold) { 

    Serial.println(Signal); 

    if (millis() - lastBeat > 600) {  
      unsigned long now = millis(); 
      unsigned long duration = now - lastBeat; 
      lastBeat = now; 

      BPM = 60000 / duration; 
      Serial.print("BPM: "); 
      Serial.println(BPM); 

    } 

  } 

  delay(100); // Stablize sensor reading

 

  if (WiFi.status() == WL_CONNECTED) { 
    HTTPClient http; 
    int pulse = Signal;  
    int beats = BPM; 

    String url = "http://" + String(serverName) + "/LeeviH/input.php"; 

    // Begin the HTTP request 

    http.begin(url);  
    http.addHeader("Content-Type", "application/x-www-form-urlencoded"); 

    if(Signal < 2500){ 
      Serial.println("Tietoja ei lähetetty"); 
    } 
    else { 
      // Construct the data to send 
      String httpRequestData = "api_key=" + String(api_key) + "&pulse=" + String(pulse) + "&BPM=" + String(beats); 

      // Send the POST request with the data 
      int httpResponseCode = http.POST(httpRequestData); 

 

      // Check the response code 
      if (httpResponseCode > 0) { 
          String response = http.getString(); 
          Serial.println(httpResponseCode); 
          Serial.println(response); 
      } else { 
          Serial.print("Error on sending POST: "); 
          Serial.println(httpResponseCode); 
      } 

    } 
    // End the HTTP connection 
    http.end(); 
  } else { 
      Serial.println("WiFi Disconnected"); 

  } 

 
 

} 

 

void TimerEvent() { 

  if(Signal > Threshold) { 
    Signal = analogRead(PulseSensor); // Read the PulseSensor's value 
    Blynk.virtualWrite(V0, Signal);   // Send the signal value to Blynk app 
    Blynk.virtualWrite(V1, BPM);      // Send the BPM to Blynk app 
    Serial.print("Sent to Blynk: ");  // Debug print to ensure data is being sent 
    Serial.println(Signal); 

  } 

 

} 
