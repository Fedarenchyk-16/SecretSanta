<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Secret Santa</title>
</head>
<style>
    * {
        box-sizing: border-box;
    }
    body {
        background: white;
    }
    .decor {
        position: relative;
        max-width: 400px;
        margin: 200px auto 0;
        background: #cc0000;
        border-radius: 30px;
    }
    .form-left-decoration, .form-right-decoration {
        content: "";
        position: absolute;
        width: 50px;
        height: 20px;
        background: brown;
        border-radius: 20px;
    }
    .form-left-decoration {
        bottom: 60px;
        left: -30px;
    }
    .form-right-decoration {
        top: 60px;
        right: -30px;
    }
    .form-left-decoration:before, .form-left-decoration:after, .form-right-decoration:before, .form-right-decoration:after {
        content: "";
        position: absolute;
        width: 50px;
        height: 20px;
        border-radius: 30px;
        background: white;
    }
    .form-left-decoration:before {
        top: -20px;
    }
    .form-left-decoration:after {
        top: 20px;
        left: 10px;
    }
    .form-right-decoration:before {
        top: -20px;
        right: 0;
    }
    .form-right-decoration:after {
        top: 20px;
        right: 10px;
    }
    .circle {
        position: absolute;
        bottom: 80px;
        left: -55px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: white;
    }
    .form-inner {
        padding: 50px;
    }
    .form-inner input, .form-inner textarea {
        display: block;
        width: 100%;
        padding: 0 20px;
        margin-bottom: 10px;
        background: #F6F6F6;
        line-height: 40px;
        border-width: 0;
        border-radius: 20px;
        font-family: 'Roboto', sans-serif;
    }
    .form-inner input[type="submit"] {
        margin-top: 30px;
        background: #f69a73;
        border-bottom: 4px solid #d87d56;
        color: white;
        font-size: 14px;
    }
    .form-inner textarea {
        resize: none;
    }
    .form-inner {
        margin-top: 0;
        font-family: 'Roboto', sans-serif;
        font-weight: 500;
        font-size: 16px;
        color: white;
    }

    .footer{
        margin-left: 70px;
        margin-top: 20px;
        margin-bottom: -20px;
        font-family: 'Roboto', sans-serif;
        font-weight: 500;
        font-size: 10px;
        color: white;
    }

    .title{
        color: white;
    }
</style>
<body>
<form class="decor"">
    <div class="form-left-decoration"></div>
    <div class="form-right-decoration"></div>
    <div class="circle"></div>
    <div class="form-inner">
<?php
//header('Content-Type: text/html; charset=utf-8');
// Файлы phpmailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/PHPMailer.php';
require '../phpmailer/SMTP.php';
require '../phpmailer/Exception.php';
//require '../phpmailer/language/phpmailer.lang-ru.php';

class User
{
    public $id = 0;
    public $name = "";
    public $email = "";
    public $idSecretName = 999;
    public $isSelected = false;

    public function __construct($id, $name, $email, $isSelected)
    {
        $this->name = $name;
        $this->email = $email;
        $this->id = $id;
        $this->idSecretName = 999;
        $this->isSelected = $isSelected;
    }
}

$arrayNames = array("NIKITA", "MISHA", "ALENA", "ALESYA", "ARTEM");
$arrayEmails = array("mikitafed@gmail.com", "mikitafed@gmail.com", "mikitafed@gmail.com", "mikitafed@gmail.com", "mikitafed@gmail.com");

try {
    $Users = distributeRandom($arrayNames, $arrayEmails);
    for ($i = 0; $i < count($Users); $i++) {
        $mail = new PHPMailer(true);

        //Server settings
        //$mail->SMTPDebug = \PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = '1616nkt2001@gmail.com';                     //SMTP username
        $mail->Password   = 'password';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;
        //$mail->setLanguage('ru', '../phpmailer/language/phpmailer.lang-ru.php');//TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('1616nkt2001@gmail.com', 'Santa Claus');
        $mail->addAddress($Users[$i]->email, $Users[$i]->name);

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Тайный Санта';
        $mail->addCustomHeader('Content-type: text/html; charset=\"windows-1251\"\n; MIME-Version: 1.0\r\n; Content-Transfer-Encoding: 8bit\r\n');


        $secretName = $Users[$Users[$i]->idSecretName]->name;
        $Name = $Users[$i]->name;
        $msg = file_get_contents("./index.html");
        $changedMsg = str_ireplace("SOMEBODY",$secretName,$msg);
        $changedMsg = str_ireplace("YOURNAME",$Name,$changedMsg);

        $mail->Body = $changedMsg;

        $mail->send();
    }

    echo '<h3 class="title"> * Письма были успешно разосланы * </h3>';
} catch (Exception $e) {
    echo '<h3 class="title"> * Упс... Что-то пошло не так. Письма не были доставлены. Ошибка: ' .  $mail->ErrorInfo . ' * </h3>';
}

function distributeRandom($arrayNames, $arrayEmails)
{
    $Users = array();

    for ($i = 0; $i < count($arrayNames); ++$i) {
        $user = new User($i, $arrayNames[$i], $arrayEmails[$i], false);
        array_push($Users, $user);
    }

    for ($i = 0; $i < count($Users); $i++) {
        $confirmed = false;
        while ($confirmed == false) {
            $index = mt_rand(0, 4);
            if ($Users[$index]->id != $Users[$i]->id && $Users[$index]->isSelected == false) {
                $Users[$i]->idSecretName = $index;
                $Users[$index]->isSelected = true;
                $confirmed = true;
            }
        }
    }
    showDistribution($Users);
    return $Users;
};

function showDistribution($Users)
{
    for ($i = 0; $i < count($Users); ++$i) {
        echo "<pre>" . " " . $Users[$i]->id . " " . $Users[$i]->name . " " . $Users[$i]->email . " " . $Users[$i]->idSecretName . " " . "</pre>";
        echo $Users[$i]->isSelected ? 'true' : 'false';
    }
    echo "<pre>";
};
?>
  </div>
</form>
</body>
</html>
