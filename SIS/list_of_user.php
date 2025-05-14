<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['gatepass'])) {
    header("Location: ./index.php");
    exit();
}

include 'connection.php';

// Capture filter inputs
$search = isset($_GET['search']) ? $con->real_escape_string($_GET['search']) : '';
$id = isset($_GET['id']) ? $con->real_escape_string($_GET['id']) : '';
$role_id = isset($_GET['role_id']) ? $con->real_escape_string($_GET['role_id']) : '';
$created_at = isset($_GET['created_at']) ? $con->real_escape_string($_GET['created_at']) : '';
$updated_at = isset($_GET['updated_at']) ? $con->real_escape_string($_GET['updated_at']) : '';

// Pagination
$limit = 2;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Build WHERE clause
$where = [];
if (!empty($search)) {
    $where[] = "username LIKE '%$search%'";
}
if (!empty($role_id)) {
    $where[] = "role_id = '$role_id'";
}
if (!empty($id)){
  $where[] = "id = '$id'";
}
if (!empty($created_at)) {
    $where[] = "DATE(created_at) = '$created_at'";
}
if (!empty($updated_at)) {
    $where[] = "DATE(updated_at) = '$updated_at'";
}
$whereSQL = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Count total records with filter
$total_result = $con->query("SELECT COUNT(*) as total FROM users $whereSQL");
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

// Fetch data
$result = $con->query("SELECT * FROM users $whereSQL LIMIT $limit OFFSET $offset");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>List of User Accounts</title>
  <link href="dashtyle.css" rel="stylesheet">
  <link href="addformstyle.css" rel="stylesheet">
  <style>
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

      form.filter-form {
        margin: 20px;
      }

      form.filter-form input,
      form.filter-form select {
        padding: 6px;
        margin-right: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
        font-size: 14px;
      }

      form.filter-form button {
        padding: 7px 15px;
        background-color: #2980b9;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        transition: background-color 0.3s;
      }

      form.filter-form button:hover {
        background-color: #1f5e8c;
      }

      form.filter-form a {
        text-decoration: none;
        color: white;
        background-color: #7f8c8d;
        padding: 7px 15px;
        border-radius: 5px;
        font-size: 14px;
        transition: background-color 0.3s;
      }

      form.filter-form a:hover {
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
        <img src="image/admin.png" alt="Admin Icon" class="admin-icon"/>
      </a>
      <span class="admin-label">Admin</span>
    </div>
  </nav>

  <h1>List of User Accounts</h1>

  <a class="add-button" href="add_user_account.php">+ Add New User</a>

  <!-- Filter Form -->
  <form method="GET" class="filter-form">
    <input type="text" name="search" placeholder="Search username" value="<?php echo htmlspecialchars($search); ?>">
    <select name="role_id">
      <option value="">All Roles</option>
      <option value="1" <?php if ($role_id === '1') echo 'selected'; ?>>Admin</option>
      <option value="2" <?php if ($role_id === '2') echo 'selected'; ?>>Student</option>
      <!-- Add more roles if needed -->
    </select>
    <input type="date" name="created_at" value="<?php echo htmlspecialchars($created_at); ?>">
    <input type="date" name="updated_at" value="<?php echo htmlspecialchars($updated_at); ?>">
    <button type="submit">Search</button>
    <a href="list_of_user.php" style="margin-left:10px;">Reset</a>
  </form>

  <!-- Data Table -->
  <table border="1">
    <thead>
      <tr>
        <th>ID</th>
        <th>USERNAME</th>
        <th>PASSWORD</th>
        <th>ROLE ID</th>
        <th>CREATED AT</th>
        <th>UPDATED AT</th>
        <th>ACTIONS</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?php echo $row['id']; ?></td>
          <td><?php echo $row['username']; ?></td>
          <td><?php echo $row['password']; ?></td>
          <td><?php echo $row['role_id']; ?></td>
          <td><?php echo $row['created_at']; ?></td>
          <td><?php echo $row['updated_at']; ?></td>
          <td>
            <a href="edit_user.php?id=<?php echo $row['id']; ?>">EDIT</a>
            <button class="btndelete" recordid="<?php echo $row['id']; ?>">DELETE</button>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <!-- Pagination -->
  <div class="pagination" style="margin: 20px;">
    <?php if ($page > 1): ?>
      <a href="?page=1&search=<?php echo $search; ?>&role_id=<?php echo $role_id; ?>&created_at=<?php echo $created_at; ?>&updated_at=<?php echo $updated_at; ?>">&laquo; First</a>
      <a href="?page=<?php echo $page - 1; ?>&search=<?php echo $search; ?>&role_id=<?php echo $role_id; ?>&created_at=<?php echo $created_at; ?>&updated_at=<?php echo $updated_at; ?>">&lt; Prev</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
      <a href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&role_id=<?php echo $role_id; ?>&created_at=<?php echo $created_at; ?>&updated_at=<?php echo $updated_at; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>

    <?php if ($page < $total_pages): ?>
      <a href="?page=<?php echo $page + 1; ?>&search=<?php echo $search; ?>&role_id=<?php echo $role_id; ?>&created_at=<?php echo $created_at; ?>&updated_at=<?php echo $updated_at; ?>">Next &gt;</a>
      <a href="?page=<?php echo $total_pages; ?>&search=<?php echo $search; ?>&role_id=<?php echo $role_id; ?>&created_at=<?php echo $created_at; ?>&updated_at=<?php echo $updated_at; ?>">Last &raquo;</a>
    <?php endif; ?>
  </div>

  <script src="./jquery.js"></script>
  <script>
    $(document).on('click', '.btndelete', function () {
      if (confirm("You are about to delete a record, continue?")) {
        let recordid = $(this).attr('recordid');
        $.ajax({
          url: './delete.php',
          method: 'POST',
          data: { recordid: recordid },
          success: function(response) {
            alert(response);
            location.reload();
          }
        });
      }
    });
  </script>
</body>
</html>
