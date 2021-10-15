<?php

namespace Ipsum\Media\app\Models;


use Ipsum\Admin\Concerns\Htmlable;
use Ipsum\Core\app\Models\BaseModel;
use Ipsum\Media\Concerns\Sortable;

class Media extends BaseModel
{
    use Sortable, Htmlable;


    protected $table = 'medias';

    protected $fillable = ['type', 'titre', 'alt', 'description', 'repertoire', 'fichier', 'url', 'publication_id', 'publication_type', 'groupe', 'order'];


    protected $appends = ['is_image', 'crop_path', 'tag_alt'];

    protected $htmlable = ['description'];

    const TYPE_IMAGE = 'image';
    const TYPE_DOCUMENT = 'document';


    public static function boot()
    {
        parent::boot();

        static::deleting(function ($objet) {
            if (\File::exists(public_path($objet->path))) {
                \Croppa::delete($objet->cropPath);
            }
        });

    }


    /*
     * Relations
     */




    /*
     * Scopes
     */

    public function scopeImages($query)
    {
        return $query->where('type', self::TYPE_IMAGE);
    }

    public function scopeDocuments($query)
    {
        return $query->where('type', self::TYPE_DOCUMENT);
    }



    /*
     * Accessors & Mutators
     */

    public function getPathAttribute()
    {
        return config('ipsum.media.path').($this->repertoire ? $this->repertoire.'/'.$this->fichier : $this->fichier);
    }

    public function getCropPathAttribute()
    {
        return config('ipsum.media.path').'crops/'.($this->repertoire ? $this->repertoire.'/'.$this->fichier : $this->fichier);
    }

    public function getCropUrlAttribute()
    {
        return config('ipsum.media.path').'crops/'.($this->repertoire ? $this->repertoire.'/'.$this->fichier : $this->fichier);
    }

    public function getIconeAttribute()
    {
        return config()->has('ipsum.media.types.'.$this->type.'.icone') ?  config()->get('ipsum.media.types.'.$this->type.'.icone') : 'fa fa-file';
    }

    public function getIsImageAttribute()
    {
        return $this->type == self::TYPE_IMAGE;
    }

    public function getIsDocumentAttribute()
    {
        return $this->type == self::TYPE_DOCUMENT;
    }

    public function getTagAltAttribute()
    {
        return $this->alt ? $this->alt : $this->titre;
    }
}
