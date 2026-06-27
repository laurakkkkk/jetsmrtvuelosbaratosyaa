<?php
// index.php - Punto de entrada para Render
// Esto permite que Render sirva tu index.html correctamente

// Si el archivo index.html existe, lo mostramos
if (file_exists('index.html')) {
    readfile('index.html');
    exit;
}

// Si no existe index.html, mostramos un mensaje
echo "Bienvenido al sistema de tiquetes";
?>