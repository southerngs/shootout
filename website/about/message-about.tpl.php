<p>Each program should do the <a href="faq.php?sort=<?=$Sort;?>#samething"><b>same&nbsp;thing</b></a>.
</p>

<p>Each program counts the number of messages sent between process/threads:</p>

<ul>
<li>take 2 command line args N M </li>
<li>create a chain of N processes/threads such that:

<ul>
<li>each process, thread
<ul>
<li>can receive an integer message</li>
<li>can store the received message</li>
<li>knows the next process/thread in the chain</li>
<li>can send the integer message + 1 to the next process, thread</li>
</ul>
</li>

<li>the last process/thread... in the chain is different, it:
<ul>
<li>can receive an integer message</li>
<li>can store the sum of received messages</li>
<li>there is no next process/thread</li>
</ul>
</li>
</ul>

<li>M times: send the integer message 0 to the first process/thread</li>
<li>print the sum of messages received by the last process/thread</li>
</ul>

<p>Correct output N = 3000, M = 200 is: 
<pre>600000</pre></p><br/>

<p>Similar benchmarks are described in <a href="http://www.sics.se/~joe/ericsson/du98024.html">Performance Measurements of Threads in Java and Processes in Erlang, 1998;</a> and <a href="http://www.sics.se/~joe/ericsson/du98024.html">A Benchmark Test for BCPL Style Coroutines, 2004.</a></p>

<p>(In some languages the same program (with different command-line arguments)  might be used for both the <a href="benchmark.php?test=message&lang=all&sort=<?=$Sort;?>">process benchmark</a> and this message benchmark.)</p>