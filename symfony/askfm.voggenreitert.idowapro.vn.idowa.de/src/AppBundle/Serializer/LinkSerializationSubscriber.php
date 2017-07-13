<?php
/**
 * Created by PhpStorm.
 * User: voggenre
 * Date: 25.04.2017
 * Time: 10:33
 */

namespace AppBundle\Serializer;


use AppBundle\Annotation\Link;
use AppBundle\Entity\Programmer;
use Doctrine\Common\Annotations\Reader;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\JsonSerializationVisitor;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Routing\RouterInterface;

class LinkSerializationSubscriber implements EventSubscriberInterface
{
    private $router;

    private $annotationsReader;

    private $expressionLanguage;

    public function __construct(RouterInterface $router, Reader $annotationsReader)
    {
        $this->router = $router;
        $this->annotationsReader = $annotationsReader;
        $this->expressionLanguage = new ExpressionLanguage();
    }

    public static function getSubscribedEvents()
    {
        return array(
            array(
                'event'  => 'serializer.post_serialize',
                'method' => 'onPostSerialize',
                'format' => 'json'
            )
        );
    }

    public function onPostSerialize(ObjectEvent $event) {
        /**
         * @var JsonSerializationVisitor $visitor
         */
        $visitor = $event->getVisitor();

        $object = $event->getObject();
        $annotations = $this->annotationsReader->getClassAnnotations(new \ReflectionObject($object));

        $links = array();
        foreach ($annotations as $annotation) {
            if ($annotation instanceof Link) {
                $uri = $this->router->generate(
                    $annotation->route,
                    $this->resolveParams($annotation->params, $object)
                );
                $links[$annotation->name] = $uri;
            }
        }
        if ($links) {
            $visitor->setData('_links', $links);
        }
    }

    public function resolveParams(array $params, $object) {
        foreach ($params as $key => $param) {
            $params[$key] = $this->expressionLanguage->evaluate($param, array('object' => $object));
        }
        return $params;
    }
}