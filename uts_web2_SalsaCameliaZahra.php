<?php

class Book
{
    public $id;
    public $judul;
    public $penulis;
    public $tahun;
    public $pinjam;
    public $gambar; // Tambah properti untuk gambar

    public function __construct($id, $judul, $penulis, $tahun, $gambar)
    {
        $this->id = $id;
        $this->judul = $judul;
        $this->penulis = $penulis;
        $this->tahun = $tahun;
        $this->pinjam = false;
        $this->gambar = $gambar; // Inisialisasi properti gambar
    }

    public function pinjamBuku()
    {
        if (!$this->pinjam) {
            $this->pinjam = true;
            return true;
        } else {
            return false; // Buku sedang dipinjam
        }
    }

    public function pengembalianBuku()
    {
        if ($this->pinjam) {
            $this->pinjam = false;
            return true;
        } else {
            return false; // Buku tidak dipinjam
        }
    }
}

class Library
{
    private $maxPeminjaman = 3; // Batasan jumlah buku yang dapat dipinjam
    private $dendaPerHari = 500; // Denda per hari keterlambatan
    public $books = [];

    // Getter method for maxPeminjaman
    public function getMaxPeminjaman()
    {
        return $this->maxPeminjaman;
    }

    public function tambahBuku($book)
    {
        $this->books[] = $book;
    }

    public function cariBuku($keyword)
    {
        $searchResults = [];
        foreach ($this->books as $book) {
            if ($book instanceof Book && (stripos($book->judul, $keyword) !== false || stripos($book->penulis, $keyword) !== false)) {
                $searchResults[] = $book;
            }
        }
        return $searchResults;
    }

    public function filterBukuByAlphabet($alphabet)
    {
        return array_filter($this->books, function ($book) use ($alphabet) {
            return stripos($book->judul, $alphabet) === 0;
        });
    }

    public function filterBukuByYear($year)
    {
        return array_filter($this->books, function ($book) use ($year) {
            return $book->tahun == $year;
        });
    }
}

// Membuat objek perpustakaan
$library = new Library();

// Membuat objek buku
$book1 = new Book(1, "Database Design", "Indrajani, S.Kom., MM.", 2015, "img/database.jpg");
$book2 = new Book(2, "Internet of Things (IoT): Mengubah Wajah Pendidikan Indonesia", "Nurhidayati & Prof.Richardus Eko", 2022, "img/iot.jpg");
$book3 = new Book(3, "STATISTIKA DASAR Panduan Bagi Dosen dan Mahasiswa", "Tri Hidayati M.Pd, Ita Handayani M.Pd, Ines Heidiani Ikasari S.Si., M.Kom", 2021, "img/statistika.jpg");
$book4 = new Book(4, "111 Kode HTML untuk Belajar Kilat", "Arista Prasetyo Adi", 2019, "img/html.jpg");
$book5 = new Book(5, "Buku Mahir Web Programming", "Ir. Yuniar Supardi & Defri Faizal Maulana S.", 2019, "img/web.jpg");
$book6 = new Book(6, "Sistem Enterprise, Konsep Dan Implementasi", "Mahendrawathi ER, Ph.D.", 2023, "img/sie.jpg");
$book7 = new Book(7, "Pengembangan Aplikasi Berbasis Web", "Hanifudin Sukri, Ach. Dafid, Ali Bardadi, & Firmansyah Adiputra", 2024, "img/pengembangan.jpg");
$book8 = new Book(8, "Dasar-dasar Teknik Informatika", "Novega Pratama Adiputra", 2020, "img/informatika.jpg");
$book9 = new Book(9, "Membuat Sendiri Jaringan Komputer", "Edy Winarno ST, M.Eng Ali Zaki SmitDev Community", 2013, "img/jarkom.jpg");
$book10 = new Book(10, "Ilmu Hacking", "Dedik Kurniawan", 2023, "img/hack.jpg");

// Menambahkan buku ke perpustakaan
$library->tambahBuku($book1);
$library->tambahBuku($book2);
$library->tambahBuku($book3);
$library->tambahBuku($book4);
$library->tambahBuku($book5);
$library->tambahBuku($book6);
$library->tambahBuku($book7);
$library->tambahBuku($book8);
$library->tambahBuku($book9);
$library->tambahBuku($book10);

// Proses pencarian buku
$searchResults = $library->books;

if (isset($_GET['keyword'])) {
    $keyword = $_GET['keyword'];
    if (!empty($keyword)) {
        $searchResults = $library->cariBuku($keyword);
    } else {
        $searchResults = $library->books;
    }
}

// Proses filter berdasarkan abjad
if (isset($_GET['alphabet']) && !empty($_GET['alphabet'])) {
    $alphabetFilter = $_GET['alphabet'];
    $searchResults = $library->filterBukuByAlphabet($alphabetFilter);
}

// Proses filter berdasarkan tahun
if (isset($_GET['year']) && !empty($_GET['year'])) {
    $yearFilter = $_GET['year'];
    $searchResults = $library->filterBukuByYear($yearFilter);
}

// Proses sorting
$sort = 'alphabet'; // Default sorting
if (isset($_GET['sort']) && !empty($_GET['sort'])) {
    $sort = $_GET['sort'];
}

// Sorting
if ($sort === 'alphabet') {
    // Urutkan berdasarkan judul buku secara alfabetis
    usort($searchResults, function ($a, $b) {
        return strcmp($a->judul, $b->judul);
    });
} elseif ($sort === 'year') {
    // Urutkan berdasarkan tahun terbit
    usort($searchResults, function ($a, $b) {
        return $a->tahun - $b->tahun;
    });
}

