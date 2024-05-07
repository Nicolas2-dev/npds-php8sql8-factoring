<?php
/**
 * Two - Metatag Manager
 *
 * @author  Nicolas Devoy
 * @email   nicolas.l.devoy@gmail.com 
 * @version 1.0.0
 * @date    07 Mai 2024
 */

declare(strict_types=1);

namespace Modules\TwoCore\Library\Metatags;

use InvalidArgumentException;

use Two\Support\Arr;
use Two\Foundation\Application;
use Two\Support\Facades\Config;
use Modules\TwoThemes\Support\Traits\ThemeFavicorait;


class MetatagManager
{

    use ThemeFavicorait;

    /**
     * The Application Instance.
     *
     * @var \Two\Application\Application
     */
    public $app;

    /**
     * Les types de métas
     *
     * @var array
     */
    protected $types = array('metatag', 'charset', 'equiv', 'name', 'link', 'link-type', 'link-title', 'property', 'favicon');

    /**
     * Les positions des métas
     *
     * @var array
     */
    protected $positions = array(
        'metatag'  => array(),        
        'charset'  => array(),        
        'favicon'  => array(),        
    );

    /**
     * Les méta-modèles
     *
     * @var array
     */
    protected static $templates = array(
        'equiv'         => '<meta http-equiv="%s" content="%s" />',
        'name'          => '<meta name="%s" content="%s" />',
        'charset'       => '<meta %s="%s" />',
        'link'          => '<link rel="%s" href="%s" />',
        'link-type'     => '<link rel="%s" href="%s" type="%s" />',
        'link-title'    => '<link rel="%s" title="%s" href="%s" type="%s" />',
        'property'      => '<meta property="%s" content="%s" />',
        'favicon'       => '<link rel="shortcut icon" href="%s" type="image/x-icon"/>'
    );

    /**
     * Meta Description
     *
     * @var string
     */
    private $metaDescription;

    /**
     * Meta Keywords
     *
     * @var string
     */
    private $metaKeywords;

    /**
     * [$metatags description]
     *
     * @var array
     */
    private $metatags;


    /**
     * Créez une nouvelle instance de Metas Manager.
     *
     * @return void
     */
    public function __construct(Application $app, array $metatags)
    {
        $this->app = $app;

        $this->metatags = $metatags;
    }

    /**
     * Prepare l'enregistrement de nouveaux métas.
     *
     * @param  string|array $metas
     * @param  string $type
     * @param  string $position
     *
     * @return void
     * @throws \InvalidArgumentException
     */
    public function prepare($metas, $type, $position, $order = 0)
    {
        if (! in_array($type, $this->types)) {
            throw new InvalidArgumentException("Invalid metas type [{$type}]");
        }
        // Le type de métas est valide.
        else if (! empty($items = $this->parseMetas($type, $metas, $order))) {
            // Nous fusionnerons les éléments pour la position spécifiés.
            Arr::set($this->positions, $key = "{$position}.{$position}", array_merge(
                Arr::get($this->positions, $key, array()), $items
            ));
        }
    }

    /**
     * Afficher les métas pour les positions spécifiées
     *
     * @param  string|array $position
     * @param  string $type
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function position($position, $type)
    {
        if (! in_array($type, $this->types)) {
            throw new InvalidArgumentException("Invalid metas type [{$type}]");
        }

        $positions = is_array($position) ? $position : array($position);

        //
        $result = array();
        foreach ($positions as $position) {
            $items = Arr::get($this->positions, "{$position}", array());
            foreach($items as $item) {
                if (! empty($item)) {
                    $result = array_merge($result, $this->renderItems($item, true));
                }
            } 
        }

        return implode("\n", array_unique($result));
    }

    /**
     * Rendre la balise Favicon ou Metatag.
     *
     * @param string       $type
     * @param string|array $metas
     *
     * @return string|null
     * @throws \InvalidArgumentException
     */
    public function render($type, $metas)
    {
        if (! in_array($type, $this->types)) {
            throw new InvalidArgumentException("Invalid metas type [{$type}]");
        }
        // Le type de métas est valide.
        else if (! empty($type)) {  
            $items = array('meta' => $metas, 'type' => $type);
            $result = $this->renderItems(array($items), false);
            
            return implode("\n", array_unique($result));
        }
    }

