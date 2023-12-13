<?php

namespace Ipsum\Media\app\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Ipsum\Admin\app\Http\Controllers\AdminController;
use Ipsum\Media\app\Http\Requests\StoreMedia;
use Ipsum\Media\app\Models\Media;
use Ipsum\Media\Concerns\Mediable;
use Prologue\Alerts\Facades\Alert;
use File;
use Str;
use Session;
use View;
use Storage;

class MediaController extends AdminController
{

    public function index(Request $request)
    {
        $medias =  $this->liste($request);

        return view('IpsumMedia::media.index', compact('medias'));
    }

    public function popin(Request $request)
    {
        $medias =  $this->liste($request);

        return view('IpsumMedia::media.popin', compact('medias'));
    }

    protected function liste(Request $request)
    {
        $query = Media::query();

        if ($request->filled('search')) {
            $query->where(function($query) use ($request) {
                foreach (['titre', 'description', 'url'] as $colonne) {
                    $query->orWhere($colonne, 'like', '%'.$request->get('search').'%');
                }
            });
        }
        $medias = $query->latest()->paginate(50);

        return $medias;
    }

    public function new()
    {
        $medias = Media::where('created_at', '>=', Carbon::today())->latest()->get();

        $views = '';
        foreach ($medias as $media) {
            $views .= View::make(
                'IpsumMedia::media._media',
                compact('media')
            )->render();
        }

        return $views;
    }

    public function publication(Request $request, $publication_type = null)
    {
        // Problème avec $publication_type en parametre de route sur serveur laragon
        // On garde une rétrocompatibilité
        if ($publication_type === null) {
            $publication_type = $request->get('publication_type');
        }

        $query = Media::where('publication_type', $publication_type)
            ->where('publication_id', $request->get('publication_id'))
            ->where('groupe', $request->filled('groupe') ? $request->get('groupe') : null)
            ->orderBy('order');

        if (!$request->filled('publication_id')) {
            $media_ids = [];
            if (Session::has('media.publications')) {
                foreach (Session::get('media.publications') as $media) {
                    if ($media['publication_type'] == $publication_type) {
                        $media_ids[] = $media['media_id'];
                    }
                }
            }
            $query->whereIn('id', $media_ids);
        }

        $medias = $query->get();

        $views = '';
        foreach ($medias as $media) {
            $views .= View::make(
                'IpsumMedia::media._media',
                ['media' => $media, 'sortable' => true]
            )->render();
        }

        return $views;
    }

    public function create()
    {
        $media = new Media;

        return view('IpsumMedia::media.create', compact('media'));
    }


