@extends('errors.layout', [
    'status' => 500,
    'title' => 'Algo fallo en el servicio',
    'message' => 'Apollo no pudo completar la solicitud por un error interno.',
    'detail' => 'El evento puede quedar registrado para revision tecnica. Intenta de nuevo o vuelve al inicio.',
    'primaryAction' => 'Volver al inicio',
    'showRetry' => true,
])
