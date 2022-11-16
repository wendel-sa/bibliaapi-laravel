<section class="container">
    <div class="row">
        <div class="col-xl-9 col-lg-8">
            <div wire:loading>
                <div class="h-100 d-flex justify-content-center align-items-center">
                    <div class="spinner-border text-primary" role="status">
                    </div>
                </div>
            </div>

            <div class="card-body" wire:loading.remove>
                <div class="row">
                    <div class="col-12 row">
                        <div class="col-4">
                            <h1 class="fs-lg-6 fs-md-5 fs-3">{{ $versiculos['book']['name'] }}</h1>
                        </div>
                        <div class="col-4">
                            <select wire:model="cap" class="form-select">
                                <option value="">Selecione um capitulo</option>
                                @for($i = 1; $i <= $qtdCapitulos; $i++) <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                            </select>
                        </div>
                        <div class="col-4">
                            <button class="btn btn-outline-secondary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasLeft">
                                Mais detalhes
                            </button>
                        </div>
                    </div>
                    <div class="col-12">
                        @foreach($versiculos['verses'] as $versiculo)
                        <p class="fs-lg-6 fs-md-5 fs-3">{{ $versiculo['number'] }} - {{ $versiculo['text'] }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-4">
            <div class="offcanvas-lg offcanvas-end" id="" tabindex="-1">

                <!-- Body -->
                <div class="offcanvas-body row row-cols-1 g-4">

                    <!-- Search form -->
                    <div class="input-group mb-4 col">
                        <input type="text" placeholder="Busque na biblia..." class="form-control rounded pe-5" wire:model.lazy="palavra">
                        <span class="badge rounded-pill bg-danger ms-2 position-absolute top-50 end-0 translate-middle-y me-3 fs-lg zindex-5">beta</span>
                    </div>

                    <!-- versoes -->
                    <div class="card card-body mb-4 col">
                        <h3 class="h5">Versões</h3>
                        <ul class="nav flex-column fs-sm">

                            @foreach ($versoes as $versao)
                            <li class="nav-item mb-1">
                                <div class="form-group">
                                    <input type="radio" class="form-check-input" wire:model="versaoSelecionada" value="{{ $versao['version'] }}" id="{{ $versao['version'] }}">
                                    <label class="form-check-label" for="{{ $versao['version'] }}">{{ $versao['version'] }}</label>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="card card-body mb-4 overflow-auto col" style="height: 300px;">
                        <h3 class="h5">Livros</h3>
                        <ul class="nav flex-column fs-sm">

                            @foreach ($livros as $livro)
                            <li class="nav-item mb-1">
                                <div class="form-group">
                                    <input type="radio" class="form-check-input" wire:model="abbrev" value="{{ $livro['abbrev']['pt'] }}" id="{{ $livro['abbrev']['pt'] }}">
                                    <label class="form-check-label" for="{{ $livro['abbrev']['pt'] }}">{{ $livro['name'] }}</label>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>


    </div>
</section>

<!-- Offcanvas -->
<div wire:loading.remove>
    <div class="offcanvas offcanvas-start" id="offcanvasLeft" tabindex="-1">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title">Livro: {{ $detalhesLivro['name'] }}</h5>
            <button class="btn-close" type="button" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h1 class="fs-lg-6 fs-md-5 fs-3">Capítulos: {{ $detalhesLivro['chapters'] }}</h1>
                            <p class="fs-lg-6 fs-md-5 fs-3">Versículos: {{ $detalhesLivro['group'] }}</p>
                            <p class="fs-lg-6 fs-md-5 fs-3">Testamento: {{ $detalhesLivro['testament'] }}</p>
                            <p class="">{{ $detalhesLivro['comment'] ?? '' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
