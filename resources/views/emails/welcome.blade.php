<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a Motum</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal p-6">

<div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8">
    <h2 class="text-2xl font-semibold text-orange-500 mb-4">¡Bienvenido a Motum, {{ $user->name }}! 🎉</h2>

    <p class="text-gray-700 mb-4">
        Gracias por registrarte en nuestra plataforma. Ahora puedes empezar a explorar entrenamientos y conectar con entrenadores.
    </p>

    <p class="text-gray-700 mb-4">Si tienes alguna duda, no dudes en contactarnos.</p>

    <p class="text-orange-600 font-medium">¡Nos vemos en el parque! 🏋️‍♂️</p>

    <hr class="my-6 border-gray-300">

    <p class="text-center text-xs text-gray-500">Este es un mensaje automático, por favor no respondas.</p>
</div>

</body>
</html>
