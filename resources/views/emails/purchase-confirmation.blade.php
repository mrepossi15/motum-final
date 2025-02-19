<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compra Confirmada</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

<div class="max-w-4xl mx-auto my-10 p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-2xl font-semibold text-orange-500 mb-4">¡Gracias por tu compra, {{ $user->name }}!</h2>

    <p class="text-gray-700 mb-4">Has adquirido el entrenamiento <strong class="text-orange-600">{{ $training->title }}</strong> con éxito.</p>

    <div class="mb-6">
        <h3 class="text-lg font-medium text-gray-800 mb-2">Detalles de la compra:</h3>
        <ul class="space-y-2">
            <li class="text-gray-600"><strong class="text-gray-800">Entrenador:</strong> {{ $training->trainer->name }}</li>
            <li class="text-gray-600"><strong class="text-gray-800">Ubicación:</strong> {{ $training->park->name }} - {{ $training->park->location }}</li>
            <li class="text-gray-600"><strong class="text-gray-800">Actividad:</strong> {{ $training->activity->name }}</li>
            <li class="text-gray-600">
                <strong class="text-gray-800">Horario:</strong> 
                {{ $training->schedules->first()->day }} a las 
                {{ \Carbon\Carbon::parse($training->schedules->first()->start_time)->format('H:i') }}
            </li>
        </ul>
    </div>

    <p class="text-gray-700 mb-4">Si tienes alguna pregunta, puedes contactar al entrenador.</p>

    <div class="text-center">
        <p class="text-orange-500 font-semibold">¡Disfruta tu entrenamiento!</p>
    </div>
</div>

</body>
</html>
