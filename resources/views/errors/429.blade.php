@extends('errors.layout', [
    'status' => 429,
    'title' => 'Demasiadas solicitudes',
    'message' => 'Recibimos muchas acciones en poco tiempo desde esta sesión.',
    'detail' => 'Espera unos segundos y vuelve a intentarlo para que el servicio pueda responder correctamente.',
    'primaryAction' => 'Volver al inicio',
    'showRetry' => true,
])
