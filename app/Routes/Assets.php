<?php
/**
 * Two - Assets
 *
 * @author  Nicolas Devoy
 * @email   nicolas.l.devoy@gmail.com 
 * @version 1.0.0
 * @date    07 Mai 2024
 */

use Two\Http\Request;
use Two\Support\Facades\Config;


// Enregistrez l'itinéraire pour les ressources à partir du dossier principal des ressources.
$dispatcher->route('assets/(:all)', function (Request $request, $path) use ($dispatcher)
{
    $basePath = Config::get('routing.assets.path', BASEPATH .'assets');

    $path = $basePath .DS .str_replace('/', DS, $path);

    return $dispatcher->serve($path, $request);
});

// Enregistrez l'itinéraire pour les actifs des packages, modules et thèmes.
$dispatcher->route('packages/(:any)/(:any)/(:all)', function (Request $request, $vendor, $package, $path) use ($dispatcher)
{
    $namespace = $vendor .'/' .$package;

    return $dispatcher->servePackageFile($namespace, $path, $request);
});

// Enregistrez l'itinéraire pour les actifs des packages, modules et thèmes.
$dispatcher->route('shared/(:all)', function (Request $request, $path) use ($dispatcher)
{
    return $dispatcher->serveSharedFile($path, $request);
});

// Enregistrez l'itinéraire pour les actifs du fournisseur.
$dispatcher->route('vendor/(:all)', function (Request $request, $path) use ($dispatcher)
{
    return $dispatcher->serveVendorFile($path, $request);
});
