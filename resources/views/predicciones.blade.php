@extends('layouts.app')

@section('content')
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title mb-30">
                    <h2>{{ __('Predicciones') }}</h2>
                </div>
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
    </div>
    <!-- ========== title-wrapper end ========== -->

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Predicción de Desperdicios</h3>
                    </div>
                    <div class="card-body">
                        @livewire('predicciondesperdicio')
                    </div>
                </div>
            </div>
        </div>
    </div>

    @livewire('predicciondos')

    @livewire('prediccionuno')

    @livewire('calculoinsumo')
@endsection
