<?php
    if($_POST['submit']){
        $path = "uploads";
        if (move_uploaded_file($_FILES['csv']['tmp_name'], "{$path}/{$_FILES['csv']['name']}")) {

            $con=mysqli_connect('localhost',"root","root","tradeunit");

            if (mysqli_connect_errno($con))
            {
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
            } else {
                $file = fopen("{$path}/{$_FILES['csv']['name']}", 'r');
                while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
                    $result[] = $data;
                }
                fclose($file);
                var_dump($result);
            }

            mysqli_close($con);

        } else {
            print "There some errors!";
        }


    } else {
?>

<html>
    <head>

    </head>
    <body>
        <form action="" method="POST" enctype="multipart/form-data" >
            <input type="file" name="csv" />
            <input type="submit" value="Submit" name="submit">
        </form>
    </body>
</html>

<?php } ?>