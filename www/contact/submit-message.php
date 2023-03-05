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
            <div>
                <p><a href="/">Click here to return to <span class="vaent-inline">Vaent</span> .uk</a></p>
            </div>
        </main>
    </body>
</html>
