<?php
/**
 * Created by PhpStorm.
 * User: voggenre
 * Date: 21.04.2017
 * Time: 16:09
 */

namespace AppBundle\Api;

use \Psr\Log\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class ApiProblem
 * @package AppBundle\Api
 *
 * A wrapper for holding data to be used for a application/problem+json response
 */
class ApiProblem
{
    const TYPE_VALIDATION_ERROR            = 'validation_error';
    const TYPE_INVALID_REQUEST_BODY_FORMAT = 'invalid_body_format';

    private static $titles = array(
        self::TYPE_VALIDATION_ERROR            => 'There was a validation error',
        self::TYPE_INVALID_REQUEST_BODY_FORMAT => 'Invalid JSON format sent'
    );

    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $title;

    /**
     * @var array
     */
    private $extraData = array();

    /**
     * ApiProblem constructor.
     * @param int $statusCode
     * @param string $type
     */
    public function __construct(int $statusCode, string $type = null)
    {
        $this->statusCode = $statusCode;

        if ($type === null) {
            // no type? The default is about:blank and the title should be the standard status code message
            $type = 'about:blank';
            $title = isset(Response::$statusTexts[$statusCode]) ? Response::$statusTexts[$statusCode] : 'Unknown status code :(';
        } else {
            if (!isset(self::$titles[$type])) {
                throw new \InvalidArgumentException('No title for type: '.$type);
            }
            $title = self::$titles[$type];
        }

        $this->type  = $type;
        $this->title = $title;
    }

    /**
     * @return array
     */
    public function toArray() {
        return array_merge(
            $this->extraData,
            array(
                'status' => $this->statusCode,
                'type'   => $this->type,
                'title'  => $this->title
            )
        );
    }

    /**
     * @param string $name
     * @param $value
     */
    public function set(string $name, $value) {
        $this->extraData[$name] = $value;
    }

    /**
     * @return int
     */
    public function getStatusCode() {
        return $this->statusCode;
    }

    /**
     * @return mixed|string
     */
    public function getTitle() {
        return $this->title;
    }
}