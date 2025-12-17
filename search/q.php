<?php

if(isset($_GET['key']))
{

    $q = string_url($_GET['key']);
    if($q != $_GET['key'])
    {
        header_redirect('/q/'.$q);
    }
 
}

// Get page number from URL if set
if(isset($_GET['page']) && is_numeric($_GET['page']))
{
    $current_page = (int)$_GET['page'];
}
else
{
    $current_page = 1;
}

define('APP_NAME', 'Search');
define('PAGE_TITLE', 'Search Results');
define('PAGE_SELECTED_SECTION', '');
define('PAGE_SELECTED_SUB_PAGE', '');

include('../templates/html_header.php');
include('../templates/nav_header.php');
include('../templates/main_header.php');

include('../templates/message.php');

if(isset($q))
{

    // Pagination setup
    $results_per_page = 10;
    $offset = ($current_page - 1) * $results_per_page;

    // Split search term by dashes
    $search_terms = explode('-', $q);
    
    // Build WHERE clause for multiple terms
    $where_conditions = [];
    foreach($search_terms as $term) 
    {

        $term = trim($term);

        if(!empty($term)) 
        {
            $where_conditions[] = 'words.word LIKE "%'.mysqli_real_escape_string($connect, $term).'%"';
        }

    }
    
    $where_clause = implode(' OR ', $where_conditions);

    // Count total results
    $count_query = 'SELECT COUNT(DISTINCT pages.id) AS total
        FROM pages JOIN page_word 
        ON pages.id = page_word.page_id 
        JOIN words 
        ON page_word.word_id = words.id 
        WHERE status = 200 
        AND ('.$where_clause.')';
    $count_result = mysqli_query($connect, $count_query);
    $count_row = mysqli_fetch_assoc($count_result);
    $total_results = $count_row['total'];
    $total_pages = ceil($total_results / $results_per_page);

    // Get paginated results
    $query = 'SELECT DISTINCT pages.id, 
        title, 
        url, 
        COUNT(word) AS hit_count 
        FROM pages JOIN page_word 
        ON pages.id = page_word.page_id 
        JOIN words 
        ON page_word.word_id = words.id 
        WHERE status = 200
        AND ('.$where_clause.') 
        GROUP BY pages.id
        ORDER BY hit_count DESC
        LIMIT '.$offset.', '.$results_per_page;
    $result = mysqli_query($connect, $query);

}

?>

<?php if(isset($_GET['key'])): ?>

    <div class="w3-center">

        <h1>BrickMMO Search</h1>

        <input 
            class="w3-input w3-border w3-margin-top w3-margin-bottom" 
            type="text" 
            value="<?=isset($_GET['key']) ? htmlspecialchars(str_replace('-', ' ', $_GET['key'])) : ''?>"
            placeholder="" 
            style="max-width: 300px; display: inline-block; box-sizing: border-box; vertical-align: middle;" 
            id="search-term">

        <a
            href="#"
            class="w3-button w3-white w3-border w3-margin-top w3-margin-bottom" 
            style="display: inline-block; box-sizing: border-box; vertical-align: middle;"
            id="search-button"
        >
            <i class="fa-solid fa-magnifying-glass"></i> Search
        </a>
        
    </div>

    <hr>

<?php else: ?>

    <div class="w3-display-middle w3-center" style="width: 100%; max-width: 500px;">

        <h1>BrickMMO Search</h1>

        <input 
            class="w3-input w3-border w3-margin-top w3-margin-bottom" 
            type="text" 
            value="<?=isset($_GET['key']) ? htmlspecialchars(str_replace('-', ' ', $_GET['key'])) : ''?>"
            placeholder="" 
            style="max-width: 300px; display: inline-block; box-sizing: border-box; vertical-align: middle;" 
            id="search-term">

        <a
            href="#"
            class="w3-button w3-white w3-border w3-margin-top w3-margin-bottom" 
            style="display: inline-block; box-sizing: border-box; vertical-align: middle;"
            id="search-button"
        >
            <i class="fa-solid fa-magnifying-glass"></i> Search
        </a>
        
    </div>

<?php endif; ?>

<?php if(isset($_GET['key'])): ?>

    <?php if (mysqli_num_rows($result) > 0): ?>

        <?php
        $start_result = ($current_page - 1) * $results_per_page + 1;
        $end_result = min($current_page * $results_per_page, $total_results);
        ?>

        <p class="w3-center">Displaying <?=$start_result?>-<?=$end_result?> of <?=$total_results?> results</p>

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

    <?php else: ?>

        <div class="w3-panel w3-light-grey">
            <h3 class="w3-margin-top"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> No Results Found</h3>
            <p>
                No results found for 
                <span class="w3-bold"><?=htmlspecialchars(str_replace('-', ' ', $q))?></span>.
            </p>
        </div>

    <?php endif; ?>

    <nav class="w3-text-center w3-section">

        <div class="w3-bar">            

            <?php
            
            // Display pagination links
            for ($i = 1; $i <= $total_pages; $i++) 
            {
                echo '<a href="'.ENV_DOMAIN.'/q';
                if($i > 1) echo '/page/'.$i;
                echo '/'.$q.'" class="w3-button';
                if($i == $current_page) echo ' w3-border';
                echo '">'.$i.'</a>';
            }

            ?>

        </div>

    </nav>

<?php endif; ?>

<script>

(function() {

    let searchButton = document.getElementById('search-button');
    let searchTerm = document.getElementById('search-term');

    function performSearch() 
    {

        let query = searchTerm.value.trim();

        if (query !== '') {
            // Remove anything that's not letters, numbers, or spaces
            query = query.replace(/[^a-zA-Z0-9\s]/g, '');
            // Replace spaces with hyphens
            query = query.replace(/\s+/g, '-');
            window.location.href = '/q/' + query;
        }

    }

    searchButton.addEventListener('click', function(event) 
    {

        event.preventDefault();
        performSearch();

    });

    searchTerm.addEventListener('keypress', function(event) 
    {

        if (event.key === 'Enter') 
        {
            event.preventDefault();
            performSearch();
        }

    });

})();

</script>

<?php

include('../templates/main_footer.php');
include('../templates/html_footer.php');
