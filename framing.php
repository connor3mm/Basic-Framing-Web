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
    <p><input type = "submit"/> </p>
</form>
    <?php
}else{

        if($units === "mm"){
            $width = $width/1000;
            $height = $height/1000;
        }
        else if($units === "cm") {
            $width = $width / 100;
            $height = $height / 100;
        }

        else if($units === "inch"){
            $width = $width/39.37;
            $height = $height/39.37;
        }

        $area = $width*$height;
        $price = number_format(($area*$area) + (100*$area) + 6, 2);
        echo "<p> Your frame will cost £$price.</p>";
    }
    ?>


</div>
</body>
</html>