    /**
     * Rendre les éléments de position donnés dans un tableau de métas.
     *
     * @param  array $items
     * @param string $type
     *
     * @return array
     */
    protected function renderItems(array $items, $sorted = true)
    {
        if ($sorted) {
            static::sortItems($items);
        }

        return array_map(function ($item)
        {
            $meta = Arr::get($item, 'meta');

            if (is_array($meta)) { 

                if($item['type'] == 'link-title') {
                    $template = Arr::get(static::$templates, "{$item['type']}");              
                    
                    return sprintf($template, $meta[0], (Config::get('app.name').' '.$meta[1]), site_url($meta[2]), $meta[3]);

                } elseif($item['type'] == 'link-type') {
                    $template = Arr::get(static::$templates, "{$item['type']}");              
                    
                    return sprintf($template, $meta[0], site_url($meta[1]), $meta[2]);

                } elseif($item['type'] == 'link') {
                    $template = Arr::get(static::$templates, "{$item['type']}");              
                    
                    return sprintf($template, $meta[0], $meta[1]);
    
                } else {
                    if (isset($this->metaDescription) && ($meta[0] == 'description') && ($item['type'] == 'name')) {
                        $meta[1] = $this->metaDescription;
                    }

                    if (isset($this->metaKeywords) && ($meta[0] == 'keywords') && ($item['type'] == 'name')) {
                        $meta[1] = $this->metaKeywords;
                    }

                    $template = Arr::get(static::$templates, "{$item['type']}");

                    return sprintf($template, $meta[0], $meta[1]);
                }
            } else {
                $template = Arr::get(static::$templates, "{$item['type']}");

                return sprintf($template, $meta);
            }
        }, $items);
    }

    /**
     * Om remplace la description de base par une nouvelle description.
     *
     * @return string
     */
    public function setMetaDescription($description)
    {
        $this->metaDescription = $description;

        return $this;
    }

    /**
     * Om remplace la keywords de base par une nouvelle description.
     *
     * @return string
     */
    public function setMetaKeywords($Keywords)
    {
        $this->metaKeywords = $Keywords;

        return $this;
    }

    /**
     * Sort the given items by their order.
     *
     * @param  array $items
     *
     * @return void
     */
    protected static function sortItems(array &$items)
    {
        usort($items, function ($a, $b)
        {
            if ($a['order'] === $b['order']) {
                return 0;
            }

            return ($a['order'] < $b['order']) ? -1 : 1;
        });
    }

    /**
     * Analyse et renvoie les metas donnés.
     *
     * @param  string|array $metas
     *
     * @return array
     */
    protected function parseMetas($type, $metas, $order = 0)
    {
        if (is_string($metas) && ! empty($metas)) {
            $metas = array($metas);
        } else if (! is_array($metas)) {
            return array();
        }

        return array_map(function ($meta) use ($type, $order)
        {
            return compact('meta', 'order', 'type');
        }, array_filter($metas, function ($value)
        {
            return ! empty($value);
        }));   
    }

    /**
     * on enregistre tout les metatags dans le registre.
     *
     *
     * @return array
     */  
    function register()
    {
        foreach($this->metatags as $metatag => $meta) {
            foreach($meta as $key) {
                $this->prepare([[$key['name'], $key['content']]], $key['type'], $metatag, $key['order']);
            }
        }

        // humans.txt
        $this->registerHumans();

        // cannonical
        $this->registerCannonical();

        // favico
        $this->favico();
    }

    /**
     * Rendre les metatags dans la balise head
     *
     * @return  string
     */
    public function metas($print = true)
    {
        $metatags = $this->position('charset', 'charset');
        $metatags .= $this->position('metatag', 'metatag');
        $metatags .= $this->position('favicon', 'favicon');

        if ($print) {
            echo $metatags;
        } else {
            return $metatags;
        }
    }

    /**
     * On enregistre le metatag humans dans le registre si le fichier existe dans la webroot.
     *
     *
     * @return array
     */
    private function registerHumans()
    {
        if ($this->app->files->exists(WEBPATH .DS. 'humans.txt')) {
            $this->prepare([['author', site_url('humans.txt'), 'text/plain']], 'link-type', 'metatag', 400);
        }
    }

    /**
     * On enregistre le metatag cannonical dans le registre.
     *
     *
     * @return array
     */
    private function registerCannonical()
    {
        $uri = $this->app->request->getUri();

        $this->prepare([['cannonical', $uri]], 'link', 'metatag', 400);
    }

}
