@extends('errors.layout', [
    'status' => 500,
    'title' => 'Algo falló en el servicio',
    'message' => 'Apollo no pudo completar la solicitud por un error interno.',
    'detail' => 'El evento puede quedar registrado para revisión técnica. Intenta de nuevo o vuelve al inicio.',
    'primaryAction' => 'Volver al inicio',
    'showRetry' => true,
])
