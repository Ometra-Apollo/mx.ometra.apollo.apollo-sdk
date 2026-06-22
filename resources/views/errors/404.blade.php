@extends('errors.layout', [
    'status' => 404,
    'title' => 'No encontramos esta pagina',
    'message' => 'La ruta que intentaste abrir no existe o ya no esta disponible.',
    'detail' => 'Revisa la URL o vuelve al inicio para retomar la navegacion en Apollo.',
    'primaryAction' => 'Volver al inicio',
    'secondaryAction' => 'Regresar',
])
