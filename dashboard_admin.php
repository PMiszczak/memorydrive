<?php
// Start pracy z sesjami.
session_start();

// Inicjacja biblioteki FPDF do generowania plików PDF.
require('assets/fpdf/fpdf.php');
$pdf = new FPDF();

// Jeśli użytkownik nie będzie zalogowany, wyrzuć go.
if (!isset($_SESSION['logged'])) {
    header("Location: index.php");
    exit();
}

// Jeśli użytkownik nie będzie administratorem, lecz będzie zalogowany, przerzuć go do dashboard'a uczniowskiego.
if ((isset($_SESSION['logged'])) && ($_SESSION['logged'] == true)) {
    if ($_SESSION['status'] === "student") {
        header("Location: dashboard_student.php");
        exit();
    }
}

// Ustawienie zmiennej wybierającej język oraz zmiennej określającej długość czasu bez akcji przed wylogowaniem (wyrażony w minutach). W obecnej wersji poprzez przyrównanie wartości w przyszłości zostanie to ustawione w pliku konfiguracyjnym.
$session_time = 5;
$language = "pl";

// Funkcja sprawdzająca ile czasu minęło od ostatniej akcji. Jeśli czas przekroczy ten ustawiony w zmiennej "session_time" nastąpi wylogowanie.
if (isset($_SESSION['last_action'])) {
    $inactive_time_seconds = time() - $_SESSION['last_action'];
    $session_time_seconds = $session_time * 60;

    if ($inactive_time_seconds >= $session_time_seconds) {
        session_unset();
        session_destroy();
    }
}

$_SESSION['last_action'] = time();

// Przywołaj plik z dostępem do bazy danych.
require_once('connect.php');

// Pobranie odpowiednich plików w przypadku każdego języka.
if ($language === "pl") {
    require_once('assets/languages/pl_PL.php');
} elseif ($language === "en") {
    require_once('assets/languages/en_US.php');
}

// Rekurencyjna funkcja usuwająca wybrany folder na serwerze.
function delete_directory($dirname)
{
    if (is_dir($dirname)) {
        $dir_handle = opendir($dirname);
    }
    if (!$dir_handle) {
        return false;
    }
    while ($file = readdir($dir_handle)) {
        if ($file != "." && $file != "..") {
            if (!is_dir($dirname."/".$file)) {
                unlink($dirname."/".$file);
            } else {
                delete_directory($dirname.'/'.$file);
            }
        }
    }
    closedir($dir_handle);
    rmdir($dirname);
    return true;
}

