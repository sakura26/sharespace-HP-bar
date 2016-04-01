import RPi.GPIO as gpio
from time import sleep
import urllib
import json
#import ujson
import datetime
import subprocess

class Shifter():
  # pin setting
  rclk=10
  clock=12
  clearPin=8
  inputB=16

  # length of led
  outBitLength=16

  def __init__(self):
    self.setupBoard()
    self.pause=0
  def tick(self):
    gpio.output(Shifter.clock,gpio.HIGH)
    sleep(self.pause)
    gpio.output(Shifter.clock,gpio.LOW)
    sleep(self.pause)   
  def setValue(self,value):
    for i in range(24):
      bitwise=0x800000>>i
      bit=bitwise&value
      gpio.output(Shifter.rclk,gpio.LOW)
      if (bit==0):
        gpio.output(Shifter.inputB,gpio.LOW)
      else:
        gpio.output(Shifter.inputB,gpio.HIGH)
      gpio.output(Shifter.rclk,gpio.HIGH)
      Shifter.tick(self)
  def clear(self):
    gpio.output(Shifter.clearPin,gpio.LOW)
    Shifter.tick(self)
    gpio.output(Shifter.clearPin,gpio.HIGH)
  def setupBoard(self):
    #gpio.setup(Shifter.inputA,gpio.OUT)
    #gpio.output(Shifter.inputA,gpio.HIGH)
    gpio.setup(Shifter.inputB,gpio.OUT)
    gpio.output(Shifter.inputB,gpio.LOW)
    gpio.setup(Shifter.clock,gpio.OUT)
    gpio.output(Shifter.clock,gpio.LOW)
    gpio.setup(Shifter.clearPin,gpio.OUT)
    gpio.output(Shifter.clearPin,gpio.HIGH)
    gpio.setup(Shifter.rclk,gpio.OUT)
    gpio.output(Shifter.rclk,gpio.HIGH)
  def setHp(self, imax, icurrent):
    if icurrent>=imax:
      o=self.outBitLength
      z=0
    else:
      o = int(float(icurrent) / float(imax) * float(self.outBitLength))
      z = self.outBitLength - o
    print(z,o)
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
  def readHp(self,link):
    #link = "http://10.0.0.79/api/get?barid=0"
    #{ "status":"success", "barid":1, "userid":1, "unit":"", "type":"typical", "name":"MozTW", "vol_current":99, "vol_max":100, "cron":"", "privacy":"readonly" }
    #f = urllib.urlopen(link)
    #jsonstr=f.read()
    stime=datetime.datetime.now()
    p = subprocess.Popen(['curl', '-XGET', 'http://10.0.0.188/api.php/get/2'], stdout=subprocess.PIPE, stderr=subprocess.PIPE)
    out, err = p.communicate()
    sleep(0.5)
    jsonstr=''
    for line in out:
      jsonstr = jsonstr + line
    #jsonstr=out.read()
    print("R:"+jsonstr)
    etime=datetime.datetime.now()
    o = json.loads(out)
    print("post cost "+str((etime-stime).seconds)+" seconds")
    #print myfile
    self.clear()
    self.setHp(o["vol_max"], o["vol_current"])
'''  def jsonLv1(self,link):
    f = urllib.urlopen(link)
    stime=datetime.datetime.now()
    jsonstr = f.read()
    etime=datetime.datetime.now()
    jsonstr = jsonstr.strip()
    if jsonstr[0]=='{' and jsonstr[-1]=='}':
      jsonstr=jsonstr[1:-1]
    lv1json=jsonstr.split(',')
    intM = -1
    intC = -1
    for e in lv1json:
      e = e.strip()
      #print(e)
      if e.find("\"vol_current\":") == 0:
        r = e[14:]
        #print(r)
        try:
          intC = int(r)
        except ValueError:
          intC = -1
      if e.find("\"vol_max\":") == 0:
        r = e[10:]
        #print(r)
        try:
          intM = int(r)
        except ValueError:
          intM = -1
    print("post cost "+str((etime-stime).seconds)+" seconds")

    if intC!=-1 and intM!=-1:
      self.clear()
      self.setHp(intM, intC)
'''

def main():
  pause=1
  gpio.setmode(gpio.BOARD)
  shifter=Shifter()
  print("init done  ")
  link = "http://10.0.0.188/api.php/get/2" 
  running=True

  while running==True:
    try:
      #shifter.clear()
      shifter.readHp(link)
      sleep(pause)
    except KeyboardInterrupt:
      running=False
    except:
      sleep(pause)

  #shifter.setHp(30,16)
  '''
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

