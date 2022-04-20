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
            This page is still in development. For now it only has thumbnail samples; larger images will be available once the necessary work is completed.
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
                    $filename = "{$image['image_name']}-thumb.png";
                    if (file_exists("images/thumbs/$filename")) {
                        echo <<<THUMB
                        <span class="thumbnail">
                            <img src="gallery/images/thumbs/$filename" alt="{$image['image_description']}">
                        </span>
                        THUMB;
                    }
                }
            ?>
        </div>
    </body>
</html>
