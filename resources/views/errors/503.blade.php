@extends('errors.layout', [
    'status' => 503,
    'title' => 'Servicio no disponible',
    'message' => 'La aplicacion esta en mantenimiento o no puede responder en este momento.',
    'detail' => 'Espera un momento y reintenta la solicitud. El acceso se restablecera cuando el servicio este listo.',
    'primaryAction' => 'Volver al inicio',
    'showRetry' => true,
])
