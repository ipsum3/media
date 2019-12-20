<?php

namespace Ipsum\Media\app\Models;


use Ipsum\Admin\Concerns\Htmlable;
use Ipsum\Core\app\Models\BaseModel;
use Ipsum\Media\Concerns\Sortable;

class Media extends BaseModel
{
    use Sortable, Htmlable;


    protected $table = 'medias';

    protected $fillable = ['titre', 'alt', 'description', 'repertoire', 'fichier', 'url', 'publication_id', 'publication_type', 'groupe', 'order'];


    protected $appends = ['is_image', 'crop_path', 'tag_alt'];

    protected $htmlable = ['description'];

    const TYPE_IMAGE = 'image';



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
    
    
    
    /*
     * Accessors & Mutators
     */

    public function getPathAttribute()
    {
        return 'uploads/'.($this->repertoire ? $this->repertoire.'/'.$this->fichier : $this->fichier);
    }

    public function getCropPathAttribute()
    {
        return 'uploads/crops/'.($this->repertoire ? $this->repertoire.'/'.$this->fichier : $this->fichier);
    }

    public function getCropUrlAttribute()
    {
        return 'uploads/crops/'.($this->repertoire ? $this->repertoire.'/'.$this->fichier : $this->fichier);
    }

    public function getIconeAttribute()
    {
        return config()->has('ipsum.media.types.'.$this->type.'.icone') ?  config()->get('ipsum.media.types.'.$this->type.'.icone') : 'fa fa-file';
    }

    public function getIsImageAttribute()
    {
        return $this->type == self::TYPE_IMAGE;
    }

    public function getTagAltAttribute()
    {
        return $this->alt ? $this->alt : $this->titre;
    }
}
