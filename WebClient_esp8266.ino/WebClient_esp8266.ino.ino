
#include <Wire.h>
//#include "RTClib.h"
#include <SPI.h>
#include "Arduino.h"
#include <ESP8266WiFi.h>

#define plotterNumber  1
#define maxPassDelay  8000
#define passesPerMeter 80
#define passLedPin 4
#define errTCPLedPin 3
#define errRTCLedPin 5
#define intPin 4

// RTC_DS1307 rtc;

boolean inTimer = false;
unsigned int passes = 0;
float meters = 0.0;
unsigned long id = 0;
String startTime = "";
String stopTime = "";
unsigned long lastHallWorked = 0;
volatile boolean isHall = false;

const char* ssid     = "PC";
const char* password = "12345678";
const char* host = "77.220.213.69";
WiFiClient client;

void setup() {

  
  Serial.begin(115200);
  while (!Serial) {
    ; // wait for serial port to connect. Needed for native USB port only
  }
  
  Serial.println(F("SerialStarted"));
//
  pinMode(passLedPin, OUTPUT);
  pinMode(errTCPLedPin, OUTPUT);
  pinMode(errRTCLedPin, OUTPUT);
  digitalWrite(passLedPin, LOW);
  digitalWrite(errTCPLedPin, LOW);
  digitalWrite(errRTCLedPin, LOW);
  pinMode(intPin, INPUT_PULLUP);

  Serial.println(F("GPIOset"));

  attachInts();
  

  Serial.println(F("SetupF"));
  delay(1000);

  interrupts();
}

void loop() {

  if (isHall) {
    digitalWrite(passLedPin, HIGH);
    hall_worked();
    isHall = false;
  }

  if ((inTimer == true) && ((millis() - lastHallWorked) > maxPassDelay) ) {
    stopPrintSession(plotterNumber);
  }
  
}

bool sendDB(int _id, byte _plotter, String _startTime, String _stopTime, int _passes, float _meters) {

  detachInts();

  if (!wifiStart()){
    digitalWrite(errTCPLedPin, HIGH);
    return false;
  }
  
  digitalWrite(errTCPLedPin, LOW);

  String url = String(String("/postdata?") + String("session_id=") + _id + String("&plotter=") + _plotter + String("&start_datetime=2017-02-12_12:12:00") + _startTime + String("&stop_datetime=2017-02-12_12:12:00") + _stopTime + String("&passes=") + _passes + String("&meters=") + _meters);

  String request = String("GET " + url + " HTTP/1.1\r\n" +
               "Host: " + host + "\r\n" + 
               "Connection: close\r\n\r\n");

  Serial.print("Request: ");
  Serial.println(request);

  client.print(request);
  
  unsigned long timeout = millis();
  while (client.available() == 0) {
    if (millis() - timeout > 5000) {
      Serial.println(">>> Client Timeout !");
      client.stop();
      return false;
    }
  }

  attachInts();
  return true;
}

void hall_worked() {
  //Serial.println(freeRam());
  lastHallWorked = millis();
  if (inTimer == false) {    //if we are out of timer
    inTimer = true;
    passes = 1;
    meters = 0;
    detachInts();
    startTime = getTime();
    attachInts();
    Serial.print(F("Pss: "));
    Serial.println(passes);
  } else {
    // if we are in timer
    passes++;
    Serial.print(F("Pss: "));
    Serial.println(passes);
  }
  digitalWrite(passLedPin, LOW);
}

void stopPrintSession(int pltoStop) {
  id++;
  inTimer = false;
  detachInts();
  stopTime = getTime();
  attachInts();
  meters = passes / float(passesPerMeter);
  if (passes > 1) {
    detachInts();
    while (!sendDB(id, pltoStop, startTime, stopTime, passes, meters)) {
      delay(10000);
    }
    attachInts();
  }
  startTime = "";
  stopTime = "";
  meters = 0;
  passes = 0;
}

String getTime() {
//  DateTime now = rtc.now();
//  
//  String monthStr;
//  int month = now.month();
//  if (month < 10){
//    monthStr = "0" + String(month);
//  } else {
//    monthStr = String(month);
//  }
//  
//  String dayStr;
//  int day = now.day();
//  if (day < 10){
//    dayStr = "0" + String(day);
//  } else {
//    dayStr = String(day);
//  }
//
//  String hourStr;
//  int hour = now.hour();
//  if (hour < 10){
//    hourStr = "0" + String(hour);
//  } else {
//    hourStr = String(hour);
//  }
//
//  String minuteStr;
//  int minute = now.minute();
//  if (minute < 10){
//    minuteStr = "0" + String(minute);
//  } else {
//    minuteStr = String(minute);
//  }
//  
//  String secondStr;
//  int second = now.second();
//  if (second < 10){
//    secondStr = "0" + String(second);
//  } else {
//    secondStr = String(second);
//  }
//
//  return String(String(now.year()) + "-" + monthStr + "-" + dayStr + "_" + hourStr + ":" + minuteStr + ":" + secondStr );
  return String("");
}

void intHall() {
  isHall = true;
}

void attachInts() {
  attachInterrupt(intPin, intHall, FALLING);
}

void detachInts() {
  detachInterrupt(intPin);
}

// start the Ethernet connection:
bool wifiStart () {
  Serial.println();
  Serial.println();
  Serial.print("Connecting to ");
  Serial.println(ssid);

  WiFi.mode(WIFI_STA);
  
  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("");
  Serial.println("WiFi connected");  
  Serial.println("IP address: ");
  Serial.println(WiFi.localIP());
  
  const int httpPort = 80;
  
  if (!client.connect(host, httpPort)) {
    Serial.println("connection failed");
    return false;
  }

  return true;
}



