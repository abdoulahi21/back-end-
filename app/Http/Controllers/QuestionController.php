<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    // Recherche des questions
    public function searchQuestion(Request $request)
    {
        try {
            $questions = Question::select('id', 'title', 'slug')
                ->where('title', 'like', '%' . $request->searchKey . '%')
                ->get();

            return response()->json([
                'status' => 200,
                'message' => 'Liste des questions',
                'questions' => $questions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => $e->getCode(),
                'message' => "Une erreur s'est produite lors de la recherche des questions",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Affiche la liste des questions
    public function index()
    {
        try {
            $questions = Question::all();

            return response()->json([
                'status' => 200,
                'message' => 'Liste des questions',
                'questions' => $questions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => $e->getCode(),
                'message' => "Une erreur s'est produite lors de la récupération des questions",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Créer une nouvelle question
    public function store(Request $request)
{
    $request->validate([
        'slug' => 'required|unique:questions,slug',
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        
    ]);

    try {
        // Récupérer l'utilisateur connecté
        $user = Auth::user();
        if (!$user) {
            throw new \Exception("Utilisateur non authentifié", 401);
        }

        // Log l'utilisateur pour debugging
        Log::info('Utilisateur authentifié:', ['user' => $user]);

        // Créer la question en utilisant les données du formulaire et l'utilisateur connecté
        $question = Question::create([
            'user_id' => $user->id,
            'slug' => $request->input('slug'),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'is_solved' => $request->input('is_solved'),
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Question créée avec succès',
            'question' => $question
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => $e->getCode() ?: 500,
            'message' => "Une erreur s'est produite lors de la création de la question",
            'error' => $e->getMessage(),
        ], 500);
    }
}
    // Affiche les détails d'une question spécifique
    public function show($id)
    {
        try {
            $question = Question::findOrFail($id);

            return response()->json([
                'status' => 200,
                'message' => 'Détails de la question',
                'question' => $question
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => $e->getCode(),
                'message' => "Une erreur s'est produite lors de la récupération de la question",
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    // Met à jour une question existante
    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'slug' => 'required|unique:questions,slug,' . $id,
            'title' => 'required|string|max:255',
            'description' => 'required|string',
          
        ]);

        try {
            $question = Question::findOrFail($id);
            $question->update($request->all());

            return response()->json([
                'status' => 200,
                'message' => 'Question mise à jour avec succès',
                'question' => $question
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => $e->getCode(),
                'message' => "Une erreur s'est produite lors de la mise à jour de la question",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Supprime une question spécifique
    public function destroy($id)
    {
        try {
            $question = Question::findOrFail($id);
            $question->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Question supprimée avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => $e->getCode(),
                'message' => "Une erreur s'est produite lors de la suppression de la question",
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
