<?php if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: .');
    exit;
} ?>
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
