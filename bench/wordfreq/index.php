<!--#set var="TITLE" value="Word Frequency" -->
<!--#set var="KEYWORDS" value="performance, benchmark, c++, stl, bash,
erlang, java, perl, python, computer, language, compare, cpu, memory,
word, frequency, count, counter" --> 

<?php require("../../html/testtop.php");
      testtop("Word Frequency"); ?>

<h4>About this test</h4>
<p>For this test, each program should be implemented to do the <a
  href="../../method.php#sameway"><i>same thing</i></a>, following
  the guidelines below:</p>
<p>This test is intended to show the best of a language at a <i>real
  world</i> computing task.  </p>
<p>The objective of this test is to take input from a file, extract all
  the words, convert them to lowercase, and count their frequency.  The
  output should be a tuple of (frequency, word) sorted in descending
  order by the primary key (frequency), and sorted in descending order by
  secondary key (word).  Although I think it would more aesthetically
  pleasing to have the words sorted in ascending order, the reason we do
  a descending sort is because that is how the bash program outputs its
  data.  It could fairly easily be changed to do an ascending sort for
  the words, but since it is already so much slower, it seemed reasonable
  to give it as much of a head start as possible.</p>
<p>Each test program should be capable of running in constant space
  over a range of input sizes, given a fixed number of words in the
  input.  If input buffering is done, it should be limited to a 4K
  input buffer.  This restriction is to avoid the solution that avoids
  I/O overhead by reading the entire input all at once, which does not
  scale to very large inputs.</p>
<p>The <b>bash</b> program is really a pipeline using <b>tr</b>,
  <b>grep</b>, <b>sort</b> and <b>uniq</b>.  This is the UNIX way of
  combining tools in the shell to get things done.</p>
<p><a href="/bench/wordfreq/Input">Input</a> file (it
  is repeated N times).<br>
  <a href="/bench/wordfreq/Output">Output</a> file (for
  N=1).<br></p>
<p>The input file to the tests is the text file <i>The Prince</i>, by
  Nicol� Machiavelli.  This input file is about 170KB, but it is
  duplicated N times.  (Visit the <a href="detail.php">detail
  page</a> to see results for different values of N).</p>
<p>While the word <i>Machiavellian</i> suggests cunning, duplicity, or
  bad faith, it would be unfair to equate the word with the man.  Old
  Nicol� was actually a devout and principled man, who had profound
  insight into human nature and the politics of his time.  Far more
  worthy of the pejorative implication is <i>Cesare Borgia</i>, the
  incestuous and multi-homicidal pope who was the inspiration for
  <i>The Prince</i>.  You too may ponder the question that preoccupied
  Machiavelli: can a government stay in power if it practices the
  morality that it preaches to its people?</p>

<h4>Observations</h4>
<p>I find it interesting that the Bash script one-liner (which uses a
  combination of shell programs like tr, uniq and sort, which are
  implemented in C) is <i>significantly slower</i> than the scripting
  language competition.  Why?  Because we chose an algorithm that
  makes very efficient use of hashes (associative arrays), something
  that Perl is very good at, for instance.</p>

<p>gawk/mawk don't have a builtin sort routine so we have to pipe
  through the shell <i>sort</i> program.</p>

<p>The <a href="wordfreq.gcc">C program</a> uses a simple Hash Table
  implemented in this header file: <a
  href="../Include/simple_hash.h">simple_hash.h</a>.</p>

<h4><a href="alt/">Alternates</a></h4>
<p><i>This section is for displaying alternate solutions that are either
  slower than ones above or perhaps don't quite meet my criteria for
  the competition, but are otherwise worthy of comment.</i></p>
<ul>
  <li>My original Python implementations were fairly slow (almost 6 times
  slower than Perl, for instance) but Joel Rosdahl suggested using a
  translation table/split instead of regexps to do the word splitting
  and that improved the speed by a factor of 3.  Here's an older <a
  href="alt/wordfreq.python2.python">Python</a> program which is much
  slower.</li>
  <li>Most languages benefit from reading input all at once, instead of line by
  line.  Roberto Ierusalimschy also contributed the following Lua variation
  that uses this technique: <a href="alt/wordfreq.lua2.lua">lua2</a>.</li>
  <li>An alternative <a href="alt/wordfreq.tcl1.tcl">Tcl test</a> though
  it's fairly short in code length, it takes longer to run.</li>
  <li>My first two C++/STL programs used <a href="alt/wordfreq.g++2.g++">
  map/multimap (g++2)</a> and <a href="alt/wordfreq.g++3.g++">a map of
  vectors (g++3)</a>, but my friend, Bill Lear, provided a much faster
  version that uses hash_map and he gave me most of the code in the
  <a href="wordfreq.g++">current version (g++)</a>, which uses a hash_map.
  (I've had to modify it a little to get it to work in my test environment).
  The hash_map code is also the longest code C++ version, but if you consider
  that the &quot;extra stuff&quot; is <i>reusable</i>, it's not that bad.
  The multimap version is the most concise, but it is about 6X slower than
  the hash_map version.</li>
</ul>

<!-- link died
<h4>Links</h4>
<p>
  Here are more benchmarks of word frequency counting that you might
  find of interest:
<ul>
<li>
  <a href="http://mageos.ifrance.com/bouchard/">Counting words in a
  text</a> by Jacques Bouchard
</ul>
 -->

  </tr>
</table>
<?php require("../../html/footer.php"); ?>
