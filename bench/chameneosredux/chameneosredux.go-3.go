/* The Computer Language Benchmarks Game
 * http://shootout.alioth.debian.org/
 *
 * contributed by Roger Peppe
 */

package main

import (
   "flag";
   "fmt";
   "strconv";
)

const (
   blue   = iota;
   red;
   yellow;
   ncol;
)

var complement = [...]int{
   red | red<<2: red,
   red | yellow<<2: blue,
   red | blue<<2: yellow,
   yellow | red<<2: blue,
   yellow | yellow<<2: yellow,
   yellow | blue<<2: red,
   blue | red<<2: yellow,
   blue | yellow<<2: red,
   blue | blue<<2: blue,
}

var colname = [...]string{
   blue: "blue",
   red: "red",
   yellow: "yellow",
}

// information about the current state of a creature.
type info struct {
   colour   int;   // creature's current colour.
   name   int;   // creature's name.
}

// exclusive access data-structure kept inside meetingplace.
// if mate is nil, it indicates there's no creature currently waiting;
// otherwise the creature's info is stored in info, and
// it is waiting to receive its mate's information on the mate channel.
type rendez struct {
   n   int;      // current number of encounters.
   mate   chan<- info;   // creature waiting when non-nil.
   info   info;      // info about creature waiting.
}

// result sent by each creature at the end of processing.
type result struct {
   met   int;
   same   int;
}

var n = 600

func main() {
   flag.Parse();
   if flag.NArg() > 0 {
      n, _ = strconv.Atoi(flag.Arg(0))
   }

   for c0 := 0; c0 < ncol; c0++ {
      for c1 := 0; c1 < ncol; c1++ {
         fmt.Printf("%s + %s -> %s\n", colname[c0], colname[c1], colname[complement[c0|c1<<2]])
      }
   }

   pallmall([]int{blue, red, yellow});
   pallmall([]int{blue, red, yellow, red, yellow, blue, red, yellow, red, blue});
}

func pallmall(cols []int) {
   fmt.Print("\n");

   // invariant: meetingplace always contains a value unless a creature
   // is currently dealing with it (whereupon it must put it back).
   meetingplace := make(chan rendez, 1);
   meetingplace <- rendez{n: 0};

   ended := make(chan result);
   msg := "";
   for i, col := range cols {
      go creature(info{col, i}, meetingplace, ended);
      msg += " " + colname[col];
   }
   fmt.Println(msg);
   tot := 0;
   // wait for all results
   for _ = range (cols) {
      result := <-ended;
      tot += result.met;
      fmt.Printf("%v%v\n", result.met, spell(result.same, true));
   }
   fmt.Println(spell(tot, true));
}

// in this function, variables ending in 0 refer to the local creature,
// variables ending in 1 to the creature we've met.
func creature(info0 info, meetingplace chan rendez, ended chan result) {
   c0 := make(chan info);
   met := 0;
   same := 0;
   for {
      var othername int;
      // get access to rendez data and decide what to do.
      switch r := <-meetingplace; {
      case r.n >= n:
         // if no more meetings left, then send our result data and exit.
         meetingplace <- rendez{n: r.n};
         ended <- result{met, same};
         return;
      case r.mate == nil:
         // no creature waiting; wait for someone to meet us,
         // get their info and send our info in reply.
         meetingplace <- rendez{n: r.n, info: info0, mate: c0};
         info1 := <-c0;
         othername = info1.name;
         info0.colour = complement[info0.colour|info1.colour<<2];
      default:
         // another creature is waiting for us with its info;
         // increment meeting count,
         // send them our info in reply.
         r.n++;
         meetingplace <- rendez{n: r.n, mate: nil};
         r.mate <- info0;
         othername = r.info.name;
         info0.colour = complement[info0.colour|r.info.colour<<2];
      }
      if othername == info0.name {
         same++
      }
      met++;
   }
}

var digits = [...]string{"zero", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine"}

func spell(n int, required bool) string {
   if n == 0 && !required {
      return ""
   }
   return spell(n/10, false) + " " + digits[n%10];
}
