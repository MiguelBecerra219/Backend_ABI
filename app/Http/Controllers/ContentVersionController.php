<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContentVersionRequest;
use App\Models\ContentVersion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContentVersionController extends Controller
{
    /**
     * Lista los valores diligenciados por contenido y versión.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $perPage = $perPage > 0 ? min($perPage, 100) : 15;

        $query = ContentVersion::query()->with(['content', 'version.project']);

        if ($versionId = $request->query('version_id')) {
            $query->where('version_id', $versionId);
        }

        if ($contentId = $request->query('content_id')) {
            $query->where('content_id', $contentId);
        }

        if ($projectId = $request->query('project_id')) {
            $query->whereHas('version', function ($q) use ($projectId) {
                $q->where('project_id', $projectId);
            });
        }

        if ($search = trim((string) $request->query('search', ''))) {
            $query->where('value', 'like', '%' . $search . '%');
        }

        $contentVersions = $query
            ->orderByDesc('updated_at')
            ->paginate($perPage)
            ->withQueryString();

        return response()->json($contentVersions);
    }

    /**
     * Registra un nuevo valor de contenido para una versión.
     */
    public function store(ContentVersionRequest $request): JsonResponse
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data) {
            $contentVersion = ContentVersion::create($data);

            return response()->json([
                'message' => 'Contenido diligenciado correctamente.',
                'data' => $contentVersion->load(['content', 'version.project']),
            ], 201);
        });
    }

    /**
     * Muestra el detalle de un registro contenido-versión.
     */
    public function show(ContentVersion $contentVersion): JsonResponse
    {
        $contentVersion->load(['content', 'version.project']);

        return response()->json($contentVersion);
    }

    /**
     * Actualiza el valor de un contenido para una versión.
     */
    public function update(ContentVersionRequest $request, ContentVersion $contentVersion): JsonResponse
    {
        $data = $request->validated();

        return DB::transaction(function () use ($contentVersion, $data) {
            $contentVersion->update($data);

            return response()->json([
                'message' => 'Registro actualizado correctamente.',
                'data' => $contentVersion->load(['content', 'version.project']),
            ]);
        });
    }

    /**
     * Elimina un registro contenido-versión.
     */
    public function destroy(ContentVersion $contentVersion): JsonResponse
    {
        $contentVersion->delete();

        return response()->json(null, 204);
    }
}
