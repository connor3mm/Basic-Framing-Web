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
    $width = strip_tags(isset($_POST["width"]) ? $_POST["width"] : "");
    $height = strip_tags(isset($_POST["height"]) ? $_POST["height"] : "");
    $units = isset($_POST["units"]) ? $_POST["units"] : "";
    $post = isset($_POST["post"]) ? $_POST["post"] : "";
    $vat = isset($_POST["vat"]) ? $_POST["vat"] : "";

    if ( ($width==="") || ($height==="")) { //conditions for erroneous submission
    //Need to output the form
    if ($_SERVER["REQUEST_METHOD"]==="POST"){
        echo "<p>Please complete all fields.</p>";//Error message
    }
    ?>


<p>Please enter your photo sizes to get a framing cost estimate.</p>
<form action = "framing.php" method="POST">

    <p>Photo Width: <input type = "text" name ="width" placeholder="Width" value="<?php echo $width; ?>" />
        <select name="units" id="units">
            <option value="mm" <?php if($units === "mm") echo "selected"; ?>> mm</option>
            <option value="cm" <?php if($units === "cm") echo "selected"; ?>> cm</option>
            <option value="inch" <?php if($units === "inch") echo "selected"; ?>> inch</option>

        </select></p>

    <p>Photo height: <input type = "text" name ="height" placeholder="height" value="<?php echo $height; ?>"/> </p>

    <p>Postage:
            <input type="radio" name="post" value="economy"<?php if($post === "economy") echo "checked"; ?>> Economy
            <input type="radio" name="post" value="rapid" <?php if($post === "rapid") echo "checked"; ?>> Rapid
            <input type="radio" name="post" value="next day" <?php if($post === "nextDay") echo "checked"; ?>> Next Day
    </p>

    <p>
        <input type="checkbox" name="vat[]" value="VAT" <?php if(isset($_POST["vat"])) echo 'checked="checked"'; ?>> Include VAT in price<br>
    </p>

    <p><input type = "submit"/> </p>
</form>
    <?php
}else{

        if($units === "cm") {
            $width = $width / 100;
            $height = $height / 100;
        }
        else if($units === "inch"){
            $width = $width/39.37;
            $height = $height/39.37;
        }
        else{
            $width = $width/1000;
            $height = $height/1000;
        }

        $edge = max($width,$height);
        $postage = 0;

        if($post === "economy"){
            $postage = (2 * $edge) + 4;


        }else if($post === "rapid"){
            $postage = (3 * $edge) + 8;


        }else if($post === "next day"){
            $postage = (5 * $edge) + 12;
        }


        $area = $width*$height;
        $price = round(($area*$area) + (100*$area) + 6, 2);
        $postage = number_format($postage,2);



        if($vat){
            $priceVat = $price + ($price * 0.2);
            $postageVat = $postage + ($postage * 0.2);

            $total = number_format($priceVat + $postageVat, 2);
            $priceVat = number_format($priceVat, 2);
            $postageVat = number_format($postageVat, 2);

            echo "<p> Your frame will cost £$priceVat plus $post postage of £$postageVat giving a total price of £$total including VAT. </p>";
        }else{

            $total = number_format($price + $postage);
            $price = number_format($price,2);
            echo "<p> Your frame will cost £$price plus $post postage of £$postage giving a total price of £$total without VAT.</p>";
        }
    }
    ?>


</div>
</body>
</html>
