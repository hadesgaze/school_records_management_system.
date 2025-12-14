@extends('layouts.chairperson')
@section('title', 'Faculty (My Program)')

@section('content')
@php use Illuminate\Support\Str; @endphp
<div class="container-fluid py-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="bi bi-people me-2"></i> Faculty (My Program)</h4>

        <form method="GET" class="d-flex" style="max-width: 340px;">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-secondary"></i>
                </span>
                <input type="text" name="q" value="{{ $q }}" class="form-control border-start-0" placeholder="Search faculty...">
                <button class="btn btn-primary"><i class="bi bi-arrow-repeat"></i></button>
            </div>
        </form>
    </div>

    @if($faculty->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center text-muted py-5">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                No faculty found for your program.
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach($faculty as $f)
                <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                    <a href="{{ route('chairperson.faculty.show', $f->id) }}"
                       class="text-decoration-none">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body d-flex align-items-center">
                                <img src="{{ $f->avatar }}" alt="{{ $f->name }}"
                                     class="rounded-circle me-3"
                                     style="width:56px; height:56px; object-fit:cover;">
                                <div class="text-truncate">
                                    <div class="fw-semibold text-dark">{{ $f->name }}</div>
                                    @if($f->username)
                                        <small class="text-muted">{{ '@'.$f->username }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
