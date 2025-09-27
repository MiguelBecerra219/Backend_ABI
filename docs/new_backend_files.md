# Resumen de archivos backend añadidos

A continuación se describe de forma breve la función de cada archivo nuevo incorporado para el CRUD solicitado. Todos estos cambios ya están versionados en la rama actual del repositorio:

- **app/Http/Controllers/ContentController.php**: expone endpoints REST para listar, crear, consultar, actualizar y eliminar contenidos de catálogo, incluyendo filtros de búsqueda y validaciones para relaciones dependientes.
- **app/Http/Controllers/ContentVersionController.php**: gestiona los registros que asocian contenidos con versiones de proyectos, permitiendo CRUD y filtros por contenido, versión o proyecto.
- **app/Http/Controllers/VersionController.php**: administra las versiones de proyecto disponibles, con endpoints para CRUD y consulta de contenidos relacionados.
- **app/Http/Requests/ContentRequest.php**: encapsula la validación y normalización de datos de contenidos (nombre, descripción y roles permitidos) para las operaciones del controlador.
- **app/Http/Requests/ContentVersionRequest.php**: define reglas de validación y prevención de duplicados al relacionar contenidos con versiones específicas de un proyecto.
- **app/Http/Requests/VersionRequest.php**: valida las operaciones sobre versiones garantizando la referencia correcta a proyectos existentes.
- **tests/Feature/ContentApiTest.php**: cubre escenarios de creación, validación de roles y eliminación protegida para los endpoints de contenidos.
- **tests/Feature/ContentVersionApiTest.php**: verifica el comportamiento del API de registros contenido-versión, incluyendo restricciones de duplicados.

