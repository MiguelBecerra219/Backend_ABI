<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContentRequest;
use App\Models\Content;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContentController extends Controller
{
    /**
     * Listado paginado de contenidos disponibles.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $perPage = $perPage > 0 ? min($perPage, 100) : 15;

        $query = Content::query();

        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $rolesFilter = $request->query('roles');
        if ($rolesFilter !== null) {
            if (is_string($rolesFilter)) {
                $rolesFilter = json_decode($rolesFilter, true);
                if (json_last_error() !== JSON_ERROR_NONE || ! is_array($rolesFilter)) {
                    $rolesFilter = array_filter(array_map('trim', explode(',', $rolesFilter)));
                }
            }

            if (! is_array($rolesFilter)) {
                $rolesFilter = [$rolesFilter];
            }

            foreach ($rolesFilter as $role) {
                $role = trim((string) $role);
                if ($role !== '') {
                    $query->whereJsonContains('roles', $role);
                }
            }
        }

        $contents = $query
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();

        return response()->json($contents);
    }

    /**
     * Crea un nuevo contenido de catálogo.
     */
    public function store(ContentRequest $request): JsonResponse
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data) {
            $content = Content::create($data);

            return response()->json([
                'message' => 'Contenido creado correctamente.',
                'data' => $content,
            ], 201);
        });
    }

    /**
     * Muestra el detalle de un contenido específico.
     */
    public function show(Content $content): JsonResponse
    {
        $content->load(['versions' => function ($query) {
            $query->orderByDesc('content_version.created_at');
        }]);

        return response()->json($content);
    }

    /**
     * Actualiza un contenido existente.
     */
    public function update(ContentRequest $request, Content $content): JsonResponse
    {
        $data = $request->validated();

        return DB::transaction(function () use ($content, $data) {
            $content->update($data);

            return response()->json([
                'message' => 'Contenido actualizado correctamente.',
                'data' => $content,
            ]);
        });
    }

    /**
     * Elimina un contenido del catálogo.
     */
    public function destroy(Content $content): JsonResponse
    {
        if ($content->contentVersions()->exists()) {
            return response()->json([
                'message' => 'No es posible eliminar el contenido porque tiene versiones asociadas.',
            ], 409);
        }

        $content->delete();

        return response()->json(null, 204);
    }
}
