<!-- resources/views/components/modal.blade.php -->
@props(['id', 'title', 'confirmText' => 'Confirmar', 'cancelText' => 'Cancelar', 'confirmAction' => '#'])

<div id="{{ $id }}" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <!-- Título del Modal -->
        <h5 class="text-xl font-bold text-orange-600">{{ $title }}</h5>

        <!-- Contenido del Modal -->
        <div class="my-4">
            {{ $slot }}
        </div>

        <!-- Botones de Acción -->
        <div class="flex justify-end gap-2 mt-6">
            <button type="button" onclick="closeModal('{{ $id }}')" class="bg-gray-300 px-4 py-2 rounded-md hover:bg-gray-400">
                {{ $cancelText }}
            </button>

            @if ($confirmAction !== '#')
                <form action="{{ $confirmAction }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">
                        {{ $confirmText }}
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

<!-- Script para Abrir y Cerrar el Modal -->
<script>
function openModal(id) {
    document.getElementById(id)?.classList.remove('hidden');
}

function closeModal(id) {
    document.getElementById(id)?.classList.add('hidden');
}
</script>