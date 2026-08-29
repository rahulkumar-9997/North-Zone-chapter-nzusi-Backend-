<!DOCTYPE html>
<html lang="en">
    <head>
        @include('backend.layouts.head')
    </head>
    <body>
        <!-- <div id="global-loader">
            <div class="whirly-loader"> </div>
        </div> -->
        <div class="main-wrapper">
            @include('backend.layouts.header')
            @include('backend.layouts.sidebar')
            <div class="page-wrapper">
			    <div class="content-section">
                    @yield('main-content')
                </div>
                @include('backend.layouts.footer')
                @include('backend.layouts.common-modal-form')
            </div>
        </div>        
        @auth
            @php
                $user = auth()->user();
                $canReviewGuidelines = $user->hasRole('abstract-reviewer');
            @endphp
            @if(
                $canReviewGuidelines &&
                request()->routeIs([
                    'dashboard',
                    'abstract-submission.index',
                    'abstract-submission.show',
                    'abstract-review.score'
                ])
            )
                @include('backend.pages.abstract-reviewer.partials.review-guidelines-modal')
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        var modalEl = document.getElementById('reviewGuidelinesModal');

                        if (modalEl && typeof bootstrap !== 'undefined') {
                            new bootstrap.Modal(modalEl).show();
                        }
                    });
                </script>
            @endif
        @endauth
        @include('backend.layouts.footer-js')        
    </body>
</html>