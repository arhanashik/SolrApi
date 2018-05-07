<?php
/**
 * Created by PhpStorm.
 * User: W3E04
 * Date: 3/27/2018
 * Time: 12:53 PM
 */

$SOLR_BASE_URL = "http://localhost:8983/solr/";
$search_res = "All data";
$q = "destination_name:canada AND price:[1000.00 TO 3000.00]";
$fq = "";
$start = "0";
$rows = "20";
$fl = "";

if (isset($_GET['submit'])) {
    $q = $_GET['q'];
    $fq = $_GET['fq'];
    $start = $_GET['start'];
    $rows = $_GET['rows'];
    $fl = $_GET['fl'];

    if(empty($q)){
        $q = "destination_name:canada AND price:[1000.00 TO 3000.00]";
    }
    else{
        //$q = "destination_name:" . $q;
    }

    if(empty($fq)){
        $fq = "";

    }

    if(empty($strat)){
        $start = "0";

    }

    if(empty($rows)){
        $rows = "100";

    }

    if(empty($fl)){
        $fl = "";

    }

}

$core = "test_core"; //core name in which search will do
$param = array(
    'q' => $q, //query string
    'fq' => $fq, //filter query
    'start' => $start, //staring row
    'rows' => $rows, //num of rows
    'fl' => $fl, //field list
);

$URL = $SOLR_BASE_URL . $core . "/select?" . http_build_query($param);
$json = file_get_contents($URL);
$json_data = json_decode($json);
$objects = $json_data->response->docs;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Solr Core test</title>
    <h1>Solr Core test</h1>
    <p>By default first 100 rows are showing for <b>price '1000-3000 USD' and location 'canada'</b>. <br><br>Please, modify the search fields to get your expected results.</p>
</head>

<body>

    <form action="load_data.php" method="get" enctype="multipart/form-data">
        <label for="q">Query String</label><br>
        <input type="text" id="q" name="q" value="destination_name:canada AND price:[1000.00 TO 3000.00]"/><br><br>
        <label for="fq">Filter Query</label><br>
        <input type="text" id="fq" name="fq" /><br><br>
        <label for="start">Start Position</label><br>
        <input type="number" min="0" id="start" name="start" value="0" /><br><br>
        <label for="rows">Total rows</label><br>
        <input type="number" min="0" id="rows" name="rows" value="100" /><br><br>
        <label for="fl">Field List(separated with comma)</label><br>
        <input type="text" id="fl" name="fl" /><br><br>
        <input type="submit" id="submit" name="submit" value="Search" />
    </form>
    <!-- printing all files from s3 bucket -->
    <?php if(!empty($objects)){ ?>
        <h3><?php echo $search_res . " for query:<br>" . $URL;?></h3>
        <table class="table" border="1px" cellspacing="5px">
            <thead>
            <tr>
                <th>#</th>
                <th>Property ID</th>
                <th>Property Name</th>
                <th>Price</th>
                <th>Destination Name</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php $i = 0;?>
            <?php foreach ($objects as $object) : ?>
                <tr>
                    <?php $i++;?>
                    <th><?php echo $i; ?></th>
                    <th><?php if(!empty($object->property_id[0])) echo $object->property_id[0]; ?></th>
                    <th><?php if(!empty($object->property_name[0]))  echo $object->property_name[0]; ?></th>
                    <th><?php if(!empty($object->price[0]))  echo $object->price[0] . " USD"; ?></th>
                    <th><?php if(!empty($object->destination_name[0]))  echo $object->destination_name[0]; ?></th>

                    <?php
                    if(!empty($object->final_url[0]))
                        echo "<th><a href=".$object->final_url[0]."> See Details</a></th>";
                     if(!empty($object->image_url[0]))
                        echo "<th><a href=".$object->image_url[0]."> View Image</a></th>";
                    ?>

                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php
        }
        else echo "<br><br>Search for result.";
    ?>

</body>
</html>
