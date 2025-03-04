<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;
use App\Models\Park;
use App\Models\Training;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FavoriteController extends Controller
{
 

    public function toggleFavorite(Request $request)
    {
        $user = Auth::user();
        $logId = uniqid();
    
        $modelClass = match ($request->favoritable_type) {
            'park' => 'App\Models\Park',
            'training' => 'App\Models\Training',
            default => throw new \InvalidArgumentException('Tipo de favorito no válido'),
        };
    
        Log::info("🟢 [$logId] Intentando modificar favoritos", [
            'user_id' => $user->id,
            'favoritable_id' => $request->favoritable_id,
            'favoritable_type' => $modelClass,
        ]);
    
        return DB::transaction(function () use ($user, $request, $modelClass, $logId) {
            $favorite = Favorite::where([
                ['user_id', '=', $user->id],
                ['favoritable_id', '=', $request->favoritable_id],
                ['favoritable_type', '=', $modelClass],
            ])->lockForUpdate()->first();
    
            if ($favorite) {
                Log::info("❌ [$logId] Eliminado de favoritos", ['id' => $favorite->id]);
                $favorite->delete();
                return response()->json([
                    'message' => 'Eliminado de favoritos',
                    'status' => 'removed',
                    'favoritable_id' => $request->favoritable_id,
                    'favoritable_type' => $request->favoritable_type
                ]);
            }
    
            Log::info("📌 [$logId] Creando favorito en la BD");
    
            $newFavorite = Favorite::create([
                'user_id' => $user->id,
                'favoritable_id' => $request->favoritable_id,
                'favoritable_type' => $modelClass,
            ]);
    
            Log::info("✅ [$logId] Agregado a favoritos", ['id' => $newFavorite->id]);
    
            return response()->json([
                'message' => 'Agregado a favoritos',
                'status' => 'added',
                'favoritable_id' => $request->favoritable_id,
                'favoritable_type' => $request->favoritable_type
            ]);
        });
    }

    // Mostrar favoritos
    public function index()
    {
        $user = Auth::user();
    
        $favoriteParks = Favorite::where('user_id', $user->id)
            ->where('favoritable_type', Park::class)
            ->with('favoritable')
            ->get()
            ->filter(fn($favorite) => $favorite->favoritable !== null); // 🔥 Filtra los que no tienen relación válida
    
        $favoriteTrainings = Favorite::where('user_id', $user->id)
            ->where('favoritable_type', Training::class)
            ->with('favoritable')
            ->get()
            ->filter(fn($favorite) => $favorite->favoritable !== null); // 🔥 Filtra los que no tienen relación válida
    
        return view('favorites.view', compact('favoriteParks', 'favoriteTrainings'));
    }
}
