<?php
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        header('Location: .');
        exit;
    }
    $conf = parse_ini_file('../../conf.ini');
    try {
        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        if ($email === false) {
            header('Location: .?failed-submission=true');
            exit;
        }
        $pg = new PDO("pgsql:host={$conf['host']};port={$conf['port']};dbname={$conf['cdbname']}", $conf['username'], $conf['password']);
        $stmt = $pg->prepare('SELECT record_message(:email, :message, :source)', [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
        $stmt->execute(['email' => $email, 'message' => $_POST['message'], 'source' => $_SERVER['REMOTE_ADDR']]);
        if (!$stmt) throw new Exception('Unable to execute prepared statement');
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $result = json_decode($result['record_message'],true);
        if ($result['status'] === 0) die("Your message wasn't stored: {$result['detail']}");
    } catch(Exception $ex) {
        die("<div>SERVER ERROR: Unable to store your message. Please check the details you entered and try again later.</div>");
    }
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Message submitted</title>
        <link rel="stylesheet" href="/styles/default.css">
    </head>
    <body>
        <main>
            <div>
                <p>Your message was submitted with the details below.</p>
                <p><span class="special">Email address:</span> <?= htmlspecialchars($email) ?></p>
                <p><span class="special">Message:</span> <?= htmlspecialchars($_POST['message']) ?></p>
            </div>
            <div>
                <p><a href="/">Click here to return to <span class="vaent-inline">Vaent</span> .uk</a></p>
            </div>
        </main>
    </body>
</html>
<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require '../../PHPMailer/src/Exception.php';
    require '../../PHPMailer/src/PHPMailer.php';
    require '../../PHPMailer/src/SMTP.php';
    try {
        $notification = new PHPMailer(true);
        $notification->isSMTP();
        $notification->Host = "{$conf['mailhost']}";
        $notification->SMTPAuth = true;
        $notification->Username = "{$conf['mailuser']}";
        $notification->Password = "{$conf['mailpass']}";
        $notification->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $notification->Port = $conf['mailport'];
        $notification->setFrom("{$conf['mailuser']}");
        $notification->addAddress("{$conf['mailuser']}");
        $notification->Subject = 'New message received!';
        $notification->AllowEmpty = true;
        $notification->send();
    } catch(Exception $ex) {}
?>
