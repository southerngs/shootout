<!--#set var="TITLE" value="Method Calls" -->
<!--#set var="KEYWORDS" value="performance, benchmark, 
computer, language, compare, cpu, memory, method calls" --> 

<?php require("../../html/testtop.php");
      testtop("Method Calls"); ?>

<h4>About this test</h4>
<p>For this test, each program should be implemented in the <a
  href="../../method.php#sameway"><i>same way</i></a>.</p>
<p>This test attempts to measure the speed of method calls in OO
  languages.  It measures a mixture of method calls of a base and a
  derived class on an object of the derived class.</p>
<p>This test uses a base class <i>Toggle</i>, which implements a simple
  boolean flip-flop device and a derived class <i>NthToggle</i>, which
  only flips every Nth time it is activated.  The base Toggle class
  should define a boolean (or integer) field to hold a true/false
  value.  It should define methods to access the value, and to
  activate the toggle (flip it's value).  The derived NthToggle class
  should inherit the boolean field, and add a counter and limit field.
  It should override the activate method so that the boolean state is
  flipped after the activate method is called <i>count</i> times.  The
  constructor for NthToggle should use the constructor for Toggle to
  inherit the boolean field and value() method.</p>
<p>These classes are also used in the <a href="../objinst/">Object
  Instantiation</a> test.</p>
<p>The correct output looks like <a href="/bench/methcall/Output">this</a>.</p>

<h4>Observations</h4>
<p>In Perl, we could &nbsp; <code>use fields;</code> &nbsp; to declare the
  attributes of our objects, but this only gives marginal speedup on this
  test (and a significant slowdown on the <a href="../objinst/">Object
  Instantiation</a> test).</p>
<p>The <a href="methcall.se">SmallEiffel solution</a> (submitted by
  Steve Thompson) is implemented in multiple files.  See also the <a
  href="../Include/toggle.e">Toggle</a> class and the <a
  href="../Include/nth_toggle.e">Nth_Toggle</a> class.</p>

<h4><a href="alt/">Alternates</a></h4>
<p><i>This section is for displaying alternate solutions that are either
  slower than ones above or perhaps don't quite meet my criteria for
  the competition, but are otherwise worthy of comment.</i></p>
<ul>
  <li>Miguel Sofer contributed a solution in <a href="alt/methcall.tcl2.tcl">
  pure Tcl</a>.  Since there are a few competing techniques for doing OO
  programming in Tcl, and this program was about 3-4 times slower than the
  Perl program (the current slowest contestant), we thought it best to make
  this an alternate.  I would like to try iTcl, but could not find any
  version of it that was designed to work with Tcl8.3.2.</li>
  <li>Here's an alternate <a href="alt/methcall.cmucl2.cmucl">CMUCL</a>
  program that uses defclass to define objects as opposed to the use
  of defstruct in the current entry.</li>
</ul>

  </tr>
</table>
<?php require("../../html/footer.php"); ?>
