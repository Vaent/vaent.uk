<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Gallery</title>
        <link rel="stylesheet" href="/styles/default.css">
        <link rel="stylesheet" href="/styles/components.css">
        <link rel="stylesheet" href="/styles/gallery.css">
        <script src="/scripts/toggles.js"></script>
    </head>
    <body>
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
                    $src = $onclick = $isNude = $display = null;

                    if (file_exists("images/thumbs/$thumb")) {
                        $src = "src=images/thumbs/$thumb";
                    }
                    if (file_exists("images/$filename")) {
                        $onclick = "onclick=\"expandImage('images/$filename')\"";
                        $src ??= "src=images/$filename";
                    }
                    if ($src) {
                        if ($image["is_nude"]) {
                            $isNude = "data-is-nude=\"true\"";
                            $src = "data-$src";
                            $display = "style=\"display:none\"";
                        }
                        echo <<<THUMB
                        <span class="thumbnail" $display>
                            <img alt="{$image['image_description']}" $src $onclick $isNude>
                        </span>
                        THUMB;
                    }
                }
            ?>
        </div>
        <div id="gallery-settings">
            <h1>Gallery settings</h1>
            <hr>
            <p>Include nudes?
                <span class="toggle-selector">
                    <span data-toggle-position="0" class="toggle-currently-selected">No</span><!--
                 --><span class="toggle-slider" data-currently-selected="0" onclick="toggleNudes(this)"></span><!--
                 --><span data-toggle-position="1" class="toggle-not-selected" onclick="toggleNudes(this)">Yes</span>
                </span>
            </p>
            <p style="font-style:italic">
                By default, images which prominently feature human nudity are not displayed or downloaded to your machine;
                so anyone who doesn't want to see that content, doesn't have to.
            </p>
            <p style="font-style:italic">
                That said, the nude art in this collection is no more "scandalous" than what you might see in a public gallery/museum.
                If you're comfortable with that, you can use the toggle to update the page with those images.
            </p>
        </div>

        <div id="expanded-image-overlay" onclick="collapseImage()">
            <img id="expanded-image">
        </div>
        <script src="/scripts/overlay.js"></script>
    </body>
</html>
