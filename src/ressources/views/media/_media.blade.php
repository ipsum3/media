<div class="media sortable-item" data-sortable="{{ $media->id }}">
    <div class="media-img">
        @if ($media->isImage)
            <img src="{{ Croppa::url($media->cropPath, 130, 130) }}" alt="{{ $media->tagAlt }}" />
        @else
            <span class="media-icone {{ $media->icone }}"></span>
        @endif
    </div>
    <div class="media-title">
        {{ $media->titre }}
    </div>
    <div class="media-toolbar">
        <ul>
            <li><a href="{{ route('admin.media.edit', $media->id) }}" data-toggle="tooltip" title="Editer"><span class="fa fa-edit"></span></a></li>
            <li><a href="{{ route('admin.media.getDestroy', $media->id) }}" data-toggle="tooltip" title="Supprimer"><span class="fa fa-trash-alt"></span></a></li>
            {{--<li>
                <form action="{{ route('admin.media.destroy', $media->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"><i class="fa fa-trash-alt"></i></button>
                </form>
            </li>--}}
            @if (!empty($sortable))
                <li class="sortable-move" data-toggle="tooltip" title="Trier"><span class="fa fa-arrows-alt"></span></li>
            @endif
            @if (!empty($media_add))
                <li class="media-add"
                    data-titre="{{ $media->titre }}"
                    data-alt="{{ $media->tagAlt }}"
                    data-path="{{ $media->isImage ? asset($media->cropPath) :  asset($media->path) }}"
                    data-isimage="{{ $media->isImage }}"
                    data-icone="{{ $media->icone }}"
                    data-toggle="tooltip" title="Ajouter">
                    <span class="fa fa-plus"></span>
                </li>
            @endif
        </ul>
    </div>
</div>