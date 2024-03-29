<?php
/**
 * Tease Plugin based on Div Syntax Component of the Wrap Plugin
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Anika Henke <anika@selfthinker.org>
 */

class syntax_plugin_dokuteaser_dokuteaser extends DokuWiki_Syntax_Plugin
{

    /** @inheritdoc  */
    function getType()
    {
        return 'formatting';
    }

    /** @inheritdoc  */
    function getAllowedTypes()
    {
        return array('container', 'formatting', 'substition', 'protected', 'disabled', 'paragraphs');
    }

    /** @inheritdoc  */
    function getPType()
    {
        return 'stack';
    }

    /** @inheritdoc  */
    function getSort()
    {
        return 195;
    }

    /**
     * override default accepts() method to allow nesting - ie, to get the plugin accepts its own entry syntax
     * @inheritdoc
     */
    function accepts($mode)
    {
        if ($mode == substr(get_class($this), 7)) return true;
        return parent::accepts($mode);
    }

    /** @inheritdoc  */
    function connectTo($mode)
    {
        $this->Lexer->addEntryPattern('<dokuteaser.*?>(?=.*?</dokuteaser>)', $mode, 'plugin_dokuteaser_dokuteaser');
    }

    /** @inheritdoc  */
    function postConnect()
    {
        $this->Lexer->addExitPattern('</dokuteaser>', 'plugin_dokuteaser_dokuteaser');
    }

    /**
     * Handle the match
     */
    function handle($match, $state, $pos, Doku_Handler $handler)
    {
        global $conf;
        switch ($state) {
            case DOKU_LEXER_ENTER:
                $sep = strpos($match, ' ');
                if ($sep === false) $sep = strlen($match);
                $data = strtolower(trim(substr($match, $sep, -1)));
                return array($state, $data);

            case DOKU_LEXER_UNMATCHED:
                // check if $match is a == header ==
                $headerMatch = preg_grep('/([ \t]*={2,}[^\n]+={2,}[ \t]*(?=))/msSi', array($match));
                if (empty($headerMatch)) {
                    $handler->addCall('cdata', array($match), $pos);
                } else {
                    // if it's a == header ==, use the core header() renderer
                    // (copied from core header() in inc/parser/handler.php)
                    $title = trim($match);
                    $level = 7 - strspn($title, '=');
                    if ($level < 1) $level = 1;
                    $title = trim($title, '=');
                    $title = trim($title);

                    $handler->addCall('header', array($title, $level, $pos), $pos);
                    // close the section edit the header could open
                    if ($title && $level <= $conf['maxseclevel']) {
                        $handler->addPluginCall('dokuteaser_closesection', array(), DOKU_LEXER_SPECIAL, $pos, '');
                    }
                }
                return false;

            case DOKU_LEXER_EXIT:
                return array($state, '');
        }
        return false;
    }

    /** @inheritdoc  */
    function render($format, Doku_Renderer $renderer, $data)
    {

        if (empty($data)) return false;
        list($state, $attribute) = $data;

        if ($format == 'xhtml') {
            /** @var Doku_Renderer_xhtml $renderer */
            switch ($state) {
                case DOKU_LEXER_ENTER:
                    // add a section edit right at the beginning of the wrap output
                    $renderer->startSectionEdit(0, ['target' => 'plugin_dokuteaser_start']);
                    $renderer->finishSectionEdit();
                    // add a section edit for the end of the wrap output. This prevents the renderer
                    // from closing the last section edit so the next section button after the wrap syntax will
                    // include the whole wrap syntax
                    $renderer->startSectionEdit(0, ['target' => 'plugin_dokuteaser_end']);

                    $class = '';
                    if ($attribute == 'left') $class = ' dokuteaser-left';
                    if ($attribute == 'right') $class = ' dokuteaser-right';
                    $renderer->doc .= '<div class="dokuteaser' . $class . '">';
                    break;

                case DOKU_LEXER_EXIT:
                    $renderer->doc .= "</div>";
                    $renderer->finishSectionEdit();
                    break;
            }
            return true;
        }
        return false;
    }

}

