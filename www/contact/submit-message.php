<?php
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        header('Location: .');
        exit;
    }
    $conf = parse_ini_file('../../conf.ini');
    try {
        $pg = new PDO("pgsql:host={$conf['host']};port={$conf['port']};dbname={$conf['cdbname']}", $conf['username'], $conf['password']);
        $stmt = $pg->prepare('CALL record_message(:email, :message)', [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
        if (! $stmt->execute(['email' => $_POST['email'], 'message' => $_POST['message']]))
            throw new Exception('Unable to execute prepared statement');
    } catch(Exception $ex) {
        die('<div>SERVER ERROR: Unable to store your message. Please check the details you entered and try again later.</div>');
    }
?>
<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require '../../PHPMailer/src/Exception.php';
    require '../../PHPMailer/src/PHPMailer.php';
    require '../../PHPMailer/src/SMTP.php';

    function defaultPHPMailer() {
        global $conf;
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = "{$conf['mailhost']}";
        $mail->SMTPAuth = true;
        $mail->Username = "{$conf['mailuser']}";
        $mail->Password = "{$conf['mailpass']}";
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = $conf['mailport'];
        return $mail;
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
                <p><span class="special">Email address:</span> <?= htmlspecialchars($_POST['email']) ?></p>
                <p><span class="special">Message:</span> <?= htmlspecialchars($_POST['message']) ?></p>
            </div>
<?php if (array_key_exists('send-copy', $_POST)) {
    $escaped_email = htmlspecialchars($_POST['email']);
    try {
        $copy = defaultPHPMailer();
        $copy->setFrom("{$conf['nrmailuser']}");
        $copy->addAddress("{$_POST['email']}");
        $copy->Subject = 'Your message was submitted to Vaent.uk';
        $copy->Body = "A copy of your message is below.\n---\n\n{$_POST['message']}";
        $copy->send();
        echo <<<SENT
            <div>
                <p>A copy of your message was sent to {$escaped_email} as requested.</p>
                <p>It may take several minutes to arrive. If you still don't see it after waiting, check your junk mail folder.</p>
            </div>
        SENT;
    } catch(Exception $ex) {
        echo <<<NOTSENT
            <div>
                <p>The server was unable to send a copy of your message to {$escaped_email} as requested.</p>
            </div>
        NOTSENT;
    }
} ?>
            <div>
                <p><a href="/">Click here to return to <span class="vaent-inline">Vaent</span> .uk</a></p>
            </div>
        </main>
    </body>
</html>
<?php
    try {
        $notification = defaultPHPMailer();
        $notification->setFrom("{$conf['mailuser']}");
        $notification->addAddress("{$conf['mailuser']}");
        $notification->Subject = 'New message received!';
        $notification->AllowEmpty = true;
        $notification->send();
    } catch(Exception $ex) {}
?>
