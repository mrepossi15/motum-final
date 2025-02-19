<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clase Suspendida</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal p-6">

<div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8">
    <h2 class="text-2xl font-semibold text-orange-500 mb-4">Motum - Clase Suspendida</h2>

    <p class="text-gray-700 mb-4">Hola,</p>
    <p class="text-gray-700 mb-4">
        Te informamos que la clase <strong class="text-orange-600">{{ $training->title }}</strong> programada para el día 
        <strong>{{ \Carbon\Carbon::parse($date)->translatedFormat('l d/m/Y') }}</strong> ha sido suspendida.
    </p>

    <div class="bg-gray-50 p-4 rounded-lg mb-4">
        <h4 class="text-lg font-medium text-gray-800 mb-2">Detalles:</h4>
        <ul class="space-y-2">
            <li class="text-gray-600"><strong class="text-gray-800">Actividad:</strong> {{ $training->activity->name }}</li>
            <li class="text-gray-600"><strong class="text-gray-800">Parque:</strong> {{ $training->park->name }}</li>
            <li class="text-gray-600"><strong class="text-gray-800">Entrenador:</strong> {{ $training->trainer->name }}</li>
        </ul>
    </div>

    <p class="text-gray-700">Para más información, puedes contactar a tu entrenador.</p>

    <p class="text-sm text-gray-500 mt-4">Saludos,<br>El equipo de Motum.</p>
</div>

</body>
</html>
