<?php

// customize your ARC2 location
     include_once("/Library/WebServer/Documents/ARC2_IOWA/semsol-arc2-1bb23a0/ARC2.php");

     $dbpconfig = array
     (
// customize your endpoint address
     "remote_store_endpoint" => "http://localhost:3030/ds/query",
     );
     $store = ARC2::getRemoteStore($dbpconfig);
     if ($errs = $store->getErrors())
         {
           echo "<h1>getRemoteSotre error<h1>" ;
         }
     $rad=null;
     if(isset($_POST['query']))
     {
       $rad=$_POST['query'];
       if($rad=="vendorRadio" || $rad=="zipRadio")
       {
         $zip=null;
         $vendorname=null;
         $storename=null;

              $zip=$_POST["zip"];
               $storename=$_POST["store"];
               $vendorname=$_POST["Vendor"];

               if($zip=='' || $zip==null)
                $zip='?zipcode';

               if($storename=='' || $storename==null){
                 $storename='?storename';
               }
                else{
                    $storename='"'.$storename.'"';
                }


               if($vendorname=='' || $vendorname==null)
                $vendorname='?vendorname';
                else
                $vendorname='"'.$vendorname.'"';

               $query ='
               PREFIX ex: <http://www.semanticweb.org/ketaki/dbi/>
               SELECT DISTINCT ?city ?vendorname ?storename ?address ?zipcode
               WHERE {
                ?vendor ex:hasStore ?store .
                ?vendor ex:VendorName '.$vendorname.' .
                ?store ex:StoreName '.$storename.' .
                ?store ex:Zip_Code '.$zip.' .
                ?store ex:Address ?address .
                ?store ex:inCity ?city .
                ?store ex:sells ?item .
                ?item ex:soldFor ?sale .
                ?sale ex:SaleAmount ?amount .
               } ';
               echo "<table border='1' align='center'>" ;
               echo "<tr><td>CITY</td><td>VENDORNAME</td><td>STORENAME</td><td>ADDRESS</td><td>ZIP</td></tr>";
                         $rows = $store->query($query, 'rows'); /* execute the query */

                          if ($errs = $store->getErrors())
                          {
                             echo "Query errors" ;
                             print_r($errs);
                          }

              /* display the results in an HTML table */
              foreach( $rows as $row )
              {
                /* loop for each returned row */
                if ($zip != '?zipcode' && $vendorname == '?vendorname' && $storename == '?storename'){

                  $arr= explode("/",$row['city']);
                  $cityval = $arr[count($arr)-1];
                  print "<tr><td>" .$cityval. "</td><td>" .
                    $row['vendorname']. "</td><td>" .
                    $row['storename']. "</td><td>" .
                    $row['address']. "</td><td>" .
                    $zip. "</td></tr>";
                }
                else if ($zip == '?zipcode' && $vendorname != '?vendorname' && $storename == '?storename') {
                  $arr= explode("/",$row['city']);
                  $cityval = $arr[count($arr)-1];
                  print "<tr><td>" .$cityval. "</td><td>"  .
                    $vendorname. "</td><td>" .
                    $row['storename']. "</td><td>" .
                    $row['address']. "</td><td>" .
                    $row['zipcode']. "</td></tr>";
                }
                else if ($zip == '?zipcode' && $vendorname == '?vendorname' && $storename != '?storename'){
                  $arr= explode("/",$row['city']);
                  $cityval = $arr[count($arr)-1];
                  print "<tr><td>" .$cityval. "</td><td>"  .
                    $row['vendorname']. "</td><td>" .
                    $storename. "</td><td>" .
                    $row['address']. "</td><td>" .
                    $row['zipcode']. "</td></tr>";
                }
                else{
                  $arr= explode("/",$row['city']);
                  $cityval = $arr[count($arr)-1];
                  print "<tr><td>" .$cityval. "</td><td>"  .
                    $row['vendorname']. "</td><td>" .
                    $row['storename']. "</td><td>" .
                    $row['address']. "</td><td>" .
                    $row['zipcode']. "</td></tr>";
                }
              }
              echo "</table>" ;
          }
          else if($rad=="storeRadio")
          {
                $storename=null;
                  $storename=$_POST["store"];

                  if($storename=='' || $storename==null){
                    $storename='?storename';
                  }
                   else{
                       $storename='"'.$storename.'"';
                   }
                  $query ='
                  PREFIX ex: <http://www.semanticweb.org/ketaki/dbi/>
                  SELECT DISTINCT ?city ?vendorname ?storename ?address ?zipcode
                  WHERE {
                   ?vendor ex:hasStore ?store .
                   ?vendor ex:VendorName ?vendorname .
                   ?store ex:StoreName '.$storename.' .
                   ?store ex:Zip_Code ?zip .
                   ?store ex:Address ?address .
                   ?store ex:inCity ?city .
                   ?store ex:sells ?item .
                   ?item ex:soldFor ?sale .
                   ?sale ex:SaleAmount ?amount .
                  } ';
                  echo "<table border='1' align='center'>" ;
                  echo "<tr><td>STORE</td><td>VENDOR</td></tr>";
                            $rows = $store->query($query, 'rows'); /* execute the query */

                             if ($errs = $store->getErrors())
                             {
                                echo "Query errors" ;
                                print_r($errs);
                             }

                 /* display the results in an HTML table */
                 foreach( $rows as $row )
                 {
                   /* loop for each returned row */
                   if ($storename != '?storename'){
                        print "<tr><td>".$storename."</td><td>" .
                        $row['vendorname']."</td></tr>";
                   }
                   else{
                       print "<tr><td>".$row['storename']. "</td><td>" .
                       $row['vendorname']. "</td></tr>" ;
                   }
                 }
                 echo "</table>" ;
          }
          else if($rad=="cityPopularRadio")
          {
            $citypop=$_POST['cityPopular'];
            $query='
            PREFIX ex: <http://www.semanticweb.org/ketaki/dbi/>
            PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>
            PREFIX op: <http://www.w3.org/2005/xpath-functions/collation/codepoint>
            SELECT ?itemname (SUM(?volume) as ?totalsale)
            where
            {
              	?store ex:inCity ex:'.$citypop.'  .
              	?store ex:sells ?item .
              	?item ex:Description ?itemname .
              	?item ex:soldFor ?sale .
                ?sale ex:VolumeSold ?volume .
            }
            GROUP BY ?itemname
            ORDER BY DESC (?totalsale) LIMIT 1 ';
            $rows = $store->query($query, 'rows'); /* execute the query */

             if ($errs = $store->getErrors())
             {
                echo "Query errors" ;
                print_r($errs);
             }

             /* display the results in an HTML table */
             echo "<table border='1' align='center'>" ;
             echo "<tr><td>POPULAR LIQUOR</td></tr>";
             foreach( $rows as $row )
             {
               /* loop for each returned row */
               print "<tr><td>" .$row['itemname']. "</td></tr>";
             }
             echo "</table>" ;

          }
          else if($rad=="cityProfitRadio")
          {

            $citypro=$_POST['cityProfit'];
            $query='
            PREFIX ex: <http://www.semanticweb.org/ketaki/dbi/>
            PREFIX op: <http://www.w3.org/2005/xpath-functions/collation/codepoint>
            SELECT ?storename (SUM(?cost*?volume) as ?abc)
            WHERE {
            ?store ex:inCity  ex:'.$citypro.' .
            ?store ex:StoreName ?storename .
            ?store ex:sells ?item .
            ?item ex:soldFor ?sale .
            ?item ex:Cost ?cost .
            ?sale ex:VolumeSold ?volume.
            ?sale ex:SaleAmount ?amount .
            }
            GROUP BY ?storename
            ORDER BY DESC (?abc) LIMIT 1 ';
            $rows = $store->query($query, 'rows'); /* execute the query */
            if ($errs = $store->getErrors())
            {
                    echo "Query errors" ;
                    print_r($errs);
            }
            /* display the results in an HTML table */
            echo "<table border='1' align='center'>" ;
            echo "<tr><td>STORE WITH MAXIMUM PROFIT</td></tr>";
            foreach( $rows as $row )
            {
                   /* loop for each returned row */
                   print "<tr><td>" .$row['storename']. "</td></tr>";
            }
            echo "</table>" ;
          }
      }

  ?>
