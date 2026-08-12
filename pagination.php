<?php
// ==== Use for Pagination data ======
// function paginate($table, $limit = 5)
// {
//     global $connect;
//     // current page
//     $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
//     if ($page < 1) $page = 1;

//     $offset = ($page - 1) * $limit;

//     // fetch data
//     $query = "SELECT * FROM $table LIMIT $limit OFFSET $offset";
//     $result = mysqli_query($connect, $query);
//     $data = mysqli_num_rows($result) ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

//     // total count
//     $total_query = "SELECT COUNT(*) as total FROM $table";
//     $total_result = mysqli_query($connect, $total_query);
//     $total_row = mysqli_fetch_assoc($total_result);

//     $total_records = $total_row['total'];
//     $total_pages = ceil($total_records / $limit);

//     return [
//         // 'data' => $result,
//         'data' => $data,
//         'total_pages' => $total_pages,
//         'current_page' => $page
//     ];
// }

function paginate($table, $default_limit = 5)
{
    global $connect;

    // dynamic limit from URL
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : $default_limit;

    // security limit check
    if (!in_array($limit, [5, 10, 20, 50])) {
        $limit = $default_limit;
    }

    // current page
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) $page = 1;

    $offset = ($page - 1) * $limit;

    // fetch data
    $query = "SELECT * FROM $table LIMIT $limit OFFSET $offset";
    $result = mysqli_query($connect, $query);
    $data = mysqli_num_rows($result) ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

    // total count
    $total_query = "SELECT COUNT(*) as total FROM $table";
    $total_result = mysqli_query($connect, $total_query);
    $total_row = mysqli_fetch_assoc($total_result);

    $total_records = $total_row['total'];
    $total_pages = ceil($total_records / $limit);

    return [
        'data' => $data,
        'total_pages' => $total_pages,
        'current_page' => $page,
        'limit' => $limit,
        'total_records' => $total_records,
        'offset' => $offset
    ];
}

// ==== Use For Pagination Links ======
// function pagination_links($total_pages, $current_page)
// {

//     echo '<ul class="pagination">';

//     // Previous
//     if ($current_page > 1) {
//         echo '<li class="page-item">
//         <a class="page-link" href="?page=' . ($current_page - 1) . '">Previous</a>
//         </li>';
//     }

//     // numbers
//     for ($i = 1; $i <= $total_pages; $i++) {
//         $active = ($i == $current_page) ? 'active' : '';

//         echo '<li class="page-item ' . $active . '">
//         <a class="page-link" href="?page=' . $i . '">' . $i . '</a>
//         </li>';
//     }

//     // Next
//     if ($current_page < $total_pages) {
//         echo '<li class="page-item">
//         <a class="page-link" href="?page=' . ($current_page + 1) . '">Next</a>
//         </li>';
//     }

//     echo '</ul>';
// }

function pagination_links($total_pages, $current_page, $limit, $total_records, $data_count)
{
    // calculate start & end
    $start = ($current_page - 1) * $limit + 1;
    $end = $start + $data_count - 1;

    if ($total_records == 0) {
        $start = 0;
        $end = 0;
    }

    echo '<div class="d-flex justify-content-between align-items-center mt-3">';

    // LEFT SIDE (Showing text)
    echo '<div>
        Showing ' . $start . ' to ' . $end . ' of ' . $total_records . ' entries
    </div>';

    // RIGHT SIDE (Pagination links)
    echo '<div><ul class="pagination">';

    // Previous
    if ($current_page > 1) {
        echo '<li class="page-item">
        <a class="page-link" href="?page=' . ($current_page - 1) . '&limit=' . $limit . '">Previous</a>
        </li>';
    }

    // Page numbers
    for ($i = 1; $i <= $total_pages; $i++) {
        $active = ($i == $current_page) ? 'active' : '';

        echo '<li class="page-item ' . $active . '">
        <a class="page-link" href="?page=' . $i . '&limit=' . $limit . '">' . $i . '</a>
        </li>';
    }

    // Next
    if ($current_page < $total_pages) {
        echo '<li class="page-item">
        <a class="page-link" href="?page=' . ($current_page + 1) . '&limit=' . $limit . '">Next</a>
        </li>';
    }

    echo '</ul></div>';

    echo '</div>';
}