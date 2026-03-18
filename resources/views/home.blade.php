@extends('layouts.app')

@section('content')
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30 text-center">
        <h2 class="page-title">{{ __('Menu Principal') }}</h2>
    </div>
    <!-- ========== title-wrapper end ========== -->
    <div class="container mt-4">
        <div class="row">
          @foreach ($platos as $plato)
            <div class="col-md-4 mb-4">
              <div class="card h-100 shadow-sm">
                <img src="{{ $plato->imagen ? asset('storage/' . $plato->imagen) : asset('images/recetas.jpg') }}" alt="{{ $plato->nombre }}">
                <div class="card-body">
                  <h5 class="card-title" style="color:#4a1c1c;">{{ $plato->nombre }}</h5>
                  <p class="card-text">{{ $plato->descripcion ?: $plato->indicaciones }}</p>
                  <p class="text-muted fw-bold">Precio: $100</p>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>

    <script src="https://cdn.jsdelivr.net/npm/inferencejs"></script>
    @vite(['resources/js/game.js'])
@endsection
