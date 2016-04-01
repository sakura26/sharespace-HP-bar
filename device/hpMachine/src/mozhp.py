#heroLv1 dummy test
import RPi.GPIO as gpio
from time import sleep
import urllib
import urllib2
import json

# modify me before use!!!
api_key = '1'
host = "http://moztw.heroeverything.com"
uri = "/api/bar.php"
outBitLength=16  # length of led

class Shifter():
  # pin setting
  rclk=10
  clock=12
  clearPin=8
  inputB=16

  def __init__(self):
    self.setupBoard()
    self.pause=0

  def tick(self):
    gpio.output(Shifter.clock,gpio.HIGH)
    sleep(self.pause)
    gpio.output(Shifter.clock,gpio.LOW)
    sleep(self.pause)   

  def setValue(self,value):
    for i in range(outBitLength):
      bitwise=0x800000>>i
      bit=bitwise&value
      gpio.output(Shifter.rclk,gpio.LOW)
      if (bit==0):
        gpio.output(Shifter.inputB,gpio.LOW)
      else:
        gpio.output(Shifter.inputB,gpio.HIGH)
      gpio.output(Shifter.rclk,gpio.HIGH)
      Shifter.tick(self)
      # pedding
      gpio.output(Shifter.rclk,gpio.LOW)
      gpio.output(Shifter.inputB,gpio.LOW)
      gpio.output(Shifter.rclk,gpio.HIGH)
      Shifter.tick(self)

  def clear(self):
    gpio.output(Shifter.clearPin,gpio.LOW)
    Shifter.tick(self)
    gpio.output(Shifter.clearPin,gpio.HIGH)

  def setupBoard(self):
    gpio.setup(Shifter.inputB,gpio.OUT)
    gpio.output(Shifter.inputB,gpio.LOW)
    gpio.setup(Shifter.clock,gpio.OUT)
    gpio.output(Shifter.clock,gpio.LOW)
    gpio.setup(Shifter.clearPin,gpio.OUT)
    gpio.output(Shifter.clearPin,gpio.HIGH)
    gpio.setup(Shifter.rclk,gpio.OUT)
    gpio.output(Shifter.rclk,gpio.HIGH)

  def setHp(self, imax, icurrent):
    if icurrent<0:
      icurrent=0;
    o = int(float(icurrent) / float(imax) * float(outBitLength))
    z = outBitLength - o
    print(imax,icurrent,o,z)
    for i in range(z):
      gpio.output(Shifter.rclk,gpio.LOW)
      gpio.output(Shifter.inputB,gpio.LOW)
      gpio.output(Shifter.rclk,gpio.HIGH)
      Shifter.tick(self)
    for i in range(o):
      gpio.output(Shifter.rclk,gpio.LOW)
      gpio.output(Shifter.inputB,gpio.HIGH)
      gpio.output(Shifter.rclk,gpio.HIGH)
      Shifter.tick(self)
    # pedding
    gpio.output(Shifter.rclk,gpio.LOW)
    gpio.output(Shifter.inputB,gpio.LOW)
    gpio.output(Shifter.rclk,gpio.HIGH)
    Shifter.tick(self)

  def readHp(self, host, uri, api_key):
    data = urllib.urlencode({'api_key': api_key})
    req = urllib2.Request(host+uri, data)
    response = urllib2.urlopen(req)
    o = json.loads(response.read())
    #h = httplib.HTTPConnection(host)
    #headers = {"Content-type": "application/x-www-form-urlencoded", "Accept": "text/plain"}
    #h.request('POST', uri, data, headers)
    #o = json.loads(h.getresponse())
    self.clear()
    self.setHp(o["value_max"], o["value_current"])

def main():
  pause=1
  gpio.setmode(gpio.BOARD)
  shifter=Shifter()
  print("init done  ")
  running=True

  while running==True:
    try:
      shifter.readHp(host, uri, api_key)
      sleep(pause)
    except KeyboardInterrupt:
      running=False

  '''
  # test for LED
  #shifter.setHp(30,16)

  while running==True:
    try:
      shifter.clear()
      data=0x0FFFFFF
      print("write "+hex(data))
      shifter.setValue(data) #0x0AAAAAA
      sleep(pause)
      shifter.clear()
      data=0x0FFFFFF
      print("write "+hex(data))
      shifter.setValue(data) #0x0555555
      sleep(pause)
    except KeyboardInterrupt:
      running=False
  '''

if __name__=="__main__":
    main()
