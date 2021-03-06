<?php

namespace gist_it_php;

/**
 * Class View
 *
 */
class View
{

    private const VAR            = '{{(?P<var>.*?)}}';
    private const VIEWS_PATH     = _APP_ . '/views/';
    private const VIEW_EXTENSION = '.tpl';

    /**
     * @var string
     */
    private $content;

    /**
     * @var Embed
     */
    private $template;

    public function __construct(string $view_name, Embed $template)
    {

        $this->template = $template;
        $this->content  = $this->getContentFromView(static::VIEWS_PATH . $view_name . static::VIEW_EXTENSION);
    }

    private function getContentFromView(string $view)
    {

        if (! file_exists($view)) {
            $this->viewNotFound($view);
        }

        return file_get_contents($view);
    }

    /**
     * @param string $view
     *
     * @throws \Exception
     */
    private function viewNotFound(string $view)
    {

        throw new \Exception("View {$view} not found");
    }

    public function parse():string
    {

        return preg_replace_callback('/' . self::VAR . '/us', [ $this, 'findTagValue' ], $this->content);
    }

    private function findTagValue($tag)
    {

        $var = $tag[ 'var' ];
        $method = "get{$var}";

        return $this->addJSNewLines($this->template->$method());
    }

    private function addJSNewLines(string $string_with_html_newlines)
    {
        return preg_replace('%\n%', '\n', $string_with_html_newlines);
    }
}
