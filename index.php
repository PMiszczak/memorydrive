<?php
// Start pracy z sesjami.
session_start();

// Jeśli osoba zalogowana próbuje wejść ponownie na stronę, przekieruj ją na odpowiedni dashboard względem ich permisji.
if ((isset($_SESSION['logged'])) && ($_SESSION['logged'] == true)) {
    if ($_SESSION['status'] === "admin") {
        header("Location: dashboard_admin.php");
        exit();
    } elseif ($_SESSION['status'] === "student") {
        header("Location: dashboard_student.php");
        exit();
    }
}

// Ustawienie zmiennej wybierającej język. W obecnej wersji poprzez przyrównanie wartości w przyszłości zostanie to ustawione w pliku konfiguracyjnym.
$language = "pl";

// Pobranie odpowiednich plików w przypadku każdego języka.
if ($language === "pl") {
    require_once('assets/languages/pl_PL.php');
} elseif ($language === "en") {
    require_once('assets/languages/en_US.php');
}

// Gdy użytkownik spróbuje się zalogować. Po kliknięciu przycisku zaloguj.
if ((isset($_POST['input_name'])) && (isset($_POST['input_password'])) && (isset($_POST['submit']))) {
    // Przywołaj plik z dostępem do bazy danych.
    require_once('connect.php');

    // Wycisz błędy poprzez try, catch.
    mysqli_report(MYSQLI_REPORT_STRICT);
    try {
        // Połącz się z bazą danych.
        $connect = new mysqli($host, $db_user, $db_password, $db_name);
        if ($connect->connect_errno != 0) {
            throw new Exception(mysqli_connect_errno());
        } else {
            // Jeśli połączenie z bazą danych przebiegło pomyślnie, wyłącz powiadamianie użytkownika o problemie z bazą danych.
            unset($_SESSION['login_error_dbproblem']);

            // Przypisanie wartości do zmiennych dla późniejszej wygody oraz początkowa walidacja danych.
            $login = $_POST['input_name'];
            $password = $_POST['input_password'];
            $login = htmlentities($login, ENT_QUOTES, "UTF-8");
            $password = htmlentities($password, ENT_QUOTES, "UTF-8");
            // Przemienienie wpisanego hasła na hash.
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Sprawdzenie, czy w bazie widnieje użytkownik o podanej nazwie.
            if ($result = $connect->query(sprintf("SELECT * FROM users WHERE nick='%s'", mysqli_real_escape_string($connect, $login)))) {
                if (($result->num_rows) > 0) {
                    // Jeżeli istnieje taki użytkownik, utwórz tablicę asocjacyjną.
                    $row = $result->fetch_assoc();
                    if (password_verify($password, $row['password'])) {
                        // Przypisanie zmiennych sesyjnych do odpowiednich danych.
                        $_SESSION['logged'] = true;
                        $_SESSION['id'] = $row['id'];
                        $_SESSION['login'] = $row['nick'];
                        $_SESSION['status'] = $row['status'];
                        $_SESSION['class'] = $row['class'];

                        // Wyłącz powiadamianie użytkownika o braku podanej kombinacji w bazie.
                        unset($_SESSION['login_error_nopairs']);
                        $result->free_result();

                        // Przenieś użytkownika do odpowiedniego dashboard'a.
                        if ($_SESSION['status'] === "admin") {
                            header("Location: dashboard_admin.php");
                        } elseif ($_SESSION['status'] === "student") {
                            header("Location: dashboard_student.php");
                        }
                    }
                    // W przeciwnych wypadkach ustaw zmienne sesyjne błędów.
                    else {
                        $_SESSION['login_error_nopairs'] = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Holy guacamole!</strong> '.$index_text_1.' 🤖'.'
                        <button type="button" class="close px-2" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                        </div>';
                    }
                } else {
                    $_SESSION['login_error_nopairs'] = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Holy guacamole!</strong> '.$index_text_1.' 🤖'.'
                    <button type="button" class="close px-2" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                    </div>';
                }
            }
            // Zakończ połączenie.
            $connect->close();
        }
    } catch (Exception $e) {
        // Jeżeli wystąpi jakikolwiek problem z bazą, wycisz błędy PHP, a w ich miejsce wrzuć błąd bazy danych.
        unset($_SESSION['login_error_nopairs']);
        $_SESSION['login_error_dbproblem'] = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Son of a gun!</strong> '.$global_7.' 🍪'.'
        <button type="button" class="close px-2" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
        </div>';
    }
}
?>
<!doctype html>
<html lang="<?php if ($language === "pl") {
    echo "pl-PL";
} elseif ($language === "en") {
    echo "en-GB";
} ?>">

