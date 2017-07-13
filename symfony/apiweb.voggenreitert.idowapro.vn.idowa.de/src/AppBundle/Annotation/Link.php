<?php
/**
 * Created by PhpStorm.
 * User: voggenre
 * Date: 25.04.2017
 * Time: 13:16
 */

namespace AppBundle\Annotation;

use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class Link
 * @package AppBundle\Annotation
 *
 * @Annotation
 * @Target("CLASS")
 */
class Link
{
    /**
     * @Required
     *
     * @var string
     */
    public $name;

    /**
     * @Required
     *
     * @var string
     */
    public $route;

    /**
     * @var array
     */
    public $params = array();
}