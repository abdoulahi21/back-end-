<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        try {
            $tags = Tag::all();
            return response()->json([
                'status' => 200,
                'message' => 'Liste des tags',
                'tags' => $tags
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => $e->getCode(),
                'message' => "Une erreur s'est produite lors de la récupération des tags",
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function store(Request $request)
    {
        $request->validate([
            'slug' => 'required|unique:tags,slug',
            'name' => 'required|string|max:255',
        ]);

        try {
            $tag = Tag::create($request->all());
            return response()->json([
                'status' => 200,
                'message' => 'Tag créé avec succès',
                'tag' => $tag
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => $e->getCode(),
                'message' => "Une erreur s'est produite lors de la création du tag",
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function show($id)
    {
        try {
            $tag = Tag::findOrFail($id);
            return response()->json([
                'status' => 200,
                'message' => 'Détails du tag',
                'tag' => $tag
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => $e->getCode(),
                'message' => "Une erreur s'est produite lors de la récupération du tag",
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'slug' => 'required|unique:tags,slug,' . $id,
            'name' => 'required|string|max:255',
        ]);

        try {
            $tag = Tag::findOrFail($id);
            $tag->update($request->all());

            return response()->json([
                'status' => 200,
                'message' => 'Tag mis à jour avec succès',
                'tag' => $tag
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => $e->getCode(),
                'message' => "Une erreur s'est produite lors de la mise à jour du tag",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    
    public function destroy($id)
    {
        try {
            $tag = Tag::findOrFail($id);
            $tag->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Tag supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => $e->getCode(),
                'message' => "Une erreur s'est produite lors de la suppression du tag",
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
