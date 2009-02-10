<?php
// Copyright (c) Isaac Gouy 2005-2009


function Weights($Tests, $Action, $Vars){
   $w = array(); $wd = array();
   foreach($Tests as $t){
      $link = $t[TEST_LINK];
      
      if (isset($Vars[$link]) && (strlen($Vars[$link]) == 1)
            && (ereg("^[0-9]$",$Vars[$link]))){ $x = intval($Vars[$link]); }
      else { $x = intval($t[TEST_WEIGHT]); }

      $w[$link] = $x;
      $wd[$link] = intval($t[TEST_WEIGHT]);
   }


   $Metrics = array('xfullcpu' => 1, 'xmem' => 0, 'xloc' => 0);
   foreach($Metrics as $k => $v){

      if (isset($Vars[$k]) && (strlen($Vars[$k]) == 1)
            && (ereg("^[0-9]$",$Vars[$k]))){ $x = intval($Vars[$k]); }
      else { $x = $v; }

      $w[$k] = $x;
      $wd[$k] = $v;
   }

   if ($Action=='reset'){ $w = $wd; }

   // normalize weights
   $minWeight = 0;
   $maxWeight = 5;

   foreach($w as $k => $v){
      if ($v > $maxWeight){ $w[$k] = $maxWeight; }
      elseif ($v < $minWeight){ $w[$k] = $minWeight; }
   }
   return $w;
}


function SelectedLangs($Langs, $Action, $Vars){
   $w = array(); $wd = array();
   $max = 15; $count = 0;
   foreach($Langs as $lang){
      $link = $lang[LANG_LINK];
      if (isset($Vars[$link])){ $w[$link] = 1; $count++; }
      if ($lang[LANG_SELECT]){ $wd[$link] = 1; }
      if ($count == $max){ break; }
   }
   if ($Action=='reset'||sizeof($w)==0){ $w = $wd; }
   return $w;
}


// Data filtering and summary ///////////////////////////////////////////


function WeightedData($FileName,&$Tests,&$Langs,&$Incl,&$Excl,&$W,$HasHeading=TRUE){
   $f = @fopen($FileName,'r') or die ('Cannot open $FileName');
   if ($HasHeading){ $row = @fgetcsv($f,1024,','); }

   // assume no values larger than 1,000,000
   $mins = array();
   foreach($Tests as $k => $v){ $mins[$k] = array(1000000,1000000,1000000); }

   $data = array();
   $timeout = array();
   while (!@feof ($f)){
      $row = @fgetcsv($f,1024,',');
      if (!is_array($row)){ continue; }

      $test = $row[DATA_TEST];
      $lang = $row[DATA_LANG];
      settype($row[DATA_ID],'integer');
      $id = $row[DATA_ID];
      $key = $test.$lang.strval($id);

      // accumulate all acceptable datarows, exclude duplicates

      if (isset($Incl[$test]) && isset($Incl[$lang]) &&
               isset($Langs[$lang]) &&
                  !isset($Excl[$key])){
                    
            // this isn't quite correct doesn't take account of more than one program
            if (PROGRAM_TIMEOUT == $row[DATA_STATUS]){
               if (!isset($timeout[$lang])){ $timeout[$lang] = 1; }
               else { $timeout[$lang]++; }
            }

            if ($row[DATA_STATUS] == 0 && (
                  ($row[DATA_TIME] > 0 && (!isset($data[$lang][$test]) ||
                     $row[DATA_TIME] < $data[$lang][$test][DATA_TIME])))){

               $data[$lang][$test] = $row;

               $mt = &$mins[$test];
               if (($row[DATA_TIME] < $mt[CPU_MIN]) && $row[DATA_TIME] > 0.0){
                  $mt[CPU_MIN] = $row[DATA_TIME];
               }
               if (($row[DATA_MEMORY] < $mt[MEM_MIN]) && $row[DATA_MEMORY] > 0){
                  $mt[MEM_MIN] = $row[DATA_MEMORY];
               }
               if (($row[DATA_GZ] < $mt[GZ_MIN]) && $row[DATA_GZ] > 0){
                  $mt[GZ_MIN] = $row[DATA_GZ];
               }
            }

      }
   }
   @fclose($f);

   $score = array();
   foreach($data as $k => $test){
//      if ((!isset($timeout[$k]) || ($timeout[$k] < 5)) && (sizeof($test) > 9)){
      if (sizeof($test)/sizeof($Tests) > 0.5){

         $s = 0.0; $ws = 0.0; $include = 0.0;
         foreach($test as $t => $v){
            $mt = &$mins[$t];

            $w1 = $W[$t] * $W['xfullcpu'];
            $w2 = $W[$t] * $W['xmem'];
            $w3 = $W[$t] * $W['xloc'];

            if ($w1>0){
              $val = $v[DATA_TIME];
              if ($val > 0){
                 $s += log($val/$mt[CPU_MIN])*$w1;
                 $ws += $w1;
                 $include += $val;
              }
            }
            if ($w2>0){
              $val = $v[DATA_MEMORY];
              if ($val > 0){
                 $s += log($val/$mt[MEM_MIN])*$w2;
                 $ws += $w2;
                 $include += $val;
              }
            }
            if ($w3>0){
              $val = $v[DATA_GZ];
              if ($val > 0){
                 $s += log($val/$mt[GZ_MIN])*$w3;
                 $ws += $w3;
                 $include += $val;
              }
            }
         }
         if ($ws == 0.0){ $ws = 1.0; }
         if ($include > 0){ $score[$k] = array(1.0,exp($s/$ws),sizeof($Tests)-sizeof($test)); }
      }
   }

   uasort($score, 'CompareMeanScore');
   $ratio = array();
   foreach($score as $k => $v){
      if (!isset($first)){ $first = $v[SCORE_MEAN]; }
      if ($first==0){ $r = 0.0; } else { $r = $v[SCORE_MEAN]/$first; }
      $score[$k][SCORE_RATIO] = $r;
      $ratio[] = $r;   // for chart
   }

   return array($score,$ratio);
}



