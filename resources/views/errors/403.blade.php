@extends('errors.layout', [
    'status' => 403,
    'title' => 'Acceso restringido',
    'message' => 'Tu usuario no tiene permisos para consultar esta seccion.',
    'detail' => 'Si necesitas acceso, solicita la autorizacion correspondiente al administrador de la suite.',
    'primaryAction' => 'Volver al inicio',
    'secondaryAction' => 'Regresar',
])
