<?php

namespace App\Http\Controllers;

use App\Models\QuestionComment;
use App\Models\QuestionLike;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class QuestionCommentController extends Controller
{
    // Affiche les commentaires d'une question spécifique

    public function index(Request $request)
    {
        $questionId = $request->query('question_id');

        if (!$questionId) {
            return response()->json(['error' => 'Question ID is required'], 400);
        }

        $comments = QuestionComment::where('question_id', $questionId)->with('user')->get();

        return response()->json(['comments' => $comments]);
    }


    // Créer un nouveau commentaire
    public function store(Request $request)
    {
        // Log the request data
        Log::info('Request data:', $request->all());

        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'comment' => 'required|string',
        ]);

        try {
            $user = Auth::user();
            if (!$user) {
                throw new \Exception("Utilisateur non authentifié", 401);
            }

            $comment = QuestionComment::create([
                'question_id' => $request->input('question_id'),
                'user_id' => $user->id,
                'comment' => $request->input('comment'),
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Commentaire créé avec succès',
                'comment' => $comment,
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error creating comment:', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 500,
                'message' => "Une erreur s'est produite lors de la création du commentaire",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Affiche les détails d'un commentaire spécifique
    public function show($id)
    {
        try {
            $comment = QuestionComment::findOrFail($id);

            return response()->json([
                'status' => 200,
                'message' => 'Détails du commentaire',
                'comment' => $comment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => $e->getCode() ?: 404,
                'message' => "Une erreur s'est produite lors de la récupération du commentaire",
                'error' => $e->getMessage(),
            ], 404);
        }
    }
    public function like($id)
    {
        // Vérifier si l'utilisateur a déjà liké ce commentaire
        $existingLike = QuestionLike::where('user_id', Auth::id())
            ->where('question_id', $id)
            ->first();
        if ($existingLike) {
            return response()->json(['success' => false, 'message' => 'Comment already liked']);
        }
        // Créer un nouveau like
        QuestionLike::create([
            'user_id' => Auth::id(),
            'question_id' => $id,
        ]);

        return response()->json(['success' => true]);
    }

    // Met à jour un commentaire existant
    public function update(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string',
        ]);

        try {
            $comment = QuestionComment::findOrFail($id);
            // Vérifier si l'utilisateur authentifié est le propriétaire du commentaire
            if ($comment->user_id != Auth::id()) {
                return response()->json([
                    'status' => 403,
                    'message' => "Vous n'êtes pas autorisé à mettre à jour ce commentaire",
                ], 403);
            }

            $comment->update($request->all());
            return response()->json([
                'status' => 200,
                'message' => 'Commentaire mis à jour avec succès',
                'comment' => $comment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => $e->getCode() ?: 500,
                'message' => "Une erreur s'est produite lors de la mise à jour du commentaire",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Supprime un commentaire spécifique
    public function destroy($id)
    {
        try {
            $comment = QuestionComment::findOrFail($id);

            // Vérifier si l'utilisateur authentifié est le propriétaire du commentaire
            if ($comment->user_id != Auth::id()) {
                return response()->json([
                    'status' => 403,
                    'message' => "Vous n'êtes pas autorisé à supprimer ce commentaire",
                ], 403);
            }

            $comment->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Commentaire supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => $e->getCode() ?: 500,
                'message' => "Une erreur s'est produite lors de la suppression du commentaire",
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function valider(string $id)
    {
        try {
            // Recherche du commentaire par son ID
            $comment = QuestionComment::findOrFail($id);

            // Vérification si le commentaire est déjà validé
            if ($comment->is_validated) {
                return response()->json(['message' => 'Le commentaire est déjà validé'], 400);
            }

            // Mettre à jour le statut du commentaire à validé
            $comment->update(['is_validated' => true]);

            // Récupération de l'utilisateur associé au commentaire
            $user = User::find($comment->user_id);

            // Incrémentation du nombre de validations pour l'utilisateur
            $user->update(['nombre' => $user->nombre + 1]);

            // Vérification si l'utilisateur atteint le nombre de validations requis (10 dans votre exemple)
            if ($user->nombre == 10) {
                $role = 'Superviseur';
                $user->syncRoles($role); // Attribution du rôle de Superviseur
            }

            // Réponse JSON avec le commentaire mis à jour
            return response()->json([
                'message' => 'Commentaire validé avec succès',
                'comment' => $comment,
            ]);
        } catch (\Exception $e) {
            // Gestion des erreurs : retourne un message d'erreur si le commentaire n'est pas trouvé
            return response()->json(['message' => 'Erreur: Aucun résultat pour le commentaire avec l\'ID ' . $id, 'code' => 404], 404);
        }
    }
}
