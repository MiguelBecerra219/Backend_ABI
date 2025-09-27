<?php

namespace App\Http\Controllers;

use App\Http\Requests\VersionRequest;
use App\Models\Version;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VersionController extends Controller
{
    /**
     * Lista las versiones registradas.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $perPage = $perPage > 0 ? min($perPage, 100) : 15;

        $query = Version::query()->with('project');

        if ($projectId = $request->query('project_id')) {
            $query->where('project_id', $projectId);
        }

        $versions = $query
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();

        return response()->json($versions);
    }

    /**
     * Crea una nueva versión para un proyecto.
     */
    public function store(VersionRequest $request): JsonResponse
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data) {
            $version = Version::create($data);

            return response()->json([
                'message' => 'Versión creada correctamente.',
                'data' => $version->load('project'),
            ], 201);
        });
    }

    /**
     * Muestra una versión concreta.
     */
    public function show(Version $version): JsonResponse
    {
        $version->load(['project', 'contents' => function ($query) {
            $query->orderBy('name');
        }]);

        return response()->json($version);
    }

    /**
     * Actualiza los datos de una versión.
     */
    public function update(VersionRequest $request, Version $version): JsonResponse
    {
        $data = $request->validated();

        return DB::transaction(function () use ($version, $data) {
            $version->update($data);

            return response()->json([
                'message' => 'Versión actualizada correctamente.',
                'data' => $version->load('project'),
            ]);
        });
    }

    /**
     * Elimina una versión.
     */
    public function destroy(Version $version): JsonResponse
    {
        if ($version->contentVersions()->exists()) {
            return response()->json([
                'message' => 'No es posible eliminar la versión porque tiene contenidos diligenciados.',
            ], 409);
        }

        $version->delete();

        return response()->json(null, 204);
    }
}
