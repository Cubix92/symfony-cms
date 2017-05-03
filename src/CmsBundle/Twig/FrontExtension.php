<?php

namespace CmsBundle\Twig;

class FrontExtension extends \Twig_Extension
{
    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('generateMenu', array($this, 'generateMenuFunction'))
        );
    }

    /**
     * Generates menu with proper deep
     *
     * @param string $slug
     * @param array $customAttr
     *
     * @return string
     */
    function generateMenuFunction($slug, array $customAttr = array())
    {

        $menuItems = $this->em->getRepository('CmsBundle:MenuItem')->fetchMenuBySlug($slug);

        if($menuItems) {

            $defaultAttr = array(
                'ul' => 'nav navbar-nav navbar-right',
                'li' => ''
            );

            $attributes = array_merge($defaultAttr, $customAttr);

            $menu = '<ul class="' . $attributes['ul'] . '">';

            foreach($menuItems as $item) {

                $in_blank = $item['in_blank'] ? 'target="_blank"' : '';

                if(isset($item['children'])) {
                    $menu .= '<li class="drop"><a href="' . $item['url'] . '" ' . $in_blank . '>' . $item['name'] . '</a>
                                    <ul class="dropdown">';

                    foreach($item['children'] as $child) {
                        $menu .= '<li class="second-drop"><a href="' . $child['url'] . '" ' . $in_blank . '>' . $child['name'] . '</a>';
                        if(isset($child['children'])) {
                            $in_blank = $child['in_blank'] ? 'target="_blank"' : '';
                            $menu .= '<ul class="second-dropdown">';
                            foreach($child['children'] as $grandchild) {
                                $in_blank = $grandchild['in_blank'] ? 'target="_blank"' : '';
                                $menu .= '<li><a href="' . $grandchild['url'] . '" ' . $in_blank . '>' . $grandchild['name'] . '</a></li>';
                            }
                            $menu .= '</ul>';
                        }

                        $menu .= '</li>';
                    }

                    $menu .= '</ul></li>';
                } else {
                    $menu .= '<li><a href="' . $item['url'] . '" ' . $in_blank . '>' . $item['name'] . '</a></li>';
                }

            }

            $menu .= '<li class="search drop" style="margin-right: 60px;"></li>';

            $menu .= '</ul>';
        } else {
            $menu = null;
        }


        return $menu;

    }

    public function getName()
    {
        return 'front_extension';
    }
}