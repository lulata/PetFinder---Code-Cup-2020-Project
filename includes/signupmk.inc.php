<?php
if (isset($_POST['signup-submit'])) {

  require 'dbh.inc.php';

  $username = $_POST['uid'];
  $email = $_POST['mail'];
  $password = $_POST['pwd'];
  $passwordRepeat = $_POST['pwd-repeat'];

  if (empty($username) || empty($email) || empty($password) || empty($passwordRepeat)) {
    header("Location: ../signupmk.php?error=emptyfields&uid=".$username."&mail=".$email);
    exit();
  }elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) && !preg_match("/^[a-zA-Z0-9]*$/", $username)) {
    header("Location: ../signupmk.php?error=invalidemailuid");
    exit();
  }
   elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../signupmk.php?error=invalidmail&uid=".$username);
    exit();
  }elseif (!preg_match("/^[a-zA-Z0-9]*$/", $username)) {
    header("Location: ../signupmk.php?error=invaliduid&mail=".$email);
    exit();
}  elseif ($password !== $passwordRepeat) {
  header("Location: ../signupmk.php?error=passwordcheck&uid=".$username."&mail=".$email);
  exit();
} else {
  $sql = "SELECT uidUsers FROM users WHERE uidUsers=?";
  $stmt = mysqli_stmt_init($conn);
  if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("Location: ../signupmk.php?error=sqlerror");
    exit();
  } else {
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $resultsCheck = mysqli_stmt_num_rows($stmt);
    if ($resultsCheck > 0) {
      header("Location: ../signupmk.php?error=usertaken&mail=".$email);
      exit();
    } else {
      $sql = "INSERT INTO users (uidUsers, emailUsers, pwdUsers) VALUES (?, ?, ?)";
      $stmt = mysqli_stmt_init($conn);
      if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: ../signupmk.php?error=sqlerror");
        exit();
      }else {
        $hasedPwd = password_hash($password, PASSWORD_DEFAULT);

        mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hasedPwd);
        mysqli_stmt_execute($stmt);
        header("Location: ../signupmk.php?error=success");
        exit();
      }
    }
  }
}
mysqli_stmt_close($stmt);
mysqli_close($conn);
} else {
  header("Location: ../signup.php");
  exit();
}
?>
