<p>Each program should do the <a href="faq.php?sort=<?=$Sort;?>#samething"><b>same&nbsp;way</b></a> &#8212; the same way as this <a href="benchmark.php?test=pidigits&lang=clean&sort=<?=$Sort;?>">Clean program</a> or this <a href="benchmark.php?test=pidigits&lang=gst&sort=<?=$Sort;?>">Smalltalk program</a>.</p>

<p>Each program should

<ul>
  <li>calculate the first N digits of Pi (by the unbounded spigot algorithm)</li>
  <li>print the digits 10-to-a-line, with the running total of digits calculated</li>
</ul>
</p>

<p>Programs should implement the <b>unoptimised</b> unbounded spigot algorithm given on pages 4,6 & 7 of <a href="http://web.comlab.ox.ac.uk/oucl/work/jeremy.gibbons/publications/spigot.pdf">Unbounded Spigot Algorithms for the Digits&nbsp;of&nbsp;Pi</a>, Jeremy Gibbons, 2004, (156KB pdf) &#8212; adapted to stop after the first N digits.</p>

<p>Correct output N = 27 is:
<pre>
3141592653	 :10
5897932384	 :20
6264338    	 :27
</pre></p><br/>

<p>Correct output N = 1000 is in this 2KB <a href="iofile.php?test=<?=$SelectedTest;?>&lang=<?=$SelectedLang;?>&sort=<?=$Sort;?>&file=output">output file</a>.</p>


<p>For more information see Eric W. Weisstein, "Pi Digits." From <a href="http://mathworld.wolfram.com"><i>MathWorld</i></a>--A Wolfram Web Resource.<br/><a href="http://mathworld.wolfram.com/PiDigits.html">http://mathworld.wolfram.com/PiDigits.html</a></p>

