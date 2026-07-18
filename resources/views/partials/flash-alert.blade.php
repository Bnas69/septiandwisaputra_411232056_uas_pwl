@if(session('success'))
    <div class="alert alert-success flash-alert alert-dismissible fade show" role="alert">
        <i data-lucide="circle-check" class="me-1"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger flash-alert alert-dismissible fade show" role="alert">
        <i data-lucide="circle-alert" class="me-1"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('warning'))
    <div class="alert alert-warning flash-alert alert-dismissible fade show" role="alert">
        <i data-lucide="triangle-alert" class="me-1"></i>{{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('info'))
    <div class="alert alert-info flash-alert alert-dismissible fade show" role="alert">
        <i data-lucide="info" class="me-1"></i>{{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
