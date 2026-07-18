@if($errors->any())
    <div class="alert-danger-custom">
        <i data-lucide="triangle-alert"></i>
        <div>
            <strong>Terjadi kesalahan!</strong> Silakan periksa input Anda.
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
