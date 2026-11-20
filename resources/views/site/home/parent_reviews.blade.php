{{-- Only real, consented reviews from parents (admin → Content → Reviews). --}}
@include('site.home.partials.reviews', ['reviews' => $parentReviews])
