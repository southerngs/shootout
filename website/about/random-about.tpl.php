<p>Each program should be implemented the <a
  href="faq.php?sort=<?=$Sort;?>#sameway"><b>same&nbsp;way</b></a> - the same way as this <a href="benchmark.php?test=random&lang=gcc&sort=<?=$Sort;?>">C program</a>.</p>
<p>Implement a function that generates
  a random double-precision floating point number using a <i>linear congruential
  generator</i> as described in <b>Numerical Recipes in C</b> by
  Press, Flannery, Teukolsky, Vetterling, section 7.1.</p>
<pre>
S[j] = (A * S[j-1] + C) modulo M
R = N * S[j] / M
A (multiplier)
C (increment)
M (modulus)
are appropriately chosen integral constants.
S[j] (seed) is calculated from S[j-1]
R (random number) is normalized to the interval [N,0].
</pre><br/>
<p>Correct output N = 1000 is
<pre>
8.163294467
</pre>
</p> 
<br/>
<p>Each program should use symbolic constants (or whatever is closest) to define the A, C, and M constants in the algorithm, not literal constants. </p>


