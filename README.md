# Memory Drive

![Memory Drive](https://i.imgur.com/kgdNyiR.png) 
<hr>

„Memory Drive” jest projektem przygotowywanym przez mnie Pawła Miszczaka na potrzeby szkół publicznych. 

<br>
<div align="center">
	<img src="https://img.shields.io/badge/Wersja-1.1.0-blue?style=for-the-badge&logo=Git&logoColor=white" alt="Wersja 1.0.0">
</div>
<br>

# Na jaką potrzebę odpowiada moje rozwiązanie?

Moje rozwiązanie odpowiada na potrzebę wygodnego przesyłania plików między uczniami a nauczycielami. Idea projektu „Memory Drive” pojawiła się w mojej głowie, gdy w pierwszej klasie techniku na lekcji informatyki zobaczyłem skrzynkę odbiorczą z pracami uczniów nauczyciela informatyki. Dziesiątki plików, niepoukładane w żadną logiczną całość. Szkoły nie mają dobrej platformy do przesyłania plików z pracami domowymi lub pracami na lekcji.

# Opis działania mojej aplikacji

Po wejściu na stronę internetową z aplikacją „Memory Drive” użytkownik zostanie przywitany formularzem logowania. W mojej aplikacji nie ma możliwości rejestracji się użytkowników, ponieważ konta generuje administrator. Po zalogowaniu się zostaniemy przekierowani odpowiednio do panelu ucznia lub administratora.

<hr> 

Po zalogowaniu się do **panelu ucznia** naszym oczom ukażą się kafelki podzielone na 3 sekcje. Po kliknięciu kafelka otworzy się okno z odpowiednią funkcją. Każdy kafelek ma na sobie ikonę mówiącą o jego zastosowaniu oraz kafelki są ułożone w 3 sekcje. Sekcję odpowiadającą za wgrywanie plików na serwer, sprawdzanie i pobieranie plików, a także sekcję odpowiadającą za zmianę nazwy i hasła. W oknach funkcji u góry mamy tytuł funkcji oraz wszystko w środku jest wytłumaczone tekstami. Uczeń może wgrywać na serwer: 

 - zdjęcia (w formacie JPG, JPEG, PNG lub GIF), 
 - prezentacje (w formacie PPTX, PPT lub KEY),
 - arkusze kalkulacyjne (w formacie XLS, XLSX, XLSM, XLSB lub NUMBERS),
 - teks (w formacie TXT, DOCX, DOC, PDF lub PAGES),
 - kod (w formacie CPP, CSS, HTML, PHP lub JS).
 
Uczeń ma możliwość pobrania, usunięcia lub wysłania do sprawdzenia przez nauczyciela każdego z plików.

<hr>

Po zalogowaniu się do **panelu administratora** naszym oczom ukażą się 3 kafelki. Wszystkie funkcje kafelków są identyczne, jak u ucznia tj. po kliknięciu otworzy się okno z tytułem u góry wraz z instrukcjami w środku. Administrator ma możliwość dodania nowej klasy, wymazania bazy danych oraz sprawdzania plików uczniów.  

Dodawanie nowej klasy polega na podaniu nazwy nowej klasy (do 3 znaków np. 2TC) oraz podaniu ilości uczniów w klasie. Po zatwierdzeniu wygenerowane zostaną konta uczniów, a administrator dostanie plik PDF z danymi do logowania się uczniów. 

Wymazywanie bazy danych polega na usunięciu wszystkich rekordów z bazy danych oprócz konta administratora i testowego konta ucznia, a następnie na odnowieniu folderu z plikami uczniów.

# Jak widzę dalszy rozwój?

Aplikacja „Memory Drive” jest dopiero w początkowym stadium rozwoju i na pewno do niej niedługo wrócę. Jest jeszcze dużo funkcjonalności, które chciałbym dodać do aplikacji przed jej uruchomieniem. Aplikacja może być już teraz używana testowo, ale jej pełny potencjał będzie dopiero widoczny w wersji 2.0.0.

# Front-end

![Memory Drive](https://i.imgur.com/3ZUL33b.png)
<hr>

![Memory Drive](https://i.imgur.com/lZ9Eda3.png)
<hr>

![Memory Drive](https://i.imgur.com/Onk8vQE.png)
<hr>

Front-end aplikacji „Memory Drive” wykonany został w dużej mierze na frameworku Bootstrap 4. Wszystkie kolory we front-endzie są zaczerpnięte z oryginalnych kolorów występujących w frameworku Bootstrap 4. Front-end miał był jak najbardziej czytelny i miał zajmować jak najmniej zasobów serwera.

# Serwer

Serwer, na którym aplikacja była pierwotnie postawiona, był serwerem postawionym na Ubuntu 18.04 Server (w wersji 64-bitowej). Aplikacja jest niewymagająca i serwer, który zupełnie wystarczał do utrzymania aplikacji, miał następujące parametry: 

 - 1 vCore,
 - 2 GHz,
 - 2 GB RAM,
 - 20 GB SSD.

Aby aplikacja działała na serwerze, musiał zostać zainstalowany pakiet [LAMP](https://pl.wikipedia.org/wiki/LAMP). 

# Back-end

Cały kod aplikacji jest skomentowany. Moim celem w tej aplikacji było bezpieczeństwo użytkownika i informacji dlatego przyłożyłem do tego dużo uwagi.

Wszystkie wiadomości, tekst i przyciski są oparte na zmiennych, które wybierane są z pliku, który określany jest wyborem języka. Jest to dość prymitywny, ale świetnie sprawdzający się sposób na stworzenie aplikacji w wielu językach naraz.

Baza danych użytkowników powinna nazywać się **users** oraz tabela też powinna nazywać się **users**. Początkowy stan bazy danych:

| id | nick | password | status | class |
| -- | -- | -- | -- | -- |
| 1 | admin | {hash} | admin | administracja |
| 2 | student | {hash} | student | administracja |

## index.php

Plik index.php jest plikiem, w którym zakodowany jest formularz logowania. Użytkownik na początku sprawdzany jest, czy nie jest już zalogowany, jeżeli nie to nie zostaje przekierowany na żadną inną podstronę. Po wysłaniu zapytania logowania hasło użytkownika jest hashowane, a następnie nazwa i hash hasła są porównywane z bazą danych. Jeżeli w bazie istnieje taki użytkownik, dane są zapisywane w zmiennych sesyjnych oraz użytkownik jest przekierowywany do odpowiedniej podstrony. 

## dashboard_student.php

Użytkownik na początku sprawdzany jest, czy nie jest już zalogowany lub, czy nie jest administratorem, jeżeli nie to nie zostaje przekierowany na żadną inną podstronę. 

Następnie sprawdzane jest, czy uczeń ma swoje foldery na serwerze, jeżeli ich nie ma, to są tworzone wraz z ustawieniem chmodu na 777. 

W nagłówku także istnieje skrypt to sprawdzania ostatniego ruchu ucznia. Jeżeli uczeń jest nieaktywny przez 5 minut określone w zmiennych konfiguracyjnych, zostaje automatycznie wylogowywany. 

Reszta skryptów zawarta jest w sekcji body strony. 

Dodawanie plików odbywa się poprzez zewnętrzne skrypty nazwane: 
 - upload_image.php, 
 - upload_presentation_and_spreadsheet.php, 
 - upload_text.php, 
 - upload_code.php.

W tych skryptach następuje walidacja plików, która opera się o testy. Jeżeli plik nie przejdzie danego testu, zmienna walidacyjna jest zmieniana na 0 lub false a tym samym cała waldacja kończy się niepowodzeniem.

Sprawdzanie plików używa pętli rekurencyjnych, które wypisują pliki, póki nie wypiszą wszystkich. 

## dashboard_admin.php

Użytkownik na początku sprawdzany jest, czy nie jest już zalogowany lub, czy nie jest uczniem, jeżeli nie to nie zostaje przekierowany na żadną inną podstronę. 

Następnie sprawdzane jest, czy administrator ma swój folder na serwerze, jeżeli ich nie ma, to są tworzone wraz z ustawieniem chmodu na 777. 

W nagłówku także istnieje skrypt to sprawdzania ostatniego ruchu administratora. Jeżeli administrator jest nieaktywny przez 5 minut określone w zmiennych konfiguracyjnych, zostaje automatycznie wylogowywany. 

Skrypty są wywoływane z formularzy w sekcji body do nagłówka. W nagłówku znajdziemy funkcję do rekurencyjnego usuwania plików, funkcję odpowiadającą za dodawanie klasy oraz funkcję do wymazywania bazy danych. 

Generowanie losowych nazw uczniów wykonywane jest poprzez pobieranie losowych słów z listy angielskich słów zawartej w pliku words.json, natomiast plik PDF generowany jest przy pomocy biblioteki FPDF.  

# TODO

 - [ ] Rozdzielenie funkcji administratora i nauczyciela,
 - [ ] Przypisanie nauczycieli do klasy,
 - [ ] Plik konfiguracyjny,
 - [ ] Plik instalacyjny,
 - [ ] Tłumaczenie na język angielski,
 - [ ] Ulepszenie preloadera,
 - [ ] Dodanie powiadomienia o wylogowaniu, 
 - [ ] Kontrola bezpieczeństwa.

# Licencja

Aplikacja działa na licencji GNU (General Public License), co oznacza, że aplikacja może być uruchamiana, kopiowana, rozpowszechniana, analizowana oraz zmieniana i poprawiana przez użytkowników.

**GNU** General Public License [(GPL)](https://www.gnu.org/licenses/gpl-3.0.html)
