"* The Computer Language Benchmarks Game
    http://shootout.alioth.debian.org/
    adapted from a program by Paolo Bonzini 
    contributed by Isaac Gouy *"!

Object subclass: #Thread   instanceVariableNames: 'name nextThread token semaphore'   classVariableNames: ''   poolDictionaries: ''   category: 'BenchmarksGame' !

!Thread methodsFor: 'accessing'!
name: anInteger   name := anInteger ! !

!Thread methodsFor: 'accessing'!
nextThread: aThread   nextThread := aThread ! !

!Thread methodsFor: 'accessing'!tokenNotDone   semaphore wait.   ^token > 0 ! !

!Thread methodsFor: 'accessing'!semaphore: aSemaphore   semaphore := aSemaphore ! !

!Thread methodsFor: 'accessing'!
fork   [ self run ] fork ! !

!Thread methodsFor: 'accessing'!
run 
   [ self tokenNotDone ] whileTrue: [ nextThread takeToken: token - 1 ].
   (token = 0) ifTrue: [Tests stdout print: name; nl].
   nextThread takeToken: -1 ! !

!Thread methodsFor: 'accessing'!takeToken: x   token := x.   semaphore signal ! !


!Thread class methodsFor: 'instance creation'!new
   ^self basicNew semaphore: Semaphore new ! !

!Thread class methodsFor: 'instance creation'!named: anInteger next: aThread   ^self new name: anInteger; nextThread: aThread; fork ! !


!Tests class methodsFor: 'benchmarking'!firstThread   | first last |
   503 to: 1 by: -1 do: [:i| 
      first := Thread named: i next: first.
      last isNil ifTrue: [ last := first ].
   ].
   last nextThread: first.   ^first ! !

!Tests class methodsFor: 'benchmarking'!threadring   self firstThread takeToken: self arg.   ^''! !

