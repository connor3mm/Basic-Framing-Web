<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Frame Price Estimator</title>
</head>
<body>
<div>
    <?php
    $pass = isset($_POST["password"]) ? $_POST["password"] : "";

    $reqPass = "WannaTellMeHow";

    if ($pass !== $reqPass)
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST")
        {
            echo "Incorrect password"; //Error message
        }

        ?>
        <form action = "getrequests.php" method="POST">

            <p>Password: <input type = "text" name ="password" placeholder="password"/> <input type = "submit"/> </p>

        </form>
        <?php
    }

    else
    {

   require_once "password.php";

    //Connect to MySQL
    $host   = "";
    $user   = "";
    $pass   = get_password();
    $dbname = "";
    $conn   = new mysqli($host, $user, $pass, $dbname);

    //Issue the query
    $sql    = "SELECT * FROM `framingrequests`";
    $result = $conn->query($sql);

    echo "
    <table  cellpadding='5' cellspacing='' width='43%'>
				<tr>
					<th>Width</th>
					<th>Height</th>
					<th>Postage</th>
					<th>Email</th>
					<th>Price(Ex.VAT)</th>
					<th>Requested</th>
					
				</tr>
				";

    if ($result->num_rows > 0){

        while ($row = $result->fetch_assoc())
        {
            $postageName = $row["postage"];
            $orderTime = $row["time"];
            $bold = "";

            echo "<tr>\n";
            if (getBold($postageName, $orderTime))
            {
                $bold = "<b>";
            }

            echo "<td>" . $bold . $row["width"] . "</td>\n";
            echo "<td>" . $bold . $row["height"] . "</td>\n";
            echo "<td>" . $bold . $row["postage"] . "</td>\n";
            echo "<td>" . $bold . $row["email"] . "</td>\n";
            echo "<td>" . $bold . $row["price"] . "</td>\n";
            echo "<td>" . $bold . $row["time"] . "</td>\n";
            echo "</tr>\n";
            echo "</b>";
        }
        echo "</table>\n";
    }

        //Disconnect
        $conn->close();

    }

    //functions
    function getBold($postageName, $orderTime)
    {
        $curTime  = date('d-m-y h:i:s');
        $curDate  = strtotime($curTime);
        $orderDay = strtotime($orderTime);


        if ($postageName === "economy") {
            if (daysBetween($curDate, $orderDay) >= 7) {
                return TRUE;
            }

        } else if ($postageName === "rapid") {
            if (daysBetween($curDate, $orderDay) >= 3 ) {
                return TRUE;
            }

        } else if ($postageName === "next day") {
            if (daysBetween($curDate, $orderDay) >= 1) {
                return TRUE;
            }
        }

    }


    function daysBetween($curTime, $orderDay ){
        $dayBet = $curTime - $orderDay;
        return round($dayBet/ (60*60*24)/365);
    }

?>
</div>
</body>
</html>
