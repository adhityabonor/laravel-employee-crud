<?php
// Ukuran matriks 7x7
$size = 7;

for ($i = 0; $i < $size; $i++) {
    for ($j = 0; $j < $size; $j++) {
        // Cek apakah di diagonal utama atau diagonal sekunder
        if ($i == $j || $i + $j == $size - 1) {
            echo "X ";
        } else {
            echo "O ";
        }
    }
    echo PHP_EOL; // Pindah ke baris berikutnya
}
