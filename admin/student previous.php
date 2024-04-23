<?php
  $title = "FYP| Student";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title><?php echo $title; ?></title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    .form-all{
        width: 700px;
    }
  </style>
</head>
<body>

<div class="container mt-3 form-all">
  <h2>Add Student</h2>
  <form action="/action_page.php" method="post">
    <div class="mb-3 mt-3">
        <label for="name">Student Full Name:</label>
        <input type="text" class="form-control" id="name" placeholder="Enter name" name="name" required>
      </div>
      <div class="mb-3 mt-3">
        <label for="email">Email:</label>
        <input type="email" class="form-control" id="email" placeholder="Enter email" name="email" required>
      </div>
      <div class="mb-3 mt-3">
        <label for="enrollment">Enrollment Number:</label>
        <input type="text" class="form-control" id="enrollment" placeholder="Enter enrollment" name="enrollment" required>
      </div>
      <div class="mb-3 mt-3">
        <label for="role" class="form-label">Role:</label>
        <input type="text" id="role" class="form-control" value="Student" readonly>
      </div>
      <div class="mb-3 mt-3">
        <label for="degree-program">Degree Program:</label>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="degree-program" id="degree-program-cs" value="CS">
            <label class="form-check-label" for="degree-program-cs">CS</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="degree-program" id="degree-program-se" value="SE">
            <label class="form-check-label" for="degree-program-se">SE</label>
          </div>
          <div class="mb-3 mt-3">
            <label for="batch" class="form-label">Batch</label>
            <select id="batch" class="form-select" name="batch" required>
              <option selected>2021</option>
              <option>2022</option>
              <option>2023</option>
              <option>2024</option>
              <option>2025</option>
              <option>2026</option>
            </select>
          </div>
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>
  </form>
</div>

</body>
</html>
