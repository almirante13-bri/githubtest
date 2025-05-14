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
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Total rows with search filter
$total_result = $con->prepare("SELECT COUNT(*) as total FROM course WHERE course_name LIKE ? OR course_id LIKE ?");
$search_param = "%" . $search_query . "%";
$total_result->bind_param("ss", $search_param, $search_param);
$total_result->execute();
$total_row = $total_result->get_result()->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

// Data query with search filter
$result = $con->prepare("SELECT * FROM course WHERE course_name LIKE ? OR course_id LIKE ? LIMIT ? OFFSET ?");
$result->bind_param("ssii", $search_param, $search_param, $limit, $offset);
$result->execute();
$data = $result->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List of Course</title>
    <link href="dashtyle.css" rel="stylesheet">
    <link href="addformstyle.css" rel="stylesheet">
    <style>

        /* Search bar */
        .search-form {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }

        .search-form input[type="text"] {
            padding: 8px;
            font-size: 14px;
            width: 250px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .search-form input[type="submit"] {
            padding: 8px 15px;
            font-size: 14px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .search-form input[type="submit"]:hover {
            background-color: #2980b9;
        }

        /* Button for adding new course */
        .add-button {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin: 0px 20px 20px 20px;
            display: inline-block;
            text-align: center;
            width: 200px;
            font-size: 16px;
            margin-left: 40%;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        }

        .add-button:hover {
            background-color: #c0392b;
        }

        form.search-form a {
        text-decoration: none;
        color: white;
        background-color: #7f8c8d;
        padding: 10px 16px;
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

    <h1>List of Courses</h1>

    <!-- Search Form -->
    <form class="search-form" method="GET" action="list_of_course.php">
        <input type="text" name="search" placeholder="Search by course ID or name" value="<?php echo htmlspecialchars($search_query); ?>">
        <input type="submit" value="Search">
        <a href="list_of_course.php" style="margin-left:2px;">Reset</a>
    </form>

    <!-- Add New Course Button -->
    <a class="add-button" href="add_course.php">+ Add New Course</a>

    <!-- Table for Courses -->
    <table>
        <thead>
            <tr>
                <th>COURSE ID</th>
                <th>COURSE NAME</th>
                <th>CREDIT</th>
                <th>ACTIONS</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $data->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['course_id']; ?></td>
                <td><?php echo $row['course_name']; ?></td>
                <td><?php echo $row['credit']; ?></td>
                <td>
                    <a href="editcourse.php?course_id=<?php echo $row['course_id']; ?>">EDIT</a>
                    <button class="btndelete" courseid="<?php echo $row['course_id']; ?>">DELETE</button>
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
            let courseid = $(this).attr('courseid');

            $.ajax({
                url: './delcourse.php',
                method: 'POST',
                data: { courseid: courseid },
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
