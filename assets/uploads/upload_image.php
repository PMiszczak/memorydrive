<?php
// Start pracy z sesjami.
session_start();

// Ustawienie zmiennej wybierającej język. W obecnej wersji poprzez przyrównanie wartości w przyszłości zostanie to ustawione w pliku konfiguracyjnym.
$language = "pl";

// Pobranie odpowiednich plików w przypadku każdego języka.
if ($language === "pl") {
    require_once('../languages/pl_PL.php');
} elseif ($language === "en") {
    require_once('../languages/en_US.php');
}

// Jeśli użytkownik nie jest zalogowany, wyrzuć go.
if (!isset($_SESSION['logged'])) {
    header("Location: ../../index.php");
    exit();
}

// Jeśli użytkownik nie przycisnął przycisku do wysyłania, wyrzuć go.
if (!isset($_POST['submit'])) {
    header("Location: ../../index.php");
    exit();
}

// Przypisanie danych do zmiennych dla późniejszej wygody.
$target_dir = '../../drive/' . $_SESSION['class'] . '/' . $_SESSION['login'] . '/zdjecia' . '/';
$target_dir_string = (string)$target_dir;
// Utworzenie zmiennej przedstawiającej przyszły plik.
$target_file = $target_dir_string . basename($_FILES["file_to_upload"]["name"]);
$file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

// Zmienna walidacyjna.
$validationOk = 1;

// Walidacja - czy plik jest napewno zdjęciem? (sprawdzanie wielości obrazu)
if (isset($_POST["submit"])) {
    $check = getimagesize($_FILES["file_to_upload"]["tmp_name"]);

    if ($check !== false) {
        $validationOk = 1;
    } else {
        $validationOk = 0;
    }
}

// Walidacja - czy plik przypadkiem już nie istnieje?
if (file_exists($target_file)) {
    $validationOk = 0;
}

// Walidacja - czy plik nie jest większy niż 20MB?
if ($_FILES["file_to_upload"]["size"] > 20000000) {
    $validationOk = 0;
}

// Walidacja - czy plik posiada rozszerzenie jpg, jpeg, png lub gif?
if ($file_type != "jpg" && $file_type != "jpeg" && $file_type != "png" && $file_type != "gif") {
    $validationOk = 0;
}

// Jeżeli walidacja się nie powiodła, ustaw błąd w zmiennej sesyjnej.
if ($validationOk == 0) {
    $_SESSION['upload_error_validation'] = '<div class="alert alert-danger alert-dismissible fade show fixed-bottom mb-0" role="alert">
    <strong>Caesar’s ghost!</strong> '.$upload_problem_1.' 💾
    <button type="button" class="close px-2" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
    </button>
    </div>';
    header("Location: ../../dashboard_student.php");
    exit();
}

// W przeciwnym wypadku, wrzuć plik na serwer, wyłącz powiadamianie użytkownika o niepowodzeniu i ustaw potwierdzenie powodzenia w zmiennej sesyjnej.
else {
    if (move_uploaded_file($_FILES["file_to_upload"]["tmp_name"], $target_file)) {
        unset($_SESSION['upload_error_validation']);
        unset($_SESSION['upload_error_uploadproblem']);
        $_SESSION['upload_success'] = '<div class="alert alert-success alert-dismissible fade show fixed-bottom mb-0" role="alert">
        <strong>Great!</strong> '.$upload_success_1.' 👌🏻
        <button type="button" class="close px-2" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
        </div>';
        header("Location: ../../dashboard_student.php");
        exit();
    }
    // Jeżeli wystąpi błąd, ustaw błąd w zmiennej sesyjnej.
    else {
        unset($_SESSION['upload_error_validation']);
        $_SESSION['upload_error_uploadproblem'] = '<div class="alert alert-danger alert-dismissible fade show fixed-bottom mb-0" role="alert">
        <strong>Dadgummit!</strong> '.$upload_problem_2.' ⚙️
        <button type="button" class="close px-2" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
        </div>';
        header("Location: ../../dashboard_student.php");
        exit();
    }
}