<head>

  <meta charset="utf-8">
  <meta content="ie=edge" http-equiv="x-ua-compatible">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <meta name="robots" content="index, follow">

  <title>
    <?php echo $index_title; ?>
  </title>

  <meta name="description" content="">
  <meta name="language" content="">

  <meta property="og:title" content="">
  <meta property="og:description" content="">
  <meta property="og:site_name" content="Memory Drive">
  <meta property="og:type" content="website">
  <meta property="og:url" content="">

  <meta property="og:email" content="">
  <meta property="og:phone_number" content="">

  <meta property="og:country-name" content="">
  <meta property="og:locale" content="">
  <meta property="og:locality" content="">
  <meta property="og:postal-code" content="">
  <meta property="og:region" content="">
  <meta property="og:street-address" content="">

  <meta property="og:image:height" content="1435">
  <meta property="og:image:width" content="957">
  <meta property="og:image" content="assets/img/favicon/og-image.jpg">

  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:site" content="@">
  <meta name="twitter:title" content="">
  <meta name="twitter:description" content="">
  <meta name="twitter:image" content="">

  <link rel="apple-touch-icon" sizes="180x180" href="assets/img/favicon/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="assets/img/favicon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="assets/img/favicon/favicon-16x16.png">
  <link rel="manifest" href="assets/img/favicon/site.webmanifest">
  <link rel="mask-icon" href="assets/img/favicon/safari-pinned-tab.svg" color="#141414">
  <link rel="shortcut icon" href="assets/img/favicon/favicon.ico">
  <meta name="apple-mobile-web-app-title" content="Memory Drive">
  <meta name="application-name" content="Memory Drive">
  <meta name="msapplication-TileColor" content="#f5f5f5">
  <meta name="msapplication-config" content="img/favicon/browserconfig.xml">
  <meta name="theme-color" content="#f5f5f5">

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500|Material+Icons" rel="stylesheet">

  <link href="assets/css/style_index.min.css" rel="stylesheet">
</head>

<body class="text-center">

  <?php
  if (isset($_SESSION['login_error_nopairs'])) {
      echo $_SESSION['login_error_nopairs'];
      unset($_SESSION['login_error_nopairs']);
  }
  if (isset($_SESSION['login_error_dbproblem'])) {
      echo $_SESSION['login_error_dbproblem'];
      unset($_SESSION['login_error_dbproblem']);
  }
  ?>

  <!-- Preloader -->
  <div id="loader">
    <div class="spinner-border" role="status">
      <span class="sr-only">
        <?php echo $global_1; ?></span>
    </div>
  </div>
  <!-- End of preloader -->

  <!-- Login form -->
  <form class="form-signin" method="post">
    <img class="mb-5" src="assets/img/logo-black-min.png" height="72" width="auto">

    <label for="input_name" class="sr-only">
      <?php echo $index_text_2; ?></label>
    <input type="text" name="input_name" class="form-control" placeholder="<?php echo $index_text_3; ?>" required autofocus>

    <label for="input_password" class="sr-only">
      <?php echo $index_text_4; ?></label>
    <input type="password" name="input_password" class="form-control" placeholder="<?php echo $index_text_5; ?>" required>

    <button id="index_submit_button" class="btn btn-lg btn-block" type="submit" name="submit">
      <?php echo $index_text_6; ?></button>

    <p class="mt-3 mb-3 text-muted">&copy; 2019</p>
  </form>
  <!-- End of login form -->

  <!--                  __                      -->
  <!--              ___( o)>  (quack, quack)    -->
  <!--              \ <_. )                     -->
  <!--               `___'                      -->

  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
  <script src="assets/js/scripts.min.js"></script>
</body>

</html>
