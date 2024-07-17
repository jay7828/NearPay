

/*
  # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #                                                               #
  #                 Installation :                                      #
  # NodeMCU ESP8266/ESP12E    RFID MFRC522 / RC522                      #
  #         D4      <---------->   SDA/SS                              #
  #         D5       <---------->   SCK                                 #
  #         D7       <---------->   MOSI                                #
  #         D6       <---------->   MISO                                #
  #         GND      <---------->   GND                                 #
  #         D3       <---------->   RST                                 #
  #         3V/3V3   <---------->   3.3V                                #
  #         D0       <---------->   LED and buzzer pin                  #
  # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
*/

//----Include the NodeMCU ESP8266 Library---//
#include <ESP8266WebServer.h>
#include <ESP8266HTTPClient.h>

//Include the library for the RFID Reader
#include <SPI.h>
#include <MFRC522.h>

//define the pin numbers
#define SS_PIN 2 //--> SDA / SS is connected to pinout D4
#define RST_PIN 5  //--> RST is connected to pinout D3

#define ON_Board_LED 2  //--> Defining an On Board LED, used for indicators when the process of connecting to a wifi router
#define Buzzer 16 // D0 pin for the buzzer

MFRC522 mfrc522(SS_PIN, RST_PIN);  //--> Create MFRC522 instance.

int readsuccess;
byte readcard[4];
char str[32] = "";
String StrUID;

//-----SSID and Password of the access point you want to create from the system-------//
const char* ssid = "Himank";
const char* password = "vjmr5397";

//set the endpoint that data will be dropped
const String paymentType = "credit";  //change to debit for debitting account
const String apikey = "somade_daniel";
const String servername = "http://192.168.4.2/nfc_payment/backend/process_payment_.php";

//add api key and payement type to the endpoint
const String serverApi = servername + "?apikey=" + String(apikey) + "&paymentType=" + String(paymentType);

ESP8266WebServer server(80);  //--> Server on port 80

void setup() {
  Serial.begin(115200); //--> Initialize serial communications with the PC
  
  SPI.begin();      //--> Init SPI bus
  
  mfrc522.PCD_Init(); //--> Init MFRC522 card

  delay(500);
  
  pinMode(ON_Board_LED, OUTPUT);
  pinMode(Buzzer, OUTPUT);
  digitalWrite(ON_Board_LED, HIGH); //--> Turn off Led On Board
  digitalWrite(Buzzer, LOW);

//create the access point
  WiFi.softAP(ssid, password);
  Serial.print("Access Point: ");
  Serial.print(ssid); Serial.println(" ...");
  Serial.print("IP address:\t");
  Serial.println(WiFi.softAPIP()); 
  server.begin();
  Serial.println("HTTP server started");

  Serial.println("");
  Serial.println("Please tag a card or keychain to see the UID !");
  Serial.println("");
}

void loop() {
  int readsuccess = getid();

  if (readsuccess) {
    String UIDresultSend, postData;
    digitalWrite(ON_Board_LED, LOW);
    digitalWrite(Buzzer, HIGH);
    
    UIDresultSend = StrUID;
    Serial.println("Card UID: " + UIDresultSend);
    
    String request = serverApi + "&card_number=" + UIDresultSend;
    Serial.println("Request: " + request);

    WiFiClient client;
    HTTPClient http;
    http.begin(client, request); 
    
    int httpResponseCode = http.GET();
    
    delay(500); // Delay for response handling
    
    digitalWrite(ON_Board_LED, HIGH);
    digitalWrite(Buzzer, LOW);
    
    if (httpResponseCode > 0) {
      String payload = http.getString();
      Serial.println("Server response: " + payload);
    } else {
      Serial.print("Error sending request. HTTP error code: ");
      Serial.println(httpResponseCode);
    }
    
    http.end();
    
    digitalWrite(ON_Board_LED, LOW);
    digitalWrite(Buzzer, HIGH);
    delay(200);
    digitalWrite(ON_Board_LED, HIGH);
    digitalWrite(Buzzer, LOW);
  }
}
