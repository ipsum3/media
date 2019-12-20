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

    public function publication(Request $request, $publication_type)
    {
        $medias = Media::where('publication_type', $publication_type)
            ->where('publication_id', $request->filled('publication_id') ? $request->get('publication_id') : null)
            ->where('groupe', $request->filled('groupe') ? $request->get('groupe') : null)
            ->orderBy('order')->get();

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
            while (File::exists(config('ipsum.media.path').$repertoire.$filename)) {
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
            $file->move(config('ipsum.media.path').$repertoire, $filename);

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
                // Cas des médias qui ne sont pas encore associé à une pulbication
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

        if (File::exists($media->path)) {
            \Croppa::delete($media->cropPath);
        }

        Alert::warning("L'enregistrement a bien été supprimé")->flash();
        return back();

    }
}
