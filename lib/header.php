<?php
    $pages = [
        'Home' => 'href="/"',
        'Gallery' => 'href="/gallery"',
        'CardPeeGee' => 'href="https://cardpeegee.vaent.uk" target="_blank"',
        'Snaky' => 'href="/Snaky/play" target="_blank"',
        'Contact' => 'href="/contact"',
    ]
?>
<header>
    <h1><span class=<?= ($GLOBALS['this_page'] == "Home") ? "vaent-title" : "vaent-inline" ?>>Vaent</span>.uk</h1>
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