// Dodawanie klasy.
if (isset($_POST['input_add_name']) && isset($_POST['input_add_size'])) {
    // Zmienna walidacyjna.
    $validationOk = 1;

    // Przypisanie danych do zmiennych dla późniejszej wygody.
    $input_add_name = $_POST['input_add_name'];
    $input_add_size = $_POST['input_add_size'];

    // Walidacja - czy znaki w nazwie klasy są mniejsze od 4?
    if (strlen($input_add_name) >= 4) {
        $validationOk = 0;
    }

    // Walidacja - czy znaki są znakami alfanumerycznymi?
    if (ctype_alnum($input_add_name) == false) {
        $validationOk = 0;
    }

    // Walidacja - czy wielkość klasy mieści się w zakresie od 1 do 35?
    if (($input_add_size > 35) && ($input_add_size < 1)) {
        $validationOk = 0;
    }

    // Walidacja - czy wielkość klasy jest zapisana znakami alfanumerycznymi?
    if (ctype_alnum($input_add_size) == false) {
        $validationOk = 0;
    }

    // Deklaracja generacji nowego dokumentu PDF oraz ustawienie podstawowych opcji.
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 8);

    // Wycisz błędy poprzez try, catch.
    mysqli_report(MYSQLI_REPORT_STRICT);
    try {
        // Połącz się z bazą danych.
        $connect = new mysqli($host, $db_user, $db_password, $db_name);
        if ($connect->connect_errno != 0) {
            throw new \Exception(mysqli_connect_errno());
        } else {
            // Jeśli połączenie z bazą danych przebiegło pomyślnie, sprawdź, czy klasa o identycznej już nie istnieje.
            $result = $connect->query("SELECT id FROM users WHERE class='$input_add_name'");
            if (!$result) {
                throw new \Exception($connect->error);
            }
            $class_num = $result->num_rows;
            if ($class_num > 0) {
                $validationOk = 0;
            }
            // Jeżeli wszystkie testy zostały zaliczone, stwórz konta ucznów.
            if ($validationOk == 1) {
                // Pętla dla każdego ucznia.
                for ($i = 0;$i < $input_add_size;$i++) {
                    // Odczytaj słowa z pliku.
                    $words = file_get_contents("assets/words.json");
                    $array = json_decode($words, true);

                    // Wylosuj z pliku losowe słowa i dodaj je do zmiennych.
                    $random_nick_1 = $array[rand(0, sizeof($array) - 1) ];
                    $random_nick_2 = $array[rand(0, sizeof($array) - 1) ];

                    // Wylosuj z pliku losowe słowa i dodaj je do zmiennych.
                    $random_password_1 = $array[rand(0, sizeof($array) - 1) ];
                    $random_password_2 = $array[rand(0, sizeof($array) - 1) ];

                    // Stwórz nazwę z dwóch losowych słów oddzielonych znakiem "_".
                    $nick = $random_nick_1 . "_" . $random_nick_2;
                    // Stwórz hasło z dwóch losowych słów oddzielonych znakiem "_".
                    $password = $random_password_1 . "_" . $random_password_2;

                    // Zahaszuj hasło.
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);

                    // Stwórz tabelę (1xIlość_uczniów) i każdą komórkę oddziel czarną linią. W środek każdej komórki wsadź jedno hasło i jedną nazwę.
                    $pdf->Cell(0, 10, "$admin_text_1: $nick, $admin_text_2: $password", 1, 1, 'C');
                    // Umieść nazwę, hash hasła oraz klasę w bazie danych.
                    if ($connect->query("INSERT INTO users VALUE (NULL, '$nick', '$password_hash', 'student', '$input_add_name')")) {
                        // Ustaw potwierdzenie powodzenia w zmiennej sesyjnej.
                        $_SESSION['add_success'] = '<div class="alert alert-success alert-dismissible fixed-bottom mb-0 fade show" role="alert">
                        <strong>Super!</strong> '.$admin_success_1.' 👍'.'
                        <button type="button" class="close px-2" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                        </div>';
                    } else {
                        throw new \Exception($connect->error);
                    }
                }
                // Wygeneruj plik PDF.
                $pdf->Output("$admin_text_3-$input_add_name.pdf", "D");
            } else {
                // Jeżeli wystąpi błąd, ustaw błąd w zmiennej sesyjnej.
                $_SESSION['add_error_validation'] = '<div class="alert alert-danger alert-dismissible fixed-bottom mb-0 fade show" role="alert">
                <strong>Cheese and rice!</strong> '.$admin_problem_1.' 📖'.'
                <button type="button" class="close px-2" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
                </div>';
            }
            // Zamknij połączenie.
            $connect->close();
        }
    } catch (\Exception $e) {
        // Jeżeli wystąpi błąd, ustaw błąd w zmiennej sesyjnej.
        unset($_SESSION['add_error_validation']);
        $_SESSION['add_error_dbproblem'] = '<div class="alert alert-danger alert-dismissible fixed-bottom mb-0 fade show" role="alert">
        <strong>Son of a gun!</strong> '.$global_7.'
        <button type="button" class="close px-2" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
        </div>';
    }
}

