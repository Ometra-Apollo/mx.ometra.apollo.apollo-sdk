@extends('errors.layout', [
    'status' => 503,
    'title' => 'Servicio no disponible',
    'message' => 'La aplicación está en mantenimiento o no puede responder en este momento.',
    'detail' => 'Espera un momento y reintenta la solicitud. El acceso se restablecerá cuando el servicio esté listo.',
    'primaryAction' => 'Volver al inicio',
    'showRetry' => true,
])
