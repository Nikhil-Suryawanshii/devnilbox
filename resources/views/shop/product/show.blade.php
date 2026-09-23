@extends('layouts.app')

@section('header-title', __('Product Details'))

@section('content')
    <div>
        <h4>
            {{ __('Product Details') }}
        </h4>
    </div>

    <div class="card mt-3 shadow-sm">
        <div class="card-body">
            <div class="row g-4">

                <!-- Product Image Section -->
                <div class="col-lg-5">
                    <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="{{ $product->thumbnail }}" class="d-block w-100" alt="Product Image">
                            </div>
                            @foreach ($product->thumbnails() as $photo)
                                <div class="carousel-item">
                                    <img src="{{ $photo->thumbnail }}" class="d-block w-100" alt="Product Image">
                                </div>
                            @endforeach
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#productCarousel"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>

                    <!-- Thumbnail Images -->
                    <div class="d-flex mt-3">
                        @foreach ($product->thumbnails() as $photo)
                            <img src="{{ $photo->thumbnail }}" class="img-thumbnail me-2" style="width: 50px;">
                        @endforeach
                    </div>
                </div>

                <!-- Product Details Section -->
                <div class="col-lg-7">
                    <span class="badge bg-primary">{{ $product->brand?->name }}</span>
                    <h2 class="mt-3">{{ $product->name }}</h2>
                    <p class="text-muted">
                        {{ $product->short_description }}
                    </p>

                    <div class="d-flex align-items-center">
                        <div class="me-2 text-warning">
                            ★★★★☆
                        </div>
                        <span class="fw-bold">4</span>
                        <span class="text-muted ms-2">({{ $product->reviews->count() }} Reviews)</span>
                        <span class="mx-3 border-start px-3">{{ $product->orders->count() }} Sold</span>
                    </div>

                    <!-- Pricing -->
                    <div class="my-4">
                        <h3 class="text-danger">
                            {{ showCurrency($product->discount_price > 0 ? $product->discount_price : $product->price) }}
                            @if ($product->discount_price > 0)
                                <del class="text-muted">{{ showCurrency($product->price) }}</del>
                            @endif
                        </h3>
                        @if ($product->getDiscountPercentage($product->price, $product->discount_price) > 0)
                            <span class="badge bg-danger">Save
                                {{ number_format($product->getDiscountPercentage($product->price, $product->discount_price)) }}%</span>
                        @endif
                    </div>

                    @if ($product->sizes->count() > 0)
                    <!-- Size Selection -->
                    <div class="mb-3">
                        <label class="fw-bold">Size</label>
                        <div class="d-flex gap-2">
                            @foreach ($product->sizes as $size)
                                <input type="radio" disabled class="btn-check" name="size" id="size{{ $size->id }}">
                                <label class="btn btn-outline-secondary"
                                    for="size{{ $size->id }}">{{ $size->name }}</label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if ($product->colors->count() > 0)
                    <!-- Color Selection -->
                    <div class="mb-3">
                        <label class="fw-bold">Color</label>
                        <div class="d-flex gap-2">
                            @foreach ($product->colors as $color)
                                <input type="radio" disabled class="btn-check" name="color" id="color{{ $color->id }}">
                                <label class="btn btn-outline-secondary"
                                    for="color{{ $color->id }}">{{ $color->name }}</label>
                            @endforeach
                        </div>
                    </div>
                     @endif
                    <!-- Quantity Selection -->
                    <div class="mb-3">
                        <label class="fw-bold">Quantity:</label>
                        <span>{{ $product->quantity }}</span>
                    </div>

                    <a href="/products/{{ $product->id }}/details" target="_blank" class="btn btn-outline-primary">
                        <i class="fa-solid fa-globe"></i> {{ __('View Live') }}
                    </a>
                </div>
            </div>

            <h5 class="text-dark fw-bold mt-4">{{ __('Description') }}</h5>
            <p>{!! $product->description !!}</p>

            <div class="row mt-4 g-3">
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <h6 class="fw-bold">{{ __('Warranty') }}</h6>
                        <p class="mb-0">{{ $product->warranty_label ?: __('—') }}</p>
                        <small class="text-muted">{{ $product->warranty_note }}</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <h6 class="fw-bold">{{ __('Return') }}</h6>
                        <p class="mb-0">
                            {{ $product->return_days ? $product->return_days.' '.__('days') : __('—') }}
                        </p>
                        <small class="text-muted">{{ $product->return_note }}</small>
                    </div>
                </div>
            </div>

            @if ($product->features->count())
                <h5 class="text-dark fw-bold mt-4">{{ __('Key Features') }}</h5>
                <ul>
                    @foreach ($product->features as $feature)
                        <li>{{ $feature->title }}</li>
                    @endforeach
                </ul>
            @endif

            @if ($product->boxItems->count())
                <h5 class="text-dark fw-bold mt-4">{{ __("What's in the Box") }}</h5>
                <ul>
                    @foreach ($product->boxItems as $item)
                        <li>{{ $item->item_name }}</li>
                    @endforeach
                </ul>
            @endif

            @if ($product->specifications->count())
                <h5 class="text-dark fw-bold mt-4">{{ __('Specifications') }}</h5>
                <table class="table table-sm">
                    @foreach ($product->specifications as $spec)
                        <tr>
                            <td class="text-muted">{{ $spec->label }}</td>
                            <td class="fw-semibold">{{ $spec->value }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif

            @if ($product->faqs->count())
                <h5 class="text-dark fw-bold mt-4">{{ __('Q&A') }}</h5>
                @foreach ($product->faqs as $faq)
                    <div class="mb-3">
                        <div class="fw-semibold">{{ $faq->question }}</div>
                        <div class="text-muted">{{ $faq->answer }}</div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

@endsection

@push('css')
    <style>
        iframe {
            height: 380px;
        }
    </style>
@endpush
