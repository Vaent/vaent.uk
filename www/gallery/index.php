<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Gallery</title>
        <link rel="stylesheet" href="/styles/default.css">
        <link rel="stylesheet" href="/styles/gallery.css">
    </head>
    <body>
        <div style="text-align:center;padding:30px">
            This page is still in development. More images and options are on the way!
        </div>
        <div class="image-group">
            <?php
                $conf = parse_ini_file('../../conf.ini');
                try {
                    $pg = new PDO("pgsql:host={$conf['host']};port={$conf['port']};dbname={$conf['dbname']}", $conf['username'], $conf['password']);
                    $images = $pg->query('SELECT * from get_all_basic_image_details();');
                } catch(Exception $ex) {
                    die('SERVER ERROR: Unable to display images (could not retrieve details from the database)');
                }

                foreach ($images as $image) {
                    $filename = "{$image['image_name']}.png";
                    $thumb = "{$image['image_name']}-thumb.png";
                    $src = $onclick = null;

                    if (file_exists("images/thumbs/$thumb")) {
                        $src = "images/thumbs/$thumb";
                    }
                    if (file_exists("images/$filename")) {
                        $onclick = " onclick=\"expandImage('images/$filename')\"";
                        $src ??= "images/$filename";
                    }
                    if ($src) {
                        echo <<<THUMB
                        <span class="thumbnail">
                            <img src="$src" alt="{$image['image_description']}"$onclick>
                        </span>
                        THUMB;
                    }
                }
            ?>
        </div>

        <div id="expanded-image-overlay" onclick="collapseImage()">
            <img id="expanded-image">
        </div>
        <script src="/scripts/overlay.js"></script>
    </body>
</html>
