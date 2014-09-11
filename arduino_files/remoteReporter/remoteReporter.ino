#include <SoftwareSerial.h>

/*
  remotereporter - This devices job is to remotely collect data and transmit it
  back to the central reporter to save to the server.
*/

  #include <SoftwareSerial.h>

  SoftwareSerial xBee = SoftwareSerial(2,3);

  unsigned long time, ntime=0;

  //Main entry point for execution.  Setup the XBEE
  void setup() {
    ntime = 1000*10; // have the first report in 10 seconds.
    xBee.begin(9600);
    Serial.begin(9600);
  }

  //looping point of execution.  Periodically check the sensors.  Then report out to the XBEE
  void loop()
  {
    report();
  }
  
  void report()
  {
    time = millis();

    //report every 60 mins
    if(ntime < time ) {
	
        ntime = 3600000 + time; //do it for an hour later.
	goXbeeTransmit();

    }  
  }
  
  void goXbeeTransmit() {
      xBee.println(readTmp());
  }

  float readVolt()
  {
   const int sensorPin = 1;
   //getting the voltage reading from the temperature sensor
   int reading = analogRead(sensorPin);  
   
   // converting that reading to voltage, for 3.3v arduino use 3.3
   float voltage = reading * 5;
   voltage /= 1024.0;
   return voltage; 
  }


  float readTmp()
  {
    float voltage = readVolt();
    float temperatureC = (voltage - 0.5) * 100;
    Serial.println("Sending Temp Data: ");
    Serial.println(temperatureC); 
    return ((temperatureC * 9.0 / 5.0) + 32.0);
  }
  
