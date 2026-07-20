@extends('errors.layout', [
    'status' => 404,
    'title' => 'No encontramos esta página',
    'message' => 'La ruta que intentaste abrir no existe o ya no está disponible.',
    'detail' => 'Revisa la URL o vuelve al inicio para retomar la navegación en Apollo.',
    'primaryAction' => 'Volver al inicio',
    'secondaryAction' => 'Regresar',
])
