@extends('errors.layout', [
    'status' => 401,
    'title' => 'Necesitas iniciar sesion',
    'message' => 'No encontramos una sesion valida para abrir este recurso.',
    'detail' => 'Inicia sesion de nuevo o vuelve al inicio para continuar trabajando en Apollo.',
    'primaryAction' => 'Ir al inicio',
    'secondaryAction' => 'Regresar',
])
