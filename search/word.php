<?php

define('APP_NAME', 'Search');
define('PAGE_TITLE', 'Page Details');
define('PAGE_SELECTED_SECTION', '');
define('PAGE_SELECTED_SUB_PAGE', '');

include('../templates/html_header.php');
include('../templates/nav_header.php');
include('../templates/nav_sidebar.php');
include('../templates/main_header.php');

include('../templates/message.php');

$query = 'SELECT *
    FROM words
    WHERE id = "'.addslashes($_GET['key']).'"
    LIMIT 1';
$result = mysqli_query($connect, $query);
$record = mysqli_fetch_assoc($result);

$query = 'SELECT pages.*
    FROM pages
    INNER JOIN page_word
    ON pages.id = page_word.page_id
    WHERE page_word.word_id = "'.$record['id'].'"
    ORDER BY title';
$result_pages = mysqli_query($connect, $query);

?>

<div class="w3-center">
    <h1>BrickMMO Search</h1>
</div>

<hr>

<p>
    Word:
    <span class="w3-bold"><?=$record['word']?></span>
</p>

<hr>

<h2>Pages</h2>

<?php if(mysqli_num_rows($result_pages) > 0): ?>

    <table class="w3-table w3-bordered w3-striped w3-margin-bottom">

        <?php while ($record = mysqli_fetch_assoc($result_pages)): ?>

            <?php
            $parsed_url = parse_url($record['url']);
            $domain = $parsed_url['scheme'] . '://' . $parsed_url['host'];
            $favicon_url = $domain . '/favicon.ico';
            ?>

            <tr>
                <td class="bm-table-icon w3-padding">
                    <img src="<?=$favicon_url?>" alt="favicon" width="50" height="50" onerror="this.style.display='none'">
                </td>
                <td class="w3-padding">
                    <?=$record['title'] ? $record['title'] : 'Missing Title'?>
                    <br>
                    <a href="<?=$record['url']?>"><?=$record['url']?></a>
                </td>
                <td class="bm-table-icon">
                    <a href="<?=ENV_DOMAIN?>/details/<?=$record['id']?>">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>

    </table>

<?php else: ?>

    <div class="w3-panel w3-light-grey">
        <h3 class="w3-margin-top"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> No Pages</h3>
        <p>
            This word does not appear on any pages.
        </p>
    </div>

<?php endif; ?>

<a href="<?=ENV_DOMAIN?>/q" class="w3-button w3-white w3-border">
    <i class="fa-solid fa-magnifying-glass fa-padding-right"></i>
    Back to Search
</a>

<?php

include('../templates/main_footer.php');
include('../templates/debug.php');
include('../templates/html_footer.php');