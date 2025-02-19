<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Reseña Recibida</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal p-6">

<div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8">
    <h2 class="text-2xl font-semibold text-orange-500 mb-4">Motum - ¡Has recibido una nueva reseña!</h2>

    <p class="text-gray-700 mb-4">Hola <strong>{{ $trainer->name }}</strong>,</p>
    <p class="text-gray-700 mb-4">Un alumno ha dejado una reseña sobre tu entrenamiento:</p>

    <div class="bg-gray-50 p-4 rounded-lg mb-4">
        <p class="text-gray-800"><strong>Calificación:</strong> ⭐ {{ $review->rating }} / 5</p>
        <p class="text-gray-800"><strong>Comentario:</strong> "{{ $review->comment }}"</p>
    </div>

    <p class="text-gray-700 mb-4">¡Sigue haciendo un gran trabajo!</p>

    <p class="text-sm text-gray-500">Saludos,<br>El equipo de Motum.</p>
</div>

</body>
</html>
