<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Constancia de Inscripción</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 14px; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; }
        .title { text-align: center; font-weight: bold; text-decoration: underline; font-size: 18px; margin-bottom: 40px; }
        .content { text-align: justify; margin: 0 50px; }
        .footer { margin-top: 80px; text-align: center; }
        .signature { display: inline-block; width: 200px; border-top: 1px solid #000; margin-top: 50px; }
    </style>
</head>
<body>
    <div class="header">
        <strong>REPÚBLICA BOLIVARIANA DE VENEZUELA</strong><br>
        <strong>MINISTERIO DEL PODER POPULAR PARA LA EDUCACIÓN</strong><br>
        <strong>LICEO NACIONAL "NOMBRE DE TU LICEO"</strong>
    </div>

    <div class="title">CONSTANCIA DE INSCRIPCIÓN</div>

    <div class="content">
        <p>Quien suscribe, la Dirección del <strong>Liceo Nacional "Nombre de tu Liceo"</strong>, hace constar por medio de la presente que el (la) ciudadano(a): 
        <strong>{{ $estudiante->nombre }} {{ $estudiante->apellido }}</strong>, titular de la Cédula de Identidad 
        <strong>{{ $estudiante->cedula }}</strong>, se encuentra formalmente 
        <strong>INSCRITO(A)</strong> en esta institución para cursar el: 
        <strong>{{ $grado->nombre }}</strong>, durante el Año Escolar 
        <strong>{{ $anio->nombre_anio ?? '2025-2026' }}</strong>.</p>

        <p>Constancia que se expide a petición de la parte interesada en la fecha: <strong>{{ $fecha }}</strong>.</p>
    </div>

    <div class="footer">
        <div class="signature">
            Directora / Director<br>
            Sello de la Institución
        </div>
    </div>
</body>
</html>