<?php
    if($_POST['submit'] && $_FILES['csv'] && $_POST['rates'] && is_numeric($_POST['rates'])){
        $path = "uploads";
        if (move_uploaded_file($_FILES['csv']['tmp_name'], "{$path}/{$_FILES['csv']['name']}")) {

            $mysqli = new mysqli('localhost',"root","root","tradeunit");

            if ($mysqli->connect_errno)
            {
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
            } else {
                $csv_result = array();
                $file = fopen("{$path}/{$_FILES['csv']['name']}", 'r');
                while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
                    $csv_result[] = $data;
                }
                fclose($file);


                $index = 0;
                $max_prod_id = null;
                if ($result = $mysqli->query("SELECT max(product_id) as max FROM product", MYSQLI_USE_RESULT)) {
                    while($row = mysqli_fetch_array($result))
                    {
                        $max_prod_id = $row['max'];
                    }

                    $result->close();
                }

                foreach($csv_result as $csv_row) {
                    echo "<br /><br />";
                    if ($mysqli->query("INSERT INTO product(product_id, model, quantity, stock_status_id, shipping, price) VALUES (".
                        ($max_prod_id+$index+1).", '".$csv_row[3]."', 1, 4, 1, ".($csv_row[4] *$_POST['rates']).")", MYSQLI_USE_RESULT)){

                        $product_id = $mysqli->insert_id;
                        echo "New product id is {$product_id} <br />";
                        $query1 = "INSERT INTO product_to_category(product_id, category_id) VALUES (".
                            $product_id.", (SELECT DISTINCT category_id FROM category_description cd WHERE cd.name LIKE '".$csv_row[0]."'))";
                        $query2 = "INSERT INTO product_to_category(product_id, category_id) VALUES (".
                            $product_id.", (SELECT DISTINCT category_id FROM category_description cd WHERE cd.name LIKE '".$csv_row[1]."'))";
                        if (!$mysqli->query($query1, MYSQLI_USE_RESULT)){
                            echo "Query: {$query1} was not completed";
                        }
                        if (!$mysqli->query($query2, MYSQLI_USE_RESULT)){
                            echo "Query: {$query2} was not completed";
                        }

                        $index++;

                    }
                }

            }

            $mysqli->close();

        } else {
            print "There some errors!";
        }


    } else {
?>

<html>
    <head>
        <meta http-equiv="content-type" content="text/html;charset=utf-8">
    </head>
    <body>
        <form action="" method="POST" enctype="multipart/form-data" >
            <label for="csv">Файл для загрузки в CSV формате</label>
            <input type="file" name="csv" id="csv" />
            <label for="rates">Текущий курс доллара по отношению к гривне</label>
            <input type='text' name='rates' id='rates' />
            <input type="submit" value="Submit" name="submit">
        </form>
    </body>
</html>

<?php } ?>