@extends('errors.layout', [
    'status' => 401,
    'title' => 'Necesitas iniciar sesión',
    'message' => 'No encontramos una sesión válida para abrir este recurso.',
    'detail' => 'Inicia sesión de nuevo o vuelve al inicio para continuar trabajando en Apollo.',
    'primaryAction' => 'Ir al inicio',
    'secondaryAction' => 'Regresar',
])
