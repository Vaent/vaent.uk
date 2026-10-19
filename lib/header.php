<?php
    $pages = [
        'Home' => 'href="/"',
        'Gallery' => 'href="/gallery"',
        'Games' => 'href="/games"',
        'Contact' => 'href="/contact"',
    ]
?>
<header>
    <h1 class=<?= ($GLOBALS['this_page'] == "Home") ? "title-large" : "title-normal" ?>><img src="/Vaent_sig.png" />.uk</h1>
    <nav>
        <ul>
            <?php
                array_walk($pages, function ($link_attributes, $page_name) {
                    echo ($GLOBALS['this_page'] == $page_name
                        ? "<li class=\"current-page\"><a>$page_name</a></li>"
                        : "<li><a $link_attributes>$page_name</a></li>");
                })
            ?>
        </ul>
    </nav>
</header>