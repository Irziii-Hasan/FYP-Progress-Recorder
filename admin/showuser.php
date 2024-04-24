<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>User List</h1>
            <a href="student.php" class="btn btn-primary">Add Student</a>
        </div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Database connection details
                $servername = "localhost"; // Change this if your database server is hosted elsewhere
                $username = "root"; // Change this if your database username is different
                $password = ""; // Change this if your database password is different
                $dbname = "FYP_Progress_Recorder"; // Change this to the desired database name

                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Check if the delete button is clicked
                if(isset($_GET['id']) && !empty($_GET['id'])) {
                    // Sanitize the input to prevent SQL injection
                    $user_id = mysqli_real_escape_string($conn, $_GET['id']);

                    // SQL to delete the user from the user table
                    $sql_delete_user = "DELETE FROM user WHERE id = '$user_id'";
                    
                    if ($conn->query($sql_delete_user) === TRUE) {
                     //   echo "User deleted successfully.";

                        // Delete corresponding student records based on the deleted user's ID
                        $sql_delete_student = "DELETE FROM student WHERE id = '$user_id'";
                        if ($conn->query($sql_delete_student) === TRUE) {
                          //  echo " Associated student records deleted successfully.";
                        } else {
                            echo "Error deleting associated student records: " . $conn->error;
                        }
                    } else {
                        echo "Error deleting user: " . $conn->error;
                    }
                }

                // SQL to select all users
                $sql = "SELECT id, username, email, role FROM user";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // Output data of each row
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["username"] . "</td>";
                        echo "<td>" . $row["email"] . "</td>";
                        echo "<td>" . $row["role"] . "</td>";
                        echo "<td>";
                        // Edit icon
                        echo "<a href='editStudent.php?id=" . $row["id"] . "' class='btn btn-warning btn-sm'><i class='bi bi-pencil'></i></a> ";
                        // Delete icon
                        echo "<a href='?id=" . $row["id"] . "' class='btn btn-danger btn-sm'><i class='bi bi-trash'></i></a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No users found</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
