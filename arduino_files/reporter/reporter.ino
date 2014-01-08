/*
  Reporter
 
 This sketch connects to a middle-ware server and reports the temperature for further analysis.
 Also features a web server that reports the current temperature.
 
 Circuit:
 * Ethernet shield attached to pins 10, 11, 12, 13

Based on example by:
 by David A. Mellis
 
 */

#include <SPI.h>
#include <Ethernet.h>

// Enter a MAC address for your controller below.
// Newer Ethernet shields have a MAC address printed on a sticker on the shield
byte mac[] = {  0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED };
IPAddress ip( 192, 168, 1, 129);

unsigned long time, ntime=0;
const int minute = 60;

IPAddress site ( 162, 243, 16, 158 ); // quotationoftheday.
//IPAddress server(173,194,33,104); // Google

EthernetServer localserver(8080);
// Initialize the Ethernet client library
// with the IP address and port of the server 
// that you want to connect to (port 80 is default for HTTP):
EthernetClient client, servC;

void setup() {

  // start the Ethernet connection:
  Ethernet.begin(mac, ip);
  Serial.begin(9600);
  localserver.begin();
  //  analogReference(EXTERNAL); use if 3.3 V
  ntime = 1000*10; // have the first report in 10 seconds.
  }


void loop()
{
 

          
   //Report Temperature Data to the Server
   report();
   
   
   //or else do server stuff as well -- like printing infos!
   servC = localserver.available();
   //Check if there is a new connection
   if(servC) {
     boolean currentLineIsBlank = true;
     while (servC.connected()) {
        if (servC.available()) {
          char c = servC.read();
          Serial.write(c);
          // if you've gotten to the end of the line (received a newline
          // character) and the line is blank, the http request has ended,
          // so you can send a reply
          if (c == '\n' && currentLineIsBlank) {
            // send a standard http response header
            servC.println("HTTP/1.1 200 OK");
            servC.println("Content-Type: text/html");
            servC.println("Connnection: close");
            servC.println();
            servC.println("<!DOCTYPE HTML>");
            servC.println("<html><head><title>Temperature Sensor Report</title></head><body>");
            // add a meta refresh tag, so the browser pulls again every 5 seconds:
            //   servC.println("<meta http-equiv=\"refresh\" content=\"5\">");
            // output the value of each analog input pin
            servC.print("<center><b>Current Temperature: </b>");
            servC.println(readTmp());
            servC.println("</center><br />");       
            
            servC.println("</body></html>");
            break;
          }
          if (c == '\n') {
            // you're starting a new line
            currentLineIsBlank = true;
          } 
          else if (c != '\r') {
            // you've gotten a character on the current line
            currentLineIsBlank = false;
          }
        }
      }
      // give the web browser time to receive the data
      delay(1);
      // close the connection:
      servC.stop();
   }
  
}

//Check if it's time to transmit a reading to the server.  If so report it.
void report()
{
  time = millis();

//report every 60 mins
if(ntime < time ) {
  Serial.println("Getting Ready to Transmit:");
  Serial.println("Temperature is read at: ");
  Serial.println(readTmp());
  ntime = 3600000 + time; //do it for an hour later.
  Serial.println(time);
  Serial.println(ntime);
    if(client.connect(site,80)){
            Serial.println("connected");
            // Make a HTTP request:
            client.print("GET /arduino/data.php?TEMP=");
            client.println(readTmp());
            Serial.println("Should be Sent");
            client.println();
            client.stop();
    }
}
}

float readVolt()
{
  const int sensorPin = 0;
 //getting the voltage reading from the temperature sensor
 int reading = analogRead(sensorPin);  
 
 // converting that reading to voltage, for 3.3v arduino use 3.3
 float voltage = reading * 5.0;
 voltage /= 1024.0;
 return voltage; 
}


float readTmp()
{
  float voltage = readVolt();
  float temperatureC = (voltage - 0.5) * 100; 
  return ((temperatureC * 9.0 / 5.0) + 32.0);
}
  
  