// Proses peminjaman buku
if (isset($_GET['action']) && isset($_GET['book_id'])) {
    $action = $_GET['action'];
    $bookId = $_GET['book_id'];

    // Cari buku berdasarkan ID
    $bookToProcess = null;
    foreach ($library->books as $book) {
        if ($book->id == $bookId) {
            $bookToProcess = $book;
            break;
        }
    }

    if ($bookToProcess) {
        if ($action === 'pinjam') {
            if ($bookToProcess->pinjamBuku()) {
                // Jika berhasil dipinjam
                echo '<script>alert("Buku berjasil dipinjam");</script>';
            } else {
                // Jika buku sudah dipinjam sebelumnya
                echo '<script>alert("Buku sedang dipinjam.");</script>';
            }
        } elseif ($action === 'kembali') {
            if ($bookToProcess->pengembalianBuku()) {
                // Jika berhasil dikembalikan
                echo '<script>alert("Buku berhasil dikembalikan.");</script>';
            } else {
                // Jika buku tidak sedang dipinjam
                echo '<script>alert("Buku tidak sedang dipinjam.");</script>';
            }
        }
    } else {
        // Jika buku tidak ditemukan
        //echo '<script>alert("Terimakasih Telah mengembalikan.");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/perpustakaan.css">
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid ">
            <a class="navbar-brand" href="#">PERPUSTAKAAN UTB</a>
        </div>
    </nav>
    <!-- End Navbar -->



    <!--content-->
    <div class="container mt-5">
        <h2>Daftar Buku</h2>
        <hr>
        <!-- Search form -->
        <form action="" method="get" class="mb-4">
            <div class="row">
                <div class="col-md-8">
                    <input type="text" name="keyword" class="form-control" placeholder="Cari buku berdasarkan judul atau penulis">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Cari</button>
                </div>
            </div>
        </form>

        <!-- Filter buttons -->
        <div class="mb-4">
            <a href="?sort=alphabet" class="btn btn-outline-primary">Urutkan A-Z</a>
            <a href="?sort=year" class="btn btn-outline-primary">Urutkan Tahun Terbit</a>
        </div>

        <!-- Book Cards -->
        <div class="row mt-4">
            <?php
            foreach ($searchResults as $book) {
                echo '<div class="col-md-6 mb-4">';
                echo '<div class="card h-100">';
                echo '<div class="row g-0">';
                echo '<div class="col-md-4">';
                echo '<img src="' . $book->gambar . '" class="card-img" alt="' . $book->judul . '">';
                echo '</div>'; // Tutup col-md-4
                echo '<div class="col-md-8">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . $book->judul . '</h5>';
                echo '<p class="card-text">Penulis: ' . $book->penulis . '</p>';
                echo '<p class="card-text">Tahun Terbit: ' . $book->tahun . '</p>';
                if (!$book->pinjam) {
                    echo '<button type="button" class="btn btn-outline-primary btn-pinjam" data-id="' . $book->id . '">Pinjam</button>';
                } else {
                    echo '<button type="button" class="btn btn-outline-secondary" disabled>Dipinjam</button>';
                    echo '<a href="?action=kembali&book_id=' . $book->id . '" class="btn btn-outline-danger btn-kembali">Pengembalian</a>';
                }
                // Button Hapus di sini
                echo '<button type="button" class="btn btn-outline-danger btn-hapus" data-id="' . $book->id . '">Hapus</button>';
                echo '</div>'; // Tutup card-body
                echo '</div>'; // Tutup col-md-8
                echo '</div>'; // Tutup row
                echo '</div>'; // Tutup card
                echo '</div>'; // Tutup col-md-6
            }
            ?>
        </div>
        <!-- End Book Cards -->
    </div>

    <!--End Content-->

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <p>&copy; UAS WEB2 SALSA CAMELIA ZAHRA 21552011135 </p>
                </div>
            </div>
        </div>
    </footer>
    <!-- End Footer -->

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-..." crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-..." crossorigin="anonymous"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const pinjamButtons = document.querySelectorAll('.btn-pinjam');
            const kembaliButtons = document.querySelectorAll('.btn-kembali');
            const deleteButtons = document.querySelectorAll('.btn-hapus'); // Define delete buttons

            pinjamButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const bookId = this.getAttribute('data-id');
                    window.location.href = "?action=pinjam&book_id=" + bookId; // Add quotes and concatenate properly
                });
            });

            kembaliButtons.forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const bookId = this.getAttribute('data-id');
                    window.location.href = "?action=kembali&book_id=" + bookId; // Add quotes and concatenate properly
                });
            });

            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const bookId = this.getAttribute('data-id');
                    const cardToRemove = this.closest('.col-md-4');
                    cardToRemove.remove();
                    // Here you can add logic to remove the book from the database or other data source.
                });
            });
        });
        window.addEventListener('scroll', function() {
            var footer = document.querySelector('.footer');
            var windowHeight = window.innerHeight;
            var bodyHeight = document.body.clientHeight;
            var scrollTop = window.scrollY || window.pageYOffset;

            if (windowHeight + scrollTop >= bodyHeight) {
                footer.style.position = 'fixed';
                footer.style.bottom = '0';
            } else {
                footer.style.position = 'relative';
                footer.style.bottom = '';
            }
        });
    </script>

</body>

</html>