// Wymazywanie bazy danych.
if (isset($_POST['input_clear']) && isset($_POST['submit'])) {
    // Wycisz błędy poprzez try, catch.
    mysqli_report(MYSQLI_REPORT_STRICT);
    try {
        // Połącz się z bazą danych.
        $connect = new mysqli($host, $db_user, $db_password, $db_name);
        if ($connect->connect_errno != 0) {
            throw new \Exception(mysqli_connect_errno());
        } else {
            // Jeśli połączenie z bazą danych przebiegło pomyślnie, usuń wszystko z bazy danych powyżej ID równego 2.
            if ($connect->query("DELETE FROM users WHERE id > 2")) {
                // Przypisanie danych do zmiennych dla późniejszej wygody.
                $students_drive = './drive/';
                // Użyj funkcji "delete_directory" do rekurencyjnego usunięcia folderu z plikami uczniów.
                delete_directory($students_drive);
                // Stwórz nowy, pusty folder z plikami uczniów.
                mkdir($students_drive);
                // Ustaw potwierdzenie powodzenia w zmiennej sesyjnej.
                $_SESSION['clear_success'] = '<div class="alert alert-success alert-dismissible fixed-bottom mb-0 fade show" role="alert">
                <strong>Super!</strong> '.$admin_success_2.' 👍'.'
                <button type="button" class="close px-2" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
                </div>';
            } else {
                throw new \Exception($connect->error);
            }
        }
        // Zamknij połączenie.
        $connect->close();
    } catch (\Exception $e) {
        // Jeżeli wystąpi błąd, ustaw błąd w zmiennej sesyjnej.
        $_SESSION['clear_error_dbproblem'] = '<div class="alert alert-danger alert-dismissible fixed-bottom mb-0 fade show" role="alert">
        <strong>Son of a gun!</strong> '.$global_7.'
        <button type="button" class="close px-2" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
        </div>';
    }
}

// Sprawdzenie, czy istnieje folder z pracami do sprawdzenia, jeżeli nie, stwórz go.
$dir_to_check = './' . 'drive/to-check';
$dir_to_check_string = (string)$dir_to_check;
chmod($dir_to_check_string, 0777);
clearstatcache(true, $dir_to_check_string);

if (!is_dir($dir_to_check_string) && !file_exists($dir_to_check_string)) {
    mkdir($dir_to_check_string);
}
?>
<!doctype html>
<html lang="<?php if ($language === "pl") {
    echo "pl-PL" ;
} elseif ($language === "en") {
    echo "en-GB" ;
} ?>">

<head>

  <meta http-equiv="refresh" content="<?php echo $session_time_seconds+1; ?>" />

  <noscript>
    <meta http-equiv="refresh" content="1;url=">
  </noscript>

  <meta charset="utf-8">
  <meta content="ie=edge" http-equiv="x-ua-compatible">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <meta name="robots" content="index, follow">

  <title>
    <?php echo $admin_title; ?>
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
  <link href="https://fonts.googleapis.com/css?family=Roboto:300,400|Material+Icons" rel="stylesheet">

  <link href="assets/css/style_dashboard_admin.min.css" rel="stylesheet">

</head>