function FullUnweightedData($FileName,&$Tests,&$Langs,&$Incl,&$Excl,&$Plot,$HasHeading=TRUE){
   // expect to encounter more than one DATA_TESTVALUE for each test 
   $f = @fopen($FileName,'r') or die ('Cannot open $FileName');
   if ($HasHeading){ $row = @fgetcsv($f,1024,','); }

   $mins = array();
   foreach($Tests as $k => $v){ $mins[$k] = array(); }

   $data = array();
   $timeout = array();
   while (!@feof ($f)){
      $row = @fgetcsv($f,1024,',');
      if (!is_array($row)){ continue; }

      $test = $row[DATA_TEST];
      $lang = $row[DATA_LANG];
      settype($row[DATA_ID],'integer');
      $id = $row[DATA_ID];
      $testvalue = $row[DATA_TESTVALUE];
      $key = $test.$lang.strval($id);

      // accumulate all acceptable datarows, exclude duplicates

      if (isset($Incl[$test]) && isset($Incl[$lang]) && isset($Langs[$lang]) &&
               ($Tests[$test][TEST_WEIGHT]>0) &&
                  !isset($Excl[$key])){

            if ($row[DATA_STATUS] == 0 && (
                  ($row[DATA_TIME] > 0 && (!isset($data[$lang][$test][$testvalue]) ||
                     $row[DATA_TIME] < $data[$lang][$test][$testvalue][DATA_TIME])))){

               $data[$lang][$test][$testvalue] = $row;

               if (!isset($mins[$test][$testvalue])){
                  $mins[$test][$testvalue] = $row[DATA_TIME];
               } else {
                  if ($row[DATA_TIME] < $mins[$test][$testvalue]){
                     $mins[$test][$testvalue] = $row[DATA_TIME];
                  }
               }
            }

      }
   }
   @fclose($f);
   
   
   // when there are multiple N values this isn't quite right
   // we might have taken times from different programs
   // for different N values for the same language implementation

   $score = array();
   foreach($data as $k => $test){
      if (sizeof($test)/sizeof($Tests) > 0.5){

         $s = array(); $include = 0.0;
         foreach($test as $t => $testvalues){
            foreach($testvalues as $tv => $v){
               $val = $v[DATA_TIME];
               if ($val > 0){
                  $s[] = log10($val/$mins[$t][$tv])*2.0;
                  $include += $val;
               }
            }
         }
         if ($include > 0){ $score[$k] = Percentiles($s); }
      }
   }
   uasort($score,'CompareMedian');

   $labels = array();
   $stats = array();
   foreach($score as $k => $test){
      $label = ' '.$Langs[$k][LANG_FULL];
      if (isset($Plot[$k])){
         $labels[] = $label;
         $stats[] = $test;
      }
   }
   return array($score,$labels,$stats);
}



function Percentiles($a){
   sort($a);
   $n = sizeof($a);
   $mid = floor($n / 2);
   if ($n % 2 != 0){
      $median = $a[$mid];
      $lower = Median( array_slice($a,0,$mid+1) ); // include median in both quartiles
      $upper = Median( array_slice($a,$mid) );
   } else {
      $median = ($a[$mid-1] + $a[$mid]) / 2.0;
      $lower = Median( array_slice($a,0,$mid) );
      $upper = Median( array_slice($a,$mid) );
   }
   $maxwhisker = ($upper - $lower) * 1.5;
   $xlower = ($lower - $maxwhisker < $a[0]) ? $a[0]: $lower - $maxwhisker;
   $xupper = ($upper + $maxwhisker > $a[$n-1]) ? $a[$n-1] : $upper + $maxwhisker;
   // what about outliers?
   return array($xlower,$lower,$median,$upper,$xupper);
}


function Median($a){
   $n = sizeof($a);
   $mid = floor($n / 2);
   return ($n % 2 != 0) ? $a[$mid] : ($a[$mid-1] + $a[$mid]) / 2.0;
}


function CompareMeanScore($a, $b){
   if ($a[SCORE_MEAN] == $b[SCORE_MEAN]) return 0;
   return  ($a[SCORE_MEAN] < $b[SCORE_MEAN]) ? -1 : 1;
}

function CompareMedian($a, $b){
   if ($a[STAT_MEDIAN] == $b[STAT_MEDIAN]) return 0;
   return  ($a[STAT_MEDIAN] < $b[STAT_MEDIAN]) ? -1 : 1;
}


// Formating ///////////////////////////////////////////

function PBlank($d){
   if ($d>0){ return number_format($d); }
   else { return "&nbsp;"; }
}


function MkDataSetMenu($DataSet){
   $dataSelected = "";
   $ndataSelected = "";
   if ($DataSet=='data'){ $dataSelected = 'selected="selected"'; } 
   else { $ndataSelected = 'selected="selected"'; }

   echo '<select name="d">', "\n";
   printf('<option value="data" %s>data for largest N</option>',
      $dataSelected); echo "\n";
   printf('<option value="ndata" %s>full data - smaller and largest N</option>',
      $ndataSelected); echo "\n";
}



?>