    public function store(Request $request)
    {
        $mimes = array();
        foreach (config('ipsum.media.types') as $type) {
            $mimes = array_merge($mimes, $type['mimes']);
        }
        $mimesAccepted = implode(',', $mimes);

        $this->validate($request, [
            'media' => 'required|mimes:'.$mimesAccepted.'|max:'.config('ipsum.media.upload_max_filesize'),
        ]);

        if ($request->filled('publication_type') and (!class_exists($request->get('publication_type'), true) or !isset(class_uses($request->get('publication_type'))[Mediable::class]))) {
            $error =  "Publication non reconnu.";
            abort(422, $error);
        }

        $path = $request->filled('path') ? $request->get('path').'/' : config('ipsum.media.path');
        $repertoire = ($request->filled('repertoire') and in_array($request->get('repertoire'), config('ipsum.media.repertoires'))) ? $request->get('repertoire').'/' : '';

        $file = $request->file('media');
        $media = null;

        try {
            $extension = strtolower(\File::extension($file->getClientOriginalName()));
            $basename = basename($file->getClientOriginalName(), '.'.$extension);
            $titre = str_replace(array('-', '_'), ' ', $basename);
            $basename = Str::slug($basename);
            $filename = $basename.'.'.$extension;

            // Renomme si fichier existe déja
            $count = 1;
            while (File::exists($path.$repertoire.$filename)) {
                $filename = $basename.'('.$count++.').'.$extension;
            }

            // Récupèration du type de fichier
            $type = null;
            foreach (config('ipsum.media.types') as $value) {
                if (in_array($extension, $value['mimes'])) {
                    $type = $value['type'];
                    break;
                }
            }

            // Enregistrement du fichier
            $file->move($path.$repertoire, $filename);

            // Enregistrement en bdd
            $media = new Media;
            $media->titre = $titre;
            $media->fichier = $filename;
            $media->type = $type;
            $media->repertoire = str_replace('/', '', $repertoire);
            $media->publication_id = $request->get('publication_id');
            $media->publication_type = $request->get('publication_type');
            $media->groupe = $request->get('groupe');
            $media->save();

            if ($request->filled('publication_type') and !$request->filled('publication_id')) {
                // Cas des médias qui ne sont pas encore associé à une publication
                // Cas de l'upload avant la création de la publication
                $mediaPublications[] = array(
                    'publication_type' => $request->get('publication_type'),
                    'media_id' => $media->id
                );
                if (Session::has('media.publications')) {
                    $mediaPublications = array_merge(Session::get('media.publications'), $mediaPublications);
                }
                Session::put('media.publications', $mediaPublications);
            }

        } catch (\Exception $e) {
            $error =  "Impossible de télécharger le média ".$file->getClientOriginalName().$e->getMessage();
            if (\App::environment('debug')) {
                $error .= ' '.$e->getMessage();
            }
            abort(500, $error);
        }

        return response()->json([
            'media' => $media,
            'message' => "Le média ".$file->getClientOriginalName()." a bien été téléchargé",
        ]);

    }

    public function edit(Media $media)
    {
        return view('IpsumMedia::media.update', compact('media'));
    }

    public function crop(Media $media)
    {
        return view('IpsumMedia::media.crop', compact('media'));
    }

    public function crop_update(Request $request, Media $media)
    {
        // Récupérer le fichier envoyé via AJAX
        $imageData = explode( ',', $request->input('image'));

        // Convertir les données base64 en fichier temporaire
        $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData[1]));

        // Générer un nouveau nom de fichier unique
        $ext = pathinfo($media->fichier, PATHINFO_EXTENSION);
        if($request->input('extension')){
            $ext = $request->input('extension');
        }
        $filename = pathinfo(Str::slug($media->titre), PATHINFO_FILENAME);
        $count = 1;
        $newFileName = $filename.uniqid().'('.$count.').'.$ext;
        while (File::exists(config('ipsum.media.path').$media->repertoire.'/'.$newFileName)) {
            $newFileName = $filename.uniqid().'('.$count++.').'.$ext;
        }

        // Sauvegarder le fichier dans le répertoire approprié
        $path = config('ipsum.media.path').$media->repertoire.'/'.$newFileName;
        file_put_contents($path, $image);

        // Suppression de l'ancien fichier
        if (File::exists(config('ipsum.media.path').$media->repertoire.'/'.$media->fichier)) {
            File::delete(config('ipsum.media.path').$media->repertoire.'/'.$media->fichier);
        }

        // Mettre à jour le nom du fichier dans $media->fichier
        $media->fichier = $newFileName;
        $media->save();



        Alert::success("L'enregistrement a bien été modifié")->flash();
        return response()->json(['success' => true, 'message' => "L'enregistrement a bien été modifié"]);
    }

    public function update(StoreMedia $request, Media $media)
    {
        $media->update($request->all());

        Alert::success("L'enregistrement a bien été modifié")->flash();
        return back();
    }

    public function changeOrder(Request $request)
    {
        foreach ($request->get('ids') as $key => $id) {
            $media = Media::find($id);
            if ($media) {
                $media->order = $key + 1;
                $media->save();
            }
        }

        return;
    }

    public function destroy(Media $media)
    {
        $media->delete();

        Alert::warning("L'enregistrement a bien été supprimé")->flash();
        return back();

    }
}
