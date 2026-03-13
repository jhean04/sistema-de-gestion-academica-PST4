<?php

return [
    'disable' => env('CAPTCHA_DISABLE', false),
    'characters' => ['2', '3', '4', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'j', 'm', 'n', 'p', 'q', 'r', 't', 'u', 'v', 'w', 'x', 'y', 'z'],
    
    // USAR LAS FUENTES POR DEFECTO DEL PAQUETE (Borra las líneas de assets/fonts)
    
    'default' => [
        'length' => 6,
        'width' => 160, // Bajé el ancho para que quepa mejor en el login
        'height' => 46,
        'quality' => 90,
        'math' => false,
        'expire' => 60,
        'encrypt' => false,
    ],
    'flat' => [
        'length' => 4, // 4 caracteres es más cómodo para el usuario
        'width' => 160,
        'height' => 46,
        'quality' => 90,
        'lines' => 6,
        'bgImage' => false, // Desactiva esto para evitar buscar imágenes que no existen
        'bgColor' => '#ecf0f1',
        'fontColors' => ['#2c3e50', '#c0392b', '#16a085'],
        'contrast' => -5,
    ],
    'mini' => [
        'length' => 3,
        'width' => 60,
        'height' => 32,
    ],
    'math' => [
        'length' => 9,
        'width' => 120,
        'height' => 36,
        'quality' => 90,
    ],
];