#include <WiFi.h>
#include <MFRC522v2.h>
#include <MFRC522DriverSPI.h>
#include <MFRC522DriverPinSimple.h>
#include <MFRC522Debug.h>
#include <HTTPClient.h>
#include <WebServer.h>

#define SS_PIN 21
#define RST_PIN 22

const char* ssid = "ssid"; //jangan lupa ganti nama wifi
const char* password = "password"; //jangan lupa juga ganti password wifi

MFRC522DriverPinSimple pinDriver(SS_PIN);
MFRC522DriverSPI driver(pinDriver, SPI);
MFRC522 mfrc522(driver);

WebServer server(80);

String fetchedHTML = "";

void setup() {
    Serial.begin(115200);
    SPI.begin();
    mfrc522.PCD_Init();

    WiFi.begin(ssid, password);
    Serial.print("Connecting to WiFi");
    while (WiFi.status() != WL_CONNECTED) {
        delay(1000);
        Serial.print(".");
    }
    Serial.println("Connected to WiFi");
    Serial.print("Local IP: ");
    Serial.println(WiFi.localIP());

    server.on("/", []() {
        if (fetchedHTML == "") {
            server.send(200, "text/html", "<h1>No data fetched yet</h1>");
        } else {
            server.send(200, "text/html", fetchedHTML);
        }
    });

    server.begin();
}

void loop() {
    server.handleClient();

    if (mfrc522.PICC_IsNewCardPresent() && mfrc522.PICC_ReadCardSerial()) {
        Serial.println("Card detected.");

        String uid = getUID();
        Serial.print("UID Card: ");
        Serial.println(uid);

        fetchHTML(uid);

        mfrc522.PICC_HaltA();
    }
}

String getUID() {
    String uid = "";
    for (byte i = 0; i < mfrc522.uid.size; i++) {
        uid += String(mfrc522.uid.uidByte[i], HEX);
        if (i < mfrc522.uid.size - 1) {
            uid += " ";
        }
    }
    uid.toUpperCase();
    return uid;
}

void fetchHTML(String uid) {
    if (WiFi.status() == WL_CONNECTED) {
        HTTPClient http;

        uid.replace(" ", "+");

        String url = "http://192.168.85.217/iot/data/?rfid=" + uid;
        Serial.print("Fetching URL: ");
        Serial.println(url);

        http.begin(url);
        int httpResponseCode = http.GET();

        if (httpResponseCode > 0) {
            fetchedHTML = http.getString();
            Serial.println("Fetched HTML:");
            Serial.println(fetchedHTML);

            int headIndex = fetchedHTML.indexOf("<head>");
            if (headIndex != -1) {
                fetchedHTML = fetchedHTML.substring(0, headIndex + 6) +
                              "<meta http-equiv=\"refresh\" content=\"1\">" +
                              fetchedHTML.substring(headIndex + 6);
            } else {
                fetchedHTML = "<meta http-equiv=\"refresh\" content=\"1\">" + fetchedHTML;
            }
        } else {
            Serial.print("HTTP GET Error: ");
            Serial.println(httpResponseCode);
        }
        http.end();
    } else {
        Serial.println("WiFi not connected.");
    }
}
