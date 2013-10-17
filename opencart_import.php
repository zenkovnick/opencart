<?php

    setlocale(LC_ALL, 'ru_RU.utf8');
    if($_POST['submit'] && $_FILES['csv'] && $_POST['rates'] && is_numeric($_POST['rates'])){
        $path = "uploads";
        if (move_uploaded_file($_FILES['csv']['tmp_name'], "{$path}/{$_FILES['csv']['name']}")) {

            $mysqli = new mysqli('localhost',"root","root","tradeunit");
            if (!$mysqli->set_charset("utf8")) {
                printf("Ошибка при загрузке набора символов utf8: %s\n", $mysqli->error);
            }

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
                $index_cat = 0;
                $max_prod_id = null;
                if ($result = $mysqli->query("SELECT max(product_id) as max FROM product", MYSQLI_USE_RESULT)) {
                    while($row = mysqli_fetch_array($result))
                    {
                        $max_prod_id = $row['max'];
                    }

                    $result->close();
                }

                $max_cat_id = null;
                if ($result = $mysqli->query("SELECT max(category_id) as max FROM category_description", MYSQLI_USE_RESULT)) {
                    while($row = mysqli_fetch_array($result))
                    {
                        $max_cat_id = $row['max'];
                    }

                    $result->close();
                }
                $languages_ids = array();
                if ($result = $mysqli->query("SELECT language_id as id FROM language", MYSQLI_USE_RESULT)) {
                    while($row = mysqli_fetch_array($result))
                    {
                        $languages_ids[] = $row['id'];
                    }

                    $result->close();
                }


                if(count($languages_ids) > 0){
                    foreach($csv_result as $csv_row) {
                        $cat = $mysqli->query("SELECT * FROM category_description WHERE name LIKE '".$csv_row[0]."'");
                        if($cat->num_rows == 0){
                            foreach($languages_ids as $l_id){
                                $mysqli->query("INSERT INTO category_description(category_id, language_id, name) VALUES (".
                                    ($max_cat_id+$index_cat+1).", ".$l_id.", '".$csv_row[0]."')", MYSQLI_USE_RESULT);
                            }
                            $query = 'INSERT INTO category(category_id, parent_id, image, top, `column`, sort_order, status) VALUES ('.
                                ($max_cat_id+$index_cat+1).', 0, "", 1, 1, 0, 1)';
                            if (!$mysqli->query($query, MYSQLI_USE_RESULT)){
                                echo "Query: {$query} was not completed";
                                printf("Ошибка: %s\n", $mysqli->error);
                            }

                            $query = 'INSERT INTO category_to_store(category_id, store_id) VALUES ('.
                                ($max_cat_id+$index_cat+1).', 0)';
                            if (!$mysqli->query($query, MYSQLI_USE_RESULT)){
                                echo "Query: {$query} was not completed";
                                printf("Ошибка: %s\n", $mysqli->error);
                            }

                            $index_cat++;
                        }
                        $cat = $mysqli->query("SELECT * FROM category_description WHERE name LIKE '".$csv_row[1]."'");
                        if($cat->num_rows == 0){
                            foreach($languages_ids as $l_id){
                                $mysqli->query("INSERT INTO category_description(category_id, language_id, name) VALUES (".
                                    ($max_cat_id+$index_cat+1).", ".$l_id.", '".$csv_row[1]."')", MYSQLI_USE_RESULT);
                            }
                            $query = 'INSERT INTO category(category_id, parent_id, image, top, `column`, sort_order, status) VALUES ('.
                                ($max_cat_id+$index_cat+1).', (SELECT DISTINCT category_id FROM category_description cd WHERE cd.name LIKE "'.$csv_row[0].'"), "", 1, 1, 0, 1)';

                            if (!$mysqli->query($query, MYSQLI_USE_RESULT)){
                                echo "Query: {$query} was not completed";
                                printf("Ошибка: %s\n", $mysqli->error);
                            }

                            $query = 'INSERT INTO category_to_store(category_id, store_id) VALUES ('.
                                ($max_cat_id+$index_cat+1).', 0)';
                            if (!$mysqli->query($query, MYSQLI_USE_RESULT)){
                                echo "Query: {$query} was not completed";
                                printf("Ошибка: %s\n", $mysqli->error);
                            }

                            $index_cat++;
                        }

                        $cat = $mysqli->query("SELECT * FROM category_description WHERE name LIKE '".$csv_row[2]."'");
                        if($cat->num_rows == 0){
                            foreach($languages_ids as $l_id){
                                $mysqli->query("INSERT INTO category_description(category_id, language_id, name) VALUES (".
                                    ($max_cat_id+$index_cat+1).", ".$l_id.", '".$csv_row[2]."')", MYSQLI_USE_RESULT);
                            }
                            $query = 'INSERT INTO category(category_id, parent_id, image, top, `column`, sort_order, status) VALUES ('.
                                ($max_cat_id+$index_cat+1).', (SELECT DISTINCT category_id FROM category_description cd WHERE cd.name LIKE "'.$csv_row[1].'"), "", 1, 1, 0, 1)';

                            if (!$mysqli->query($query, MYSQLI_USE_RESULT)){
                                echo "Query: {$query} was not completed";
                                printf("Ошибка: %s\n", $mysqli->error);
                            }

                            $query = 'INSERT INTO category_to_store(category_id, store_id) VALUES ('.
                                ($max_cat_id+$index_cat+1).', 0)';
                            if (!$mysqli->query($query, MYSQLI_USE_RESULT)){
                                echo "Query: {$query} was not completed";
                                printf("Ошибка: %s\n", $mysqli->error);
                            }

                            $index_cat++;
                        }

                    }
                }
                foreach($csv_result as $csv_row) {
                    $cat = $mysqli->query("SELECT * FROM category_description WHERE name LIKE '".$csv_row[0]."'");
                    if($cat->num_rows == 0){
                        foreach($languages_ids as $l_id){
                            $mysqli->query("INSERT INTO category_description(category_id, language_id, name) VALUES (".
                                ($max_cat_id+$index_cat+1).", ".$l_id.", '".$csv_row[0]."')", MYSQLI_USE_RESULT);
                        }
                        $index_cat++;
                    }
                    $cat = $mysqli->query("SELECT * FROM category_description WHERE name LIKE '".$csv_row[1]."'");
                    if($cat->num_rows == 0){
                        foreach($languages_ids as $l_id){
                            $mysqli->query("INSERT INTO category_description(category_id, language_id, name) VALUES (".
                                ($max_cat_id+$index_cat+1).", ".$l_id.", '".$csv_row[1]."')", MYSQLI_USE_RESULT);
                        }
                        $index_cat++;
                    }
                    $cat = $mysqli->query("SELECT * FROM category_description WHERE name LIKE '".$csv_row[2]."'");
                    if($cat->num_rows == 0){
                        foreach($languages_ids as $l_id){
                            $mysqli->query("INSERT INTO category_description(category_id, language_id, name) VALUES (".
                                ($max_cat_id+$index_cat+1).", ".$l_id.", '".$csv_row[2]."')", MYSQLI_USE_RESULT);
                        }
                        $index_cat++;
                    }
                }

                echo "Categories imported: {$index_cat} <br />";

                foreach($csv_result as $csv_row) {
                    if ($mysqli->query("INSERT INTO product(product_id, model, quantity, stock_status_id, shipping, price, status) VALUES (".
                        ($max_prod_id+$index+1).", '".$csv_row[3]."', 1, 4, 1, ".($csv_row[4] *$_POST['rates']).", 1)", MYSQLI_USE_RESULT)){

                        $product_id = $mysqli->insert_id;
                        $query1 = "INSERT INTO product_to_category(product_id, category_id) VALUES (".
                            $product_id.", (SELECT DISTINCT category_id FROM category_description cd WHERE cd.name LIKE '".$csv_row[0]."'))";
                        $query2 = "INSERT INTO product_to_category(product_id, category_id) VALUES (".
                            $product_id.", (SELECT DISTINCT category_id FROM category_description cd WHERE cd.name LIKE '".$csv_row[1]."'))";
                        $query3 = "INSERT INTO product_to_category(product_id, category_id) VALUES (".
                            $product_id.", (SELECT DISTINCT category_id FROM category_description cd WHERE cd.name LIKE '".$csv_row[2]."'))";
                        if (!$mysqli->query($query1, MYSQLI_USE_RESULT)){
                            echo "Query: {$query1} was not completed";
                        }
                        if (!$mysqli->query($query2, MYSQLI_USE_RESULT)){
                            echo "Query: {$query2} was not completed";
                        }
                        if (!$mysqli->query($query3, MYSQLI_USE_RESULT)){
                            echo "Query: {$query2} was not completed";
                        }

                        $query = 'INSERT INTO product_to_store(product_id, store_id) VALUES ('.
                            $product_id.', 0)';
                        if (!$mysqli->query($query, MYSQLI_USE_RESULT)){
                            echo "Query: {$query} was not completed";
                            printf("Ошибка: %s\n", $mysqli->error);
                        }

                        if(count($languages_ids) > 0){
                            foreach($languages_ids as $l_id){
                                $mysqli->query("INSERT INTO product_description(product_id, language_id, name, description) VALUES (".
                                    $product_id.", ".$l_id.", '".$csv_row[3]."', '".$csv_row[5]."')", MYSQLI_USE_RESULT);
                            }
                        }



                        $index++;

                    }
                }
                echo "Products imported: {$index} <br />";

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