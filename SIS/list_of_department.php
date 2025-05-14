<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['gatepass'])) {
    header("Location: ./index.php");
    exit();
}

include 'connection.php';

$limit = 2;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Search functionality
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
}

// Total rows with search filter
$total_result = $con->query("SELECT COUNT(*) as total FROM department WHERE department_name LIKE '%$search_query%' OR department_id LIKE '%$search_query%'");
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

// Data query with search filter
$result = $con->query("SELECT * FROM department WHERE department_name LIKE '%$search_query%' OR department_id LIKE '%$search_query%' LIMIT $limit OFFSET $offset");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List of Department</title>
    <link href="dashtyle.css" rel="stylesheet">
    <link href="addformstyle.css" rel="stylesheet">
    <style>

        /* Button for adding new department */
        .add-button {
            display: inline-block;
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
            width: 200px;
            font-size: 16px;
            margin-left: 40%;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        }

        .add-button:hover {
            background-color: #c0392b;
        }

        /* Table styling */
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        }

        table th, table td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #3498db;
            color: white;
        }

        table td a {
            color: #3498db;
            text-decoration: none;
        }

        table td a:hover {
            text-decoration: underline;
        }

        /* Delete button */
        .btndelete {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btndelete:hover {
            background-color: #c0392b;
        }

        /* Pagination */
        .pagination {
            text-align: center;
            margin-top: 20px;
        }

        .pagination a {
            padding: 8px 16px;
            margin: 0 5px;
            color: #3498db;
            text-decoration: none;
            border-radius: 5px;
        }

        .pagination a.active {
            background-color: #3498db;
            color: white;
        }

        .pagination a:hover {
            background-color: #2980b9;
            color: white;
        }

        /* Search bar */
        .search-form {
            margin: 20px auto;
            width: 80%;
            text-align: center;
        }

        .search-form input {
            padding: 8px;
            font-size: 16px;
            width: 250px;
            border-radius: 5px;
            border: 1px solid #ccc;
            
        }

        .search-form input[type="submit"] {
            padding: 8px 15px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: auto; /* Ensure it only takes needed space */
            display: inline-block;
        }

        .search-form input[type="submit"]:hover {
            background-color: #2980b9;
        }

        form.search-form a {
        text-decoration: none;
        color: white;
        background-color: #7f8c8d;
        padding: 8px 16px;
        border-radius: 5px;
        font-size: 16px;
        transition: background-color 0.3s;
        }

        form.search-form a:hover {
        background-color: #616a6b;
        }

    </style>
</head>
<body>

    <nav class="navbar">
        <ul class="nav-list">
            <li><a href="list_of_students.php">Student</a></li>
            <li><a href="list_of_course.php">Course</a></li>
            <li><a href="list_of_department.php">Department</a></li>
            <li><a href="setcontent.php">Settings</a></li>
        </ul>
        <div class="admin-info">
            <a href="dashboard.php">
                <img src="image/admin.png" alt="Admin Icon" class="admin-icon">
            </a>
            <span class="admin-label">Admin</span>
        </div>
    </nav>

    <h1>List of Departments</h1>

    <!-- Search Form -->
    <form class="search-form" method="GET" action="list_of_department.php">
        <input type="text" name="search" placeholder="Search by department ID or name" value="<?php echo htmlspecialchars($search_query); ?>">
        <input type="submit" value="Search">
        <a href="list_of_department.php" style="margin-left:2px;">Reset</a>
    </form>

    <!-- Add New Department Button -->
    <a class="add-button" href="add_department.php">+ Add New Department</a>

    <!-- Table for Departments -->
    <table>
        <thead>
            <tr>
                <th>DEPARTMENT ID</th>
                <th>DEPARTMENT NAME</th>
                <th>ACTIONS</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['department_id']; ?></td>
                <td><?php echo $row['department_name']; ?></td>
                <td>
                    <a href="editdept.php?department_id=<?php echo $row['department_id']; ?>">EDIT</a>
                    <button class="btndelete" deptid="<?php echo $row['department_id']; ?>">DELETE</button>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=1&search=<?php echo urlencode($search_query); ?>">&laquo; First</a>
            <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search_query); ?>">&lt; Prev</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search_query); ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search_query); ?>">Next &gt;</a>
            <a href="?page=<?php echo $total_pages; ?>&search=<?php echo urlencode($search_query); ?>">Last &raquo;</a>
        <?php endif; ?>
    </div>

<script src="./jquery.js"></script>
<script>
    $(document).on('click', '.btndelete', function () {
        if (confirm("You are about to delete a record, continue?")) {
            let deptid = $(this).attr('deptid');

            $.ajax({
                url: './deletedept.php',
                method: 'POST',
                data: { deptid: deptid },
                success: function(response) {
                    alert(response);
                    location.reload();
                }
            })
        }
    })
</script>

</body>
</html>
