@extends('errors.layout', [
    'status' => 419,
    'title' => 'La sesion expiro',
    'message' => 'La pagina estuvo abierta demasiado tiempo y la solicitud ya no es valida.',
    'detail' => 'Actualiza la pagina e intenta la accion de nuevo para mantener tus datos protegidos.',
    'primaryAction' => 'Volver al inicio',
    'showRetry' => true,
])
