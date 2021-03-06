<?php

namespace  Ipsum\Media\Concerns;

use Ipsum\Media\app\Models\Media;
use Session;

trait Mediable
{

    protected static function bootMediable()
    {

        static::saved(function ($objet) {

            // Association des medias uploadés avant enregistrement de la publication
            if (Session::has('media.publications')) {
                foreach (Session::get('media.publications') as $medias) {
                    $media = Media::find($medias['media_id']);
                    if ($media) {
                        $media->publication_id = $objet->id;
                        $media->save();
                    }
                }
                Session::forget('media.publications');
            }
        });

        static::deleting(function ($objet) {
            if ($objet->mediable_delete) {
                foreach ($objet->medias as $media) {
                    $media->delete();
                }
            } else {
                $objet->medias()->update(['publication_id' => null, 'publication_type' => null]);
            }
        });

    }




    /*
     * Relations
     */

    public function medias()
    {
        return $this->morphMany(Media::class, 'publication')->orderBy('order');
    }

    public function images()
    {
        return $this->medias()->images();
    }

    public function documents()
    {
        return $this->medias()->documents();
    }

    public function illustration()
    {
        return $this->morphOne(Media::class, 'publication')->images()->orderBy('order')->orderBy('groupe');
    }
}
