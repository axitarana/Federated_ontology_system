<?php

// customize your ARC2 location
     include_once("/Library/WebServer/Documents/ARC2_IOWA/semsol-arc2-1bb23a0/ARC2.php");

     $dbpconfig = array
     (
// customize your endpoint address
     "remote_store_endpoint" => "http://localhost:3030/ds/query",
     );
     $store = ARC2::getRemoteStore($dbpconfig);
     $max=null;
     $min=null;
     if ($errs = $store->getErrors())
         {
           echo "<h1>getRemoteSotre error<h1>" ;
         }
    if(isset($_POST['max']))
       {
          $max= $_POST['max'];
          if($max=="vendor")
          {
            $query='
               PREFIX ex: <http://www.semanticweb.org/ketaki/dbi/>
               SELECT ?vendorname (SUM(?amount) as ?totalsale)
               WHERE {
                ?vendor ex:hasStore ?store .
                ?vendor ex:VendorName ?vendorname .
                ?store ex:inCity ?city .
                ?store ex:sells ?item .
                ?item ex:soldFor ?sale .
                ?sale ex:SaleAmount ?amount .
               }
               GROUP BY ?vendorname
               ORDER BY DESC ($totalsale) LIMIT 1 ';
               $rows = $store->query($query, 'rows'); /* execute the query */

                if ($errs = $store->getErrors())
                {
                   echo "Query errors" ;
                   print_r($errs);
                }

                /* display the results in an HTML table */
                echo "<table border='1' align='center'>" ;
                echo "<tr><td>Vendor</td><td>TotalSale</td></tr>";
                foreach( $rows as $row )
                {
                  /* loop for each returned row */
                  print "<tr><td>" .$row['vendorname']. "</td><td>$" .

                    $row['totalsale']. "</td></tr>";
                }
                echo "</table>" ;
            }
            else if($max=="city"){
              $query='
              PREFIX ex: <http://www.semanticweb.org/ketaki/dbi/>
              SELECT ?city (SUM(?amount) as ?totalsale)
              WHERE {
                ?vendor ex:hasStore ?store .
                ?vendor ex:VendorName ?vendorname .
                ?store ex:inCity ?city .
                ?store ex:sells ?item .
                ?item ex:soldFor ?sale .
                ?sale ex:SaleAmount ?amount .
              }
               GROUP BY ?city
               ORDER BY DESC (?totalsale) LIMIT 1 ';
               $rows = $store->query($query, 'rows'); /* execute the query */

                if ($errs = $store->getErrors())
                {
                   echo "Query errors" ;
                   print_r($errs);
                }

                /* display the results in an HTML table */
                echo "<table border='1' align='center'>" ;
                echo "<tr><td>City</td><td>TotalSale</td></tr>";
                foreach( $rows as $row )
                {
                  /* loop for each returned row */
                  $arr= explode("/",$row['city']);
                  $cityval = $arr[count($arr)-1];
                  print "<tr><td>" .$cityval. "</td><td>$" .

                    $row['totalsale']. "</td></tr>";
                }
                echo "</table>" ;
            }
          //  Displaying Selected Value
       }
      else if(isset($_POST['min']))
       {
         $min=$_POST['min'];
         if($min=='vendor')
         {
           $query='
              PREFIX ex: <http://www.semanticweb.org/ketaki/dbi/>
              SELECT ?vendorname (SUM(?amount) as ?totalsale)
              WHERE {
               ?vendor ex:hasStore ?store .
               ?vendor ex:VendorName ?vendorname .
               ?store ex:inCity ?city .
               ?store ex:sells ?item .
               ?item ex:soldFor ?sale .
               ?sale ex:SaleAmount ?amount .
              }
              GROUP BY ?vendorname
              ORDER BY (?totalsale) LIMIT 1 ';
              $rows = $store->query($query, 'rows'); /* execute the query */

               if ($errs = $store->getErrors())
               {
                  echo "Query errors" ;
                  print_r($errs);
               }

               /* display the results in an HTML table */
               echo "<table border='1' align='center'>" ;
               echo "<tr><td>Vendor</td><td>TotalSale</td></tr>";
               foreach( $rows as $row )
               {
                 /* loop for each returned row */
                 print "<tr><td>" .$row['vendorname']. "</td><td>$" .

                   $row['totalsale']. "</td></tr>";
               }
               echo "</table>" ;
           }
          //  Displaying Selected Value
          else if($min=='city') {
            $query='
            PREFIX ex: <http://www.semanticweb.org/ketaki/dbi/>
           SELECT ?city (SUM(?amount) as ?totalsale)
           WHERE {
            ?vendor ex:hasStore ?store .
            ?vendor ex:VendorName ?vendorname .
            ?store ex:inCity ?city .
            ?store ex:sells ?item .
            ?item ex:soldFor ?sale .
            ?sale ex:SaleAmount ?amount .
           }
           GROUP BY ?city
           ORDER BY (?totalsale) LIMIT 1 ';
          $rows = $store->query($query, 'rows'); /* execute the query */

           if ($errs = $store->getErrors())
           {
              echo "Query errors" ;
              print_r($errs);
           }

           /* display the results in an HTML table */
           echo "<table border='1' align='center'>" ;
           echo "<tr><td>City</td><td>TotalSale</td></tr>";
           foreach( $rows as $row )
           {
             /* loop for each returned row */
             $arr= explode("/",$row['city']);
             $cityval = $arr[count($arr)-1];
             print "<tr><td>" .$cityval. "</td><td>$" .

               $row['totalsale']. "</td></tr>";
           }
           echo "</table>" ;
       }
     }


  ?>
