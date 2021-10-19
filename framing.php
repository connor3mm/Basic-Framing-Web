<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Frame Price Estimator</title>
</head>
<body>
<div>
<h1>Frame Price Estimator</h1>

    <?php
    $width  = strip_tags(isset($_POST["width"]) ? $_POST["width"] : "");
    $height = strip_tags(isset($_POST["height"]) ? $_POST["height"] : "");
    $email  = strip_tags(isset($_POST["email"]) ? $_POST["email"] : "");
    $units  = isset($_POST["units"]) ? $_POST["units"] : "";
    $post   = isset($_POST["post"]) ? $_POST["post"] : "";
    $vat    = isset($_POST["vat"]) ? $_POST["vat"] : "";
    $emailList  = isset($_POST["emailList"]) ? $_POST["emailList"] : "";


    $emailBool = TRUE;
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) && isset($_POST["emailList"])) {
        $emailBool = FALSE;
    }

    $numVal     = TRUE;
    $heightVal  = TRUE;
    $widthVal   = TRUE;
    $valMessage = "";

    if ($units === "cm") {
        if ($height < 20 || $height > 200) {
            $heightVal = FALSE;
        }
        if ($width < 20 || $width > 200) {
            $widthVal = FALSE;
        }
        $valMessage = "20 and 200";
    }

    if ($units === "inch") {
        if ($height < 7.8 || $height > 78.7) {
            $heightVal = FALSE;
        }
        if ($width < 7.8 || $width > 78.7) {
            $widthVal = FALSE;
        }
        $valMessage = "7.8 and 78.7";
    }

    if ($units === "mm") {
        if ($height < 200 || $height > 2000) {
            $heightVal = FALSE;
        }
        if ($width < 200 || $width > 2000) {
            $widthVal = FALSE;
        }
        $valMessage = "200 and 2000";
    }

    if ($heightVal === FALSE || $widthVal === FALSE) {
        echo "Make sure height are width and numeric and between $valMessage whilst using $units.<br><br>";
    }



    if (($emailBool === FALSE) || ($heightVal === FALSE) || ($widthVal === FALSE) || ($height === "") || ($width === "")) { //conditions for erroneous submission
        //Need to output the form
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            echo "Please complete or fix fields. This includes:"; //Error message
        }

        if ($widthVal === FALSE) {
            echo " WIDTH; ";
        }
        if ($heightVal === FALSE) {
            echo " HEIGHT; ";
        }
        if($emailBool === FALSE){
            echo "EMAIL;";
        }
        ?>


<p>Please enter your photo sizes to get a framing cost estimate.</p>
<form action = "framing.php" method="POST">

    <p>Photo Width: <input type = "text" name ="width" placeholder="Width" value="<?php echo $width; ?>" />
        <select name="units" id="units">
            <option value= "mm"   <?php if($units === "mm")   echo "selected"; ?>>   mm</option>
            <option value= "cm"   <?php if($units === "cm")   echo "selected"; ?>>   cm</option>
            <option value= "inch" <?php if($units === "inch") echo "selected"; ?>> inch</option>
        </select></p>

    <p>Photo Height: <input type = "text" name ="height" placeholder="Height" value="<?php echo $height; ?>"/> </p>

    <p>Postage:
            <input type="radio" name="post" value="economy"  <?php if($post === "economy")  echo "checked"; ?> checked> Economy
            <input type="radio" name="post" value="rapid"    <?php if($post === "rapid")    echo "checked"; ?>> Rapid
            <input type="radio" name="post" value="next day" <?php if($post === "next day") echo "checked"; ?>> Next Day
    </p>


    <p>
        <input type="checkbox" name="vat[]" value="VAT" <?php if(isset($_POST["vat"])) echo 'checked="checked"'; ?>checked> Include VAT in price<br>
    </p>

    <p>
        <input type="checkbox" name="emailList[]" value="emailList" <?php if(isset($_POST["emailList"])) echo 'checked="checked"'; ?>> Receive mail and future information about my framing calculation<br>
    </p>

    <?php
    if ($emailBool === FALSE) {
        echo "The email address should be non-empty and in a valid email format: example@mail.com";
    }
    ?>

    <p>
        Email: <input type = "text" name ="email" placeholder="email address" value="<?php echo $email; ?>"/>
    </p>

    <p><input type = "submit"/> </p>
</form>

    <?php
}else {
        if ($units === "cm") {
            $width  = $width / 100;
            $height = $height / 100;
        } else if ($units === "inch") {
            $width  = $width / 39.37;
            $height = $height / 39.37;
        } else {
            $width  = $width / 1000;
            $height = $height / 1000;
        }

        $edge = max($width, $height);
        $postage = 0;

        if ($post === "economy") {
            $postage = (2 * $edge) + 4;

        } else if ($post === "rapid") {
            $postage = (3 * $edge) + 8;

        } else if ($post === "next day") {
            $postage = (5 * $edge) + 12;
        }


        $area = $width * $height;
        $price   = round(($area * $area) + (100 * $area) + 6, 2);
        $postage = number_format($postage, 2);


        if ($vat) {
            $priceVat   = $price + ($price * 0.2);
            $postageVat = $postage + ($postage * 0.2);

            $total      = number_format($priceVat + $postageVat,2);
            $priceVat   = number_format($priceVat, 2);
            $postageVat = number_format($postageVat, 2);

            $output = "Your frame will cost £$priceVat plus $post postage of £$postageVat giving a total price of £$total including VAT.";

        } else {

            $total  = number_format($price + $postage,2);
            $price  = number_format($price, 2);
            $output = "Your frame will cost £$price plus $post postage of £$postage giving a total price of £$total without VAT.";
        }

        echo $output;

        if (isset($_POST["emailList"])) {
            echo "<br><br>A confirmation email has been sent to $email";

            $message = "https://devweb2020.cis.strath.ac.uk/~ykb20160/317a1/index.html";
            $msg     = "Thank you for shopping with us.\n$output\nPlease use the following link to place your order: $message";

            //Send email
            mail($email, "Framing Confirmation", $msg);


            //Connecting to DB and inserting into it
            require_once "password.php";

            //Connect to MySQL
            $host   = "devweb2021.cis.strath.ac.uk";
            $user   = "ykb20160";
            $pass   = get_password();
            $dbname = "ykb20160";
            $conn   = new mysqli($host, $user, $pass, $dbname);


            $date   = date('d-m-y h:i:s');
            $total  = number_format($price + $postage,2);
            $width  = number_format($width, 2);
            $height = number_format($height, 2);


            $sql = "INSERT INTO `framingrequests` (`id`, `width`, `height`, `postage`, `email`, `price`, `time`) VALUES (NULL,'$width','$height','$post','$email','$total','$date')";

            $result = $conn->query($sql);


            //Disconnect
            $conn->close();
        }
    }
    ?>

</div>
</body>
</html>
