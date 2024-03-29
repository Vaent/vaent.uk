<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
      <meta charset="utf-8">
        <title>Contact Vaent</title>
        <link rel="stylesheet" href="/styles/default.css">
    </head>
    <body>
        <header>
            <span class="vaent-title">Vaent</span>.uk
            <span>Contact Form</span>
        </header>
        <main>
            <div>
<?php if (isset($_GET['failed-submission']) && $_GET['failed-submission'] === 'true'): ?>
                <p class="warning">The email address you entered was invalid.</p>
<?php endif ?>
                <p>Commission artwork, send feedback about the website, tell me if something on
                    <a href="/"><span class="vaent-inline">Vaent</span> .uk</a>
                    isn't working, or just say hi - you can use this form to contact Vaent about whatever.</p>
                <p>The email address and message will be stored securely, and will not be shared with anyone unless UK law
                    requires me to. I'll only write to the email address if you ask me to, or if I need to because of a
                    query or comment you raise.</p>
                <p>The network address from which your message was submitted will also be stored securely,
                    to assist with spam prevention. Please don't submit multiple messages
                    or you may be blocked from using the form.</p>
            </div>
            <div>
                <form action="/contact/submit-message.php" method="post">
                    <label for="email">Email address:</label>
                    <input type="email" id="email" name="email" value="">
                    <label for="message">Your message:</label>
                    <textarea id="message" name="message" rows="5"></textarea>
                    <input type="submit" value="Click here to send">
                </form>
            </div>
        </main>
    </body>
</html>
