@extends('Layouts/Default')

@section('content')
<div class="row">
    @if ($title)
    <h1><strong>{{ ($title !== 'Home') ? $title : ''; }}</strong></h1>
    @endif
    <hr style="margin-top: 0;">
</div>

{{ $content; }}

@stop

@section('footer')
<footer class="footer">
    <div class="container-fluid">
        <div class="row" style="margin: 15px 0 0;">
            <div class="col-lg-5">
                <p class="text-muted">Copyright &copy; {{ date('Y') }} <a href="http://www.Twoframework.fr/" target="_blank"><b>Two Framework {{ VERSION }} / Kernel {{ App::version() }}</b></a></p>
            </div>
            <div class="col-lg-7">
                <p class="text-muted pull-right">
                    <small><!-- DO NOT DELETE! - Statistics --></small>
                </p>
            </div>
        </div>
    </div>
</footer>
@stop
