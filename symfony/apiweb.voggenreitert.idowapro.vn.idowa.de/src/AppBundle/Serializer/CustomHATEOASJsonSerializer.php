<?php
/**
 * Created by PhpStorm.
 * User: voggenre
 * Date: 26.04.2017
 * Time: 15:17
 */

namespace AppBundle\Serializer;


use Hateoas\Model\Embedded;
use Hateoas\Model\Link;
use Hateoas\Serializer\JsonHalSerializer;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\SerializationContext;

class CustomHATEOASJsonSerializer extends JsonHalSerializer
{
    /**
     * @param Link[]                   $links
     * @param JsonSerializationVisitor $visitor
     * @param SerializationContext     $context
     */
    public function serializeLinks(array $links, JsonSerializationVisitor $visitor, SerializationContext $context)
    {
        $serializedLinks = array();
        foreach ($links as $link) {
            $serializedLinks[$link->getRel()] = $link->getHref();
        }
        $visitor->setData('_links', $serializedLinks);
    }

    /**
     * @param Embedded[]               $embeddeds
     * @param JsonSerializationVisitor $visitor
     * @param SerializationContext     $context
     */
    public function serializeEmbeddeds(array $embeddeds, JsonSerializationVisitor $visitor, SerializationContext $context)
    {

    }
}