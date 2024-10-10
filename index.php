<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>
    <style>
        /* Gaya untuk elemen-elemen di halaman */
        label { display: block; margin-top: 10px; }
        .hidden { display: none; }
        .highlight-title { color: red; }
        .highlight-author { color: blue; }
        .highlight-year { color: green; }
        .highlight-size { color: purple; }
        .highlight-pages { color: brown; }
        h1 { text-align: center; } /* Mengatur judul agar berada di tengah */
        h2 { margin-top: 40px; } /* Mengatur jarak atas untuk elemen h2 */
    </style>
    <script>
        /* Fungsi untuk validasi jumlah buku yang diinput */
        function validateInput() {
            const numBooks = document.getElementById('numBooks').value;
            if (numBooks < 1 || numBooks > 100) {
                alert("Please enter a valid number of books between 1 and 100.");
                return false;
            }
            return true;
        }

        /* Mengatur tampilan field sesuai dengan tipe buku (EBook atau PrintedBook) */
        function toggleFields(type, id) {
            const fileSizeField = document.getElementById(`fileSize${id}`);
            const numPagesField = document.getElementById(`numberOfPages${id}`);

            if (type === "ebook") {
                fileSizeField.classList.remove('hidden');
                numPagesField.classList.add('hidden');
            } else if (type === "printedbook") {
                fileSizeField.classList.add('hidden');
                numPagesField.classList.remove('hidden');
            }
        }
        
        /* Validasi data di form buku sebelum dikirim */
        function validateForm() {
            const formElements = document.querySelectorAll('input, select');
            let valid = true;

            formElements.forEach(function(element) {
                const id = element.id.match(/\d+/); // Mendapatkan nomor buku
                if (element.name.startsWith('title') && element.value.length > 100) {
                    alert('The title should not exceed 100 characters.');
                    valid = false;
                }
                if (element.name.startsWith('author') && element.value.length > 100) {
                    alert('The author\'s name should not exceed 100 characters.');
                    valid = false;  
                }
                if (element.name.startsWith('publicationYear')) {
                    const publicationYear = parseInt(element.value);
                    if (publicationYear < 1500 || publicationYear > 2024) {
                        alert('The publication year must be between 1500 and 2024.');
                        valid = false;
                    }
                }
                const type = document.getElementById(`type${id}`).value;
                if (type === "ebook" && element.name.startsWith('fileSize')) {
                    const fileSize = parseInt(element.value);
                    if (fileSize < 1 || fileSize > 100) {
                        alert('The file size must be between 1 and 100 MB.');
                        valid = false;
                    }
                }
            });

            return valid;
        }
    </script>
</head>
<body>
    <h1>Library Management System</h1>

    <!-- Bagian untuk menambah buku -->
    <h2>Add Book</h2>
    
    <!-- Form untuk memasukkan jumlah buku -->
    <form method="POST" onsubmit="return validateInput()">
        <label for="numBooks">How many books do you want to input?</label>
        <input type="number" name="numBooks" id="numBooks" min="1" max="100" required>
        <button type="submit">Submit</button>
    </form>

    <?php
    include 'ebook.php';
    include 'printedbook.php';

    session_start();

    // Jika form jumlah buku dikirim, tampilkan form untuk detail setiap buku
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['numBooks'])) {
        $_SESSION['numBooks'] = $_POST['numBooks'];
        echo '<form method="POST" onsubmit="return validateForm()">';
        for ($i = 1; $i <= $_SESSION['numBooks']; $i++) {
            echo "<h3>Book $i</h3>";
            echo '<label for="type' . $i . '">Select Book Type:</label>';
            echo '<select name="type' . $i . '" id="type' . $i . '" onchange="toggleFields(this.value, ' . $i . ')" required>
                    <option value="" disabled selected>Select a type</option>
                    <option value="ebook">EBook</option>
                    <option value="printedbook">Printed Book</option>
                  </select>';

            echo '<div>';
            echo '<label for="title' . $i . '">Title:</label>';
            echo '<input type="text" id="title' . $i . '" name="title' . $i . '" maxlength="100" required>';
            echo '</div>';

            echo '<div>';
            echo '<label for="author' . $i . '">Author:</label>';
            echo '<input type="text" id="author' . $i . '" name="author' . $i . '" maxlength="100" required>';
            echo '</div>';

            echo '<div>';
            echo '<label for="publicationYear' . $i . '">Publication Year:</label>';
            echo '<input type="number" id="publicationYear' . $i . '" name="publicationYear' . $i . '" min="1500" max="2024" required>';
            echo '</div>';

            // Field khusus untuk EBook
            echo '<div id="fileSize' . $i . '" class="hidden">';
            echo '<label for="fileSize' . $i . '">File Size (MB) (for EBook):</label>';
            echo '<input type="number" id="fileSizeInput' . $i . '" name="fileSize' . $i . '" min="1" max="100">';
            echo '</div>';

            // Field khusus untuk PrintedBook
            echo '<div id="numberOfPages' . $i . '" class="hidden">';
            echo '<label for="numberOfPages' . $i . '">Number of Pages (for PrintedBook):</label>';
            echo '<input type="number" id="numberOfPagesInput' . $i . '" name="numberOfPages' . $i . '" min="1">';
            echo '</div>';
        }
        echo '<button type="submit" name="submitBooks">Submit Books</button>';
        echo '</form>';
    }

    // Proses penginputan buku
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submitBooks'])) {
        if (!isset($_SESSION['books'])) {
            $_SESSION['books'] = [];
        }

        for ($i = 1; $i <= $_SESSION['numBooks']; $i++) {
            $type = $_POST['type' . $i];
            $title = $_POST['title' . $i];
            $author = $_POST['author' . $i];
            $publicationYear = $_POST['publicationYear' . $i];

            if ($type == 'ebook') {
                $fileSize = $_POST['fileSize' . $i];
                $book = new EBook($title, $author, $publicationYear, $fileSize);
            } elseif ($type == 'printedbook') {
                $numberOfPages = $_POST['numberOfPages' . $i];
                $book = new PrintedBook($title, $author, $publicationYear, $numberOfPages);
            }

            // Menyimpan buku ke dalam session
            $_SESSION['books'][] = $book;
        }

        unset($_SESSION['numBooks']); // Menghapus session setelah input selesai
    } 
    ?>

    <!-- Form untuk mencari buku berdasarkan index -->
    <h2>Search for a Book by Index</h2>
    <form method="POST">
        <label for="bookIndex">Enter Book Index:</label>
        <input type="number" name="bookIndex" id="bookIndex" min="1" required>
        <button type="submit" name="searchBook">Search Book</button>
    </form>

    <?php
    // Proses pencarian buku berdasarkan index
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['searchBook'])) {
        $bookIndex = $_POST['bookIndex'] - 1;
        if (isset($_SESSION['books'][$bookIndex])) {
            $book = $_SESSION['books'][$bookIndex];
            echo "<p><span class='highlight-title'>Title:</span> {$book->getTitle()}, <span class='highlight-author'>Author:</span> {$book->getAuthor()}, <span class='highlight-year'>Year:</span> {$book->getYear()}";

            if (get_class($book) == 'EBook') {
                echo ", <span class='highlight-size'>File Size:</span> {$book->getFileSize()}MB</p>";
            } else {
                echo ", <span class='highlight-pages'>Number of Pages:</span> {$book->getNumberOfPages()}</p>";
            }
        } else {
            echo "<p>Sorry, the book is not available.</p>";
        }
    }
    ?>
</body>
</html>
