<?php

namespace Modules\TwoThemes\Controllers;


use PDO;
use Two\Support\Str;
use Two\Support\Facades\DB;
use Two\Support\Facades\Config;
use Two\Support\Facades\Package;
use Two\Support\Facades\Request;
use Modules\TwoThemes\Models\User as User_Model;
use Modules\TwoCore\Core\FrontController;
use Modules\TwoCore\Support\Facades\Metatag;
use Modules\TwoThemes\Support\Facades\Theme;
use Modules\TwoCore\Support\Facades\Metalang;
use Two\Routing\Controller as BaseController;
use Modules\TwoCore\Support\Facades\CmsAssets;
use Modules\TwoCore\Support\Facades\MetaFunction;


class Front extends FrontController
{

    public $packages;


    /**
     * test
     */
    public function test(Request $request)
    {
        //DB::connection()->setFetchMode(PDO::FETCH_ASSOC);
        //DB::connection()->setFetchMode(PDO::FETCH_OBJ);
        //DB::connection()->setFetchMode(PDO::FETCH_CLASS);
        //$query = DB::table('users')->select('*')->get();
        //vd($query[1]['uname']);

        // test multilangue defaut site or langue user
        // vd(__d('two_themes', 'Name'));
        // vd(__d('two_themes', 'Name', null, 'es'));
        // vd(__d('two_themes', 'Name', null, 'de'));

        // $user = User_Model::getUserMailAndLanguage(2);
        // vd($user->email, $user->user_langue, $user['email'], $user['user_langue']);

        // test admin head
        // $fpackage = 'TwoEdito';
        // $icone = 'edito';
        // $package = Package::where('basename', $fpackage);

        // $admf_ext = Config::get('two_core::config.admf_ext');
        // $adminimg = Config::get('two_core::config.adminimg');

        // $namespace = $this->getPackageName($package['name']);
        // vd(app('files')->exists($package['path'] . 'Assets/' . $adminimg . $icone . '.' . $admf_ext));
        // echo '<img src="' . asset_url($adminimg . $icone . '.' . $admf_ext, $namespace) . '" class="vam " alt="test" />';

        // test mise a jours des module enabled or disabled
        // vd(Config::get('packages.options'));

    //    Config::set('packages.options.two_twi.enabled', true);
    //      Config::set('packages.options.two_geoloc.enabled', true);

    //     DB::table('fonctions')->where('fpackage', 'TwoTwi')->update(array(
    //         'fenabled'       => Config::get('packages.options.two_twi.enabled'),
    //     ));

    //     DB::table('fonctions')->where('fpackage', 'TwoGeoloc')->update(array(
    //         'fenabled'       => Config::get('packages.options.two_geoloc.enabled'),
    //     ));

    //     Package::optimize();

        // vd(Config::get('packages.options'));

        //  Config::set('packages.options.two_forum.enabled', true);

        // DB::table('fonctions')->where('fpackage', 'TwoForm')->update(array(
        //     'fenabled'       => Config::get('packages.options.two_forum.enabled'),
        // ));


        return $this->createView()
            ->shares('title', __d('two_themes', 'Test Front theme'));
    }


protected function getPackageName($package)
{
    if (strpos($package, '/') === false) {
        return $package;
    }

    list ($vendor, $namespace) = explode('/', $package);

    $slug = (Str::length($namespace) <= 3) ? Str::lower($namespace) : Str::snake($namespace);

    return Str::lower($vendor) . '/' . $slug;
}

}

