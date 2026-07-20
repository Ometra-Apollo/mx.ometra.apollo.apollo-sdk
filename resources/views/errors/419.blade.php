@extends('errors.layout', [
    'status' => 419,
    'title' => 'La sesión expiró',
    'message' => 'La página estuvo abierta demasiado tiempo y la solicitud ya no es válida.',
    'detail' => 'Actualiza la página e intenta la acción de nuevo para mantener tus datos protegidos.',
    'primaryAction' => 'Volver al inicio',
    'showRetry' => true,
])
