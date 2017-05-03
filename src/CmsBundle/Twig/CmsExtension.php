<?php

namespace CmsBundle\Twig;

use CmsBundle\Twig\Sort_TokenParser;

class CmsExtension extends \Twig_Extension
{
    /**
     * @var array
     */
    protected $roleDictionary = array(
        'ROLE_ADMIN' => 'Administrator'
    );

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('querystring', array($this, 'querystringFilter')),
            new \Twig_SimpleFilter('truncate', array($this, 'truncateFilter')),
            new \Twig_SimpleFilter('role', array($this, 'roleFilter'))
        );
    }

    /**
     * Filtr przycinający opisy do podanej długości w ten sposób, że nie przerywa końcowych wyrazów w połowie.
     *
     * @package    Smarty
     *
     * @param string $string formatowany ciąg
     * @param int $len długość skróconego opisu
     * @param string $end znak kończący skrócony opis
     *
     * @return string przeformatowany ciąg
     */
    function truncateFilter($string, $len = 50, $end = '...')
    {
        if (strlen($string) > $len) {
            $arr = explode(' ', $string);
            $return = '';
            foreach ((array)$arr as $item) {
                if (mb_strlen($return . $item . ' ') > $len) {
                    break;
                }

                $return .= $item . ' ';
            }

            $last = mb_substr(trim($return), -1);

            // usuwanie znaków interpunkcyjnych wyświetlających się na końcu ciągu
            if (preg_match('/[,;:"\.\']+/', $last)) {
                return trim(mb_substr(trim($return), 0, -1)) . '...';
            } else {
                return trim($return) . $end;
            }
        } else {
            echo $string;
        }
    }

    /**
     * Filtr konwertujący tablicę parametrów w querystring.
     * Umożliwia również dodanie własnego parametru.
     *
     * @param array $queryParams Parametry z pobrane z querystring
     * @param array $customParams Dodatkowy parametr
     *
     * @return string
     */
    public function querystringFilter($queryParams, $customParams = array())
    {
        $querystring = "?";

        $params = array_merge($queryParams, $customParams);

        if ($params) {
            foreach ($params as $key => $value) {
                $querystring .= $key . "=" . $value . "&";
            }
        }

        return substr($querystring, 0, -1);
    }

    /**
     * Filtr tłumacząca rolę zaciąganą z bazy.
     *
     * @param $role
     *
     * @return mixed
     */
    public function roleFilter($role) {
        if(isset($this->roleDictionary[$role])) {
            return $this->roleDictionary[$role];
        } else {
            return $role;
        }
    }

    public function getName()
    {
        return 'app_extension';
    }
}