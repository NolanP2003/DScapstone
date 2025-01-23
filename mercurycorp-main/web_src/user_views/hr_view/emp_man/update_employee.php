<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $role = $_POST['role'];
    $salary = $_POST['salary'];
    
    $query = "UPDATE employees SET name='$name', role='$role', salary='$salary' WHERE id='$id'";
    mysqli_query($conn, $query);
    header('Location: employee_management.php');
}
?>
<form method="POST">
    <input type="hidden" name="id" value="<?php echo $employee['id']; ?>">
    <input type="text" name="name" value="<?php echo $employee['name']; ?>">
    <input type="text" name="role" value="<?php echo $employee['role']; ?>">
    <input type="number" name="salary" value="<?php echo $employee['salary']; ?>">
    <button type="submit">Save Changes</button>
</form>