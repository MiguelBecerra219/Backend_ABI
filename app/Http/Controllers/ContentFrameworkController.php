<?php

namespace App\Http\Controllers;

use App\Models\Framework;
use App\Models\ContentFramework;
use App\Http\Requests\ContentFrameworkRequest;

class ContentFrameworkController extends Controller
{
    public function store(ContentFrameworkRequest $request, Framework $framework)
    {
        $data = $request->safe()->only(['name','description']);
        // Importante: la columna no es nullable
        if (!isset($data['description']) || $data['description'] === null) {
            $data['description'] = '';
        }

        $framework->contents()->create($data);
        return back()->with('ok','Contenido agregado');
    }

    public function update(ContentFrameworkRequest $request, ContentFramework $content)
    {
        $data = $request->safe()->only(['name','description']);
        if (!isset($data['description']) || $data['description'] === null) {
            $data['description'] = '';
        }

        $content->update($data);
        return back()->with('ok','Contenido actualizado');
    }

    public function destroy(ContentFramework $content)
    {
        $content->delete();
        return back()->with('ok','Contenido eliminado');
    }
}
