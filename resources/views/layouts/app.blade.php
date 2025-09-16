{{-- resources/views/layouts/app.blade.php --}}
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>@yield('title','ABI')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- Tabler CSS (CDN) --}}
  <link href="https://unpkg.com/@tabler/core@1.0.0-beta19/dist/css/tabler.min.css" rel="stylesheet"/>

  {{-- Vite (opción A, sin Tailwind) --}}
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>
  <div class="page">
    <header class="navbar navbar-expand-md d-print-none">
      <div class="container-xl">
        <a class="navbar-brand" href="{{ route('frameworks.index') }}">
          <span class="navbar-brand-text">ABI</span>
        </a>
      </div>
    </header>

    <div class="page-wrapper">
      <div class="page-body">
        <div class="container-xl">
          @if(session('ok'))
            <div class="alert alert-success">{{ session('ok') }}</div>
          @endif
          @yield('content')
        </div>
      </div>
    </div>
  </div>

  {{-- Tabler JS (CDN) --}}
  <script src="https://unpkg.com/@tabler/core@1.0.0-beta19/dist/js/tabler.min.js"></script>

  {{-- Doble confirmación reutilizable --}}
  <script>
    // Botón con data-confirm y data-action="#formId"
    document.addEventListener('click', (e) => {
      const b = e.target.closest('[data-confirm]');
      if(!b) return;
      e.preventDefault();
      const wrap = b.parentElement;
      let confirmBtn = wrap.querySelector('.btn-confirm');
      if (confirmBtn) return;

      confirmBtn = document.createElement('button');
      confirmBtn.className = 'btn btn-danger btn-confirm';
      confirmBtn.style.opacity = .35;
      confirmBtn.textContent = 'Confirmar';
      wrap.appendChild(confirmBtn);

      setTimeout(() => { confirmBtn.style.opacity = 1; confirmBtn.dataset.armed = '1'; }, 1200);

      confirmBtn.addEventListener('click', () => {
        if (!confirmBtn.dataset.armed) return;
        const formId = b.dataset.action;
        document.querySelector(formId)?.submit();
      });

      const cancel = document.createElement('button');
      cancel.className = 'btn btn-link text-secondary';
      cancel.textContent = 'Cancelar';
      cancel.addEventListener('click', () => { confirmBtn.remove(); cancel.remove(); });
      wrap.appendChild(cancel);
    });
  </script>

  @stack('scripts')
</body>
</html>
