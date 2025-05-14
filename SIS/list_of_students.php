<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['gatepass'])) {
    header("Location: ./index.php");
    exit();
}

include 'connection.php';

// Collect filters from GET
$search = isset($_GET['search']) ? $con->real_escape_string($_GET['search']) : '';
$gender = isset($_GET['gender']) ? $con->real_escape_string($_GET['gender']) : '';
$course = isset($_GET['course']) ? $con->real_escape_string($_GET['course']) : '';
$enroll_date = isset($_GET['enroll_date']) ? $con->real_escape_string($_GET['enroll_date']) : '';

// Pagination setup
$limit = 2;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// WHERE conditions
$where = [];
if (!empty($search)) {
    $where[] = "(Student_Id LIKE '%$search%' OR First_Name LIKE '%$search%' OR Last_Name LIKE '%$search%')";
}
if (!empty($gender)) {
    $where[] = "Gender = '$gender'";
}
if (!empty($course)) {
    $where[] = "Course = '$course'";
}
if (!empty($enroll_date)) {
    $where[] = "Enrollment_Date = '$enroll_date'";
}
$whereSQL = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Total filtered rows
$total_result = $con->query("SELECT COUNT(*) as total FROM studinfo $whereSQL");
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

// Data fetch
$result = $con->query("SELECT * FROM studinfo $whereSQL LIMIT $limit OFFSET $offset");

// Get course list for dropdown
$courses_res = $con->query("SELECT DISTINCT Course FROM studinfo ORDER BY Course ASC");
$courses = [];
while ($row = $courses_res->fetch_assoc()) {
    $courses[] = $row['Course'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>List of Students</title>
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
        margin: 0px 20px 20px 40%;
        display: inline-block;
        text-align: center;
        width: 200px;
        font-size: 16px;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);

    }

    .add-button:hover {
      background-color: #c0392b;
    }

    .search-form {
      margin: 20px;
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
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
      width: auto;
      display: inline-block;
    }

    .search-form input[type="submit"]:hover {
      background-color: #2980b9;
      
    }

    .search-form select {
      padding: 7px 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

    .search-form button {
      padding: 7px 15px;
      background-color: #3498db;
      color: white;
      border: none;
      border-radius: 3px;
      cursor: pointer;
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

  <h1>List of Student</h1>
  <a class="add-button" href="add_studentinfo.php">+ Add New Student</a>

  <!-- 🔍 Search & Filter Form -->
  <form method="GET" class="search-form">
    <input type="text" name="search" placeholder="Search by ID or Name" value="<?php echo htmlspecialchars($search); ?>">
    <select name="gender">
      <option value="">All Genders</option>
      <option value="Male" <?php if ($gender === 'Male') echo 'selected'; ?>>Male</option>
      <option value="Female" <?php if ($gender === 'Female') echo 'selected'; ?>>Female</option>
    </select>
    <select name="course">
      <option value="">All Courses</option>
      <?php foreach ($courses as $c): ?>
        <option value="<?php echo $c; ?>" <?php if ($course === $c) echo 'selected'; ?>><?php echo $c; ?></option>
      <?php endforeach; ?>
    </select>
    <input type="date" name="enroll_date" value="<?php echo $enroll_date; ?>">
    <button type="submit">Search</button>
    <a href="list_of_students.php" style="margin-left:10px;">Reset</a>
  </form>

  <table>
    <thead>
      <tr>
        <th class="tabhead">STUDENT ID</th>
        <th class="tabhead">FIRSTNAME</th>
        <th class="tabhead">LASTNAME</th>
        <th class="tabhead">DATE OF BIRTH</th>
        <th class="tabhead">GENDER</th>
        <th class="tabhead">EMAIL</th>
        <th class="tabhead">PHONE NUMBER</th>
        <th class="tabhead">ADDRESS</th>
        <th class="tabhead">COURSE</th>
        <th class="tabhead">ENROLLMENT DATE</th>
        <th class="tabhead">ACTIONS</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?php echo $row['Student_Id']; ?></td>
        <td><?php echo $row['First_Name']; ?></td>
        <td><?php echo $row['Last_Name']; ?></td>
        <td><?php echo $row['Date_of_Birth']; ?></td>
        <td><?php echo $row['Gender']; ?></td>
        <td><?php echo $row['Email']; ?></td>
        <td><?php echo $row['Phone_Number']; ?></td>
        <td><?php echo $row['Address']; ?></td>
        <td><?php echo $row['Course']; ?></td>
        <td><?php echo $row['Enrollment_Date']; ?></td>
        <td>
          <a href="edit_stdlist.php?Student_Id=<?php echo $row['Student_Id']; ?>">EDIT</a>
          <button class="btndelete" stdid="<?php echo $row['Student_Id']; ?>">DELETE</button>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <div class="pagination">
    <?php if ($page > 1): ?>
      <a href="?page=1&search=<?php echo $search; ?>&gender=<?php echo $gender; ?>&course=<?php echo $course; ?>&enroll_date=<?php echo $enroll_date; ?>">&laquo; First</a>
      <a href="?page=<?php echo $page - 1; ?>&search=<?php echo $search; ?>&gender=<?php echo $gender; ?>&course=<?php echo $course; ?>&enroll_date=<?php echo $enroll_date; ?>">&lt; Prev</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
      <a href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&gender=<?php echo $gender; ?>&course=<?php echo $course; ?>&enroll_date=<?php echo $enroll_date; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>

    <?php if ($page < $total_pages): ?>
      <a href="?page=<?php echo $page + 1; ?>&search=<?php echo $search; ?>&gender=<?php echo $gender; ?>&course=<?php echo $course; ?>&enroll_date=<?php echo $enroll_date; ?>">Next &gt;</a>
      <a href="?page=<?php echo $total_pages; ?>&search=<?php echo $search; ?>&gender=<?php echo $gender; ?>&course=<?php echo $course; ?>&enroll_date=<?php echo $enroll_date; ?>">Last &raquo;</a>
    <?php endif; ?>
  </div>

  <script src="./jquery.js"></script>
  <script>
    $(document).on('click', '.btndelete', function () {
      if (confirm("You are about to delete a record, continue?")) {
        let stdid = $(this).attr('stdid');
        $.ajax({
          url: './deletestdin.php',
          method: 'POST',
          data: { stdid: stdid },
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
