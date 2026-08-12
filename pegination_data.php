<?php
include 'admin/includes/db.php';
include 'pagination.php';

// call function
$pagination = paginate('registration');

$data = $pagination['data'];
$total_pages = $pagination['total_pages'];
$page = $pagination['current_page'];
$limit = $pagination['limit'];
$total_records = $pagination['total_records'];
$offset = $pagination['offset'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Job List</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

    <div class="container mt-5">
        <form method="GET" class="mb-3">

            <select name="limit" onchange="this.form.submit()" class="form-select w-auto">

                <option value="5" <?php if ($limit == 5) echo 'selected'; ?>>5</option>
                <option value="10" <?php if ($limit == 10) echo 'selected'; ?>>10</option>
                <option value="20" <?php if ($limit == 20) echo 'selected'; ?>>20</option>
                <option value="50" <?php if ($limit == 50) echo 'selected'; ?>>50</option>

            </select>

        </form>
        <h2 class="mb-4">Job List</h2>

        <table class="table table-bordered table-striped">

            <thead class="table-dark">
                <tr>
                    <th>SL</th>
                    <th>Name</th>
                    <th>Email</th>
                </tr>
            </thead>

            <tbody>

                <?php if (!empty($data)) { ?>

                    <?php foreach ($data as $key => $row) { ?>
                        <tr>
                            <td><?php echo ($offset + $key + 1); ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['email_id']; ?></td>
                        </tr>
                    <?php } ?>

                <?php } else { ?>

                    <tr>
                        <td colspan="3" class="text-center">No Data Found</td>
                    </tr>

                <?php } ?>

            </tbody>

        </table>

        <!-- Pagination -->
        <!-- <div class="d-flex justify-content-center"> -->
        <?php
        pagination_links(
            $total_pages,
            $page,
            $limit,
            $total_records,
            count($data)
        );
        ?>
        <!-- </div> -->

    </div>

</body>

</html>