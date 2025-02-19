<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrenamiento Creado</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal p-6">

<div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8">
    <h2 class="text-2xl font-semibold text-orange-500 mb-4">¡Hola, {{ $trainerName }}! 👋</h2>
    
    <p class="text-gray-700 mb-4">Tu entrenamiento <strong class="text-orange-600">{{ $trainingTitle }}</strong> ha sido creado con éxito.</p>

    <div class="mb-6">
        <p><strong class="text-gray-800">📍 Parque:</strong> {{ $parkName }}</p>
        <p><strong class="text-gray-800">🏋️ Actividad:</strong> {{ $activity }}</p>
    </div>

    <h4 class="text-lg font-medium text-gray-800 mb-2">⏰ Horarios:</h4>
    <ul class="list-disc list-inside space-y-2">
        @foreach ($schedule as $s)
            <li class="text-gray-600">
                {{ ucfirst($s->day) }}: 
                {{ \Carbon\Carbon::createFromFormat('H:i:s', $s->start_time)->format('H:i') }} - 
                {{ \Carbon\Carbon::createFromFormat('H:i:s', $s->end_time)->format('H:i') }}
            </li>
        @endforeach
    </ul>

    <p class="text-gray-700 mt-6">¡Gracias por usar Motum! 🚀</p>
</div>

</body>
</html>
