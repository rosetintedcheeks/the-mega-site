{{$downloadInfo->progressTarget}}
{{$downloadInfo->percentage}}
{{$downloadInfo->size}}
@if ($downloadInfo->speed)
    {{$downloadInfo->speed}}
@endif
@if ($downloadInfo->eta)
    {{$downloadInfo->eta}}
@endif
@if ($downloadInfo->totalTime)
    {{$downloadInfo->totalTime}}
@endif