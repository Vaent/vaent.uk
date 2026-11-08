<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Music videos and scores - Vaent.uk</title>
        <link rel="icon" href="/favicon.png">
        <link rel="stylesheet" href="/styles/default.css">
        <link rel="stylesheet" href="/styles/music.css">
    </head>
    <body>
        <?php
            $this_page = "Music";
            require('../../lib/header.php');
        ?>
        <main>
            <?php
                $conf = parse_ini_file('../../music-conf.ini');
                try {
                    $pg = new PDO("pgsql:host={$conf['host']};port={$conf['port']};dbname={$conf['dbname']}", $conf['username'], $conf['password']);
                    $allMusic = $pg->query("SELECT * FROM {$conf['get_music_details_function']}();")->fetchAll(PDO::FETCH_ASSOC);
                } catch(Exception $ex) {
                    die('<div>SERVER ERROR: Unable to display songs (could not retrieve details from the database)</div>');
                }
            ?>
            <noscript><div class="warning">
                Your browser is preventing scripts from running in this page.
                It will not work correctly unless you enable Javascript.
            </div></noscript>
            <div>
                <p>Recordings and scores of my own arrangements.</p>
                <p>Mostly covers of popular songs, with a few original numbers mixed in.</p>
                <p>I'm transitioning from my old, mostly abandoned YouTube channel,
                    to a new one associated with this website, which is where I'll post new videos going forward.</p>
            </div>
            <div class="container">
                <?php
                    is_null($allMusic)
                        ? "<p>No details were found.</p>"
                        : generateCards(json_decode($allMusic[0]['details'], false));
                ?>
            </div>
        </main>
    </body>
</html>
<?php
    function generateCards($songs) {
        foreach ($songs as $song) {
            $songTitle = $song->title;
            $songAndArtist = is_null($song->artist) ? $songTitle : "$songTitle | {$song->artist}";
            $linksHtml = implode(array_map(fn($link) => htmlForLink($link, $songTitle), $song->links));
            echo <<<THUMB
            <span class="content-card">
                <h2 class="video-title">$songAndArtist</h2>
                $linksHtml
            </span>
            THUMB;
        }
    }
    function htmlForLink($link, $songTitle) {
        switch ($link->link_type) {
            case 'YouTube':
                $videoId = $link->link_identifier;
                return <<<EMBED
                <p class="video-embed">
                    <iframe
                        width="336"
                        height="189"
                        src="https://www.youtube-nocookie.com/embed/$videoId"
                        title="YouTube video player"
                        allow="accelerometer; clipboard-write; encrypted-media; fullscreen; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin"
                        loading="lazy"
                    ></iframe>
                </p>
                EMBED;
            default:
                // Links other than YouTube are assumed to be sheet music files.
                $file_location = $link->link_identifier ?? '';
                if ($file_location == '') {
                    // Infer filename from song title and link type.
                    $titleForFile = str_replace(' ', '_', $songTitle);
                    $file_location = "$titleForFile.{$link->link_type}";
                }
                if (!file_exists($file_location)) {
                    // Check the URL relative to default locations.
                    if (file_exists("scores/$file_location")) {
                        $file_location = "/music/scores/$file_location";
                    } elseif (file_exists("../$file_location")) {
                        $file_location = "/$file_location";
                    } else {
                        return; // Don't create a link if the file doesn't exist.
                    }
                }
                $link_description = $link->link_description ?? '';
                if ($link_description == '') switch($link->link_type) {
                    case 'pdf':
                        $link_description = 'Sheet music (PDF)';
                        break;
                    case 'mscz':
                        $link_description = 'Sheet music (MuseScore file)';
                        break;
                    default:
                        return; // Don't create a link if the description is blank and can't be inferred.
                }
                return "<a href=\"$file_location\">$link_description</a>";
        }
    }
?>
