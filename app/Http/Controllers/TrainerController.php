<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\HandlesImages;
use App\Models\User;
use App\Models\Training;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use App\Models\Park;
use App\Models\UserExperience;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;



class TrainerController extends Controller
{
    use HandlesImages;

    //////REGISTRO ENTRENADOR
    public function registerTrainer()
    {
        return view('auth.register-trainer');
    }

    public function storeTrainer(Request $request)
    {
     
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'mercado_pago_email' => 'required|email|unique:users',
            'collector_id' => 'required|string|max:255|unique:users', // Validar Collector ID
            'password' => 'required|min:6|confirmed',
            'certification' => 'required|string|max:255',
            'biography' => 'nullable|string|max:500',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'park_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'opening_hours' => 'nullable|string',
            'especialty' => 'nullable|string|max:255',
            'birth' => 'required|date|before_or_equal:' . Carbon::now()->subYears(18)->format('Y-m-d'),
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg',
            'certification_pic' => 'nullable|image|mimes:jpeg,png,jpg',
            'experiences' => 'nullable|array',
            'experiences.*.role' => 'nullable|string|max:255',
            'experiences.*.company' => 'nullable|string|max:255',
            'experiences.*.year_start' => 'nullable|integer|min:1900|max:' . now()->year,
            'experiences.*.year_end' => 'nullable|integer|min:1900|max:' . now()->year,
           'experiences.*.currently_working' => 'nullable|boolean',
           'medical_fit' => 'nullable|image|mimes:jpeg,png,jpg',
           'photo_reference' => 'nullable|array',
           
        ]);
        // dd('Validación realizada correctamente');
        $userData = $request->only([
            'name', 'email',  'mercado_pago_email','password', 'certification', 'biography', 'especialty', 'birth','collector_id'
        ]);
        $userData['role'] = 'entrenador';
        $userData['password'] = Hash::make($request->password);
    
        if ($request->hasFile('profile_pic')) {
            $userData['profile_pic'] = $this->resizeAndSaveImage($request->file('profile_pic'), 'profile_pics', 300, 300);
            $userData['profile_pic_description'] = 'Foto de portada del entrenador ' . $request->name;
        }
        if ($request->hasFile('medical_fit')) {
            $userData['medical_fit'] = $this->resizeAndSaveImage($request->file('medical_fit'), 'medical_fits', 300, 300);
            $userData['medical_fit_description'] = 'Foto de portada del entrenador ' . $request->name;
        }
       
     // Convertir `photo_references` de JSON a array
    $photoReferences = json_decode($request->photo_references, true);

    if (!is_array($photoReferences)) {
        $photoReferences = [];
    }

    // Descargar y guardar hasta 4 fotos
    $photoUrls = [];
    foreach (array_slice($photoReferences, 0, 4) as $photoReference) {
        try {
            // Descargar la imagen desde la URL proporcionada
            $imageContents = Http::get($photoReference)->body();

            // Generar un nombre único para la imagen
            $imageName = 'parks/' . uniqid() . '.jpg';

            // Guardar la imagen en storage/app/public/parks
            Storage::disk('public')->put($imageName, $imageContents);

            // Guardar la ruta pública
            $photoUrls[] = Storage::url($imageName);
        } catch (\Exception $e) {
            \Log::error("❌ Error al guardar imagen: " . $e->getMessage());
        }
    }

    
        // Guardar parque con fotos en la base de datos
        $park = Park::firstOrCreate(
            ['name' => $request->park_name],
            [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'location' => $request->location,
                'opening_hours' => $request->opening_hours,
                'photo_urls' => json_encode($photoUrls), // Guardar como JSON
            ]
        );
    
    
        $user = User::create($userData);
        
        Mail::to($user->email)->send(new WelcomeMail($user));

        Auth::login($user);
    
        $user->parks()->attach($park->id);
        if ($request->has('experiences')) {
            foreach ($request->experiences as $experience) {
                $user->experiences()->create([
                    'role' => $experience['role'],
                    'company' => $experience['company'] ?? null,
                    'year_start' => $experience['year_start'],
                    'year_end' => $experience['currently_working'] ? null : $experience['year_end'],
                    'currently_working' => $experience['currently_working'] ?? false,
                ]);
            }
        }
    
        return redirect('/trainer/calendar')->with('success', 'Entrenador registrado exitosamente.');
    }
    
  
    
}
