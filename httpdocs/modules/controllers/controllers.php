<?php

function controllers_add2cart(){



if (is_array($_SESSION["cart"]) || is_object($_SESSION["cart"])) {
    $_SESSION["cart"] = array_values($_SESSION["cart"]);
}

    $id =$_POST["id"];

    $_SESSION["cart"][count($_SESSION["cart"])] = $id;

    echo count($_SESSION["cart"]);
    exit();

}

function controllers_cart(){
    global $argv;

    if ($argv[2] == "remove"){

       unset($_SESSION["cart"][$argv[3]]);
       header("location: /cart");
   }


}
function controllers_contact(){


    $secret = '6Lc1S2EUAAAAAMKnVGXqnELtee-26HyEm-_UE6xG';
    $response = $_POST['g-recaptcha-response'];

    $verifyResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secret."&response=".$response);
    $responseData = json_decode($verifyResponse);

    if($responseData->success != true){
        echo json_encode(array("status" => "fail", "message" => "Bewijs dat u geen robot bent."));
        exit();
    }

    $array[] = array($_POST["naam"], "varchar", true, "Naam is niet ingevuld");
    $array[] = array($_POST["email"], "email", true, "E-mailadres is niet ingevuld");
    $array[] = array($_POST["bericht"], "varchar", true, "Bericht is niet ingevuld");
    validation_valid($array);

    $mail = fc_email_data(1, $_POST,'nl');

    fc_email_sent("info@themindoffice.nl",$mail[0],$mail[1],$_POST["emailadres"],$_POST["naam"]);

    echo json_encode(array("status" => "success","clear" => "all", "message" => "Bedankt voor uw bericht. Wij nemen zo snel mogelijk contact met u op."));
    exit();

}

?>
