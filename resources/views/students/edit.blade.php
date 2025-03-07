@extends('layouts.main')

@section('title', 'Editar Perfil')

@section('content')
<div class="max-w-4xl mx-auto p-4 mt-6 relative">
    <a href="{{ route('students.profile',['id' => $user->id]) }}" 
     class="text-orange-500 font-medium">&lt; Volver a mi perfil</a>
    <div class="bg-white pt-6 rounded-lg mt-4 shadow-md p-6">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Editar Perfil</h2>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md mb-6">
                <ul class="list-disc ml-5">
                    @foreach ($errors->all() as $error)
                        <li class="text-sm">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('students.updateProfile') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Primera Fila: Foto de Perfil + Nombre, Email y Teléfono -->
            <div class="grid grid-cols-1 md:grid-cols-4 sm:gap-6 items-start">
                <!-- Foto de Perfil -->
                <div class="col-span-1 flex flex-col items-center md:items-start" x-data="photoPreview('{{ $user->profile_pic ? asset('storage/' . $user->profile_pic) : null }}')">
                    <div class="relative w-40 h-40 rounded-full overflow-hidden shadow-lg border cursor-pointer" @click="$refs.fileInput.click()">
                        <img :src="photo" class="w-full h-full object-cover">
                        <button type="button" @click="removeImage($event)" class="absolute top-1 right-1 w-6 h-6 flex items-center justify-center bg-white rounded-full shadow-md">
                            <x-lucide-square-x class="h-4 w-4 text-red-500" />
                        </button>
                    </div>
                    <input type="file" id="profile_pic" name="profile_pic" accept="image/*" @change="previewImage(event)" class="hidden" x-ref="fileInput">
                    <p class="text-gray-500 text-sm text-center mt-3">Haz clic en la imagen para cambiar la foto</p>
                </div>

                <!-- Datos Personales -->
                <div class="col-span-3 space-y-4">
                    <h5 class="block text-gray-700 font-bold">Datos personales</h5>
                    <x-form.input name="name" label="Nombre" value="{{ $user->name }}" required />
                    <x-form.input name="email" label="Correo Electrónico" type="email" value="{{ $user->email }}" required />
                    <x-form.input name="phone" label="Teléfono" type="tel" value="{{ $user->phone }}" />
                </div>
            </div>

            <!-- Segunda Fila: Biografía + Certificación y Apto Médico -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6 ">
            <div class="hidden md:block"></div>
                <div class="md:col-span-3">
                    <div class="space-y-4">
                    <x-form.textarea name="biography" label="Biografía" value="{{ $user->biography }}" rows="4" />
                    <div class="space-y-2">
                            <label for="medical_fit" class="block text-gray-700 font-bold">Apto Médico</label>
                            <input type="file" id="medical_fit" name="medical_fit" accept="image/*"
                                class="w-full bg-gray-50 text-black border border-gray-500 rounded-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                            @error('medical_fit')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                </div>
            </div>
</div>
            <!-- Botón de Guardar -->
            <div class="flex justify-end mt-8">
                <button type="submit" class="bg-orange-500 text-white text-md px-6 py-3 rounded-md hover:bg-orange-600 transition">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function photoPreview(existingPhoto = null) {
    return {
        photo: existingPhoto ? existingPhoto : null,

        previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.photo = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },

        removeImage(event) {
            event.stopPropagation(); // Evita que al hacer clic en "X" también abra el selector de archivos
            this.photo = null;
        }
    };
}
</script>
@endsection