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
    FROM pages
    WHERE id = "'.addslashes($_GET['key']).'"
    LIMIT 1';
$result = mysqli_query($connect, $query);
$record = mysqli_fetch_assoc($result);

$from_page = $record;

$query = 'SELECT *
    FROM pages
    WHERE page_id = "'.$record['id'].'"
    ORDER BY title';
$result_children = mysqli_query($connect, $query);

$query = 'SELECT words.*,
    count
    FROM words
    INNER JOIN page_word
    ON words.id = page_word.word_id
    WHERE page_word.page_id = "'.$record['id'].'"
    ORDER BY words.word ASC';
$result_words = mysqli_query($connect, $query);

?>

<div class="w3-center">
    <h1>BrickMMO Search</h1>
</div>

<hr>

<p>
    Title:
    <span class="w3-bold"><?=$record['title']?></span>
    <br>
    URL:
    <a href="<?=$record['url']?>">
        <span class="w3-bold"><?=$record['url']?></span>
    </a>
    <br>
    Status:
    <span class="w3-bold"><?=$record['status']?></span>
</p>

<a href="#" class="w3-button w3-white w3-border" id="relink">
    <i class="fa-solid fa-paperclip fa-padding-right"></i>
    Rescrape for Link
</a>

<a href="#" class="w3-button w3-white w3-border" id="rewords">
    <i class="fa-solid fa-arrow-rotate-left"></i>
    Rescrape for Words
</a>

<script>

let relink = document.getElementById('relink');
let reswords = document.getElementById('rewords');

relink.addEventListener('click', function(event) {

    loading();

    event.preventDefault();

    fetch('<?=ENV_DOMAIN?>/cron/links.php?id=<?=$record['id']?>')
        .then(response => {

            window.location.reload();
        
        });

});

rewords.addEventListener('click', function(event) {

    loading();

    event.preventDefault();

    fetch('<?=ENV_DOMAIN?>/cron/words.php?id=<?=$record['id']?>')
        .then(response => {

            window.location.reload();
        
        });

});

</script>

<hr>

<h2>Source Page</h2>

<?php if($record['page_id']): ?>

    <table class="w3-table w3-bordered w3-striped w3-margin-bottom">

        <?php do { ?>
            
            <?php

            $from_query = 'SELECT *
                FROM pages
                WHERE id = "'.addslashes($from_page['page_id']).'"
                LIMIT 1';
            $from_result = mysqli_query($connect, $from_query);
            $from_page = mysqli_fetch_assoc($from_result);

            $parsed_url = parse_url($from_page['url']);
            $domain = $parsed_url['scheme'] . '://' . $parsed_url['host'];
            $favicon_url = $domain . '/favicon.ico';

            ?>

            <tr>
                <td class="bm-table-icon w3-padding">
                    <img src="<?=$favicon_url?>" alt="favicon" width="50" height="50" onerror="this.style.display='none'">
                </td>
                <td class="bm-table-icon">
                    <?=$from_page['status']?>
                </td>
                <td class="w3-padding">
                    <?=$from_page['title'] ? $from_page['title'] : 'Missing Title'?>
                    <br>
                    <a href="<?=$from_page['url']?>"><?=$from_page['url']?></a>
                </td>
                <td class="bm-table-icon">
                    <a href="<?=ENV_DOMAIN?>/details/<?=$from_page['id']?>">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </a>
                </td>
            </tr>

        <?php } while ($from_page['page_id'] != 0); ?>

    </table>

<?php else: ?>

    <div class="w3-panel w3-light-grey">
        <h3 class="w3-margin-top"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> No Source Page</h3>
        <p>
            This page does not have a source URL. This was the first page searched!
        </p>
    </div>

<?php endif; ?>

<h2>Child Pages</h2>

<?php if(mysqli_num_rows($result_children) > 0): ?>

    <table class="w3-table w3-bordered w3-striped w3-margin-bottom">

        <?php while ($record = mysqli_fetch_assoc($result_children)): ?>

            <?php
            $parsed_url = parse_url($record['url']);
            $domain = $parsed_url['scheme'] . '://' . $parsed_url['host'];
            $favicon_url = $domain . '/favicon.ico';
            ?>

            <tr>
                <td class="bm-table-icon w3-padding">
                    <img src="<?=$favicon_url?>" alt="favicon" width="50" height="50" onerror="this.style.display='none'">
                </td>
                <td class="bm-table-icon">
                    <?=$from_page['status']?>
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
        <h3 class="w3-margin-top"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> No Child Pages</h3>
        <p>
            This page does not have any child pages or has not yet been scraped for links.
        </p>
    </div>

<?php endif; ?>

<h2>Word List</h2>

<?php if(mysqli_num_rows($result_words) > 0): ?>    

    <table class="w3-table w3-bordered w3-striped w3-margin-bottom">

        <?php while ($record = mysqli_fetch_assoc($result_words)): ?>

            <tr>    
                <td class="bm-table-icon">
                    <?=$record['count']?>
                </td>
                <td>
                    <?=$record['word']?>
                </td>
                <td class="bm-table-icon">
                    <a href="<?=ENV_DOMAIN?>/word/<?=$record['id']?>">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>

    </table>

<?php else: ?>

    <div class="w3-panel w3-light-grey">
        <h3 class="w3-margin-top"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> No Words</h3>
        <p>
            This page does not have any words or has not yet been scraped for words.
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