<?php
// Start pracy z sesjami.
session_start();

// Jeśli użytkownik nie będzie zalogowany, wyrzuć go.
if (!isset($_SESSION['logged'])) {
    header("Location: index.php");
    exit();
}

// Jeśli użytkownik nie będzie uczniem, lecz będzie zalogowany, przerzuć go do dashboard'a administracyjnego.
if ((isset($_SESSION['logged'])) && ($_SESSION['logged'] == true)) {
    if ($_SESSION['status'] === "admin") {
        header("Location: dashboard_admin.php");
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

// Sprawdzenie, czy istnieją foldery: klasowe, imienne oraz plikowe, a w przypadku gdy nie, stworzenie ich.
$dir_class = './' . 'drive/' . $_SESSION['class'];
$dir_login = './' . 'drive/' . $_SESSION['class'] . '/' . $_SESSION['login'];

$dir_images = './' . 'drive/' . $_SESSION['class'] . '/' . $_SESSION['login'] . '/zdjecia';
$dir_presentations = './' . 'drive/' . $_SESSION['class'] . '/' . $_SESSION['login'] . '/prezentacje_i_arkusze';
$dir_text = './' . 'drive/' . $_SESSION['class'] . '/' . $_SESSION['login'] . '/pliki_tekstowe';
$dir_code = './' . 'drive/' . $_SESSION['class'] . '/' . $_SESSION['login'] . '/kod';

$dir_class_string = (string)$dir_class;
$dir_login_string = (string)$dir_login;

$dir_images_string = (string)$dir_images;
$dir_presentations_string = (string)$dir_presentations;
$dir_text_string = (string)$dir_text;
$dir_code_string = (string)$dir_code;

chmod($dir_class_string, 0777);
chmod($dir_login_string, 0777);

chmod($dir_images_string, 0777);
chmod($dir_presentations_string, 0777);
chmod($dir_text_string, 0777);
chmod($dir_code_string, 0777);

clearstatcache(true, $dir_class_string);

if (!is_dir($dir_class_string) && !file_exists($dir_class_string)) {
    mkdir($dir_class_string);
}

clearstatcache(true, $dir_login_string);

if (!is_dir($dir_login_string) && !file_exists($dir_login_string)) {
    mkdir($dir_login_string);
}

clearstatcache(true, $dir_images_string);

if (!is_dir($dir_images_string) && !file_exists($dir_images_string)) {
    mkdir($dir_images_string);
}

clearstatcache(true, $dir_presentations_string);

if (!is_dir($dir_presentations_string) && !file_exists($dir_presentations_string)) {
    mkdir($dir_presentations_string);
}

clearstatcache(true, $dir_text_string);

if (!is_dir($dir_text_string) && !file_exists($dir_text_string)) {
    mkdir($dir_text_string);
}

clearstatcache(true, $dir_code_string);

if (!is_dir($dir_code_string) && !file_exists($dir_code_string)) {
    mkdir($dir_code_string);
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
    <?php echo $student_title; ?>
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

  <link href="assets/css/style_dashboard_student.min.css" rel="stylesheet">

</head>

<body class="text-center">

  <?php
  if (isset($_SESSION['upload_error_validation'])) {
      echo $_SESSION['upload_error_validation'];
      unset($_SESSION['upload_error_validation']);
  }
  if (isset($_SESSION['upload_error_uploadproblem'])) {
      echo $_SESSION['upload_error_uploadproblem'];
      unset($_SESSION['upload_error_uploadproblem']);
  }
  if (isset($_SESSION['upload_success'])) {
      echo $_SESSION['upload_success'];
      unset($_SESSION['upload_success']);
  }
  if (isset($_SESSION['delete_success'])) {
      echo $_SESSION['delete_success'];
      unset($_SESSION['delete_success']);
  }
  if (isset($_SESSION['send_success'])) {
      echo $_SESSION['send_success'];
      unset($_SESSION['send_success']);
  }
  if (isset($_SESSION['new_login_error_validation'])) {
      echo $_SESSION['new_login_error_validation'];
      unset($_SESSION['new_login_error_validation']);
  }
  if (isset($_SESSION['new_login_error_already'])) {
      echo $_SESSION['new_login_error_already'];
      unset($_SESSION['new_login_error_already']);
  }
  if (isset($_SESSION['new_login_error_dbproblem'])) {
      echo $_SESSION['new_login_error_dbproblem'];
      unset($_SESSION['new_login_error_dbproblem']);
  }
  if (isset($_SESSION['new_password_error_validation'])) {
      echo $_SESSION['new_password_error_validation'];
      unset($_SESSION['new_password_error_validation']);
  }
  if (isset($_SESSION['new_password_error_dbproblem'])) {
      echo $_SESSION['new_password_error_dbproblem'];
      unset($_SESSION['new_password_error_dbproblem']);
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
        <label class="custom-control-label" for="mode_change">Włącz ciemny motyw</label>
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
  <div class="container">
    <h3 class="pt-5">
      <?php echo $student_text_1; ?>
    </h3>
    <div class="row my-5">
      <div class="col-md-3 my-3">
        <a type="button" data-toggle="modal" data-target="#image_modal">
          <div class="btn-warning py-5">
            <i class="material-icons pt-2">image</i>
          </div>
        </a>
      </div>
      <div class="col-md-3 my-3">
        <a type="button" data-toggle="modal" data-target="#presentation_and_spreadsheet_modal">
          <div class="btn-success py-5">
            <i class="material-icons pt-2">pie_chart</i>
          </div>
        </a>
      </div>
      <div class="col-md-3 my-3">
        <a type="button" data-toggle="modal" data-target="#text_modal">
          <div class="btn-danger py-5">
            <i class="material-icons pt-2">format_size</i>
          </div>
        </a>
      </div>
      <div class="col-md-3 my-3">
        <a type="button" data-toggle="modal" data-target="#code_modal">
          <div class="btn-primary py-5">
            <i class="material-icons pt-2">code</i>
          </div>
        </a>
      </div>
    </div>
    <div class="row my-5">
      <div class="col-md-6 my-3">
        <h3>
          <?php echo $student_text_2; ?>
        </h3>
        <div class="row my-3">
          <div class="col-md-12 my-3">
            <a type="button" data-toggle="modal" data-target="#login_modal">
              <div class="btn-info py-5">
                <i class="material-icons pt-2">account_circle</i>
              </div>
            </a>
          </div>
          <div class="w-100"></div>
          <div class="col-md-12 my-3">
            <a type="button" data-toggle="modal" data-target="#password_modal">
              <div class="btn-info py-5">
                <i class="material-icons pt-2">lock</i>
              </div>
            </a>
          </div>
        </div>
      </div>
      <div class="col-md-6 my-3">
        <h3>
          <?php echo $student_text_3; ?>
        </h3>
        <div class="row my-3">
          <div class="col-md-6 my-3">
            <a type="button" data-toggle="modal" data-target="#image_modal_show">
              <div class="btn-warning py-5">
                <i class="material-icons pt-2">image</i>
              </div>
            </a>
          </div>
          <div class="col-md-6 my-3">
            <a type="button" data-toggle="modal" data-target="#presentation_and_spreadsheet_modal_show">
              <div class="btn-success py-5">
                <i class="material-icons pt-2">pie_chart</i>
              </div>
            </a>
          </div>
          <div class="w-100"></div>
          <div class="col-md-6 my-3">
            <a type="button" data-toggle="modal" data-target="#text_modal_show">
              <div class="btn-danger py-5">
                <i class="material-icons pt-2">format_size</i>
              </div>
            </a>
          </div>
          <div class="col-md-6 my-3">
            <a type="button" data-toggle="modal" data-target="#code_modal_show">
              <div class="btn-primary py-5">
                <i class="material-icons pt-2">code</i>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- End of buttons -->

  <!-- Image modal -->
  <div class="modal fade" id="image_modal" tabindex="-1" role="dialog" aria-labelledby="image_modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <?php echo $student_text_4; ?>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="assets/uploads/upload_image.php" method="post" enctype="multipart/form-data">
            <div class="input-group mb-3">
              <div class="custom-file">
                <input type="file" class="custom-file-input" name="file_to_upload" id="file_to_upload" accept=".jpg, .jpeg, .png, .gif">
                <label class="custom-file-label" for="file_to_upload">
                  <?php echo $global_2; ?></label>
              </div>
            </div>
            <p class="py-3">
              <?php echo $student_text_5; ?>
            </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <?php echo $global_4; ?></button>
          <input class="btn btn-warning" type="submit" name="submit" value="<?php echo $global_5; ?>">
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- End of image modal -->

  <!-- Presentation and spreadsheet modal -->
  <div class="modal fade" id="presentation_and_spreadsheet_modal" tabindex="-1" role="dialog" aria-labelledby="presentation_and_spreadsheet_modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <?php echo $student_text_6; ?>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="assets/uploads/upload_presentation_and_spreadsheet.php" method="post" enctype="multipart/form-data">
            <div class="input-group mb-3">
              <div class="custom-file">
                <input type="file" class="custom-file-input" name="file_to_upload" id="file_to_upload" accept=".pptx, .ppt, .key, .xls, .xlsx, .xlsm, .xlsb, .numbers">
                <label class="custom-file-label" for="file_to_upload">
                  <?php echo $global_2; ?></label>
              </div>
            </div>
            <p class="py-3">
              <?php echo $student_text_7; ?>
            </p>
            <hr>
            <p class="py-3">
              <?php echo $student_text_8; ?>
            </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <?php echo $global_4; ?></button>
          <input class="btn btn-success" type="submit" name="submit" value="<?php echo $global_5; ?>">
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- End of presentation and spreadsheet modal -->

  <!-- Text modal -->
  <div class="modal fade" id="text_modal" tabindex="-1" role="dialog" aria-labelledby="text_modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <?php echo $student_text_9; ?>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="assets/uploads/upload_text.php" method="post" enctype="multipart/form-data">
            <div class="input-group mb-3">
              <div class="custom-file">
                <input type="file" class="custom-file-input" name="file_to_upload" id="file_to_upload" accept=".txt, .docx, .doc, .pdf, .pages">
                <label class="custom-file-label" for="file_to_upload">
                  <?php echo $global_2; ?></label>
              </div>
            </div>
            <p class="py-3">
              <?php echo $student_text_10; ?>
            </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <?php echo $global_4; ?></button>
          <input class="btn btn-danger" type="submit" name="submit" value="<?php echo $global_5; ?>">
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- End of text modal -->

  <!-- Code modal -->
  <div class="modal fade" id="code_modal" tabindex="-1" role="dialog" aria-labelledby="code_modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <?php echo $student_text_11; ?>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="assets/uploads/upload_code.php" method="post" enctype="multipart/form-data">
            <div class="input-group mb-3">
              <div class="custom-file">
                <input type="file" class="custom-file-input" name="file_to_upload" id="file_to_upload" accept=".cpp, .css, .html, .php, .js">
                <label class="custom-file-label" for="file_to_upload">
                  <?php echo $global_2; ?></label>
              </div>
            </div>
            <p class="py-3">
              <?php echo $student_text_12; ?>
            </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <?php echo $global_4; ?></button>
          <input class="btn btn-primary" type="submit" name="submit" value="<?php echo $global_5; ?>">
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- End of code modal -->

  <!-- Image show modal -->
  <div class="modal fade" id="image_modal_show" tabindex="-1" role="dialog" aria-labelledby="image_modal_show" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <?php echo $student_text_13; ?>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item active">
                <?php echo $_SESSION['class']; ?>
              </li>
              <li class="breadcrumb-item active">
                <?php echo $_SESSION['login']; ?>
              </li>
              <li class="breadcrumb-item active" aria-current="page">
                <?php echo $student_text_14; ?>
              </li>
            </ol>
          </nav>
          <hr>
          <?php
          // Przypisanie danych do zmiennych dla późniejszej wygody.
          $dir_images = './drive/' . $_SESSION['class'] . '/' . $_SESSION['login'] . '/zdjecia' . '/';
          $dir_images_string = (string)$dir_images;
          $dir_to_check = './drive/to-check/';
          $dir_to_check_string = (string)$dir_to_check;

          // Rekurencyjne otwieranie folderów i wypisywanie plików. Dodanie możliwości usunięcia, wysłania do administratora lub pobrania plików.
          if ($dir = opendir($dir_images_string)) {
              while (false !== ($entry = readdir($dir))) {
                  if ($entry != "." && $entry != "..") {
                      echo '<p style="float: left;" class="py-1">'.$entry.'<div style="text-align:right;">
                      <a href="'.$dir_images.$entry.'" class="px-2" download><button class="btn btn-warning" title="Pobierz plik"><i class="material-icons" style="font-size: 1.5em !important;">cloud_download</i></button></a>
                      <form method="post" class="px-2" style="float:right;"><button type="submit" name="delete" class="btn btn-warning" value="'.htmlspecialchars($entry).'" title="Usuń plik"><i class="material-icons" style="font-size: 1.5em !important;">delete_forever</i></button></form>
                      <form method="post" class="px-2" style="float:right;"><button type="submit" name="send" class="btn btn-warning" value="'.htmlspecialchars($entry).'" title="Wyślij plik do sprawdzenia"><i class="material-icons" style="font-size: 1.5em !important;">send</i></button></form>
                      </div></p><hr>'.'<br>';
                  }
              }
              closedir($dir);
              if (array_key_exists("send", $_POST)) {
                  if (copy($dir_images_string.$_POST['send'], $dir_to_check_string.$_SESSION['class'].'_'.$_SESSION['login'].'_'.$_POST['send'])) {
                      $_SESSION['send_success'] = '<div class="alert alert-success alert-dismissible fade show fixed-bottom mb-0" role="alert">
                      <strong>Nice!</strong> '.$student_success_1.'! 🤞
                      <button type="button" class="close px-2" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                      </button>
                      </div>';
                      echo '<script>window.location.replace("index.php");</script>';
                  }
              }
              if (array_key_exists("delete", $_POST)) {
                  unlink($dir_images_string.$_POST['delete']);
                  $_SESSION['delete_success'] = '<div class="alert alert-success alert-dismissible fade show fixed-bottom mb-0" role="alert">
                  <strong>Super!</strong> '.$student_success_2.'! 🤞
                  <button type="button" class="close px-2" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
                  </div>';
                  echo '<script>window.location.replace("index.php");</script>';
              }
          }
        ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-warning" data-dismiss="modal">
            <?php echo $global_4; ?></button>
        </div>
      </div>
    </div>
  </div>
  <!-- End of image show modal -->

  <!-- Presentation and spreadsheet show modal -->
  <div class="modal fade" id="presentation_and_spreadsheet_modal_show" tabindex="-1" role="dialog" aria-labelledby="presentation_and_spreadsheet_modal_show" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <?php echo $student_text_15; ?>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item active">
                <?php echo $_SESSION['class']; ?>
              </li>
              <li class="breadcrumb-item active">
                <?php echo $_SESSION['login']; ?>
              </li>
              <li class="breadcrumb-item active" aria-current="page">
                <?php echo $student_text_16; ?>
              </li>
            </ol>
          </nav>
          <hr>
          <?php
          // Przypisanie danych do zmiennych dla późniejszej wygody.
          $dir_presentations = './drive/' . $_SESSION['class'] . '/' . $_SESSION['login'] . '/prezentacje_i_arkusze' . '/';
          $dir_presentations_string = (string)$dir_presentations;
          $dir_to_check = './drive/to-check/';
          $dir_to_check_string = (string)$dir_to_check;

          // Rekurencyjne otwieranie folderów i wypisywanie plików. Dodanie możliwości usunięcia, wysłania do administratora lub pobrania plików.
          if ($dir = opendir($dir_presentations_string)) {
              while (false !== ($entry = readdir($dir))) {
                  if ($entry != "." && $entry != "..") {
                      echo '<p style="float: left;" class="py-1">'.$entry.'<div style="text-align:right;">
                      <a href="'.$dir_presentations_string.$entry.'" class="px-2" download><button class="btn btn-success" title="Pobierz plik"><i class="material-icons" style="font-size: 1.5em !important;">cloud_download</i></button></a>
                      <form method="post" class="px-2" style="float:right;"><button type="submit" name="delete" class="btn btn-success" value="'.htmlspecialchars($entry).'" title="Usuń plik"><i class="material-icons" style="font-size: 1.5em !important;">delete_forever</i></button></form>
                      <form method="post" class="px-2" style="float:right;"><button type="submit" name="send" class="btn btn-success" value="'.htmlspecialchars($entry).'" title="Wyślij plik do sprawdzenia"><i class="material-icons" style="font-size: 1.5em !important;">send</i></button></form>
                      </div></p><hr>'.'<br>';
                  }
              }
              closedir($dir);
              if (array_key_exists("send", $_POST)) {
                  if (copy($dir_presentations_string.$_POST['send'], $dir_to_check_string.$_SESSION['class'].'_'.$_SESSION['login'].'_'.$_POST['send'])) {
                      $_SESSION['send_success'] = '<div class="alert alert-success alert-dismissible fade show fixed-bottom mb-0" role="alert">
                      <strong>Nice!</strong> '.$student_success_1.'! 🤞
                      <button type="button" class="close px-2" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                      </button>
                      </div>';
                      echo '<script>window.location.replace("index.php");</script>';
                  }
              }
              if (array_key_exists("delete", $_POST)) {
                  unlink($dir_presentations_string.$_POST['delete']);
                  $_SESSION['delete_success'] = '<div class="alert alert-success alert-dismissible fade show fixed-bottom mb-0" role="alert">
                  <strong>Super!</strong> '.$student_success_2.'! 🤞
                  <button type="button" class="close px-2" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
                  </div>';
                  echo '<script>window.location.replace("index.php");</script>';
              }
          }
        ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success" data-dismiss="modal">
            <?php echo $global_4; ?></button>
        </div>
      </div>
    </div>
  </div>
  <!-- Presentation and spreadsheet show modal -->

  <!-- Text show modal -->
  <div class="modal fade" id="text_modal_show" tabindex="-1" role="dialog" aria-labelledby="text_modal_show" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <?php echo $student_text_17; ?>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item active">
                <?php echo $_SESSION['class']; ?>
              </li>
              <li class="breadcrumb-item active">
                <?php echo $_SESSION['login']; ?>
              </li>
              <li class="breadcrumb-item active" aria-current="page">
                <?php echo $student_text_18; ?>
              </li>
            </ol>
          </nav>
          <hr>
          <?php
          // Przypisanie danych do zmiennych dla późniejszej wygody.
          $dir_text = './drive/' . $_SESSION['class'] . '/' . $_SESSION['login'] . '/pliki_tekstowe' . '/';
          $dir_text_string = (string)$dir_text;
          $dir_to_check = './drive/to-check/';
          $dir_to_check_string = (string)$dir_to_check;

          // Rekurencyjne otwieranie folderów i wypisywanie plików. Dodanie możliwości usunięcia, wysłania do administratora lub pobrania plików.
          if ($dir = opendir($dir_text_string)) {
              while (false !== ($entry = readdir($dir))) {
                  if ($entry != "." && $entry != "..") {
                      echo '<p style="float: left;" class="py-1">'.$entry.'<div style="text-align:right;">
                      <a href="'.$dir_text_string.$entry.'" class="px-2" download><button class="btn btn-danger" title="Pobierz plik"><i class="material-icons" style="font-size: 1.5em !important;">cloud_download</i></button></a>
                      <form method="post" class="px-2" style="float:right;"><button type="submit" name="delete" class="btn btn-danger" value="'.htmlspecialchars($entry).'" title="Usuń plik"><i class="material-icons" style="font-size: 1.5em !important;">delete_forever</i></button></form>
                      <form method="post" class="px-2" style="float:right;"><button type="submit" name="send" class="btn btn-danger" value="'.htmlspecialchars($entry).'" title="Wyślij plik do sprawdzenia"><i class="material-icons" style="font-size: 1.5em !important;">send</i></button></form>
                      </div></p><hr>'.'<br>';
                  }
              }
              closedir($dir);
              if (array_key_exists("send", $_POST)) {
                  if (copy($dir_text_string.$_POST['send'], $dir_to_check_string.$_SESSION['class'].'_'.$_SESSION['login'].'_'.$_POST['send'])) {
                      $_SESSION['send_success'] = '<div class="alert alert-success alert-dismissible fade show fixed-bottom mb-0" role="alert">
                      <strong>Nice!</strong> '.$student_success_1.'! 🤞
                      <button type="button" class="close px-2" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                      </button>
                      </div>';
                      echo '<script>window.location.replace("index.php");</script>';
                  }
              }
              if (array_key_exists("delete", $_POST)) {
                  unlink($dir_text_string.$_POST['delete']);
                  $_SESSION['delete_success'] = '<div class="alert alert-success alert-dismissible fade show fixed-bottom mb-0" role="alert">
                  <strong>Super!</strong> '.$student_success_2.'! 🤞
                  <button type="button" class="close px-2" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
                  </div>';
                  echo '<script>window.location.replace("index.php");</script>';
              }
          }
        ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">
            <?php echo $global_4; ?></button>
        </div>
      </div>
    </div>
  </div>
  <!-- Text show modal -->

  <!-- Code show modal -->
  <div class="modal fade" id="code_modal_show" tabindex="-1" role="dialog" aria-labelledby="code_modal_show" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <?php echo $student_text_19; ?>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item active">
                <?php echo $_SESSION['class']; ?>
              </li>
              <li class="breadcrumb-item active">
                <?php echo $_SESSION['login']; ?>
              </li>
              <li class="breadcrumb-item active" aria-current="page">
                <?php echo $student_text_20; ?>
              </li>
            </ol>
          </nav>
          <hr>
          <?php
          // Przypisanie danych do zmiennych dla późniejszej wygody.
          $dir_code = './drive/' . $_SESSION['class'] . '/' . $_SESSION['login'] . '/kod' . '/';
          $dir_code_string = (string)$dir_code;
          $dir_to_check = './drive/to-check/';
          $dir_to_check_string = (string)$dir_to_check;

          // Rekurencyjne otwieranie folderów i wypisywanie plików. Dodanie możliwości usunięcia, wysłania do administratora lub pobrania plików.
          if ($dir = opendir($dir_code_string)) {
              while (false !== ($entry = readdir($dir))) {
                  if ($entry != "." && $entry != "..") {
                      echo '<p style="float: left;" class="py-1">'.$entry.'<div style="text-align:right;">
                      <a href="'.$dir_code_string.$entry.'" class="px-2" download><button class="btn btn-primary" title="Pobierz plik"><i class="material-icons" style="font-size: 1.5em !important;">cloud_download</i></button></a>
                      <form method="post" class="px-2" style="float:right;"><button type="submit" name="delete" class="btn btn-primary" value="'.htmlspecialchars($entry).'" title="Usuń plik"><i class="material-icons" style="font-size: 1.5em !important;">delete_forever</i></button></form>
                      <form method="post" class="px-2" style="float:right;"><button type="submit" name="send" class="btn btn-primary" value="'.htmlspecialchars($entry).'" title="Wyślij plik do sprawdzenia"><i class="material-icons" style="font-size: 1.5em !important;">send</i></button></form>
                      </div></p><hr>'.'<br>';
                  }
              }
              closedir($dir);
              if (array_key_exists("send", $_POST)) {
                  if (copy($dir_code_string.$_POST['send'], $dir_to_check_string.$_SESSION['class'].'_'.$_SESSION['login'].'_'.$_POST['send'])) {
                      $_SESSION['send_success'] = '<div class="alert alert-success alert-dismissible fade show fixed-bottom mb-0" role="alert">
                      <strong>Nice!</strong> '.$student_success_1.'! 🤞
                      <button type="button" class="close px-2" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                      </button>
                      </div>';
                      echo '<script>window.location.replace("index.php");</script>';
                  }
              }
              if (array_key_exists("delete", $_POST)) {
                  unlink($dir_code_string.$_POST['delete']);
                  $_SESSION['delete_success'] = '<div class="alert alert-success alert-dismissible fade show fixed-bottom mb-0" role="alert">
                  <strong>Super!</strong> '.$student_success_2.'! 🤞
                  <button type="button" class="close px-2" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
                  </div>';
                  echo '<script>window.location.replace("index.php");</script>';
              }
          }
        ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-dismiss="modal">
            <?php echo $global_4; ?></button>
        </div>
      </div>
    </div>
  </div>
  <!-- Code show modal -->

  <!-- Login modal -->
  <div class="modal fade" id="login_modal" tabindex="-1" role="dialog" aria-labelledby="login_modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle">
            <?php echo $student_text_21; ?>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p class="text-center">
            <?php echo $student_text_22; ?> <strong>
              <?php echo $_SESSION['login']; ?></strong></p>
          <p class="py-3">
            <?php echo $student_text_23; ?>
          </p>
          <hr>
          <p>
            <?php echo $student_text_24; ?>
          </p>
          <form method="post">
            <div class="form-group">
              <input type="text" class="form-control" name="input_login_new" placeholder="<?php echo $student_text_25; ?>">
            </div>
            <div class="form-group">
              <input type="password" class="form-control" name="input_password_old" placeholder="<?php echo $student_text_26; ?>">
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <?php echo $global_4; ?></button>
          <button type="submit" class="btn btn-info">
            <?php echo $global_6; ?></button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php
  // Zmiana loginu.
  if (isset($_POST['input_login_new']) && isset($_POST['input_password_old'])) {
      // Wycisz błędy poprzez try, catch.
      mysqli_report(MYSQLI_REPORT_STRICT);
      try {
          // Połącz się z bazą danych.
          $connect = new mysqli($host, $db_user, $db_password, $db_name);
          if ($connect->connect_errno != 0) {
              throw new Exception(mysqli_connect_errno());
          } else {
              // Jeśli połączenie z bazą danych przebiegło pomyślnie, wyłącz powiadamianie użytkownika o problemie z bazą danych.
              unset($_SESSION['new_login_error_dbproblem']);

              // Przypisanie danych do zmiennych dla późniejszej wygody.
              $old_login = $_SESSION['login'];
              $input_login_new = $_POST['input_login_new'];
              $input_password_old = $_POST['input_password_old'];

              $input_login_new = htmlentities($input_login_new, ENT_QUOTES, "UTF-8");
              $input_password_old = htmlentities($input_password_old, ENT_QUOTES, "UTF-8");

              // Przemienienie wpisanego hasła na hash.
              $input_password_old_hash = password_hash($input_password_old, PASSWORD_DEFAULT);

              // Wybranie użytkownika w bazie.
              if ($result = $connect->query(sprintf("SELECT * FROM users WHERE nick='$old_login'"))) {
                  if (!$result) {
                      throw new \Exception($connect->error);
                  }
                  if (($result->num_rows) > 0) {
                      $row = $result->fetch_assoc();
                      if (password_verify($input_password_old, $row['password'])) {
                          $validationOk = 1;

                          // Walidacja - czy nowy login jest taki sam jak poprzedni?
                          if ($input_login_new === $old_login) {
                              $_SESSION['new_login_error_validation'] = '<div class="alert alert-danger alert-dismissible fade show fixed-bottom mb-0" role="alert">
                              <strong>God bless it!</strong> '.$student_problem_1.' 📖
                              <button type="button" class="close px-2" data-dismiss="alert" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                              </button>
                              </div>';
                              echo '<script>window.location.replace("index.php");</script>';
                          }

                          // Walidacja - czy login nie jest dłuższy niż 16 liter?
                          if (strlen($input_login_new) >= 16) {
                              $validationOk = 0;
                          }

                          // Walidacja - czy login składa się tylko ze znaków alfanumerycznych?
                          if (ctype_alnum($input_login_new) == false) {
                              $validationOk = 0;
                          }

                          // Walidacja - czy login nie jest już zajęty?
                          if ($result_validation = $connect->query("SELECT * FROM users WHERE nick='$input_login_new'")) {
                              if (!$result_validation) {
                                  throw new \Exception($connect->error);
                              }
                              if (!($result_validation->num_rows) > 0) {
                                  if ($validationOk == 1) {
                                      // Jeżeli walidacja się powiedzie, zmień nick użytkownika a także jego foldery.
                                      if ($connect->query("UPDATE users SET nick='$input_login_new' WHERE nick='$old_login'")) {
                                          $new_dir_login = 'drive/' . $_SESSION['class'] . '/' . $input_login_new;
                                          $new_dir_login_string = (string)$new_dir_login;
                                          if (rename($dir_login_string, $new_dir_login_string)) {
                                              echo '<script>window.location.replace("logout.php");</script>';
                                          }
                                      } else {
                                          throw new \Exception($connect->error);
                                      }
                                  }
                                  if ($validationOk == 0) {
                                      // W przeciwnych wypadkach ustaw zmienne sesyjne błędów.
                                      $_SESSION['new_login_error_validation'] = '<div class="alert alert-danger alert-dismissible fade show fixed-bottom mb-0" role="alert">
                                      <strong>God bless it!</strong> '.$problem_1.' 📖
                                      <button type="button" class="close px-2" data-dismiss="alert" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                      </button>
                                      </div>';
                                      echo '<script>window.location.replace("index.php");</script>';
                                  }
                              } else {
                                  unset($_SESSION['new_login_error_validation']);
                                  $_SESSION['new_login_error_already'] = '<div class="alert alert-danger alert-dismissible fade show fixed-bottom mb-0" role="alert">
                                  <strong>God bless it!</strong> '.$student_problem_2.' 🔫
                                  <button type="button" class="close px-2" data-dismiss="alert" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                  </button>
                                  </div>';
                                  echo '<script>window.location.replace("index.php");</script>';
                              }
                          } else {
                              throw new \Exception($connect->error);
                          }
                      } else {
                          $_SESSION['new_login_error_validation'] = '<div class="alert alert-danger alert-dismissible fade show fixed-bottom mb-0" role="alert">
                          <strong>God bless it!</strong> '.$problem_1.' 📖
                          <button type="button" class="close px-2" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                          </button>
                          </div>';
                          echo '<script>window.location.replace("index.php");</script>';
                      }
                  } else {
                      $_SESSION['new_login_error_validation'] = '<div class="alert alert-danger alert-dismissible fade show fixed-bottom mb-0" role="alert">
                      <strong>God bless it!</strong> '.$problem_1.' 📖
                      <button type="button" class="close px-2" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                      </button>
                      </div>';
                      echo '<script>window.location.replace("index.php");</script>';
                  }
              }
          }
          // Zakończ połączenie.
          $connect->close();
      } catch (Exception $e) {
          // Jeżeli wystąpi jakikolwiek problem z bazą, wycisz błędy PHP, a w ich miejsce wrzuć błąd bazy danych.
          unset($_SESSION['new_login_error_validation']);
          unset($_SESSION['new_login_error_already']);
          $_SESSION['new_login_error_dbproblem'] = '<div class="alert alert-danger alert-dismissible fade show fixed-bottom mb-0" role="alert">
          <strong>Son of a gun!</strong> '.$global_7.' 🍪!
          <button type="button" class="close px-2" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          </button>
          </div>';
          echo '<script>window.location.replace("index.php");</script>';
          exit();
      }
  }
  ?>
  <!-- End of login modal -->

  <!-- Password modal -->
  <div class="modal fade" id="password_modal" tabindex="-1" role="dialog" aria-labelledby="password_modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle">
            <?php echo $student_text_27; ?>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p class="py-3">
            <?php echo $student_text_28; ?>
          </p>
          <hr>
          <p class="text-center py-3">
            <?php echo $student_text_29; ?>
          </p>
          <p>
            <?php echo $student_text_30; ?>
          </p>
          <form method="post">
            <div class="form-group">
              <input type="password" class="form-control" name="input_password_old" placeholder="<?php echo $student_text_31; ?>">
            </div>
            <div class="form-group">
              <input type="password" class="form-control" name="input_password_new" placeholder="<?php echo $student_text_32; ?>">
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <?php echo $global_4; ?></button>
          <button type="submit" class="btn btn-info">
            <?php echo $global_6; ?></button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php
  // Zmiana hasła.
  if (isset($_POST['input_password_old']) && isset($_POST['input_password_new'])) {
      // Wycisz błędy poprzez try, catch.
      mysqli_report(MYSQLI_REPORT_STRICT);
      try {
          // Połącz się z bazą danych.
          $connect = new mysqli($host, $db_user, $db_password, $db_name);
          if ($connect->connect_errno != 0) {
              throw new Exception(mysqli_connect_errno());
          } else {
              // Jeśli połączenie z bazą danych przebiegło pomyślnie, wyłącz powiadamianie użytkownika o problemie z bazą danych.
              unset($_SESSION['new_login_error_dbproblem']);

              // Przypisanie danych do zmiennych dla późniejszej wygody.
              $old_login = $_SESSION['login'];
              $input_password_old = $_POST['input_password_old'];
              $input_password_new = $_POST['input_password_new'];

              $input_password_old = htmlentities($input_password_old, ENT_QUOTES, "UTF-8");
              $input_password_new = htmlentities($input_password_new, ENT_QUOTES, "UTF-8");

              // Przemienienie haseł na hashe.
              $input_password_old_hash = password_hash($input_password_old, PASSWORD_DEFAULT);
              $input_password_new_hash = password_hash($input_password_new, PASSWORD_DEFAULT);

              // Wybranie użytkownika w bazie.
              if ($result = $connect->query(sprintf("SELECT * FROM users WHERE nick='$old_login'"))) {
                  if (!$result) {
                      throw new \Exception($connect->error);
                  }
                  if (($result->num_rows) > 0) {
                      $row = $result->fetch_assoc();
                      if (password_verify($input_password_old, $row['password'])) {
                          $validationOk = 1;

                          // Walidacja - czy nowe hasło jest takie samo jak poprzednie?
                          if ($input_password_new === $input_password_old) {
                              $_SESSION['new_password_error_validation'] = '<div class="alert alert-danger alert-dismissible fade show fixed-bottom mb-0" role="alert">
                              <strong>Dagnabbit!</strong> '.$student_problem_3.' 📖
                              <button type="button" class="close px-2" data-dismiss="alert" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                              </button>
                              </div>';
                              echo '<script>window.location.replace("index.php");</script>';
                          }

                          // Walidacja - czy hasło nie jest dłuższe niż 16 liter?
                          if (strlen($input_password_new) >= 16) {
                              $validationOk = 0;
                          }

                          // Walidacja - czy hasło nie jest krótsze niż 8 liter?
                          if (strlen($input_password_new) <= 8) {
                              $validationOk = 0;
                          }

                          // Walidacja - czy hasło składa się tylko ze znaków alfanumerycznych?
                          if (ctype_alnum($input_password_new) == false) {
                              $validationOk = 0;
                          }

                          // Jeżeli walidacja się powiedzie, zmień hasło użytkownika.
                          if ($validationOk == 1) {
                              if ($connect->query("UPDATE users SET password='$input_password_new_hash' WHERE nick='$old_login'")) {
                                  echo '<script>window.location.replace("logout.php");</script>';
                              } else {
                                  throw new \Exception($connect->error);
                              }
                          }
                          if ($validationOk == 0) {
                              // W przeciwnych wypadkach ustaw zmienne sesyjne błędów.
                              $_SESSION['new_password_error_validation'] = '<div class="alert alert-danger alert-dismissible fade show fixed-bottom mb-0" role="alert">
                              <strong>Dagnabbit!</strong> '.$problem_3.' 📖
                              <button type="button" class="close px-2" data-dismiss="alert" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                              </button>
                              </div>';
                              echo '<script>window.location.replace("index.php");</script>';
                          }
                      } else {
                          $_SESSION['new_password_error_validation'] = '<div class="alert alert-danger alert-dismissible fade show fixed-bottom mb-0" role="alert">
                          <strong>Dagnabbit!</strong> '.$problem_3.' 📖
                          <button type="button" class="close px-2" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                          </button>
                          </div>';
                          echo '<script>window.location.replace("index.php");</script>';
                      }
                  } else {
                      $_SESSION['new_password_error_validation'] = '<div class="alert alert-danger alert-dismissible fade show fixed-bottom mb-0" role="alert">
                      <strong>Dagnabbit!</strong> '.$problem_3.' 📖
                      <button type="button" class="close px-2" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                      </button>
                      </div>';
                      echo '<script>window.location.replace("index.php");</script>';
                  }
              }
          }
          // Zakończ połączenie.
          $connect->close();
      } catch (Exception $e) {
          // Jeżeli wystąpi jakikolwiek problem z bazą, wycisz błędy PHP, a w ich miejsce wrzuć błąd bazy danych.
          unset($_SESSION['new_password_error_validation']);
          $_SESSION['new_password_error_dbproblem'] = '<div class="alert alert-danger alert-dismissible fade show fixed-bottom mb-0" role="alert">
          <strong>Son of a gun!</strong> '.$global_7.' 🍪!
          <button type="button" class="close px-2" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          </button>
          </div>';
          echo '<script>window.location.replace("index.php");</script>';
          exit();
      }
  }
  ?>
  <!-- End of password modal -->

  <!-- Footer -->
  <footer class="footer mt-auto py-3">
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
