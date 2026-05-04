<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-sm-0 font-size-18">{{ $title }}</h4>
                @isset($subtitle)
                    <p class="text-muted mb-0 mt-1">{{ $subtitle }}</p>
                @endisset
            </div>

            @isset($action)
                <div class="page-title-right">
                    {!! $action !!}
                </div>
            @endisset
        </div>
    </div>
</div>
