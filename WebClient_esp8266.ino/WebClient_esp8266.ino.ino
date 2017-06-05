
#include <Wire.h>
#include <SPI.h>
#include "Arduino.h"
#include <ESP8266WiFi.h>
#include <WiFiUdp.h>
#include <TimeLib.h>

#define plotterNumber  5
#define maxPassDelay  8000
#define passesPerMeter 80
#define passLedPin 4        // D2 pin
#define errWiFiLedPin 3     // D9 pin
#define errNTPLedPin 5      // D1 pin
#define intPin 12           // D6 pin

boolean inTimer = false;
unsigned int passes = 0;
unsigned int wifiConnectAttemptsCounter = 0;
unsigned int maxWiFiConnectionAttempts = 50;
float meters = 0.0;
unsigned long id = 0;
String startTime = "";
String stopTime = "";
unsigned long lastHallWorked = 0;
volatile boolean isHall = false;

const char* ssid     = "PCPC";
const char* password = "12345678";
const char* host = "192.168.0.113";

const int timeZone = 4;
unsigned int localPort = 2390; 

// NTP Servers:
IPAddress timeServer(129, 6, 15, 28); // time-a.timefreq.bldrdoc.gov

const int NTP_PACKET_SIZE = 48; // NTP time is in the first 48 bytes of message
byte packetBuffer[NTP_PACKET_SIZE]; //buffer to hold incoming & outgoing packets

WiFiUDP Udp;

void setup() {

    Serial.begin(115200);
    while (!Serial) {
        ; // wait for serial port to connect. Needed for native USB port only
    }
  
    Serial.println(F("SerialStarted"));

    pinMode(passLedPin, OUTPUT);
    pinMode(errWiFiLedPin, OUTPUT);
    pinMode(errNTPLedPin, OUTPUT);
    digitalWrite(passLedPin, LOW);
    wifi_error_off();
    ntp_error_off();
    pinMode(intPin, INPUT_PULLUP);

    Serial.println(F("GPIOset"));

    Serial.println();
    Serial.println();
    Serial.print("Connecting to ");
    Serial.println(ssid);

    WiFi.mode(WIFI_STA);
  
    WiFi.begin(ssid, password);

    while (WiFi.status() != WL_CONNECTED) {
        delay(1000);
        wifiConnectAttemptsCounter++;
        Serial.print(".");
        if (wifiConnectAttemptsCounter > maxWiFiConnectionAttempts){
            wifi_error_on();
        }
    }

    wifi_error_off();

    Serial.println("");
    Serial.println("WiFi connected");
    Serial.println("IP address: ");
    Serial.println(WiFi.localIP());

    Serial.println("Starting UDP");
    Udp.begin(localPort);
    Serial.print("Local port: ");
    Serial.println(Udp.localPort());
    Serial.println("waiting for sync");
    setSyncProvider(getNtpTime);

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

void(* resetFunc) (void) = 0; // Reset MC function

bool sendDB(int _id, byte _plotter, String _startTime, String _stopTime, int _passes, float _meters) {

    detachInts();
  
    const int httpPort = 80;

    WiFiClient client;

    if (!client.connect(host, httpPort)) {
        Serial.println("connection failed");
        wifi_error_on();
        return false;
    }
  
    wifi_error_off();

    String url = String(String("/postdata?") + String("session_id=") + _id + String("&plotter=") + _plotter + String("&start_datetime=") + _startTime + String("&stop_datetime=") + _stopTime + String("&passes=") + _passes + String("&meters=") + _meters);

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
            wifi_error_on();
            return false;
        }
    }

    Serial.println();
    Serial.println("closing connection");
  
    attachInts();

    wifi_error_off();

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
            delay(5000);
        }
        wifi_error_off();
        attachInts();
    }
    startTime = "";
    stopTime = "";
    meters = 0;
    passes = 0;
}

String getTime() {
  
    String monthStr;
    int monthN = month();
    if (monthN < 10){
        monthStr = "0" + String(monthN);
    } else {
        monthStr = String(monthN);
    }
  
    String dayStr;
    int dayN = day();
    if (dayN < 10){
        dayStr = "0" + String(dayN);
    } else {
        dayStr = String(dayN);
    }

    String hourStr;
    int hourN = hour();
    if (hourN < 10){
        hourStr = "0" + String(hourN);
    } else {
        hourStr = String(hourN);
    }

    String minuteStr;
    int minuteN = minute();
    if (minuteN < 10){
        minuteStr = "0" + String(minuteN);
    } else {
        minuteStr = String(minuteN);
    }
  
    String secondStr;
    int secondN = second();
    if (secondN < 10){
        secondStr = "0" + String(secondN);
    } else {
        secondStr = String(secondN);
    }

    return String(String(year()) + "-" + monthStr + "-" + dayStr + "_" + hourStr + ":" + minuteStr + ":" + secondStr );

}

time_t getNtpTime()
{
    while (Udp.parsePacket() > 0) ; // discard any previously received packets
    Serial.println("Transmit NTP Request");
    sendNTPpacket(timeServer);
    uint32_t beginWait = millis();
    while (millis() - beginWait < 1500) {
        int size = Udp.parsePacket();
        if (size >= NTP_PACKET_SIZE) {
            Serial.println("Receive NTP Response");
            Udp.read(packetBuffer, NTP_PACKET_SIZE);  // read packet into the buffer
            unsigned long secsSince1900;
            // convert four bytes starting at location 40 to a long integer
            secsSince1900 =  (unsigned long)packetBuffer[40] << 24;
            secsSince1900 |= (unsigned long)packetBuffer[41] << 16;
            secsSince1900 |= (unsigned long)packetBuffer[42] << 8;
            secsSince1900 |= (unsigned long)packetBuffer[43];
            ntp_error_off();
            return secsSince1900 - 2208988800UL + timeZone * SECS_PER_HOUR;
        }
    }
    Serial.println("No NTP Response :-(");
    ntp_error_on();
    return 0; // return 0 if unable to get the time
}

// send an NTP request to the time server at the given address
void sendNTPpacket(IPAddress &address)
{
    // set all bytes in the buffer to 0
    memset(packetBuffer, 0, NTP_PACKET_SIZE);

    // Initialize values needed to form NTP request
    // (see URL above for details on the packets)
    packetBuffer[0] = 0b11100011;   // LI, Version, Mode
    packetBuffer[1] = 0;     // Stratum, or type of clock
    packetBuffer[2] = 6;     // Polling Interval
    packetBuffer[3] = 0xEC;  // Peer Clock Precision
    // 8 bytes of zero for Root Delay & Root Dispersion
    packetBuffer[12]  = 49;
    packetBuffer[13]  = 0x4E;
    packetBuffer[14]  = 49;
    packetBuffer[15]  = 52;

    // all NTP fields have been given values, now
    // you can send a packet requesting a timestamp:
    Udp.beginPacket(address, 123); //NTP requests are to port 123
    Udp.write(packetBuffer, NTP_PACKET_SIZE);
    Udp.endPacket();
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

void ntp_error_off() {
    digitalWrite(errNTPLedPin, LOW);
}

void ntp_error_on() {
    digitalWrite(errNTPLedPin, HIGH);
}

void wifi_error_off() {
    digitalWrite(errWiFiLedPin, LOW);
}

void wifi_error_on() {
    digitalWrite(errWiFiLedPin, HIGH);
}
