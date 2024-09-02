<div>
    @if($sections->isEmpty())
        <p>Tukšs</p>
    @else
        <ul class="list-disc pl-5" style="padding-left:25px;">
            @foreach ($sections as $section)
                <li>
                    {{ $section->type->getLabel() }} /
                    {{ $section->teeth_position->getLabel()}} /
                    {{ $section->material->getLabel() }}
                </li>
            @endforeach
        </ul>
    @endif
</div>
