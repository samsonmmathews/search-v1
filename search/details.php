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
$result = mysqli_query($connect, $query);

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
    </p>

    <?php if($record['page_id']): ?>

        <hr>

        <h2>Source Page</h2>

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

    <?php endif; ?>


    <?php if(mysqli_num_rows($result) > 0): ?>

        <h2>Child Pages</h2>

        <table class="w3-table w3-bordered w3-striped w3-margin-bottom">

            <?php while ($display = mysqli_fetch_assoc($result)): ?>

                <?php
                $parsed_url = parse_url($display['url']);
                $domain = $parsed_url['scheme'] . '://' . $parsed_url['host'];
                $favicon_url = $domain . '/favicon.ico';
                ?>

                <tr>
                    <td class="bm-table-icon w3-padding">
                        <img src="<?=$favicon_url?>" alt="favicon" width="50" height="50" onerror="this.style.display='none'">
                    </td>
                    <td class="w3-padding">
                        <?=$display['title'] ? $display['title'] : 'Missing Title'?>
                        <br>
                        <a href="<?=$display['url']?>"><?=$display['url']?></a>
                    </td>
                    <td class="bm-table-icon">
                        <a href="<?=ENV_DOMAIN?>/details/<?=$display['id']?>">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>

        </table>

    <?php endif; ?>

<hr>

<a href="<?=ENV_DOMAIN?>/q" class="w3-button w3-white w3-border">
    <i class="fa-solid fa-magnifying-glass fa-padding-right"></i>
    Back to Search
</a>

<?php

include('../templates/main_footer.php');
include('../templates/debug.php');
include('../templates/html_footer.php');