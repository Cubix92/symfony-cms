<?php

namespace CmsBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class SlugToPageTransformer implements DataTransformerInterface
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Transforms a slug to a page.
     *
     * @param  string $slug
     * @return page
     */
    public function transform($slug)
    {
        return $this->manager->getRepository('CmsBundle:Page')->findOneBySlug($slug);
    }

    /**
     * Transforms a page to a slug
     *
     * @param  Page $page
     * @return Issue|null
     */
    public function reverseTransform($page)
    {
        return $page->getSlug();
    }
}