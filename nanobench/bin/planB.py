# The Computer Language Benchmarks Game
# $Id: planB.py,v 1.3 2008-08-01 01:55:03 igouy-guest Exp $

"""
measure without libgtop2
"""
__author__ =  'Isaac Gouy'


from domain import Record

import os, sys, cPickle, time, threading, signal
from errno import ENOENT
from subprocess import Popen


def measure(arg,commandline,delay,maxtime,
      outFile=None,errFile=None,inFile=None,logger=None):

   r,w = os.pipe()
   forkedPid = os.fork()

   if forkedPid: # read pickled measurements from the pipe
      os.close(w); rPipe = os.fdopen(r); r = cPickle.Unpickler(rPipe)
      measurements = r.load()
      rPipe.close()
      os.waitpid(forkedPid,0)
      return measurements

   else: 
      # Sample thread will be destroyed when the forked process _exits
      class Sample(threading.Thread):

         def __init__(self,program):
            threading.Thread.__init__(self)
            self.setDaemon(1)
            self.p = program
            self.maxMem = 0
            self.timedout = False     
            self.start()     
 
         def run(self):
            try:
               remaining = maxtime
               while remaining > 0:


                  time.sleep(delay)
                  remaining -= delay
               else:
                  self.timedout = True
                  #os.kill(self.p, signal.SIGTERM)
                  os.kill(self.p, signal.SIGKILL) # YAP can be too persistent
            except OSError, (e,err):
               if logger: logger.error('%s %s',e,err)

       
      try:
         m = Record(arg)

         # only write pickles to the pipe
         os.close(r); wPipe = os.fdopen(w, 'w'); w = cPickle.Pickler(wPipe)





         # spawn the program in a separate process
         p = Popen(commandline,stdout=outFile,stderr=errFile,stdin=inFile)

         # start a thread to sample the program's resident memory use
         t = Sample( program = p.pid )

         # wait for program exit status and resource usage
         rusage = os.wait3(0)




         # summarize measurements 
         if t.timedout:
            m.setTimedout()
         elif rusage[1] == os.EX_OK:
            m.setOkay()
         else:
            m.setError()

         m.userSysTime = rusage[2][0] + rusage[2][1]
         m.maxMem = t.maxMem












      except KeyboardInterrupt:
         w.dump(m)
         wPipe.close()
         raise # needed to clean up first 

      except ZeroDivisionError, (e,err): 
         if logger: logger.warn('%s %s',err,'too fast to measure?')

      except (OSError,ValueError), (e,err):
         if e == ENOENT: # No such file or directory
            if logger: logger.warn('%s %s',err,commandline)
            m.setMissing() 
         else:
            if logger: logger.error('%s %s',e,err)
            m.setError()       
   
      finally:
         w.dump(m)
         wPipe.close()

         # Sample thread will be destroyed when the forked process _exits
         os._exit(0)



