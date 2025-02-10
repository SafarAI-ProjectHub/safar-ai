<div class="alert alert-{{ $type }} border-0 bg-{{ $type }} alert-dismissible fade show py-2 position-fixed top-0 end-0 m-3"
    role="alert" style="z-index: 10">
    <div class="d-flex align-items-center">
        <div class="font-35 text-white">
            @if ($icon)
                <i class="{{ $icon }}"></i>
            @else
                @switch($type)
                    @case('primary')
                        <i class='bx bx-bookmark-heart'></i>
                    @break

                    @case('secondary')
                        <i class='bx bx-tag-alt'></i>
                    @break

                    @case('success')
                        <i class='bx bxs-check-circle'></i>
                    @break

                    @case('danger')
                        <i class='bx bxs-message-square-x'></i>
                    @break

                    @case('warning')
                        <i class='bx bx-info-circle'></i>
                    @break

                    @case('info')
                        <i class='bx bx-info-square'></i>
                    @break

                    @case('dark')
                        <i class='bx bx-bell'></i>
                    @break

                    @default
                        <i class='bx bx-info-circle'></i>
                @endswitch
            @endif
        </div>
        <div class="ms-3">
            <h6 class="mb-0 text-white">{{ $title }}</h6>
            <div class="text-white">{{ $message }}</div>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<style>
    .alert {
        transition: opacity 0.5s ease-in-out;
        z-index: 10 !important;
    }

    .alert-dismissible .btn-close {
        position: absolute;
        top: 0.25rem;
        right: 0.25rem;
    }
</style>
