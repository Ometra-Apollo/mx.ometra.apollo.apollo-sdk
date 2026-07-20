@extends('errors.layout', [
    'status' => 403,
    'title' => 'Acceso restringido',
    'message' => 'Tu usuario no tiene permisos para consultar esta sección.',
    'detail' => 'Si necesitas acceso, solicita la autorización correspondiente al administrador de la suite.',
    'primaryAction' => 'Volver al inicio',
    'secondaryAction' => 'Regresar',
])
