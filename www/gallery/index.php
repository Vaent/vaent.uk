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
        <?php
            $conf = parse_ini_file('../../conf.ini');
            try {
                $pg = new PDO("pgsql:host={$conf['host']};port={$conf['port']};dbname={$conf['dbname']}", $conf['username'], $conf['password']);
                $allImages = $pg->query('SELECT * FROM get_recent_and_older_image_details();')->fetch(PDO::FETCH_ASSOC);
            } catch(Exception $ex) {
                die('<div>SERVER ERROR: Unable to display images (could not retrieve details from the database)</div>');
            }
        ?>
        <div class="image-group">
            <h1>Recent</h1>
            <?php generateThumbnails(json_decode($allImages['recent'], false)); ?>
        </div>
        <div class="image-group">
            <h1>Older work</h1>
            <?php generateThumbnails(json_decode($allImages['older'], false)); ?>
        </div>
        <div id="gallery-settings">
            <h1>Gallery settings</h1>
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

        <div id="expanded-image-overlay" onclick="updateHash('')">
            <img id="expanded-image">
        </div>
        <script src="/scripts/overlay.js"></script>
    </body>
</html>
<?php
    function generateThumbnails($images) {
        foreach ($images as $image) {
            $filenameStub = imageNameToFilename($image->image_name);
            $filename = "$filenameStub.png";
            $thumb = "$filenameStub-thumb.png";
            $src = $onclick = $isNude = $display = null;

            if (file_exists("images/thumbs/$thumb")) {
                $src = "src=images/thumbs/$thumb";
            }
            if (file_exists("images/$filename")) {
                $onclick = "onclick=\"updateHash('$filenameStub')\"";
                $src ??= "src=images/$filename";
            }
            if ($src) {
                if ($image->is_nude) {
                    $isNude = "data-is-nude=\"true\"";
                    $src = "data-$src";
                    $display = "style=\"display:none\"";
                }
                $nameLabel = str_starts_with($image->image_name, "untitled") ? "" : "<i>{$image->image_name}</i>";
                echo <<<THUMB
                <span class="image-box" $isNude $display">
                    <span class="thumbnail">
                        <img alt="{$image->image_description}" $src $onclick>
                    </span>
                    <span class="image-label">
                        $nameLabel
                        <br>
                        {$image->media}, {$image->yr}
                    </span>
                </span>
                THUMB;
            }
        }
    }

    function imageNameToFilename($n) {
        $n = preg_replace("/^[^[:alnum:]]+|[^[:alnum:]]+$/", "", $n); //trim leading/trailing punctuation/ws
        $n = preg_replace("/([[:alpha:]])'([[:alpha:]])/", "$1$2", $n); //strip contraction apostrophes
        return preg_replace("/[^[:alnum:]]+/", "-", strtolower($n));
    }
?>