<body class="text-center">

  <?php
  if (isset($_SESSION['add_error_validation'])) {
      echo $_SESSION['add_error_validation'];
      unset($_SESSION['add_error_validation']);
  }
  if (isset($_SESSION['add_error_dbproblem'])) {
      echo $_SESSION['add_error_dbproblem'];
      unset($_SESSION['add_error_dbproblem']);
  }
  if (isset($_SESSION['add_success'])) {
      echo $_SESSION['add_success'];
      unset($_SESSION['add_success']);
  }
  if (isset($_SESSION['clear_error_dbproblem'])) {
      echo $_SESSION['clear_error_dbproblem'];
      unset($_SESSION['clear_error_dbproblem']);
  }
  if (isset($_SESSION['clear_success'])) {
      echo $_SESSION['clear_success'];
      unset($_SESSION['clear_success']);
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

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg bg-color">
    <a class="nav-link d-md-none d-none d-xl-block">
      <!--
      <div class="custom-control custom-switch">
        <input type="checkbox" class="custom-control-input" id="mode_change" name="mode_change">
        <label class="custom-control-label" for="mode_change">[...]</label>
      </div>
      -->
      <img src="assets/img/logo-black-min.png" height="50" width="auto">
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#Navbar" aria-controls="Navbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="Navbar">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a class="nav-link"><i class="material-icons pr-2">face</i>
            <?php echo $_SESSION['login']; ?></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="logout.php"><i class="material-icons pr-2"> power_settings_new</i>
            <?php echo $global_3; ?></a>
        </li>
      </ul>
    </div>
  </nav>
  <!-- End of navbar -->

  <!-- Buttons -->
  <div class="container buttons">
    <h3 class="pt-5">
      <?php echo $admin_text_4.":"; ?>
    </h3>
    <div class="row my-5">
      <div class="col-md-6 my-3">
        <a type="button" data-toggle="modal" data-target="#add_modal">
          <div class="btn-success py-5">
            <i class="material-icons pt-2">add</i>
          </div>
        </a>
      </div>
      <div class="col-md-6 my-3">
        <a type="button" data-toggle="modal" data-target="#clear_modal">
          <div class="btn-danger py-5">
            <i class="material-icons pt-2">clear</i>
          </div>
        </a>
      </div>
      <div class="w-100"></div>
      <div class="col-md-12 my-3">
        <a type="button" data-toggle="modal" data-target="#files_modal">
          <div class="btn-warning py-5">
            <i class="material-icons pt-2">folder</i>
          </div>
        </a>
      </div>
    </div>
  </div>
  <!-- End of buttons -->

  <!-- Add modal -->
  <div class="modal fade" id="add_modal" tabindex="-1" role="dialog" aria-labelledby="add_modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <?php echo $admin_text_5; ?>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p class="py-3">
            <?php echo $admin_text_6; ?>
          </p>
          <hr>
          <form method="post">
            <div class="form-group">
              <input type="text" class="form-control" name="input_add_name" placeholder="<?php echo $admin_text_7; ?>">
            </div>
            <div class="form-group">
              <input type="number" class="form-control" name="input_add_size" placeholder="<?php echo $admin_text_8; ?>" min="5" max="40">
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <?php echo $global_4; ?></button>
          <input class="btn btn-success" type="submit" name="submit" value="<?php echo $admin_text_9; ?>">
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- End of add modal -->

  <!-- Clear modal -->
  <div class="modal fade" id="clear_modal" tabindex="-1" role="dialog" aria-labelledby="clear_modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <?php echo $admin_text_10; ?>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p class="py-3 text-center">
            <?php echo $admin_text_11; ?>
          </p>
          <hr>
          <form method="post">
            <div class="custom-control custom-switch text-center">
              <input type="checkbox" class="custom-control-input" id="input_clear" name="input_clear" required>
              <label class="custom-control-label" for="input_clear">
                <?php echo $admin_text_12 ?></label>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <?php echo $global_4 ?></button>
          <input class="btn btn-danger" type="submit" name="submit" value="<?php echo $admin_text_13 ?>">
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- End of clear modal -->

  <!-- Files modal -->
  <div class="modal fade" id="files_modal" tabindex="-1" role="dialog" aria-labelledby="files_modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><?php echo $admin_text_14 ?></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item active" aria-current="page">
                <?php echo $admin_text_15 ?>
              </li>
              <li class="breadcrumb-item active" aria-current="page">
                <?php echo $admin_text_16 ?>
              </li>
            </ol>
          </nav>
          <?php
          // Wypisz wszystkie pliki z folderu z pracami do sprawdzenia z możliwością pobrania i usunięcia poszczególnych.
          $dir_to_check = './drive/to-check/';
          $dir_to_check_string = (string)$dir_to_check;

          if ($dir = opendir($dir_to_check_string)) {
              while (false !== ($entry = readdir($dir))) {
                  if ($entry != "." && $entry != "..") {
                      echo '<p style="float: left;" class="py-1">'.$entry.'<div style="text-align:right;">
                    <a href="'.$dir_to_check_string.$entry.'" style="float:right;" class="px-3" download><button class="btn btn-warning"><i class="material-icons" style="font-size: 1.5em !important;">cloud_download</i></button></a>
                    <form method="post" class="px-3"><button type="submit" name="delete" class="btn btn-warning" value="'.htmlspecialchars($entry).'"><i class="material-icons" style="font-size: 1.5em !important;">delete_forever</i></button></form>
                    </div></p><hr>'.'<br>';
                  }
              }
              closedir($dir);
          }
        ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <?php echo $global_4 ?></button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- End of files modal -->

  <!-- Footer -->
  <footer class="footer fixed-bottom py-3">
    <div class="container">
      <span class="text-muted"><i class="material-icons">code</i> with <i class="material-icons">favorite</i> and <i class="material-icons">local_pizza</i> by <a href="https://www.facebook.com/pawelmiszczaq" class="text-muted">Pawełek</a></span>
    </div>
  </footer>
  <!-- End of footer -->

  <!--                  __                      -->
  <!--              ___( o)>  (quack, quack)    -->
  <!--              \ <_. )                     -->
  <!--               `___'                      -->

  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
  <script src="assets/js/scripts.min.js"></script>
</body>

</html>
