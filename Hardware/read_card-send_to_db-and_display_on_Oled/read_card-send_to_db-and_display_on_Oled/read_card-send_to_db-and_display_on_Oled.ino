#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <SPI.h>
#include <MFRC522.h>

// Replace with your WiFi credentials
const char* ssid = "Himank";
const char* password = "vjmr5697";

// Server details
const String serverUrl = "http://192.168.137.1/NFC/backend/process_payment.php"; // Replace with your server IP address and script name
const String apikey = "Jayesh"; // Replace with your API key

// RFID setup
#define SS_PIN D4
#define RST_PIN D3
MFRC522 mfrc522(SS_PIN, RST_PIN);

void setup() {
  Serial.begin(115200);
  SPI.begin(); // Init SPI bus
  mfrc522.PCD_Init(); // Init MFRC522

  // Connect to WiFi
  WiFi.begin(ssid, password);
  Serial.println("Connecting to WiFi...");
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Connecting...");
  }
  Serial.println("Connected to WiFi");

  delay(1000); // Optional delay for stabilization
}

void loop() {
  // Look for new cards
  if (mfrc522.PICC_IsNewCardPresent() && mfrc522.PICC_ReadCardSerial()) {
    // Read card UID
    String cardUid = "";
    for (byte i = 0; i < mfrc522.uid.size; i++) {
      cardUid += String(mfrc522.uid.uidByte[i] < 0x10 ? "0" : "");
      cardUid += String(mfrc522.uid.uidByte[i], HEX);
    }
    cardUid.toUpperCase();

    // Determine payment type (debit or credit)
    String paymentType = "debit"; // Change this based on your application logic

    // Send data to server
    sendPaymentRequest(cardUid);

    // Halt for a while to prevent multiple reads
    delay(500);
  }
}

void sendPaymentRequest(String cardUid) {
  // Build the complete URL with parameters
  String url = serverUrl + "?apikey=" + apikey + "&card_number=" + cardUid;

  // Create WiFi client instance
  WiFiClient client;

  // Create HTTP client instance
  HTTPClient http;

  // Send GET request to server
  Serial.print("Sending GET request to server: ");
  Serial.println(url);
  if (http.begin(client, url)) { // Use updated begin method
    int httpCode = http.GET();

    // Check HTTP response
    if (httpCode > 0) {
      String payload = http.getString();
      Serial.print("Server response: ");
      Serial.println(payload);
      // Process server response as needed
    } else {
      Serial.print("Error sending request. HTTP error code: ");
      Serial.println(httpCode);
    }

    http.end(); // Close connection
  } else {
    Serial.println("Unable to connect to server");
  }
}
