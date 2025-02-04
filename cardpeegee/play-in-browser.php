<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>CardPeeGee browser games</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div id="content">
            <p>Work continues on the CardPeeGee "Vanilla refreshment" intended to replace the CardPeeGee Vanilla browser game from 2015.
            An early prototype has been built with very limited functionality, to demonstrate progress on the refresh project.</p>
            <p><a href="/builds/refreshment/0.0.3/index.html">Click here to play Vanilla refreshment v0.0.3</a>.</p>
            <p>You can select earlier versions from the table below.</p>
            <details>
                <summary>CardPeeGee refreshed versions (click to expand/collapse)</summary>
                <?php
                    $conf = parse_ini_file('../cardpeegee-conf.ini');
                    try {
                        $pg = new PDO("pgsql:host={$conf['host']};port={$conf['port']};dbname={$conf['dbname']}", $conf['username'], $conf['password']);
                        $allVersionDetails = $pg->query("SELECT * FROM ${conf['tblname']} ORDER BY version_number DESC;");
                        $versionDataLoaded = true;
                    } catch(Exception $ex) {
                        $versionDataLoaded = false;
                    }
                    if ($versionDataLoaded === true):
                ?>
                <table>
                    <thead>
                        <th>Version</th>
                        <th>Description</th>
                        <th>Released</th>
                    </thead>
                    <tbody>
                        <?php toTableRows($allVersionDetails); ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p class="errorMessage">
                    There was an error loading the version details. Please <a href="https://vaent.uk/contact">let me know</a>!
                </p>
                <?php endif ?>
            </details>
            <p>Note that these early prototypes will contain some bugs, particularly display errors, on top of the limited gameplay.</p>
            <p>The original game is also provided for archive purposes - <a href="builds/legacy/builds.html">click here for the original</a>.
            Although the technology is now obsolete, you might be able to play it in an older browser.</p>
        </div>
    </body>
</html>
<?php
    function toTableRows($versionDetails) {
        foreach ($versionDetails as $version) {
            $versionHref = "/builds/refreshment/{$version[0]}/index.html";
            $nonBreakingDate = str_replace("-", "&#8209;", $version[2]);
            echo "<tr><td><a href=\"$versionHref\">{$version[0]}</td><td>{$version[1]}</td><td>$nonBreakingDate</td></tr>";
        }
    }
?>
