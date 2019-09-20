<?php

namespace Ipsum\Media\Concerns;

trait Sortable
{

    protected static function bootSortable()
    {

        self::saving(function(self $objet)
        {
            if (!$objet->exists) {

                // On récupère le dernier order
                $objet->order = self::where('publication_type', $objet->publication_type)->where('publication_id', $objet->publication_id)->where('groupe', $objet->groupe)->count() + 1;

            }
        });

        self::deleted(function(self $objet)
        {
            self::updateOrder($objet->publication_type, $objet->publication_id, $objet->groupe);
        });
    }



    static public function updateOrder($publication_type = null, $publication_id = null, $groupe = null, $exclude_id = null)
    {
        $query = self::select(['id', 'order', 'publication_type', 'publication_id', 'groupe']);
        if ($publication_type !== null) {
            $query->where('publication_type', $publication_type)->where('publication_id', $publication_id)->where('groupe', $groupe);
        }
        if ($exclude_id !== null) {
            $query->where('id', '!=', $exclude_id);
        }
        $objets = $query->orderBy('order', 'asc')->orderBy('id', 'asc')->get();

        $datas = [];
        foreach ($objets as $objet) {
            $datas[$objet->publication_type.'-'.$objet->publication_id.'-'.$groupe][] = $objet;
        }

        foreach ($datas as $data) {
            $order = 1;
            foreach ($data as $objet) {
                $objet->order = $order;
                $objet->save();
                $order++;
            }
        }
    }

}